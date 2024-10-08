<?php

namespace WpOrg\Requests;

interface Transport {
	public function request($url, $headers = [], $data = [], $options = []);

	public function request_multiple($requests, $options);

	public static function test($capabilities = []);
}
