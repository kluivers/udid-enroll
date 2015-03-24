<?php

class UrbanAirship {
	const BASE_URL = "https://go.urbanairship.com";
	const DEFAULT_VERSION = "application/vnd.urbanairship+json; version=3";
	
	const PUSH_URL = "/api/push/";
	
	public $key;
	public $secret;
	
	public function __construct($key, $secret) {
		$this->key = $key;
		$this->secret = $secret;
	}
	
	public function push($msg) {
		$payload = array(
			"audience" => "all",
			"notification" => array(
				"alert" => $msg
			),
			"device_types" => "all"
		);
		
		$url = $this->buildURL(self::PUSH_URL);
		
		$response = $this->request(
			"POST", 
			json_encode($payload),
			$url
		);
	}
	
	function buildURL($url) {
		return self::BASE_URL . $url;
	}
	
	function request($method, $body, $url) {
		// Example:
		// https://support.urbanairship.com/customer/portal/articles/91072-simple-php-api-v3-examples
		
		$headers = array(
			"Accept: " . self::DEFAULT_VERSION,
			"Content-Type: application/json",
			"Authorization: Basic " . base64_encode($this->key . ":" . $this->secret)
		);
		
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
		
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		
		curl_exec($curl);
		
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		curl_close($curl);
		
		return $status == 202;
	}
}