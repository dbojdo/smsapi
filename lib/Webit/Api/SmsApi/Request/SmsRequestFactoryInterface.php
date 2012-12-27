<?php
namespace Webit\Api\SmsApi\Request;

interface SmsRequestFactoryInterface {
	/**
	 * @return SmsRequest
	 */
	public function createRequest();
}
?>