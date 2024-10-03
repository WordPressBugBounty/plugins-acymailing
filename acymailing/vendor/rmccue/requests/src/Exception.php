<?php

namespace WpOrg\Requests;

use Exception as PHPException;

class Exception extends PHPException {
	protected $type;

	protected $data;

	public function __construct($message, $type, $data = null, $code = 0) {
		parent::__construct($message, $code);

		$this->type = $type;
		$this->data = $data;
	}

	public function getType() {
		return $this->type;
	}

	public function getData() {
		return $this->data;
	}
}
