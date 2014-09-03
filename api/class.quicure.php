<?php
/*
	Just 30 lines of PHP - Copyright 2014 Quicure.
*/
class Quicure {
	private $email;
	public function __construct($email) {
		$this->email = $email;
	}
	public function simple_payment ($amount, $title = "QUICURE SIMPLE PAYMENT") {
		$email = $this->email;
		return new Quicure_URL("https://quicure.com/api/simple_payment/" . urlencode($email) . "/" . urlencode($amount) . "/" . urlencode($title));
	}
}
class Quicure_URL {
	private $url;
	public function __construct($url) {
		$this->url = $url;
	}
	public function getApiURL() {
		return $this->url;
	}
	public function getInvoiceURL() {
		return file_get_contents($this->url);
	}
	public function excecute() {
		$i = $this->getInvoiceURL();
		header("Location: $i");
	}
}