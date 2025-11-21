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
	 * @property $meta->effectiveUrl {string}
	 * @property $meta->curlErrorCode {integer}
	 * @property $meta->curlErrorMessage {string}
	 * @property $meta->code {integer}
	 */
	public $meta = [
		'isSuccess' => false,
		'effectiveUrl' => '',
		'curlErrorCode' => 0,
		'curlErrorMessage' => '',
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
}
?>