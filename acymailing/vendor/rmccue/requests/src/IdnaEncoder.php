<?php

namespace WpOrg\Requests;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Utility\InputValidator;

class IdnaEncoder {
	const ACE_PREFIX = 'xn--';

	const MAX_LENGTH = 64;

	const BOOTSTRAP_BASE         = 36;
	const BOOTSTRAP_TMIN         = 1;
	const BOOTSTRAP_TMAX         = 26;
	const BOOTSTRAP_SKEW         = 38;
	const BOOTSTRAP_DAMP         = 700;
	const BOOTSTRAP_INITIAL_BIAS = 72;
	const BOOTSTRAP_INITIAL_N    = 128;

	public static function encode($hostname) {
		if (InputValidator::is_string_or_stringable($hostname) === false) {
			throw InvalidArgument::create(1, '$hostname', 'string|Stringable', gettype($hostname));
		}

		$parts = explode('.', $hostname);
		foreach ($parts as &$part) {
			$part = self::to_ascii($part);
		}

		return implode('.', $parts);
	}

	public static function to_ascii($text) {
		if (self::is_ascii($text)) {
			if (strlen($text) < self::MAX_LENGTH) {
				return $text;
			}

			throw new Exception('Provided string is too long', 'idna.provided_too_long', $text);
		}

		$text = self::nameprep($text);

		if (self::is_ascii($text)) {
			if (strlen($text) < self::MAX_LENGTH) {
				return $text;
			}

			throw new Exception('Prepared string is too long', 'idna.prepared_too_long', $text);
		}

		if (strpos($text, self::ACE_PREFIX) === 0) {
			throw new Exception('Provided string begins with ACE prefix', 'idna.provided_is_prefixed', $text);
		}

		$text = self::punycode_encode($text);

		$text = self::ACE_PREFIX . $text;

		if (strlen($text) < self::MAX_LENGTH) {
			return $text;
		}

		throw new Exception('Encoded string is too long', 'idna.encoded_too_long', $text);
	}

	protected static function is_ascii($text) {
		return (preg_match('/(?:[^\x00-\x7F])/', $text) !== 1);
	}

	protected static function nameprep($text) {
		return $text;
	}

	protected static function utf8_to_codepoints($input) {
		$codepoints = [];

		$strlen = strlen($input);

		for ($position = 0; $position < $strlen; $position++) {
			$value = ord($input[$position]);

			if ((~$value & 0x80) === 0x80) {            // One byte sequence:
				$character = $value;
				$length    = 1;
				$remaining = 0;
			} elseif (($value & 0xE0) === 0xC0) {       // Two byte sequence:
				$character = ($value & 0x1F) << 6;
				$length    = 2;
				$remaining = 1;
			} elseif (($value & 0xF0) === 0xE0) {       // Three byte sequence:
				$character = ($value & 0x0F) << 12;
				$length    = 3;
				$remaining = 2;
			} elseif (($value & 0xF8) === 0xF0) {       // Four byte sequence:
				$character = ($value & 0x07) << 18;
				$length    = 4;
				$remaining = 3;
			} else {                                    // Invalid byte:
				throw new Exception('Invalid Unicode codepoint', 'idna.invalidcodepoint', $value);
			}

			if ($remaining > 0) {
				if ($position + $length > $strlen) {
					throw new Exception('Invalid Unicode codepoint', 'idna.invalidcodepoint', $character);
				}

				for ($position++; $remaining > 0; $position++) {
					$value = ord($input[$position]);

					if (($value & 0xC0) !== 0x80) {
						throw new Exception('Invalid Unicode codepoint', 'idna.invalidcodepoint', $character);
					}

					--$remaining;
					$character |= ($value & 0x3F) << ($remaining * 6);
				}

				$position--;
			}

			if (// Non-shortest form sequences are invalid
				$length > 1 && $character <= 0x7F
				|| $length > 2 && $character <= 0x7FF
				|| $length > 3 && $character <= 0xFFFF
				|| ($character & 0xFFFE) === 0xFFFE
				|| $character >= 0xFDD0 && $character <= 0xFDEF
				|| (
					$character > 0xD7FF && $character < 0xF900
					|| $character < 0x20
					|| $character > 0x7E && $character < 0xA0
					|| $character > 0xEFFFD
				)
			) {
				throw new Exception('Invalid Unicode codepoint', 'idna.invalidcodepoint', $character);
			}

			$codepoints[] = $character;
		}

		return $codepoints;
	}

	public static function punycode_encode($input) {
		$output = '';
		$n = self::BOOTSTRAP_INITIAL_N;
		$delta = 0;
		$bias = self::BOOTSTRAP_INITIAL_BIAS;
		$h = 0;
		$b = 0; // see loop
		$codepoints = self::utf8_to_codepoints($input);
		$extended   = [];

		foreach ($codepoints as $char) {
			if ($char < 128) {
				$output .= chr($char);
				$h++;

			} elseif ($char < $n) {
				throw new Exception('Invalid character', 'idna.character_outside_domain', $char);
			} else {
				$extended[$char] = true;
			}
		}

		$extended = array_keys($extended);
		sort($extended);
		$b = $h;
		if (strlen($output) > 0) {
			$output .= '-';
		}

		$codepointcount = count($codepoints);
		while ($h < $codepointcount) {
			$m = array_shift($extended);
			$delta += ($m - $n) * ($h + 1);
			$n = $m;
			for ($num = 0; $num < $codepointcount; $num++) {
				$c = $codepoints[$num];
				if ($c < $n) {
					$delta++;
				} elseif ($c === $n) { // if c == n then begin
					$q = $delta;
					for ($k = self::BOOTSTRAP_BASE; ; $k += self::BOOTSTRAP_BASE) {
						if ($k <= ($bias + self::BOOTSTRAP_TMIN)) {
							$t = self::BOOTSTRAP_TMIN;
						} elseif ($k >= ($bias + self::BOOTSTRAP_TMAX)) {
							$t = self::BOOTSTRAP_TMAX;
						} else {
							$t = $k - $bias;
						}

						if ($q < $t) {
							break;
						}

						$digit   = (int) ($t + (($q - $t) % (self::BOOTSTRAP_BASE - $t)));
						$output .= self::digit_to_char($digit);
						$q = (int) floor(($q - $t) / (self::BOOTSTRAP_BASE - $t));
					} // end
					$output .= self::digit_to_char($q);
					$bias = self::adapt($delta, $h + 1, $h === $b);
					$delta = 0;
					$h++;
				} // end
			} // end
			$delta++;
			$n++;
		} // end

		return $output;
	}

	protected static function digit_to_char($digit) {
		if ($digit < 0 || $digit > 35) {
			throw new Exception(sprintf('Invalid digit %d', $digit), 'idna.invalid_digit', $digit);
		}

		$digits = 'abcdefghijklmnopqrstuvwxyz0123456789';
		return substr($digits, $digit, 1);
	}

	protected static function adapt($delta, $numpoints, $firsttime) {
		if ($firsttime) {
			$delta = floor($delta / self::BOOTSTRAP_DAMP);
		} else {
			$delta = floor($delta / 2);
		}

		$delta += floor($delta / $numpoints);
		$k = 0;
		$max = floor(((self::BOOTSTRAP_BASE - self::BOOTSTRAP_TMIN) * self::BOOTSTRAP_TMAX) / 2);
		while ($delta > $max) {
			$delta = floor($delta / (self::BOOTSTRAP_BASE - self::BOOTSTRAP_TMIN));
			$k += self::BOOTSTRAP_BASE;
		} // end
		return $k + floor(((self::BOOTSTRAP_BASE - self::BOOTSTRAP_TMIN + 1) * $delta) / ($delta + self::BOOTSTRAP_SKEW));
	}
}
