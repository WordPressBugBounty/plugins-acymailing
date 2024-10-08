<?php

namespace WpOrg\Requests;

use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Utility\InputValidator;

final class Ssl {
	public static function verify_certificate($host, $cert) {
		if (InputValidator::is_string_or_stringable($host) === false) {
			throw InvalidArgument::create(1, '$host', 'string|Stringable', gettype($host));
		}

		if (InputValidator::has_array_access($cert) === false) {
			throw InvalidArgument::create(2, '$cert', 'array|ArrayAccess', gettype($cert));
		}

		$has_dns_alt = false;

		if (!empty($cert['extensions']['subjectAltName'])) {
			$altnames = explode(',', $cert['extensions']['subjectAltName']);
			foreach ($altnames as $altname) {
				$altname = trim($altname);
				if (strpos($altname, 'DNS:') !== 0) {
					continue;
				}

				$has_dns_alt = true;

				$altname = trim(substr($altname, 4));

				if (self::match_domain($host, $altname) === true) {
					return true;
				}
			}

			if ($has_dns_alt === true) {
				return false;
			}
		}

		if (!empty($cert['subject']['CN'])) {
			return (self::match_domain($host, $cert['subject']['CN']) === true);
		}

		return false;
	}

	public static function verify_reference_name($reference) {
		if (InputValidator::is_string_or_stringable($reference) === false) {
			throw InvalidArgument::create(1, '$reference', 'string|Stringable', gettype($reference));
		}

		if ($reference === '') {
			return false;
		}

		if (preg_match('`\s`', $reference) > 0) {
			return false;
		}

		$parts = explode('.', $reference);
		if ($parts !== array_filter($parts)) {
			return false;
		}

		$first = array_shift($parts);

		if (strpos($first, '*') !== false) {
			if ($first !== '*') {
				return false;
			}

			if (count($parts) < 2) {
				return false;
			}
		}

		foreach ($parts as $part) {
			if (strpos($part, '*') !== false) {
				return false;
			}
		}

		return true;
	}

	public static function match_domain($host, $reference) {
		if (InputValidator::is_string_or_stringable($host) === false) {
			throw InvalidArgument::create(1, '$host', 'string|Stringable', gettype($host));
		}

		if (self::verify_reference_name($reference) !== true) {
			return false;
		}

		if ((string) $host === (string) $reference) {
			return true;
		}

		if (ip2long($host) === false) {
			$parts    = explode('.', $host);
			$parts[0] = '*';
			$wildcard = implode('.', $parts);
			if ($wildcard === (string) $reference) {
				return true;
			}
		}

		return false;
	}
}
