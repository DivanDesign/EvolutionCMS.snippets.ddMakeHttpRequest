<?php
namespace ddMakeHttpRequest;

/**
 * Result
 * 
 * @desc Snippet result object
 */
class Result {
	/**
	 * @property $meta {stdClass} — Response meta
	 * @property $meta->isSuccess {boolean}
	 * @property $meta->isCurlSuccess {boolean}
	 * @property $meta->isHttpCodeSuccess {boolean}
	 * @property $meta->isDataValid {boolean}
	 * @property $meta->effectiveUrl {string}
	 * @property $meta->curlErrorCode {integer}
	 * @property $meta->message {string}
	 * @property $meta->code {integer}
	 */
	public $meta = [
		'isSuccess' => false,
		'isCurlSuccess' => false,
		'isHttpCodeSuccess' => false,
		'isDataValid' => false,
		'effectiveUrl' => '',
		'curlErrorCode' => 0,
		'message' => '',
		'code' => 0,
	];
	
	/**
	 * @property $data {mixed} — Response body
	 */
	public $data = '';
	
	/**
	 * __construct
	 * @version 1.0 (2025-11-21)
	 */
	public function __construct(){
		$this->meta = (object) $this->meta;
	}
	
	/**
	 * fetchFromCurl
	 * @version 1.2.1 (2025-11-25)
	 * 
	 * @desc Fetches data from a CURL handle and sets the properties of the instance.
	 * 
	 * @param $params {stdClass|arrayAssociative}
	 * @param $params->curlHandle {resource} — CURL handle
	 * 
	 * @return {void}
	 */
	public function fetchFromCurl($params = []){
		$params = (object) $params;
		
		// Execute request
		$this->data = curl_exec($params->curlHandle);
		
		// Get information about the request
		$this->meta->effectiveUrl = curl_getinfo(
			$params->curlHandle,
			CURLINFO_EFFECTIVE_URL
		);
		$this->meta->curlErrorCode = curl_errno($params->curlHandle);
		$this->meta->message = curl_error($params->curlHandle);
		$this->meta->code = curl_getinfo(
			$params->curlHandle,
			CURLINFO_HTTP_CODE
		);
		
		// Calculate success flags
		// CURL technical success (no CURL errors)
		$this->meta->isCurlSuccess =
			$this->meta->curlErrorCode == 0
		;
		
		// If the HTTP code is not an error
		$this->meta->isHttpCodeSuccess =
			$this->meta->code < 400
			|| $this->meta->code >= 600
		;
		
		$this->meta->isSuccess =
			$this->meta->isCurlSuccess
			&& $this->meta->isHttpCodeSuccess
		;
		
		// True data validation will be done in the DataProcessor::process() method
		$this->meta->isDataValid = $this->meta->isSuccess;
	}
}
?>