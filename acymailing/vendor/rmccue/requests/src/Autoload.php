<?php

namespace WpOrg\Requests;

if (class_exists('WpOrg\Requests\Autoload') === false) {

	final class Autoload {

		private static $deprecated_classes = [
			'requests_auth'                              => '\WpOrg\Requests\Auth',
			'requests_hooker'                            => '\WpOrg\Requests\HookManager',
			'requests_proxy'                             => '\WpOrg\Requests\Proxy',
			'requests_transport'                         => '\WpOrg\Requests\Transport',

			'requests_cookie'                            => '\WpOrg\Requests\Cookie',
			'requests_exception'                         => '\WpOrg\Requests\Exception',
			'requests_hooks'                             => '\WpOrg\Requests\Hooks',
			'requests_idnaencoder'                       => '\WpOrg\Requests\IdnaEncoder',
			'requests_ipv6'                              => '\WpOrg\Requests\Ipv6',
			'requests_iri'                               => '\WpOrg\Requests\Iri',
			'requests_response'                          => '\WpOrg\Requests\Response',
			'requests_session'                           => '\WpOrg\Requests\Session',
			'requests_ssl'                               => '\WpOrg\Requests\Ssl',
			'requests_auth_basic'                        => '\WpOrg\Requests\Auth\Basic',
			'requests_cookie_jar'                        => '\WpOrg\Requests\Cookie\Jar',
			'requests_proxy_http'                        => '\WpOrg\Requests\Proxy\Http',
			'requests_response_headers'                  => '\WpOrg\Requests\Response\Headers',
			'requests_transport_curl'                    => '\WpOrg\Requests\Transport\Curl',
			'requests_transport_fsockopen'               => '\WpOrg\Requests\Transport\Fsockopen',
			'requests_utility_caseinsensitivedictionary' => '\WpOrg\Requests\Utility\CaseInsensitiveDictionary',
			'requests_utility_filterediterator'          => '\WpOrg\Requests\Utility\FilteredIterator',
			'requests_exception_http'                    => '\WpOrg\Requests\Exception\Http',
			'requests_exception_transport'               => '\WpOrg\Requests\Exception\Transport',
			'requests_exception_transport_curl'          => '\WpOrg\Requests\Exception\Transport\Curl',
			'requests_exception_http_304'                => '\WpOrg\Requests\Exception\Http\Status304',
			'requests_exception_http_305'                => '\WpOrg\Requests\Exception\Http\Status305',
			'requests_exception_http_306'                => '\WpOrg\Requests\Exception\Http\Status306',
			'requests_exception_http_400'                => '\WpOrg\Requests\Exception\Http\Status400',
			'requests_exception_http_401'                => '\WpOrg\Requests\Exception\Http\Status401',
			'requests_exception_http_402'                => '\WpOrg\Requests\Exception\Http\Status402',
			'requests_exception_http_403'                => '\WpOrg\Requests\Exception\Http\Status403',
			'requests_exception_http_404'                => '\WpOrg\Requests\Exception\Http\Status404',
			'requests_exception_http_405'                => '\WpOrg\Requests\Exception\Http\Status405',
			'requests_exception_http_406'                => '\WpOrg\Requests\Exception\Http\Status406',
			'requests_exception_http_407'                => '\WpOrg\Requests\Exception\Http\Status407',
			'requests_exception_http_408'                => '\WpOrg\Requests\Exception\Http\Status408',
			'requests_exception_http_409'                => '\WpOrg\Requests\Exception\Http\Status409',
			'requests_exception_http_410'                => '\WpOrg\Requests\Exception\Http\Status410',
			'requests_exception_http_411'                => '\WpOrg\Requests\Exception\Http\Status411',
			'requests_exception_http_412'                => '\WpOrg\Requests\Exception\Http\Status412',
			'requests_exception_http_413'                => '\WpOrg\Requests\Exception\Http\Status413',
			'requests_exception_http_414'                => '\WpOrg\Requests\Exception\Http\Status414',
			'requests_exception_http_415'                => '\WpOrg\Requests\Exception\Http\Status415',
			'requests_exception_http_416'                => '\WpOrg\Requests\Exception\Http\Status416',
			'requests_exception_http_417'                => '\WpOrg\Requests\Exception\Http\Status417',
			'requests_exception_http_418'                => '\WpOrg\Requests\Exception\Http\Status418',
			'requests_exception_http_428'                => '\WpOrg\Requests\Exception\Http\Status428',
			'requests_exception_http_429'                => '\WpOrg\Requests\Exception\Http\Status429',
			'requests_exception_http_431'                => '\WpOrg\Requests\Exception\Http\Status431',
			'requests_exception_http_500'                => '\WpOrg\Requests\Exception\Http\Status500',
			'requests_exception_http_501'                => '\WpOrg\Requests\Exception\Http\Status501',
			'requests_exception_http_502'                => '\WpOrg\Requests\Exception\Http\Status502',
			'requests_exception_http_503'                => '\WpOrg\Requests\Exception\Http\Status503',
			'requests_exception_http_504'                => '\WpOrg\Requests\Exception\Http\Status504',
			'requests_exception_http_505'                => '\WpOrg\Requests\Exception\Http\Status505',
			'requests_exception_http_511'                => '\WpOrg\Requests\Exception\Http\Status511',
			'requests_exception_http_unknown'            => '\WpOrg\Requests\Exception\Http\StatusUnknown',
		];

		public static function register() {
			if (defined('REQUESTS_AUTOLOAD_REGISTERED') === false) {
				spl_autoload_register([self::class, 'load'], true);
				define('REQUESTS_AUTOLOAD_REGISTERED', true);
			}
		}

		public static function load($class_name) {
			$psr_4_prefix_pos = strpos($class_name, 'WpOrg\\Requests\\');

			if (stripos($class_name, 'Requests') !== 0 && $psr_4_prefix_pos !== 0) {
				return false;
			}

			$class_lower = strtolower($class_name);

			if ($class_lower === 'requests') {
				$file = dirname(__DIR__) . '/library/Requests.php';
			} elseif ($psr_4_prefix_pos === 0) {
				$file = __DIR__ . '/' . strtr(substr($class_name, 15), '\\', '/') . '.php';
			}

			if (isset($file) && file_exists($file)) {
				include $file;
				return true;
			}

			if (isset(self::$deprecated_classes[$class_lower])) {
				if (!defined('REQUESTS_SILENCE_PSR0_DEPRECATIONS') || REQUESTS_SILENCE_PSR0_DEPRECATIONS !== true) {
					trigger_error(
						'The PSR-0 `Requests_...` class names in the Requests library are deprecated.'
						. ' Switch to the PSR-4 `WpOrg\Requests\...` class names at your earliest convenience.',
						E_USER_DEPRECATED
					);

					if (!defined('REQUESTS_SILENCE_PSR0_DEPRECATIONS')) {
						define('REQUESTS_SILENCE_PSR0_DEPRECATIONS', true);
					}
				}

				return class_alias(self::$deprecated_classes[$class_lower], $class_name, true);
			}

			return false;
		}
	}
}
