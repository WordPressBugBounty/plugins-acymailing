<?php

namespace WpOrg\Requests\Transport;

use WpOrg\Requests\Capability;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Port;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Ssl;
use WpOrg\Requests\Transport;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;
use WpOrg\Requests\Utility\InputValidator;

final class Fsockopen implements Transport {
	const SECOND_IN_MICROSECONDS = 1000000;

	public $headers = '';

	public $info;

	private $max_bytes = false;

	private $connect_error = '';

	public function request($url, $headers = [], $data = [], $options = []) {
		if (InputValidator::is_string_or_stringable($url) === false) {
			throw InvalidArgument::create(1, '$url', 'string|Stringable', gettype($url));
		}

		if (is_array($headers) === false) {
			throw InvalidArgument::create(2, '$headers', 'array', gettype($headers));
		}

		if (!is_array($data) && !is_string($data)) {
			if ($data === null) {
				$data = '';
			} else {
				throw InvalidArgument::create(3, '$data', 'array|string', gettype($data));
			}
		}

		if (is_array($options) === false) {
			throw InvalidArgument::create(4, '$options', 'array', gettype($options));
		}

		$options['hooks']->dispatch('fsockopen.before_request');

		$url_parts = parse_url($url);
		if (empty($url_parts)) {
			throw new Exception('Invalid URL.', 'invalidurl', $url);
		}

		$host                     = $url_parts['host'];
		$context                  = stream_context_create();
		$verifyname               = false;
		$case_insensitive_headers = new CaseInsensitiveDictionary($headers);

		if (isset($url_parts['scheme']) && strtolower($url_parts['scheme']) === 'https') {
			$remote_socket = 'ssl://' . $host;
			if (!isset($url_parts['port'])) {
				$url_parts['port'] = Port::HTTPS;
			}

			$context_options = [
				'verify_peer'       => true,
				'capture_peer_cert' => true,
			];
			$verifyname      = true;

			if (defined('OPENSSL_TLSEXT_SERVER_NAME') && OPENSSL_TLSEXT_SERVER_NAME) {
				$context_options['SNI_enabled'] = true;
				if (isset($options['verifyname']) && $options['verifyname'] === false) {
					$context_options['SNI_enabled'] = false;
				}
			}

			if (isset($options['verify'])) {
				if ($options['verify'] === false) {
					$context_options['verify_peer']      = false;
					$context_options['verify_peer_name'] = false;
					$verifyname                          = false;
				} elseif (is_string($options['verify'])) {
					$context_options['cafile'] = $options['verify'];
				}
			}

			if (isset($options['verifyname']) && $options['verifyname'] === false) {
				$context_options['verify_peer_name'] = false;
				$verifyname                          = false;
			}

			stream_context_set_option($context, ['ssl' => $context_options]);
		} else {
			$remote_socket = 'tcp://' . $host;
		}

		$this->max_bytes = $options['max_bytes'];

		if (!isset($url_parts['port'])) {
			$url_parts['port'] = Port::HTTP;
		}

		$remote_socket .= ':' . $url_parts['port'];

		set_error_handler([$this, 'connect_error_handler'], E_WARNING | E_NOTICE);

		$options['hooks']->dispatch('fsockopen.remote_socket', [&$remote_socket]);

		$socket = stream_socket_client($remote_socket, $errno, $errstr, ceil($options['connect_timeout']), STREAM_CLIENT_CONNECT, $context);

		restore_error_handler();

		if ($verifyname && !$this->verify_certificate_from_context($host, $context)) {
			throw new Exception('SSL certificate did not match the requested domain name', 'ssl.no_match');
		}

		if (!$socket) {
			if ($errno === 0) {
				throw new Exception(rtrim($this->connect_error), 'fsockopen.connect_error');
			}

			throw new Exception($errstr, 'fsockopenerror', null, $errno);
		}

		$data_format = $options['data_format'];

		if ($data_format === 'query') {
			$path = self::format_get($url_parts, $data);
			$data = '';
		} else {
			$path = self::format_get($url_parts, []);
		}

		$options['hooks']->dispatch('fsockopen.remote_host_path', [&$path, $url]);

		$request_body = '';
		$out          = sprintf("%s %s HTTP/%.1F\r\n", $options['type'], $path, $options['protocol_version']);

		if ($options['type'] !== Requests::TRACE) {
			if (is_array($data)) {
				$request_body = http_build_query($data, '', '&');
			} else {
				$request_body = $data;
			}

			if (!empty($data) || $options['type'] === Requests::POST) {
				if (!isset($case_insensitive_headers['Content-Length'])) {
					$headers['Content-Length'] = strlen($request_body);
				}

				if (!isset($case_insensitive_headers['Content-Type'])) {
					$headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
				}
			}
		}

		if (!isset($case_insensitive_headers['Host'])) {
			$out         .= sprintf('Host: %s', $url_parts['host']);
			$scheme_lower = strtolower($url_parts['scheme']);

			if (($scheme_lower === 'http' && $url_parts['port'] !== Port::HTTP) || ($scheme_lower === 'https' && $url_parts['port'] !== Port::HTTPS)) {
				$out .= ':' . $url_parts['port'];
			}

			$out .= "\r\n";
		}

		if (!isset($case_insensitive_headers['User-Agent'])) {
			$out .= sprintf("User-Agent: %s\r\n", $options['useragent']);
		}

		$accept_encoding = $this->accept_encoding();
		if (!isset($case_insensitive_headers['Accept-Encoding']) && !empty($accept_encoding)) {
			$out .= sprintf("Accept-Encoding: %s\r\n", $accept_encoding);
		}

		$headers = Requests::flatten($headers);

		if (!empty($headers)) {
			$out .= implode("\r\n", $headers) . "\r\n";
		}

		$options['hooks']->dispatch('fsockopen.after_headers', [&$out]);

		if (substr($out, -2) !== "\r\n") {
			$out .= "\r\n";
		}

		if (!isset($case_insensitive_headers['Connection'])) {
			$out .= "Connection: Close\r\n";
		}

		$out .= "\r\n" . $request_body;

		$options['hooks']->dispatch('fsockopen.before_send', [&$out]);

		fwrite($socket, $out);
		$options['hooks']->dispatch('fsockopen.after_send', [$out]);

		if (!$options['blocking']) {
			fclose($socket);
			$fake_headers = '';
			$options['hooks']->dispatch('fsockopen.after_request', [&$fake_headers]);
			return '';
		}

		$timeout_sec = (int) floor($options['timeout']);
		if ($timeout_sec === $options['timeout']) {
			$timeout_msec = 0;
		} else {
			$timeout_msec = self::SECOND_IN_MICROSECONDS * $options['timeout'] % self::SECOND_IN_MICROSECONDS;
		}

		stream_set_timeout($socket, $timeout_sec, $timeout_msec);

		$response   = '';
		$body       = '';
		$headers    = '';
		$this->info = stream_get_meta_data($socket);
		$size       = 0;
		$doingbody  = false;
		$download   = false;
		if ($options['filename']) {
			$download = @fopen($options['filename'], 'wb');
			if ($download === false) {
				$error = error_get_last();
				throw new Exception($error['message'], 'fopen');
			}
		}

		while (!feof($socket)) {
			$this->info = stream_get_meta_data($socket);
			if ($this->info['timed_out']) {
				throw new Exception('fsocket timed out', 'timeout');
			}

			$block = fread($socket, Requests::BUFFER_SIZE);
			if (!$doingbody) {
				$response .= $block;
				if (strpos($response, "\r\n\r\n")) {
					list($headers, $block) = explode("\r\n\r\n", $response, 2);
					$doingbody             = true;
				}
			}

			if ($doingbody) {
				$options['hooks']->dispatch('request.progress', [$block, $size, $this->max_bytes]);
				$data_length = strlen($block);
				if ($this->max_bytes) {
					if ($size === $this->max_bytes) {
						continue;
					}

					if (($size + $data_length) > $this->max_bytes) {
						$limited_length = ($this->max_bytes - $size);
						$block          = substr($block, 0, $limited_length);
					}
				}

				$size += strlen($block);
				if ($download) {
					fwrite($download, $block);
				} else {
					$body .= $block;
				}
			}
		}

		$this->headers = $headers;

		if ($download) {
			fclose($download);
		} else {
			$this->headers .= "\r\n\r\n" . $body;
		}

		fclose($socket);

		$options['hooks']->dispatch('fsockopen.after_request', [&$this->headers, &$this->info]);
		return $this->headers;
	}

	public function request_multiple($requests, $options) {
		if (empty($requests)) {
			return [];
		}

		if (InputValidator::has_array_access($requests) === false || InputValidator::is_iterable($requests) === false) {
			throw InvalidArgument::create(1, '$requests', 'array|ArrayAccess&Traversable', gettype($requests));
		}

		if (is_array($options) === false) {
			throw InvalidArgument::create(2, '$options', 'array', gettype($options));
		}

		$responses = [];
		$class     = get_class($this);
		foreach ($requests as $id => $request) {
			try {
				$handler        = new $class();
				$responses[$id] = $handler->request($request['url'], $request['headers'], $request['data'], $request['options']);

				$request['options']['hooks']->dispatch('transport.internal.parse_response', [&$responses[$id], $request]);
			} catch (Exception $e) {
				$responses[$id] = $e;
			}

			if (!is_string($responses[$id])) {
				$request['options']['hooks']->dispatch('multiple.request.complete', [&$responses[$id], $id]);
			}
		}

		return $responses;
	}

	private static function accept_encoding() {
		$type = [];
		if (function_exists('gzinflate')) {
			$type[] = 'deflate;q=1.0';
		}

		if (function_exists('gzuncompress')) {
			$type[] = 'compress;q=0.5';
		}

		$type[] = 'gzip;q=0.5';

		return implode(', ', $type);
	}

	private static function format_get($url_parts, $data) {
		if (!empty($data)) {
			if (empty($url_parts['query'])) {
				$url_parts['query'] = '';
			}

			$url_parts['query'] .= '&' . http_build_query($data, '', '&');
			$url_parts['query']  = trim($url_parts['query'], '&');
		}

		if (isset($url_parts['path'])) {
			if (isset($url_parts['query'])) {
				$get = $url_parts['path'] . '?' . $url_parts['query'];
			} else {
				$get = $url_parts['path'];
			}
		} else {
			$get = '/';
		}

		return $get;
	}

	public function connect_error_handler($errno, $errstr) {
		if (($errno & E_WARNING) === 0 && ($errno & E_NOTICE) === 0) {
			return false;
		}

		$this->connect_error .= $errstr . "\n";
		return true;
	}

	public function verify_certificate_from_context($host, $context) {
		$meta = stream_context_get_options($context);

		if (empty($meta) || empty($meta['ssl']) || empty($meta['ssl']['peer_certificate'])) {
			throw new Exception(rtrim($this->connect_error), 'ssl.connect_error');
		}

		$cert = openssl_x509_parse($meta['ssl']['peer_certificate']);

		return Ssl::verify_certificate($host, $cert);
	}

	public static function test($capabilities = []) {
		if (!function_exists('fsockopen')) {
			return false;
		}

		if (isset($capabilities[Capability::SSL]) && $capabilities[Capability::SSL]) {
			if (!extension_loaded('openssl') || !function_exists('openssl_x509_parse')) {
				return false;
			}
		}

		return true;
	}
}