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
	 * execute
	 * @version 2.0 (2025-11-24)
	 * 
	 * @desc Executes HTTP request and returns result. Handles manual redirects if needed.
	 * 
	 * @param $params {stdClass|arrayAssociative} — Request parameters
	 * @param $params->url {string}
	 * @param $params->method {string}
	 * @param $params->data {string|array}
	 * @param $params->headers {array}
	 * @param $params->userAgent {string}
	 * @param $params->timeout {integer}
	 * @param $params->proxy {string}
	 * @param $params->isCookieUsed {boolean}
	 * 
	 * @return {\ddMakeHttpRequest\Result}
	 */
	public function execute($params = []){
		$params = (object) $params;
		
		// Initialize result object
		$theResultInstance = new \ddMakeHttpRequest\Result();
		
		
		if (!empty($params->url)){
			$isManualRedirect = false;
			
			// Разбиваем адрес на компоненты
			$urlObject = self::parseUrlStrToObject([
				'url' => $params->url,
			]);
			
			// Инициализируем сеанс CURL
			$curlHandle = curl_init($urlObject->full);
			
			// Выставление таймаута
			curl_setopt(
				$curlHandle,
				CURLOPT_TIMEOUT,
				$params->timeout
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
				
				$isManualRedirect = true;
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
					$params->method,
					[
						'post',
						'put',
						'patch',
						'delete',
					]
				)
				&& !empty($params->data)
			){
				// Если он массив — делаем query string
				if (is_array($params->data)){
					$params->data = http_build_query($params->data);
				}
				
				// Для POST используем стандартный метод
				if ($params->method == 'post'){
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
						strtoupper($params->method)
					);
				}
				
				curl_setopt(
					$curlHandle,
					CURLOPT_POSTFIELDS,
					$params->data
				);
			}elseif ($params->method != 'get'){
				// Для других методов (кроме GET и POST/PUT/PATCH/DELETE с данными) используем кастомный метод
				curl_setopt(
					$curlHandle,
					CURLOPT_CUSTOMREQUEST,
					strtoupper($params->method)
				);
			}
			
			// Если заданы какие-то HTTP заголовки
			if (is_array($params->headers)){
				curl_setopt(
					$curlHandle,
					CURLOPT_HTTPHEADER,
					$params->headers
				);
			}
			
			// Если задан UserAgent
			if (!empty($params->userAgent)){
				curl_setopt(
					$curlHandle,
					CURLOPT_USERAGENT,
					$params->userAgent
				);
			}
			
			// Если задано использование печенек
			if ($params->isCookieUsed){
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
			if(!empty($params->proxy)){
				curl_setopt(
					$curlHandle,
					CURLOPT_PROXY,
					$params->proxy
				);
			}
			
			// Выполняем запрос
			$theResultInstance->fetchFromCurl([
				'curlHandle' => $curlHandle,
			]);
			
			// Log errors or debug info
			if (!is_null($this->theLoggerInstance)){
				$this->theLoggerInstance->log([
					'theResultInstance' => $theResultInstance,
				]);
			}
			
			if (!$theResultInstance->meta->isCurlSuccess){
				$theResultInstance->data = '';
			}elseif ($isManualRedirect){
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
						$lastUrlObject = self::parseUrlStrToObject([
							'url' => curl_getinfo(
								$curlHandle,
								CURLINFO_EFFECTIVE_URL
							),
						]);
						
						$redirectUrlObject = self::parseUrlStrToObject([
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
						if (!is_null($this->theLoggerInstance)){
							$this->theLoggerInstance->log([
								'theResultInstance' => $theResultInstance,
								'context' => 'during manual redirect',
							]);
						}
						
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
		
		return $theResultInstance;
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
