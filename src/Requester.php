<?php
namespace ddMakeHttpRequest;

/**
 * Requester
 * 
 * @desc Handles HTTP request execution
 */
class Requester {
	/**
	 * @property $theLoggerInstance {\ddMakeHttpRequest\Logger|null} — Logger instance for logging requests
	 */
	private $theLoggerInstance = null;
	
	/**
	 * __construct
	 * @version 1.0 (2025-11-24)
	 * 
	 * @param $params {stdClass|arrayAssociative}
	 * @param [$params->theLoggerInstance=null] {\ddMakeHttpRequest\Logger|null} — Logger instance for logging requests
	 */
	public function __construct($params = []){
		$params = (object) $params;
		
		if (isset($params->theLoggerInstance)){
			$this->theLoggerInstance = $params->theLoggerInstance;
		}
	}
	
	/**
	 * parseUrlStrToObject
	 * @version 1.0 (2025-11-21)
	 * 
	 * @param $params {stdClass|arrayAssociative}
	 * @param $params->url {string}
	 * @param [$params->defaults] {stdClass|arrayAssociative} — Default values for missing URL components
	 * @param [$params->defaults->scheme='http'] {string}
	 * @param [$params->defaults->host=''] {string}
	 * @param [$params->defaults->path=''] {string}
	 * 
	 * @return $result {stdClass} — Parsed URL object with all components and full URL string
	 * @return $result->scheme {string}
	 * @return $result->host {string}
	 * @return $result->path {string}
	 * @return $result->query {string} — With '?' prefix if present, empty string otherwise
	 * @return $result->full {string} — Complete URL string
	 */
	public static function parseUrlStrToObject($params = []){
		$params = \DDTools\Tools\Objects::extend([
			'objects' => [
				(object) [
					'url' => '',
					'defaults' => [
						'scheme' => 'http',
						'host' => '',
						'path' => '',
					],
				],
				$params,
			],
		]);
		
		// Parse URL
		$resultUrlObject = parse_url($params->url);
		$resultUrlObject =
			is_array($resultUrlObject)
			? (object) $resultUrlObject
			: new \stdClass()
		;
		
		// Apply defaults
		if (!isset($resultUrlObject->scheme)){
			$resultUrlObject->scheme = $params->defaults->scheme;
		}
		if (!isset($resultUrlObject->host)){
			$resultUrlObject->host = $params->defaults->host;
		}
		if (!isset($resultUrlObject->path)){
			$resultUrlObject->path = $params->defaults->path;
		}
		if (!isset($resultUrlObject->query)){
			$resultUrlObject->query = $params->defaults->query;
		}
		
		$resultUrlObject->query =
			!empty($resultUrlObject->query)
			? '?' . $resultUrlObject->query
			: ''
		;
		
		// Build full URL
		$resultUrlObject->full =
			$resultUrlObject->scheme . '://'
			. $resultUrlObject->host
			. $resultUrlObject->path
			. $resultUrlObject->query
		;
		
		return $resultUrlObject;
	}
}
?>
