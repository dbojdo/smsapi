<?php
namespace Webit\Api\SmsApi\Request;

use Webit\Api\SmsCommon\Message\SmsInterface;

class SmsRequest extends AbstractRequest {
	/**
	 * Message: 160 (max 918) chars - no accents, 70 chars (max 402) - with accents.
	 * Messages longer than 160 chars (or 70) will be send as merged message (max 6 messages).
	 * See point 13 of documetation for details. 
	 * 
	 * Treść wiadomości. Standardowo do 160 znaków lub 70 znaków w przypadku 
	 * wystąpienia chociaż jednego znaku specjalnego (polskie znaki uważane są za 
	 * specjalne). Maksymalna długość wiadomości wynosi 918 znaków (lub 402 ze 
	 * znakami specjalnymi) i jest wysłana jako 6 połączonych SMS-ów, obciążając konto 
	 * zgodnie z aktualnym cennikiem. Więcej szczegółów odnośnie znaków specjalnych 
	 * znajduje się w pkt 13.
	 * 
	 * @var string
	 */
	protected $message;

	/**
	 * Nazwa nadawcy wiadomości. Pozostawienie pola pustego powoduje wysłanie 
	 * wiadomości od „SMSAPI”. Przyjmowane są tylko nazwy zweryfikowane. 
	 * (&from=aktywna_nazwa). Pole nadawcy należy dodać po zalogowaniu na stronie 
	 * SMSAPI, w zakładce USTAWIENIA → POLA NADAWCY.
	 * 
	 * @var string
	 */
	protected $from;

	/**
	 * Message encoding. Default to: windows-1250 (it sucks!)
	 * 
	 * Parametr określa kodowanie polskich znaków w SMS-ie. Domyślne kodowanie jest 
	 * windows-1250. Jeżeli występuje konieczność zmiany kodowania, należy użyć 
	 * parametru encoding z danymi:
	 * - dla iso-8859-2 (latin2) – należy podać wartość „iso-8859-2”,
	 * - dla utf-8 – należy podać wartość „utf-8”
	 * 
	 * @var string
	 */
	protected $encoding;

	/**
	 * Wysyłanie wiadomości trybem „flash”, odbywa się poprzez podanie parametru flash o
	 * wartości „1”. SMS-y flash są automatycznie wyświetlane na ekranie głównym telefonu
	 * komórkowego i nie są przechowywane w skrzynce odbiorczej (jeśli nie zostaną 
	 * zapisane). (&flash=1)
	 * 
	 * @var int (0 or 1)
	 */
	protected $flash;

	/**
	 * 
	 * Wiadomość nie jest wysyłana, wyświetlana jest jedynie odpowiedź (w celach 
	 * testowych). (&test=1)
	 * 
	 * @var int (0 or 1)
	 */
	protected $test;

	/**
	 * W odpowiedzi zawarte jest więcej szczegółów. (Treść wiadomości, długość 
	 * wiadomość, ilość części z jakich składa się wiadomość). (&details=1)
	 * 
	 * @var int (0 or 1)
	 */
	protected $details;

	/**
	 * Data w formacie unixtime (&date=1287734110) lub ISO 8601 (&date=2012-05-
	 * 10T08:40:27+00:00). Określa kiedy wiadomość ma być wysłana. W przypadku 
	 * wstawienia daty przeszłej wiadomość zostanie wysłana od razu. Wiadomość można 
	 * zaplanować na maksymalnie 3 miesiące do przodu.
	 * 
	 * @var \DateTime
	 */
	protected $date;

	/**
	 * Ustawienie „1” sprawdza poprawność formatu podanej daty. W przypadku 
	 * wystąpienia błędnej daty zwrócony zostanie błąd ERROR:54
	 * 
	 * @var int (0 or 1)
	 */
	protected $dateValidate;

	/**
	 * Parametr pozwalający na wysyłanie wiadomości WAP PUSH. (&datacoding=bin)
	 * @var string
	 */
	protected $datacoding;

	/**
	 * Opcjonalny parametr użytkownika wysyłany z wiadomością a następnie zwracany 
	 * przy wywołaniu zwrotnym CALLBACK. Parametr idx może mieć maksymalnie 36 
	 * znaków dopuszczalne są cyfry 0 - 9 oraz litery a – z (wielkość liter nie jest 
	 * rozróżniana). (&idx=123)
	 * 
	 * @var string
	 */
	protected $idx;

	/**
	 * Pozwala zabezpieczyć przed wysłanie dwóch wiadomości z identyczną wartością 
	 * parametru idx. W przypadku ustawienia parametru (&check_idx=1) system sprawdza 
	 * czy wiadomość z takim idx już została przyjęta, jeśli tak zwracany jest błąd 53.
	 * 
	 * @var int (0 or 1)
	 */
	protected $checkIdx;

	/**
	 * Ustawienie parametru &eco=1 spowoduje wysłanie wiadomości Eco (brak możliwości
	 * wyboru pola nadawcy, wiadomość wysyłana z losowego numeru 
	 * dziewięciocyfrowego) szczegóły dotyczące wiadomości Eco znajdują się na naszej 
	 * stronie: http://www.smsapi.pl/
	 * 
	 * @var int (0 or 1)
	 */
	protected $eco;

	/**
	 * Ustawienie 1 zabezpiecza przed wysłaniem wiadomości ze znakami specjalnymi (w 
	 * tym polskimi) (ERROR:11).
	 * 
	 * @var int (0 or 1)
	 */
	protected $nounicode;

	/**
	 * Ustawienie „1” powoduje zamianę znaków diakrytycznych takich jak „ą”, „ś”, „ć” na ich
	 * odpowiedniki „a”, „s”, „c”. Pełna lista zamienianych znaków znajduje się w uwagach 
	 * końcowych. Uwaga! Inne znaki specjalne niż wymienione w sekcji „Uwagi końcowe” 
	 * zostaną wysłane jako specjalne i wiadomość będzie miała zmniejszoną ilość 
	 * dostępnych znaków.
	 * 
	 * @var int (0 or 1)
	 */
	protected $normalize;

	/**
	 * Ustawienie 1 spowoduje wysłanie wiadomości przy wykorzystaniu osobnego kanału 
	 * zapewniającego szybkie doręczenie wiadomości Fast. Z parametru korzystać można 
	 * podczas wysyłania wiadomości Pro oraz Eco, Ilość punktów za wysyłkę pomnożona 
	 * będzie przez 1.5 Uwaga! Dla tego parametru zabronione jest prowadzenie wysyłek 
	 * masowych i marketingowych.
	 * 
	 * @var int (0 or 1)
	 */
	protected $fast;

	/**
	 * Kod partnerski, który otrzymać można po podpisaniu umowy partnerskiej. Kod nie 
	 * będzie brany pod uwagę jeżeli użytkownik wysyłający polecony jest przez innego 
	 * klienta lub podaje swój kod.
	 * 
	 * @var string
	 */
	protected $partnerId;

	/**
	 * Kod partnerski, który otrzymać można po podpisaniu umowy partnerskiej. Kod nie 
	 * będzie brany pod uwagę jeżeli użytkownik wysyłający polecony jest przez innego 
	 * klienta lub podaje swój kod.
	 * 
	 * @var int (max=6)
	 */
	protected $maxParts;

	/**
	 * Data wygaśnięcia wiadomości (jeżeli do tej daty nie zostanie dostarczona nie będzie 
	 * więcej prób jej dostarczenia) podana w formacie unix timestamp. Różnica pomiędzy 
	 * datą wysyłki a datą wygaśnięcia musi być większa niż 1 i mniejsza niż 12 godzin. 
	 * Dokładność daty wygaśnięcia to +/- 5 minut.
	 * 
	 * @var \DateTime
	 */
	protected $expirationDate;

	public function getMessage() {
		return $this->message;
	}

	public function setMessage($message) {
		$this->message = $message;
	}

	public function getFrom() {
		return $this->from;
	}

	public function setFrom($from) {
		$this->from = $from;
	}

	public function getEncoding() {
		return $this->encoding;
	}

	public function setEncoding($encoding) {
		$this->encoding = $encoding;
	}

	public function getFlash() {
		return $this->flash;
	}

	public function setFlash($flash) {
		$this->flash = $flash;
	}

	public function getTest() {
		return $this->test;
	}

	public function setTest($test) {
		$this->test = $test;
	}

	public function getDetails() {
		return $this->details;
	}

	public function setDetails($details) {
		$this->details = $details;
	}

	public function getDate() {
		return $this->date;
	}

	public function setDate($date) {
		$this->date = $date;
	}

	public function getDateValidate() {
		return $this->dateValidate;
	}

	public function setDateValidate($dateValidate) {
		$this->dateValidate = $dateValidate;
	}

	public function getDatacoding() {
		return $this->datacoding;
	}

	public function setDatacoding($datacoding) {
		$this->datacoding = $datacoding;
	}

	public function getIdx() {
		return $this->idx;
	}

	public function setIdx($idx) {
		$this->idx = $idx;
	}

	public function getCheckIdx() {
		return $this->checkIdx;
	}

	public function setCheckIdx($checkIdx) {
		$this->checkIdx = $checkIdx;
	}

	public function getEco() {
		return $this->eco;
	}

	public function setEco($eco) {
		$this->eco = $eco;
	}

	public function getNounicode() {
		return $this->nounicode;
	}

	public function setNounicode($nounicode) {
		$this->nounicode = $nounicode;
	}

	public function getNormalize() {
		return $this->normalize;
	}

	public function setNormalize($normalize) {
		$this->normalize = $normalize;
	}

	public function getFast() {
		return $this->fast;
	}

	public function setFast($fast) {
		$this->fast = $fast;
	}

	public function getPartnerId() {
		return $this->partnerId;
	}

	public function setPartnerId($partnerId) {
		$this->partnerId = $partnerId;
	}

	public function getMaxParts() {
		return $this->maxParts;
	}

	public function setMaxParts($maxParts) {
		$this->maxParts = $maxParts;
	}

	public function getExpirationDate() {
		return $this->expirationDate;
	}

	public function setExpirationDate($expirationDate) {
		$this->expirationDate = $expirationDate;
	}
	
	public function fromSms(SmsInterface $sms) {
		$this->setFrom($sms->getFrom());
		$this->setEncoding($sms->getEncoding());
		$this->setMessage($sms->getContent());
		
		foreach($sms->getRecivers() as $reciver) {
			$this->addTo($reciver);
		}
	}
}
?>