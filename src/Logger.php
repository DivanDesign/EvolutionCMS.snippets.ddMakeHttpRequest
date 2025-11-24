<?php
namespace ddMakeHttpRequest;

/**
 * Logger
 * 
 * @desc Handles logging of HTTP requests and responses
 */
class Logger {
	private $params;
	
	/**
	 * __construct
	 * @version 1.0 (2025-11-24)
	 * 
	 * @param $params {stdClass|arrayAssociative} — Snippet parameters
	 * @param $params->isDebug {boolean} — Whether to log debug information.
	 */
	public function __construct($params){
		$this->params = $params;
	}
	
	/**
	 * log
	 * @version 3.0.2 (2025-11-21)
	 * 
	 * @param $params {stdClass|arrayAssociative}
	 * @param $params->theResultInstance {\ddMakeHttpRequest\Result}
	 * @param [$params->context=''] {string}
	 * 
	 * @return {void}
	 */
	public function log($params = []): void {
		$params = \DDTools\Tools\Objects::extend([
			'objects' => [
				// Defaults
				(object) [
					'theResultInstance' => null,
					'context' => '',
				],
				$params,
			],
		]);
		
		if (
			!$params->theResultInstance->meta->isSuccess
			|| $this->params->isDebug
		){
			// Compose message title
			$messageTitle = 'Request debug info';
			
			if (!$params->theResultInstance->meta->isSuccess){
				$messageTitle =
					!$params->theResultInstance->meta->isHttpCodeSuccess
					? 'HTTP error response received'
					: 'CURL request failed'
				;
			}
			
			if (!empty($params->context)){
				$messageTitle .= ' (' . $params->context . ')';
			}
			
			\ddTools::logEvent([
				'message' =>
					'<p>' . $messageTitle . '.</p>'
					. '<ul>'
						. '<li>URL: <code>' . htmlspecialchars($params->theResultInstance->meta->effectiveUrl) . '</code>;</li>'
						. '<li>HTTP code: <code>' . $params->theResultInstance->meta->code . '</code>;</li>'
						. (
							$params->theResultInstance->meta->curlErrorCode != 0
							? (
								'<li>CURL error code: <code>' . $params->theResultInstance->meta->curlErrorCode . '</code>;</li>'
								. '<li>CURL error message: <code>' . htmlspecialchars($params->theResultInstance->meta->message) . '</code>;</li>'
							)
							: ''
						)
						. '<li>Snippet parameters: <pre>' . htmlspecialchars(var_export($this->params, true)) . '</pre>;</li>'
					. '</ul>'
				,
				'eventType' =>
					$params->theResultInstance->meta->isSuccess
					? 'information'
					: 'error'
				,
				'source' => 'ddMakeHttpRequest',
			]);
		}
	}
}
?>
