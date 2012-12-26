<?php
use Webit\Api\SmsApi\Request\AbstractRequest;

namespace Webit\Api\SmsApi\Sender;

use Webit\Api\SmsCommon\Error\Error;

use Webit\Api\SmsApi\Request\SmsRequestFactoryInterface;

use Webit\Api\SmsCommon\Message\SmsInterface;
use Webit\Api\SmsApi\Request\SmsRequest;
use Webit\Api\SmsApi\Sender\SmsSenderInterface;

class SmsHttpSender implements SmsSenderInterface {	
	/**
	 * 
	 * @var SmsRequestFactoryInterface
	 */
	protected $requestFactory;
	
	public function __construct(SmsRequestFactoryInterface $factory) {
		$this->requestFactory = $factory;
	}
	
	/**
	 * 
	 * @param SmsInterface $sms
	 * @return Response
	 */
	public function sendSms(SmsInterface $sms) {
		$request = $this->requestFactory->createRequest();
		$request->fromSms($sms);
		
		$curl = $this->createCurl($request);
		$response = curl_exec($curl);
		
		return $response;
	}
	
	/**
	 * 
	 * @param SmsRequest $request
	 * @return resource (curl handle)
	 */
	private function createCurl(SmsRequest $request) {
		$arCurlParams = $this->getCurlParameters($request);
		if($request->getMethod() == AbstractRequest::METHOD_POST) {
			$curl = curl_init($request->getBaseUrl());
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $arCurlParams);
		} else {
			
			array_walk($arCurlParams, function(&$value) {
				urlencode($value);
			});
			$strParams = implode('&',$arCurlParams);
			$curl = curl_init($request->getBaseUrl().'?'.$strParams);
		}
		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		
		return $curl;
	}
	
	private function getCurlParameters(SmsRequest $request) {
		$arOmmit = array('baseUrl','method','to');
		$obj = new \ReflectionObject($request);
		$arProperties = $obj->getProperties();
		
		$arCurlProp = array();
		foreach($arProperties as $property) {
			if(in_array($property->getName()),$arOmmit) {
				continue;
			};
				
			$value = $property->getValue($obj);
			if($value !== null) {
				$key = $this->camelToUnderscore($property->getName());
				switch(true) {
					case $value instanceof \DateTime:
						$arCurlProp[$key] = $value->getTimestamp();
						break;
					default:
						$arCurlProp[$key] = $value;
				}
			}
		}
		
		$arCurlProp['to'] = $this->normalizeRecivers($request->getTo());
		
		return $arCurlProp;
	}
	
	/**
	 * 
	 * @param array $recivers
	 * @return string
	 */
	private function normalizeRecivers(array $recivers) {
		$arRecivers = array();
		foreach($recivers as $reciver) {
			$phoneNo = $reciver->getPhoneNo();
			$phoneNo = preg_replace('\D','',$phoneNo);
			$arRecivers[] = $phoneNo;
		}
		
		$arRecivers = array_unique($arRecivers);
		
		return implode(',',$arRecivers);
	}
	
	/**
	 * 
	 * @param string $responeString
	 * @return Response
	 */
	private function parseResponse($responeString) {
		$response = new Response();
		if(empty($responeString)) {
			$response->setSuccess(false);
			$response->addError(new Error(Error::ERROR_NO_RESPONSE,'Api hasn\'t returned any response.'));
			
			return $response;
		}
		
		$arResponse = explode(':',$responeString);
		$result = array_shift($arResponse);
		
		if($result == 'OK') {
			$id = array_shift($arResponse);
			$points = array_shift($arResponse);
			
			$response->setSuccess(true);
			$response->setId($id);
			$response->setPoints($points);
		} else {
			$code = array_pop($arResponse);
			
			$response->setSuccess(false);
			$response->addError(new Error($code));
		}
		
		return $response;
	}
	
	private function camelToUnderscore($text) {
		return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $text));
	}
}
?>
