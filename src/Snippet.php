<?php
namespace ddMakeHttpRequest;

class Snippet extends \DDTools\Snippet {
	protected $version = '2.3.2';
	
	protected $params = [
		// Defaults
		'url' => null,
		'method' => 'get',
		'data' => null,
		'isRawDataEnabled' => false,
		'headers' => [],
		'userAgent' => null,
		'timeout' => 60,
		'proxy' => null,
		'isCookieUsed' => false,
		'isDebug' => false,
		'outputter' => [
			'type' => 'data',
			'convertTo' => '',
		],
	];
	
	protected $paramsTypes = [
		'isRawDataEnabled' => 'boolean',
		'headers' => 'objectArray',
		'timeout' => 'integer',
		'isCookieUsed' => 'boolean',
		'isDebug' => 'boolean',
		'outputter' => 'objectStdClass',
	];
	
	protected $renamedParamsCompliance = [
		'method' => 'metod',
		'userAgent' => 'uagent',
		'data' => ['post', 'postData'],
		'isRawDataEnabled' => 'sendRawPostData',
		'isCookieUsed' => ['useCookie', 'cookie'],
	];
	
	/**
	 * prepareParams
	 * @version 1.2.1 (2025-11-19)
	 * 
	 * @param $this->params {stdClass|arrayAssociative|stringJsonObject|stringQueryFormatted}
	 * 
	 * @return {void}
	 */
	protected function prepareParams($params = []){
		// Call base method
		parent::prepareParams($params);
		
		$this->params->method = strtolower($this->params->method);
		$this->params->outputter->type = strtolower($this->params->outputter->type);
		
		if (is_object($this->params->data)){
			$this->params->data = (array) $this->params->data;
		}
		
		if (!empty($this->params->data)){
			if (empty($this->params->method)){
				$this->params->method = 'post';
			}
			
			if (
				// Если отправляемые данные переданы строкой
				!is_array($this->params->data)
				// И обрабатывать её можно
				&& !$this->params->isRawDataEnabled
			){
				$this->params->data = \DDTools\Tools\Objects::convertType([
					'object' => $this->params->data,
					'type' => 'objectArray',
				]);
			}
		}
	}
	
	/**
	 * run
	 * @version 1.4.6 (2025-11-21)
	 * 
	 * @return {mixed} — Response data, metadata, or both depending on outputter.
	 */
	public function run(){
		// Initialize result object
		$theResultInstance = new \ddMakeHttpRequest\Result();
		
		if (!empty($this->params->url)){
			$manualRedirect = false;
			
			// Разбиваем адрес на компоненты
			$urlObject = $this->parseUrlStrToObject([
				'url' => $this->params->url,
			]);
			
			// Инициализируем сеанс CURL
			$curlHandle = curl_init($urlObject->full);
			
			// Выставление таймаута
			curl_setopt(
				$curlHandle,
				CURLOPT_TIMEOUT,
				$this->params->timeout
			);
			
			// Если необходимо соединиться с https
			if ($urlObject->scheme === 'https'){
				curl_setopt(
					$curlHandle,
					CURLOPT_SSL_VERIFYPEER,
					0
				);
				curl_setopt(
					$curlHandle,
					CURLOPT_SSL_VERIFYHOST,
					0
				);
			}
			
			// Устанавливаем порт, если задан
			if(isset($urlObject->port)){
				curl_setopt(
					$curlHandle,
					CURLOPT_PORT,
					$urlObject->port
				);
			}
			
			// Результат должен быть возвращен, а не выведен
			curl_setopt(
				$curlHandle,
				CURLOPT_RETURNTRANSFER,
				1
			);
			
			// Не включаем полученные заголовки в результат
			
			if (
				ini_get('open_basedir') != ''
				|| ini_get('safe_mode')
			){
				curl_setopt(
					$curlHandle,
					CURLOPT_HEADER,
					1
				);
				
				$manualRedirect = true;
			}else{
				curl_setopt(
					$curlHandle,
					CURLOPT_HEADER,
					0
				);
				// При установке этого параметра в ненулевое значение, при получении HTTP заголовка "Location: " будет происходить перенаправление на указанный этим заголовком URL (это действие выполняется рекурсивно, для каждого полученного заголовка "Location:").
				curl_setopt(
					$curlHandle,
					CURLOPT_FOLLOWLOCATION,
					true
				);
			}
			
			curl_setopt(
				$curlHandle,
				CURLOPT_MAXREDIRS,
				10
			);
			
			// Если есть переменные для отправки
			if (
				in_array(
					$this->params->method,
					[
						'post',
						'put',
						'patch',
						'delete',
					]
				)
				&& !empty($this->params->data)
			){
				// Если он массив — делаем query string
				if (is_array($this->params->data)){
					$this->params->data = http_build_query($this->params->data);
				}
				
				// Для POST используем стандартный метод
				if ($this->params->method == 'post'){
					// Запрос будет методом POST типа application/x-www-form-urlencoded (используемый браузерами при отправке форм)
					curl_setopt(
						$curlHandle,
						CURLOPT_POST,
						1
					);
				// Для остальных методов используем кастомный метод
				}else{
					curl_setopt(
						$curlHandle,
						CURLOPT_CUSTOMREQUEST,
						strtoupper($this->params->method)
					);
				}
				
				curl_setopt(
					$curlHandle,
					CURLOPT_POSTFIELDS,
					$this->params->data
				);
			}elseif ($this->params->method != 'get'){
				// Для других методов (кроме GET и POST/PUT/PATCH/DELETE с данными) используем кастомный метод
				curl_setopt(
					$curlHandle,
					CURLOPT_CUSTOMREQUEST,
					strtoupper($this->params->method)
				);
			}
			
			// Если заданы какие-то HTTP заголовки
			if (is_array($this->params->headers)){
				curl_setopt(
					$curlHandle,
					CURLOPT_HTTPHEADER,
					$this->params->headers
				);
			}
			
			// Если задан UserAgent
			if (!empty($this->params->userAgent)){
				curl_setopt(
					$curlHandle,
					CURLOPT_USERAGENT,
					$this->params->userAgent
				);
			}
			
			// Если задано использование печенек
			if ($this->params->isCookieUsed){
				curl_setopt(
					$curlHandle,
					CURLOPT_COOKIEFILE,
					(
						\ddTools::$modx->getConfig('base_path')
						. 'assets/cache/ddMakeHttpRequest_cookie.txt'
					)
				);
				curl_setopt(
					$curlHandle,
					CURLOPT_COOKIEJAR,
					(
						\ddTools::$modx->getConfig('base_path')
						. 'assets/cache/ddMakeHttpRequest_cookie.txt'
					)
				);
			}
			
			// Если задан прокси-сервер
			if(!empty($this->params->proxy)){
				curl_setopt(
					$curlHandle,
					CURLOPT_PROXY,
					$this->params->proxy
				);
			}
			
			// Выполняем запрос
			$theResultInstance->fetchFromCurl([
				'curlHandle' => $curlHandle,
			]);
			
			// Log errors or debug info
			$this->log([
				'theResultInstance' => $theResultInstance,
			]);
			
			if (!$theResultInstance->meta->isCurlSuccess){
				$theResultInstance->data = '';
			}elseif ($manualRedirect){
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
						$lastUrlObject = $this->parseUrlStrToObject([
							'url' => curl_getinfo(
								$curlHandle,
								CURLINFO_EFFECTIVE_URL
							),
						]);
						
						$redirectUrlObject = $this->parseUrlStrToObject([
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
						$this->log([
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
	private function parseUrlStrToObject($params = []){
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
	private function log($params = []): void {
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