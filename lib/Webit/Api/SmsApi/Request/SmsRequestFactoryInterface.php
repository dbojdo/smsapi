<?php
namespace Webit\Api\SmsApi\Request;

class SmsRequestFactoryInterface {
	/**
	 * @return SmsRequest
	 */
	public function createRequest();
}
?>