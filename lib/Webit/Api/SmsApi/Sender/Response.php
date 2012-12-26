<?php
namespace Webit\Api\SmsApi\Sender;
use Webit\Api\SmsCommon\Sender\Response as BaseResponse;

class Response extends BaseResponse {
	/**
	 * 
	 * @var string
	 */
	protected $id;

	/**
	 * 
	 * @var string
	 */
	protected $points;

	/**
	 * 
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * 
	 * @return string
	 */
	public function getPoints() {
		return $this->points;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function setPoints($points) {
		$this->points = $points;
	}
}
?>
