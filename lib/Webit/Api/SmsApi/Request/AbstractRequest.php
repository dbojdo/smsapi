<?php
namespace Webit\Api\SmsApi\Request;
use Webit\Api\SmsCommon\Message\RecipientInterface;

abstract class AbstractRequest {
	const METHOD_POST = 'post';
	const METHOD_GET = 'get';
	
	const URL_HTTP = 'http://api.smsapi.pl/sms.do';
	const URL_HTTP2 = 'http://api2.smsapi.pl/sms.do';
	const URL_HTTPS = 'https://ssl.smsapi.pl/sms.do';
	const URL_HTTPS2 = 'https://ssl2.smsapi.pl/sms.do';
	
	/**
	 * 
	 * @var string
	 */
	protected $baseUrl;

	/**
	 * Your SMSAPI username or main e-mail address
	 * Nazwa użytkownika lub główny adres e-mail przypisany do konta  w serwisie SMSAPI
	 * @var string
	 */
	protected $username;

	/**
	 * Your SMSAPI account password (MD5 hash)
	 * Hasło do Twojego konta w serwisie SMSAPI zaszyfrowane w MD5
	 * MD5(api password)
	 * @var string
	 */
	protected $password;

	/**
	 * 
	 * @var string
	 */
	protected $method = self::METHOD_POST;
	
	/**
	 * Phone no. Valid formats: 48xxxxxxxxx or xxxxxxxxx, ex. 48505602702 or 505602702
	 * Numer odbiorcy wiadomości w formacie 48xxxxxxxxx lub xxxxxxxxx. Np. 48505602702 lub 505602702
	 * @var string
	 */
	protected $to = array();

	/**
	 * Address book group name
	 * Nazwa grupy kontaktów z książki telefonicznej, do których ma zostać wysłana wiadomość
	 * @var string
	 */
	protected $group;

	public function __construct($username = null, $password = null) {
		$this->baseUrl = self::URL_HTTP;
		$this->username = $this->setUsername($username);
		$this->password = $this->setPassword($password);
	}
	
	/**
	 * @return string
	 */
	public function getBaseUrl() {
		return $this->baseUrl;
	}
	
	/**
	 * 
	 * @param string $baseUrl
	 */
	public function setBaseUrl($baseUrl) {
		$this->baseUrl = $baseUrl;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * 
	 * @param string $username
	 */
	public function setUsername($username) {
		$this->username = $username;
	}

	/**
	 * 
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * 
	 * @param string $password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}

	/**
	 * 
	 * @param string $passwordPlain
	 */
	public function setPasswordPlain($passwordPlain) {
		$this->setPassword(md5($passwordPlain));
	}

	/**
	 * 
	 * @param string $method
	 */
	public function setMethod($method) {
		$this->method = strtolower($method);
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}
	
	/**
	 * 
	 * @param RecipientInterface
	 */
	public function addTo(RecipientInterface $to) {
		if (!in_array($to, $this->to)) {
			$this->to[] = $to;
		}
	}

	/**
	 * @return array
	 */
	public function getTo() {
		return $this->to;
	}

	/**
	 * 
	 * @param string $group
	 */
	public function setGroup($group) {
		$this->group = $group;
	}

	/**
	 * 
	 * @return string
	 */
	public function getGroup() {
		return $this->group;
	}
}
?>
