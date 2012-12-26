<?php
namespace Webit\Api\SmsApi\Request;

class SmsRequestFactory implements SmsRequestFactoryInterface {
	protected $requestDefaults = array();
	
	public function __construct($requestDefaults = array()) {
		$this->requestDefaults = array_merge($this->requestDefaults,$requestDefaults);
	}
	
	/**
	 * @return SmsRequest
	 */
	public function createRequest() {
		$request = new SmsRequest();
		$this->applyDefaults($request);
		
		return $request;
	}
	
	private function applyDefaults(SmsRequest $request) {
		$obj = new \ReflectionObject($request);
		foreach($this->requestDefaults as $key=>$value) {
			if($obj->hasProperty($key)) {
				$property = $obj->getProperty($key);
				$property->setAccessible(true);
				$property->setValue($request, $value);
			}
		}
	}
}
?>