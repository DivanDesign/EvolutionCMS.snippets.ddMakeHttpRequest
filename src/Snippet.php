<?php
namespace ddMakeHttpRequest;

class Snippet extends \DDTools\Snippet {
	protected $version = '2.3.2';
	
	// Defaults
	protected $params = [
		'requester' => [
			'url' => null,
			'method' => 'get',
			'data' => null,
			'isRawDataEnabled' => false,
			'headers' => [],
			'userAgent' => null,
			'timeout' => 60,
			'proxy' => null,
			'isCookieUsed' => false,
		],
		'isDebug' => false,
		'outputter' => [
			'type' => 'data',
			'convertTo' => '',
		],
	];
	
	protected $paramsTypes = [
		'requester' => 'objectStdClass',
		'isDebug' => 'boolean',
		'outputter' => 'objectStdClass',
	];
	
	/**
	 * prepareParams
	 * @version 1.2.2 (2025-11-23)
	 * 
	 * @param $this->params {stdClass|arrayAssociative|stringJsonObject|stringQueryFormatted}
	 * 
	 * @return {void}
	 */
	protected function prepareParams($params = []){
		// Call base method
		parent::prepareParams($params);
		
		$this->prepareParams_backwardCompatibility();
		
		$this->params->requester->method = strtolower($this->params->requester->method);
		$this->params->outputter->type = strtolower($this->params->outputter->type);
		
		if (is_object($this->params->requester->data)){
			$this->params->requester->data = (array) $this->params->requester->data;
		}
		
		if (!empty($this->params->requester->data)){
			if (empty($this->params->requester->method)){
				$this->params->requester->method = 'post';
			}
			
			if (
				// Если отправляемые данные переданы строкой
				!is_array($this->params->requester->data)
				// И обрабатывать её можно
				&& !$this->params->requester->isRawDataEnabled
			){
				$this->params->requester->data = \DDTools\Tools\Objects::convertType([
					'object' => $this->params->requester->data,
					'type' => 'objectArray',
				]);
			}
		}
	}
	
	/**
	 * prepareParams_backwardCompatibility
	 * @version 1.0 (2025-11-23)
	 * 
	 * @desc Backward compatibility with old parameter structure
	 * 
	 * @return {void}
	 */
	private function prepareParams_backwardCompatibility(){
		$isLogMessageNeeded = false;
		
		$rootLevelParams = \ddTools::verifyRenamedParams([
			'params' => $this->params,
			// Compliance for renaming old parameter names
			'compliance' => [
				'method' => 'metod',
				'userAgent' => 'uagent',
				'data' => ['post', 'postData'],
				'isRawDataEnabled' => 'sendRawPostData',
				'isCookieUsed' => ['useCookie', 'cookie'],
			],
			'returnCorrectedOnly' => false,
		]);
		
		// Check if any `requester` parameters are on root level and move to `requester`
		foreach (
			array_keys((array) $this->params->requester)
			as $paramName
		){
			if (
				\DDTools\Tools\Objects::isPropExists([
					'object' => $rootLevelParams,
					'propName' => $paramName,
				])
			){
				$isLogMessageNeeded = true;
				
				// Move to requester
				$this->params->requester->{$paramName} = $rootLevelParams->{$paramName};
				// Remove from root level
				unset($this->params->{$paramName});
			}
		}
		
		// If something was found on root level
		if ($isLogMessageNeeded){
			// Log deprecation warning
			\ddTools::logEvent([
				'message' => '<p>You are using deprecated snippet parameters.</p><p>Backward compatibility is maintained and everything is working fine right now. But we strongly recommend to stay up to date.</p><p>Please use <code>requester</code> parameter with nested properties instead of root-level parameters.</p><p>Checkout documentation and fix it ASAP.</p>',
				'source' => 'ddMakeHttpRequest',
			]);
		}
	}
	
	/**
	 * run
	 * @version 1.4.11 (2025-11-24)
	 * 
	 * @return {mixed} — Response data, metadata, or both depending on outputter.
	 */
	public function run(){
		// Initialize logger
		$theLoggerInstance = new \ddMakeHttpRequest\Logger($this->params);
		// Initialize requester
		$theRequester = new \ddMakeHttpRequest\Requester([
			'theLoggerInstance' => $theLoggerInstance,
		]);
		
		$requestResult = $theRequester->execute($this->params->requester);
		
		$theResultInstance = $requestResult->theResultInstance;
		$curlHandle = $requestResult->curlHandle;
		$isManualRedirect = $requestResult->isManualRedirect;
		
		if ($curlHandle){
			// Handle manual redirects if needed
			if ($isManualRedirect){
				$redirectCount = 10;
				
				while (0 < $redirectCount--){
					// Получаем заголовки, контент и код ответа
					$resultHeader = substr(
						$theResultInstance->data,
						0,
						curl_getinfo(
							$curlHandle,
							CURLINFO_HEADER_SIZE
						)
					);
					$resultData = substr(
						$theResultInstance->data,
						curl_getinfo(
							$curlHandle,
							CURLINFO_HEADER_SIZE
						)
					);
					$resultResponseCode = curl_getinfo(
						$curlHandle,
						CURLINFO_HTTP_CODE
					);
					
					// Проверяем код на редирект
					if (intval($resultResponseCode / 100) == 3){
						// Ищем новый url в заголовках
						$matches = [];
						
						preg_match(
							'/location:(.*?)\n/i',
							$resultHeader,
							$matches
						);
						
						$newUrlStr = '';
						
						if (count($matches)){
							$newUrlStr = array_pop($matches);
						}
						
						
						// Парсим url
						$lastUrlObject = \ddMakeHttpRequest\Requester::parseUrlStrToObject([
							'url' => curl_getinfo(
								$curlHandle,
								CURLINFO_EFFECTIVE_URL
							),
						]);
						
						$redirectUrlObject = \ddMakeHttpRequest\Requester::parseUrlStrToObject([
							'url' => trim($newUrlStr),
							'defaults' => [
								'scheme' => $lastUrlObject->scheme,
								'host' => $lastUrlObject->host,
								'path' => $lastUrlObject->path,
							],
						]);
						
						// Выполняем запрос с новым адресом
						curl_setopt(
							$curlHandle,
							CURLOPT_URL,
							$redirectUrlObject->full
						);
						
						$theResultInstance->fetchFromCurl([
							'curlHandle' => $curlHandle,
						]);
						
						// Log errors or debug info
						$theLoggerInstance->log([
							'theResultInstance' => $theResultInstance,
							'context' => 'during manual redirect',
						]);
						
						if (!$theResultInstance->meta->isCurlSuccess){
							$theResultInstance->data = false;
							
							break;
						}
					}else{
						$theResultInstance->data = $resultData;
						
						break;
					}
				}
			}
			
			// Закрываем сеанс CURL
			curl_close($curlHandle);
		}
		
		// Process result based on outputter->type parameter
		switch ($this->params->outputter->type){
			case 'meta':
				$result = $theResultInstance->meta;
			break;
			
			case 'metadata':
				$result = $theResultInstance;
			break;
			
			// case 'data' or default
			default:
				$result = $theResultInstance->data;
		}
		
		if (!empty($this->params->outputter->convertTo)){
			$result = \DDTools\Tools\Objects::convertType([
				'object' => $result,
				'type' => $this->params->outputter->convertTo,
			]);
		}
		
		return $result;
	}
}