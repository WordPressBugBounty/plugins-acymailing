<?php

namespace WpOrg\Requests;

use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Ipv6;
use WpOrg\Requests\Port;
use WpOrg\Requests\Utility\InputValidator;

class Iri {
	protected $scheme = null;

	protected $iuserinfo = null;

	protected $ihost = null;

	protected $port = null;

	protected $ipath = '';

	protected $iquery = null;

	protected $ifragment = null;

	protected $normalization = array(
		'acap' => array(
			'port' => Port::ACAP,
		),
		'dict' => array(
			'port' => Port::DICT,
		),
		'file' => array(
			'ihost' => 'localhost',
		),
		'http' => array(
			'port' => Port::HTTP,
		),
		'https' => array(
			'port' => Port::HTTPS,
		),
	);

	public function __toString() {
		return $this->get_iri();
	}

	public function __set($name, $value) {
		if (method_exists($this, 'set_' . $name)) {
			call_user_func(array($this, 'set_' . $name), $value);
		}
		elseif (
			   $name === 'iauthority'
			|| $name === 'iuserinfo'
			|| $name === 'ihost'
			|| $name === 'ipath'
			|| $name === 'iquery'
			|| $name === 'ifragment'
		) {
			call_user_func(array($this, 'set_' . substr($name, 1)), $value);
		}
	}

	public function __get($name) {
		$props = get_object_vars($this);

		if (
			$name === 'iri' ||
			$name === 'uri' ||
			$name === 'iauthority' ||
			$name === 'authority'
		) {
			$method = 'get_' . $name;
			$return = $this->$method();
		}
		elseif (array_key_exists($name, $props)) {
			$return = $this->$name;
		}
		elseif (($prop = 'i' . $name) && array_key_exists($prop, $props)) {
			$name = $prop;
			$return = $this->$prop;
		}
		elseif (($prop = substr($name, 1)) && array_key_exists($prop, $props)) {
			$name = $prop;
			$return = $this->$prop;
		}
		else {
			trigger_error('Undefined property: ' . get_class($this) . '::' . $name, E_USER_NOTICE);
			$return = null;
		}

		if ($return === null && isset($this->normalization[$this->scheme][$name])) {
			return $this->normalization[$this->scheme][$name];
		}
		else {
			return $return;
		}
	}

	public function __isset($name) {
		return (method_exists($this, 'get_' . $name) || isset($this->$name));
	}

	public function __unset($name) {
		if (method_exists($this, 'set_' . $name)) {
			call_user_func(array($this, 'set_' . $name), '');
		}
	}

	public function __construct($iri = null) {
		if ($iri !== null && InputValidator::is_string_or_stringable($iri) === false) {
			throw InvalidArgument::create(1, '$iri', 'string|Stringable|null', gettype($iri));
		}

		$this->set_iri($iri);
	}

	public static function absolutize($base, $relative) {
		if (!($relative instanceof self)) {
			$relative = new self($relative);
		}
		if (!$relative->is_valid()) {
			return false;
		}
		elseif ($relative->scheme !== null) {
			return clone $relative;
		}

		if (!($base instanceof self)) {
			$base = new self($base);
		}
		if ($base->scheme === null || !$base->is_valid()) {
			return false;
		}

		if ($relative->get_iri() !== '') {
			if ($relative->iuserinfo !== null || $relative->ihost !== null || $relative->port !== null) {
				$target = clone $relative;
				$target->scheme = $base->scheme;
			}
			else {
				$target = new self;
				$target->scheme = $base->scheme;
				$target->iuserinfo = $base->iuserinfo;
				$target->ihost = $base->ihost;
				$target->port = $base->port;
				if ($relative->ipath !== '') {
					if ($relative->ipath[0] === '/') {
						$target->ipath = $relative->ipath;
					}
					elseif (($base->iuserinfo !== null || $base->ihost !== null || $base->port !== null) && $base->ipath === '') {
						$target->ipath = '/' . $relative->ipath;
					}
					elseif (($last_segment = strrpos($base->ipath, '/')) !== false) {
						$target->ipath = substr($base->ipath, 0, $last_segment + 1) . $relative->ipath;
					}
					else {
						$target->ipath = $relative->ipath;
					}
					$target->ipath = $target->remove_dot_segments($target->ipath);
					$target->iquery = $relative->iquery;
				}
				else {
					$target->ipath = $base->ipath;
					if ($relative->iquery !== null) {
						$target->iquery = $relative->iquery;
					}
					elseif ($base->iquery !== null) {
						$target->iquery = $base->iquery;
					}
				}
				$target->ifragment = $relative->ifragment;
			}
		}
		else {
			$target = clone $base;
			$target->ifragment = null;
		}
		$target->scheme_normalization();
		return $target;
	}

	protected function parse_iri($iri) {
		$iri = trim($iri, "\x20\x09\x0A\x0C\x0D");
		$has_match = preg_match('/^((?P<scheme>[^:\/?#]+):)?(\/\/(?P<authority>[^\/?#]*))?(?P<path>[^?#]*)(\?(?P<query>[^#]*))?(#(?P<fragment>.*))?$/', $iri, $match);
		if (!$has_match) {
			throw new Exception('Cannot parse supplied IRI', 'iri.cannot_parse', $iri);
		}

		if ($match[1] === '') {
			$match['scheme'] = null;
		}
		if (!isset($match[3]) || $match[3] === '') {
			$match['authority'] = null;
		}
		if (!isset($match[5])) {
			$match['path'] = '';
		}
		if (!isset($match[6]) || $match[6] === '') {
			$match['query'] = null;
		}
		if (!isset($match[8]) || $match[8] === '') {
			$match['fragment'] = null;
		}
		return $match;
	}

	protected function remove_dot_segments($input) {
		$output = '';
		while (strpos($input, './') !== false || strpos($input, '/.') !== false || $input === '.' || $input === '..') {
			if (strpos($input, '../') === 0) {
				$input = substr($input, 3);
			}
			elseif (strpos($input, './') === 0) {
				$input = substr($input, 2);
			}
			elseif (strpos($input, '/./') === 0) {
				$input = substr($input, 2);
			}
			elseif ($input === '/.') {
				$input = '/';
			}
			elseif (strpos($input, '/../') === 0) {
				$input = substr($input, 3);
				$output = substr_replace($output, '', (strrpos($output, '/') ?: 0));
			}
			elseif ($input === '/..') {
				$input = '/';
				$output = substr_replace($output, '', (strrpos($output, '/') ?: 0));
			}
			elseif ($input === '.' || $input === '..') {
				$input = '';
			}
			elseif (($pos = strpos($input, '/', 1)) !== false) {
				$output .= substr($input, 0, $pos);
				$input = substr_replace($input, '', 0, $pos);
			}
			else {
				$output .= $input;
				$input = '';
			}
		}
		return $output . $input;
	}

	protected function replace_invalid_with_pct_encoding($text, $extra_chars, $iprivate = false) {
		$text = preg_replace_callback('/(?:%[A-Fa-f0-9]{2})+/', array($this, 'remove_iunreserved_percent_encoded'), $text);

		$text = preg_replace('/%(?![A-Fa-f0-9]{2})/', '%25', $text);

		$extra_chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~%';

		$position = 0;
		$strlen = strlen($text);
		while (($position += strspn($text, $extra_chars, $position)) < $strlen) {
			$value = ord($text[$position]);

			$start = $position;

			$valid = true;

			if (($value & 0xE0) === 0xC0) {
				$character = ($value & 0x1F) << 6;
				$length = 2;
				$remaining = 1;
			}
			elseif (($value & 0xF0) === 0xE0) {
				$character = ($value & 0x0F) << 12;
				$length = 3;
				$remaining = 2;
			}
			elseif (($value & 0xF8) === 0xF0) {
				$character = ($value & 0x07) << 18;
				$length = 4;
				$remaining = 3;
			}
			else {
				$valid = false;
				$length = 1;
				$remaining = 0;
			}

			if ($remaining) {
				if ($position + $length <= $strlen) {
					for ($position++; $remaining; $position++) {
						$value = ord($text[$position]);

						if (($value & 0xC0) === 0x80) {
							$character |= ($value & 0x3F) << (--$remaining * 6);
						}
						else {
							$valid = false;
							$position--;
							break;
						}
					}
				}
				else {
					$position = $strlen - 1;
					$valid = false;
				}
			}

			if (
				!$valid
				|| $length > 1 && $character <= 0x7F
				|| $length > 2 && $character <= 0x7FF
				|| $length > 3 && $character <= 0xFFFF
				|| ($character & 0xFFFE) === 0xFFFE
				|| $character >= 0xFDD0 && $character <= 0xFDEF
				|| (
					   $character > 0xD7FF && $character < 0xF900
					|| $character < 0xA0
					|| $character > 0xEFFFD
				)
				&& (
					   !$iprivate
					|| $character < 0xE000
					|| $character > 0x10FFFD
				)
			) {
				if ($valid) {
					$position--;
				}

				for ($j = $start; $j <= $position; $j++) {
					$text = substr_replace($text, sprintf('%%%02X', ord($text[$j])), $j, 1);
					$j += 2;
					$position += 2;
					$strlen += 2;
				}
			}
		}

		return $text;
	}

	protected function remove_iunreserved_percent_encoded($regex_match) {
		$bytes = explode('%', $regex_match[0]);

		$string = '';
		$remaining = 0;

		for ($i = 1, $len = count($bytes); $i < $len; $i++) {
			$value = hexdec($bytes[$i]);

			if (!$remaining) {
				$start = $i;

				$valid = true;

				if ($value <= 0x7F) {
					$character = $value;
					$length = 1;
				}
				elseif (($value & 0xE0) === 0xC0) {
					$character = ($value & 0x1F) << 6;
					$length = 2;
					$remaining = 1;
				}
				elseif (($value & 0xF0) === 0xE0) {
					$character = ($value & 0x0F) << 12;
					$length = 3;
					$remaining = 2;
				}
				elseif (($value & 0xF8) === 0xF0) {
					$character = ($value & 0x07) << 18;
					$length = 4;
					$remaining = 3;
				}
				else {
					$valid = false;
					$remaining = 0;
				}
			}
			else {
				if (($value & 0xC0) === 0x80) {
					$remaining--;
					$character |= ($value & 0x3F) << ($remaining * 6);
				}
				else {
					$valid = false;
					$remaining = 0;
					$i--;
				}
			}

			if (!$remaining) {
				if (
					!$valid
					|| $length > 1 && $character <= 0x7F
					|| $length > 2 && $character <= 0x7FF
					|| $length > 3 && $character <= 0xFFFF
					|| $character < 0x2D
					|| $character > 0xEFFFD
					|| ($character & 0xFFFE) === 0xFFFE
					|| $character >= 0xFDD0 && $character <= 0xFDEF
					|| $character === 0x2F
					|| $character > 0x39 && $character < 0x41
					|| $character > 0x5A && $character < 0x61
					|| $character > 0x7A && $character < 0x7E
					|| $character > 0x7E && $character < 0xA0
					|| $character > 0xD7FF && $character < 0xF900
				) {
					for ($j = $start; $j <= $i; $j++) {
						$string .= '%' . strtoupper($bytes[$j]);
					}
				}
				else {
					for ($j = $start; $j <= $i; $j++) {
						$string .= chr(hexdec($bytes[$j]));
					}
				}
			}
		}

		if ($remaining) {
			for ($j = $start; $j < $len; $j++) {
				$string .= '%' . strtoupper($bytes[$j]);
			}
		}

		return $string;
	}

	protected function scheme_normalization() {
		if (isset($this->normalization[$this->scheme]['iuserinfo']) && $this->iuserinfo === $this->normalization[$this->scheme]['iuserinfo']) {
			$this->iuserinfo = null;
		}
		if (isset($this->normalization[$this->scheme]['ihost']) && $this->ihost === $this->normalization[$this->scheme]['ihost']) {
			$this->ihost = null;
		}
		if (isset($this->normalization[$this->scheme]['port']) && $this->port === $this->normalization[$this->scheme]['port']) {
			$this->port = null;
		}
		if (isset($this->normalization[$this->scheme]['ipath']) && $this->ipath === $this->normalization[$this->scheme]['ipath']) {
			$this->ipath = '';
		}
		if (isset($this->ihost) && empty($this->ipath)) {
			$this->ipath = '/';
		}
		if (isset($this->normalization[$this->scheme]['iquery']) && $this->iquery === $this->normalization[$this->scheme]['iquery']) {
			$this->iquery = null;
		}
		if (isset($this->normalization[$this->scheme]['ifragment']) && $this->ifragment === $this->normalization[$this->scheme]['ifragment']) {
			$this->ifragment = null;
		}
	}

	public function is_valid() {
		$isauthority = $this->iuserinfo !== null || $this->ihost !== null || $this->port !== null;
		if ($this->ipath !== '' &&
			(
				$isauthority && $this->ipath[0] !== '/' ||
				(
					$this->scheme === null &&
					!$isauthority &&
					strpos($this->ipath, ':') !== false &&
					(strpos($this->ipath, '/') === false ? true : strpos($this->ipath, ':') < strpos($this->ipath, '/'))
				)
			)
		) {
			return false;
		}

		return true;
	}

	protected function set_iri($iri) {
		static $cache;
		if (!$cache) {
			$cache = array();
		}

		if ($iri === null) {
			return true;
		}

		$iri = (string) $iri;

		if (isset($cache[$iri])) {
			list($this->scheme,
				 $this->iuserinfo,
				 $this->ihost,
				 $this->port,
				 $this->ipath,
				 $this->iquery,
				 $this->ifragment,
				 $return) = $cache[$iri];
			return $return;
		}

		$parsed = $this->parse_iri($iri);

		$return = $this->set_scheme($parsed['scheme'])
			&& $this->set_authority($parsed['authority'])
			&& $this->set_path($parsed['path'])
			&& $this->set_query($parsed['query'])
			&& $this->set_fragment($parsed['fragment']);

		$cache[$iri] = array($this->scheme,
							 $this->iuserinfo,
							 $this->ihost,
							 $this->port,
							 $this->ipath,
							 $this->iquery,
							 $this->ifragment,
							 $return);
		return $return;
	}

	protected function set_scheme($scheme) {
		if ($scheme === null) {
			$this->scheme = null;
		}
		elseif (!preg_match('/^[A-Za-z][0-9A-Za-z+\-.]*$/', $scheme)) {
			$this->scheme = null;
			return false;
		}
		else {
			$this->scheme = strtolower($scheme);
		}
		return true;
	}

	protected function set_authority($authority) {
		static $cache;
		if (!$cache) {
			$cache = array();
		}

		if ($authority === null) {
			$this->iuserinfo = null;
			$this->ihost = null;
			$this->port = null;
			return true;
		}
		if (isset($cache[$authority])) {
			list($this->iuserinfo,
				 $this->ihost,
				 $this->port,
				 $return) = $cache[$authority];

			return $return;
		}

		$remaining = $authority;
		if (($iuserinfo_end = strrpos($remaining, '@')) !== false) {
			$iuserinfo = substr($remaining, 0, $iuserinfo_end);
			$remaining = substr($remaining, $iuserinfo_end + 1);
		}
		else {
			$iuserinfo = null;
		}

		if (($port_start = strpos($remaining, ':', (strpos($remaining, ']') ?: 0))) !== false) {
			$port = substr($remaining, $port_start + 1);
			if ($port === false || $port === '') {
				$port = null;
			}
			$remaining = substr($remaining, 0, $port_start);
		}
		else {
			$port = null;
		}

		$return = $this->set_userinfo($iuserinfo) &&
				  $this->set_host($remaining) &&
				  $this->set_port($port);

		$cache[$authority] = array($this->iuserinfo,
								   $this->ihost,
								   $this->port,
								   $return);

		return $return;
	}

	protected function set_userinfo($iuserinfo) {
		if ($iuserinfo === null) {
			$this->iuserinfo = null;
		}
		else {
			$this->iuserinfo = $this->replace_invalid_with_pct_encoding($iuserinfo, '!$&\'()*+,;=:');
			$this->scheme_normalization();
		}

		return true;
	}

	protected function set_host($ihost) {
		if ($ihost === null) {
			$this->ihost = null;
			return true;
		}
		if (substr($ihost, 0, 1) === '[' && substr($ihost, -1) === ']') {
			if (Ipv6::check_ipv6(substr($ihost, 1, -1))) {
				$this->ihost = '[' . Ipv6::compress(substr($ihost, 1, -1)) . ']';
			}
			else {
				$this->ihost = null;
				return false;
			}
		}
		else {
			$ihost = $this->replace_invalid_with_pct_encoding($ihost, '!$&\'()*+,;=');

			$position = 0;
			$strlen = strlen($ihost);
			while (($position += strcspn($ihost, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ%', $position)) < $strlen) {
				if ($ihost[$position] === '%') {
					$position += 3;
				}
				else {
					$ihost[$position] = strtolower($ihost[$position]);
					$position++;
				}
			}

			$this->ihost = $ihost;
		}

		$this->scheme_normalization();

		return true;
	}

	protected function set_port($port) {
		if ($port === null) {
			$this->port = null;
			return true;
		}

		if (strspn($port, '0123456789') === strlen($port)) {
			$this->port = (int) $port;
			$this->scheme_normalization();
			return true;
		}

		$this->port = null;
		return false;
	}

	protected function set_path($ipath) {
		static $cache;
		if (!$cache) {
			$cache = array();
		}

		$ipath = (string) $ipath;

		if (isset($cache[$ipath])) {
			$this->ipath = $cache[$ipath][(int) ($this->scheme !== null)];
		}
		else {
			$valid = $this->replace_invalid_with_pct_encoding($ipath, '!$&\'()*+,;=@:/');
			$removed = $this->remove_dot_segments($valid);

			$cache[$ipath] = array($valid, $removed);
			$this->ipath = ($this->scheme !== null) ? $removed : $valid;
		}
		$this->scheme_normalization();
		return true;
	}

	protected function set_query($iquery) {
		if ($iquery === null) {
			$this->iquery = null;
		}
		else {
			$this->iquery = $this->replace_invalid_with_pct_encoding($iquery, '!$&\'()*+,;=:@/?', true);
			$this->scheme_normalization();
		}
		return true;
	}

	protected function set_fragment($ifragment) {
		if ($ifragment === null) {
			$this->ifragment = null;
		}
		else {
			$this->ifragment = $this->replace_invalid_with_pct_encoding($ifragment, '!$&\'()*+,;=:@/?');
			$this->scheme_normalization();
		}
		return true;
	}

	protected function to_uri($iri) {
		if (!is_string($iri)) {
			return false;
		}

		static $non_ascii;
		if (!$non_ascii) {
			$non_ascii = implode('', range("\x80", "\xFF"));
		}

		$position = 0;
		$strlen = strlen($iri);
		while (($position += strcspn($iri, $non_ascii, $position)) < $strlen) {
			$iri = substr_replace($iri, sprintf('%%%02X', ord($iri[$position])), $position, 1);
			$position += 3;
			$strlen += 2;
		}

		return $iri;
	}

	protected function get_iri() {
		if (!$this->is_valid()) {
			return false;
		}

		$iri = '';
		if ($this->scheme !== null) {
			$iri .= $this->scheme . ':';
		}
		if (($iauthority = $this->get_iauthority()) !== null) {
			$iri .= '//' . $iauthority;
		}
		$iri .= $this->ipath;
		if ($this->iquery !== null) {
			$iri .= '?' . $this->iquery;
		}
		if ($this->ifragment !== null) {
			$iri .= '#' . $this->ifragment;
		}

		return $iri;
	}

	protected function get_uri() {
		return $this->to_uri($this->get_iri());
	}

	protected function get_iauthority() {
		if ($this->iuserinfo === null && $this->ihost === null && $this->port === null) {
			return null;
		}

		$iauthority = '';
		if ($this->iuserinfo !== null) {
			$iauthority .= $this->iuserinfo . '@';
		}
		if ($this->ihost !== null) {
			$iauthority .= $this->ihost;
		}
		if ($this->port !== null) {
			$iauthority .= ':' . $this->port;
		}
		return $iauthority;
	}

	protected function get_authority() {
		$iauthority = $this->get_iauthority();
		if (is_string($iauthority)) {
			return $this->to_uri($iauthority);
		}
		else {
			return $iauthority;
		}
	}
}
