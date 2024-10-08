<?php

namespace WpOrg\Requests\Transport;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use WpOrg\Requests\Capability;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Exception\InvalidArgument;
use WpOrg\Requests\Exception\Transport\Curl as CurlException;
use WpOrg\Requests\Requests;
use WpOrg\Requests\Transport;
use WpOrg\Requests\Utility\InputValidator;

final class Curl implements Transport {
	const CURL_7_10_5 = 0x070A05;
	const CURL_7_16_2 = 0x071002;

	public $headers = '';

	public $response_data = '';

	public $info;

	public $version;

	private $handle;

	private $hooks;

	private $done_headers = false;

	private $stream_handle;

	private $response_bytes;

	private $response_byte_limit;

	public function __construct() {
		$curl          = curl_version();
		$this->version = $curl['version_number'];
		$this->handle  = curl_init();

		curl_setopt($this->handle, CURLOPT_HEADER, false);
		curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1);
		if ($this->version >= self::CURL_7_10_5) {
			curl_setopt($this->handle, CURLOPT_ENCODING, '');
		}

		if (defined('CURLOPT_PROTOCOLS')) {
			curl_setopt($this->handle, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
		}

		if (defined('CURLOPT_REDIR_PROTOCOLS')) {
			curl_setopt($this->handle, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
		}
	}

	public function __destruct() {
		if (is_resource($this->handle)) {
			curl_close($this->handle);
		}
	}

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

		$this->hooks = $options['hooks'];

		$this->setup_handle($url, $headers, $data, $options);

		$options['hooks']->dispatch('curl.before_send', [&$this->handle]);

		if ($options['filename'] !== false) {
			$this->stream_handle = @fopen($options['filename'], 'wb');
			if ($this->stream_handle === false) {
				$error = error_get_last();
				throw new Exception($error['message'], 'fopen');
			}
		}

		$this->response_data       = '';
		$this->response_bytes      = 0;
		$this->response_byte_limit = false;
		if ($options['max_bytes'] !== false) {
			$this->response_byte_limit = $options['max_bytes'];
		}

		if (isset($options['verify'])) {
			if ($options['verify'] === false) {
				curl_setopt($this->handle, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($this->handle, CURLOPT_SSL_VERIFYPEER, 0);
			} elseif (is_string($options['verify'])) {
				curl_setopt($this->handle, CURLOPT_CAINFO, $options['verify']);
			}
		}

		if (isset($options['verifyname']) && $options['verifyname'] === false) {
			curl_setopt($this->handle, CURLOPT_SSL_VERIFYHOST, 0);
		}

		curl_exec($this->handle);
		$response = $this->response_data;

		$options['hooks']->dispatch('curl.after_send', []);

		if (curl_errno($this->handle) === CURLE_WRITE_ERROR || curl_errno($this->handle) === CURLE_BAD_CONTENT_ENCODING) {
			curl_setopt($this->handle, CURLOPT_ENCODING, 'none');

			$this->response_data  = '';
			$this->response_bytes = 0;
			curl_exec($this->handle);
			$response = $this->response_data;
		}

		$this->process_response($response, $options);

		curl_setopt($this->handle, CURLOPT_HEADERFUNCTION, null);
		curl_setopt($this->handle, CURLOPT_WRITEFUNCTION, null);

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

		$multihandle = curl_multi_init();
		$subrequests = [];
		$subhandles  = [];

		$class = get_class($this);
		foreach ($requests as $id => $request) {
			$subrequests[$id] = new $class();
			$subhandles[$id]  = $subrequests[$id]->get_subrequest_handle($request['url'], $request['headers'], $request['data'], $request['options']);
			$request['options']['hooks']->dispatch('curl.before_multi_add', [&$subhandles[$id]]);
			curl_multi_add_handle($multihandle, $subhandles[$id]);
		}

		$completed       = 0;
		$responses       = [];
		$subrequestcount = count($subrequests);

		$request['options']['hooks']->dispatch('curl.before_multi_exec', [&$multihandle]);

		do {
			$active = 0;

			do {
				$status = curl_multi_exec($multihandle, $active);
			} while ($status === CURLM_CALL_MULTI_PERFORM);

			$to_process = [];

			while ($done = curl_multi_info_read($multihandle)) {
				$key = array_search($done['handle'], $subhandles, true);
				if (!isset($to_process[$key])) {
					$to_process[$key] = $done;
				}
			}

			foreach ($to_process as $key => $done) {
				$options = $requests[$key]['options'];
				if ($done['result'] !== CURLE_OK) {
					$reason          = curl_error($done['handle']);
					$exception       = new CurlException(
						$reason,
						CurlException::EASY,
						$done['handle'],
						$done['result']
					);
					$responses[$key] = $exception;
					$options['hooks']->dispatch('transport.internal.parse_error', [&$responses[$key], $requests[$key]]);
				} else {
					$responses[$key] = $subrequests[$key]->process_response($subrequests[$key]->response_data, $options);

					$options['hooks']->dispatch('transport.internal.parse_response', [&$responses[$key], $requests[$key]]);
				}

				curl_multi_remove_handle($multihandle, $done['handle']);
				curl_close($done['handle']);

				if (!is_string($responses[$key])) {
					$options['hooks']->dispatch('multiple.request.complete', [&$responses[$key], $key]);
				}

				$completed++;
			}
		} while ($active || $completed < $subrequestcount);

		$request['options']['hooks']->dispatch('curl.after_multi_exec', [&$multihandle]);

		curl_multi_close($multihandle);

		return $responses;
	}

	public function &get_subrequest_handle($url, $headers, $data, $options) {
		$this->setup_handle($url, $headers, $data, $options);

		if ($options['filename'] !== false) {
			$this->stream_handle = fopen($options['filename'], 'wb');
		}

		$this->response_data       = '';
		$this->response_bytes      = 0;
		$this->response_byte_limit = false;
		if ($options['max_bytes'] !== false) {
			$this->response_byte_limit = $options['max_bytes'];
		}

		$this->hooks = $options['hooks'];

		return $this->handle;
	}

	private function setup_handle($url, $headers, $data, $options) {
		$options['hooks']->dispatch('curl.before_request', [&$this->handle]);

		if (!isset($headers['Connection'])) {
			$headers['Connection'] = 'close';
		}

		if (!isset($headers['Expect']) && $options['protocol_version'] === 1.1) {
			$headers['Expect'] = $this->get_expect_header($data);
		}

		$headers = Requests::flatten($headers);

		if (!empty($data)) {
			$data_format = $options['data_format'];

			if ($data_format === 'query') {
				$url  = self::format_get($url, $data);
				$data = '';
			} elseif (!is_string($data)) {
				$data = http_build_query($data, '', '&');
			}
		}

		switch ($options['type']) {
			case Requests::POST:
				curl_setopt($this->handle, CURLOPT_POST, true);
				curl_setopt($this->handle, CURLOPT_POSTFIELDS, $data);
				break;
			case Requests::HEAD:
				curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, $options['type']);
				curl_setopt($this->handle, CURLOPT_NOBODY, true);
				break;
			case Requests::TRACE:
				curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, $options['type']);
				break;
			case Requests::PATCH:
			case Requests::PUT:
			case Requests::DELETE:
			case Requests::OPTIONS:
			default:
				curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, $options['type']);
				if (!empty($data)) {
					curl_setopt($this->handle, CURLOPT_POSTFIELDS, $data);
				}
		}

		$timeout = max($options['timeout'], 1);

		if (is_int($timeout) || $this->version < self::CURL_7_16_2) {
			curl_setopt($this->handle, CURLOPT_TIMEOUT, ceil($timeout));
		} else {
			curl_setopt($this->handle, CURLOPT_TIMEOUT_MS, round($timeout * 1000));
		}

		if (is_int($options['connect_timeout']) || $this->version < self::CURL_7_16_2) {
			curl_setopt($this->handle, CURLOPT_CONNECTTIMEOUT, ceil($options['connect_timeout']));
		} else {
			curl_setopt($this->handle, CURLOPT_CONNECTTIMEOUT_MS, round($options['connect_timeout'] * 1000));
		}

		curl_setopt($this->handle, CURLOPT_URL, $url);
		curl_setopt($this->handle, CURLOPT_USERAGENT, $options['useragent']);
		if (!empty($headers)) {
			curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);
		}

		if ($options['protocol_version'] === 1.1) {
			curl_setopt($this->handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		} else {
			curl_setopt($this->handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		}

		if ($options['blocking'] === true) {
			curl_setopt($this->handle, CURLOPT_HEADERFUNCTION, [$this, 'stream_headers']);
			curl_setopt($this->handle, CURLOPT_WRITEFUNCTION, [$this, 'stream_body']);
			curl_setopt($this->handle, CURLOPT_BUFFERSIZE, Requests::BUFFER_SIZE);
		}
	}

	public function process_response($response, $options) {
		if ($options['blocking'] === false) {
			$fake_headers = '';
			$options['hooks']->dispatch('curl.after_request', [&$fake_headers]);
			return false;
		}

		if ($options['filename'] !== false && $this->stream_handle) {
			fclose($this->stream_handle);
			$this->headers = trim($this->headers);
		} else {
			$this->headers .= $response;
		}

		if (curl_errno($this->handle)) {
			$error = sprintf(
				'cURL error %s: %s',
				curl_errno($this->handle),
				curl_error($this->handle)
			);
			throw new Exception($error, 'curlerror', $this->handle);
		}

		$this->info = curl_getinfo($this->handle);

		$options['hooks']->dispatch('curl.after_request', [&$this->headers, &$this->info]);
		return $this->headers;
	}

	public function stream_headers($handle, $headers) {
		if ($this->done_headers) {
			$this->headers      = '';
			$this->done_headers = false;
		}

		$this->headers .= $headers;

		if ($headers === "\r\n") {
			$this->done_headers = true;
		}

		return strlen($headers);
	}

	public function stream_body($handle, $data) {
		$this->hooks->dispatch('request.progress', [$data, $this->response_bytes, $this->response_byte_limit]);
		$data_length = strlen($data);

		if ($this->response_byte_limit) {
			if ($this->response_bytes === $this->response_byte_limit) {
				return $data_length;
			}

			if (($this->response_bytes + $data_length) > $this->response_byte_limit) {
				$limited_length = ($this->response_byte_limit - $this->response_bytes);
				$data           = substr($data, 0, $limited_length);
			}
		}

		if ($this->stream_handle) {
			fwrite($this->stream_handle, $data);
		} else {
			$this->response_data .= $data;
		}

		$this->response_bytes += strlen($data);
		return $data_length;
	}

	private static function format_get($url, $data) {
		if (!empty($data)) {
			$query     = '';
			$url_parts = parse_url($url);
			if (empty($url_parts['query'])) {
				$url_parts['query'] = '';
			} else {
				$query = $url_parts['query'];
			}

			$query .= '&' . http_build_query($data, '', '&');
			$query  = trim($query, '&');

			if (empty($url_parts['query'])) {
				$url .= '?' . $query;
			} else {
				$url = str_replace($url_parts['query'], $query, $url);
			}
		}

		return $url;
	}

	public static function test($capabilities = []) {
		if (!function_exists('curl_init') || !function_exists('curl_exec')) {
			return false;
		}

		if (isset($capabilities[Capability::SSL]) && $capabilities[Capability::SSL]) {
			$curl_version = curl_version();
			if (!(CURL_VERSION_SSL & $curl_version['features'])) {
				return false;
			}
		}

		return true;
	}

	private function get_expect_header($data) {
		if (!is_array($data)) {
			return strlen((string) $data) >= 1048576 ? '100-Continue' : '';
		}

		$bytesize = 0;
		$iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($data));

		foreach ($iterator as $datum) {
			$bytesize += strlen((string) $datum);

			if ($bytesize >= 1048576) {
				return '100-Continue';
			}
		}

		return '';
	}
}
