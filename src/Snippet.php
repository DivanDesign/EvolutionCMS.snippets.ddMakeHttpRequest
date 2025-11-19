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
	 * @version 1.2 (2025-11-19)
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
				$this->params->data = \DDTools\ObjectTools::convertType([
					'object' => $this->params->data,
					'type' => 'objectArray',
				]);
			}
		}
	}
	
	/**
	 * run
	 * @version 1.4 (2025-11-19)
	 * 
	 * @return {mixed} — Response data, metadata, or both depending on outputter.
	 */
	public function run(){
		// Initialize result with all fields
		$resultObject = (object) [
			'meta' => (object) [
				'isSuccess' => false,
				'effectiveUrl' => '',
				'curlErrorCode' => 0,
				'curlErrorMessage' => '',
				'code' => 0,
			],
			'data' => '',
		];
		
		if (!empty($this->params->url)){
			$manualRedirect = false;
			
			// Разбиваем адрес на компоненты
			$urlObject = (object) parse_url($this->params->url);
			$urlObject->scheme =
				isset($urlObject->scheme)
				? $urlObject->scheme
				: 'http'
			;
			$urlObject->path =
				isset($urlObject->path)
				? $urlObject->path
				: ''
			;
			$urlObject->query =
				isset($urlObject->query)
				? '?' . $urlObject->query
				: ''
			;
			
			// Инициализируем сеанс CURL
			$ch = curl_init(
				$urlObject->scheme . '://'
				. $urlObject->host
				. $urlObject->path
				. $urlObject->query
			);
			
			// Выставление таймаута
			curl_setopt(
				$ch,
				CURLOPT_TIMEOUT,
				$this->params->timeout
			);
			
			// Если необходимо соединиться с https
			if ($urlObject->scheme === 'https'){
				curl_setopt(
					$ch,
					CURLOPT_SSL_VERIFYPEER,
					0
				);
				curl_setopt(
					$ch,
					CURLOPT_SSL_VERIFYHOST,
					0
				);
			}
			
			// Устанавливаем порт, если задан
			if(isset($urlObject->port)){
				curl_setopt(
					$ch,
					CURLOPT_PORT,
					$urlObject->port
				);
			}
			
			// Результат должен быть возвращен, а не выведен
			curl_setopt(
				$ch,
				CURLOPT_RETURNTRANSFER,
				1
			);
			
			// Не включаем полученные заголовки в результат
			
			if (
				ini_get('open_basedir') != ''
				|| ini_get('safe_mode')
			){
				curl_setopt(
					$ch,
					CURLOPT_HEADER,
					1
				);
				
				$manualRedirect = true;
			}else{
				curl_setopt(
					$ch,
					CURLOPT_HEADER,
					0
				);
				// При установке этого параметра в ненулевое значение, при получении HTTP заголовка "Location: " будет происходить перенаправление на указанный этим заголовком URL (это действие выполняется рекурсивно, для каждого полученного заголовка "Location:").
				curl_setopt(
					$ch,
					CURLOPT_FOLLOWLOCATION,
					true
				);
			}
			
			curl_setopt(
				$ch,
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
						$ch,
						CURLOPT_POST,
						1
					);
				// Для остальных методов используем кастомный метод
				}else{
					curl_setopt(
						$ch,
						CURLOPT_CUSTOMREQUEST,
						strtoupper($this->params->method)
					);
				}
				
				curl_setopt(
					$ch,
					CURLOPT_POSTFIELDS,
					$this->params->data
				);
			}elseif ($this->params->method != 'get'){
				// Для других методов (кроме GET и POST/PUT/PATCH/DELETE с данными) используем кастомный метод
				curl_setopt(
					$ch,
					CURLOPT_CUSTOMREQUEST,
					strtoupper($this->params->method)
				);
			}
			
			// Если заданы какие-то HTTP заголовки
			if (is_array($this->params->headers)){
				curl_setopt(
					$ch,
					CURLOPT_HTTPHEADER,
					$this->params->headers
				);
			}
			
			// Если задан UserAgent
			if (!empty($this->params->userAgent)){
				curl_setopt(
					$ch,
					CURLOPT_USERAGENT,
					$this->params->userAgent
				);
			}
			
			// Если задано использование печенек
			if ($this->params->isCookieUsed){
				curl_setopt(
					$ch,
					CURLOPT_COOKIEFILE,
					(
						\ddTools::$modx->getConfig('base_path')
						. 'assets/cache/ddMakeHttpRequest_cookie.txt'
					)
				);
				curl_setopt(
					$ch,
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
					$ch,
					CURLOPT_PROXY,
					$this->params->proxy
				);
			}
			
			// Выполняем запрос
			$resultObject->data = curl_exec($ch);
			
			// Get information about the request
			$resultObject->meta->effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
			$resultObject->meta->curlErrorCode = curl_errno($ch);
			$resultObject->meta->curlErrorMessage = curl_error($ch);
			$resultObject->meta->code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			// If there are no errors or something was received
			$isCurlSuccess =
				$resultObject->meta->curlErrorCode == 0
				|| !empty($resultObject->data)
			;
			// If the HTTP code is not an error
			$isHttpCodeSuccess =
				$resultObject->meta->code < 400
				|| $resultObject->meta->code >= 600
			;
			$resultObject->meta->isSuccess =
				$isCurlSuccess
				&& $isHttpCodeSuccess
			;
			
			// Log errors or debug info
			$this->log([
				'isSuccess' => $resultObject->meta->isSuccess,
				'isHttpCodeSuccess' => $isHttpCodeSuccess,
				'effectiveUrl' => $resultObject->meta->effectiveUrl,
				'httpCode' => $resultObject->meta->code,
				'curlErrorCode' => $resultObject->meta->curlErrorCode,
				'curlErrorMessage' => $resultObject->meta->curlErrorMessage,
			]);
			
			if (!$isCurlSuccess){
				$resultObject->data = '';
			}elseif ($manualRedirect){
				$redirectCount = 10;
				
				while (0 < $redirectCount--){
					// Получаем заголовки, контент и код ответа
					$resultHeader = substr(
						$resultObject->data,
						0,
						curl_getinfo(
							$ch,
							CURLINFO_HEADER_SIZE
						)
					);
					$resultData = substr(
						$resultObject->data,
						curl_getinfo(
							$ch,
							CURLINFO_HEADER_SIZE
						)
					);
					$resultResponseCode = curl_getinfo(
						$ch,
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
						$redirectUrlObject = parse_url(trim($newUrlStr));
						$redirectUrlObject =
							is_array($redirectUrlObject)
							? (object) $redirectUrlObject
							: new \stdClass()
						;
						
						
						// Собираем новый url
						$lastUrlObject = (object) parse_url(curl_getinfo(
							$ch,
							CURLINFO_EFFECTIVE_URL
						));
						
						if (!$redirectUrlObject->scheme){
							$redirectUrlObject->scheme = $lastUrlObject->scheme;
						}
						if (!$redirectUrlObject->host){
							$redirectUrlObject->host = $lastUrlObject->host;
						}
						if (!$redirectUrlObject->path){
							$redirectUrlObject->path = $lastUrlObject->path;
						}
						
						$newUrl =
							$redirectUrlObject->scheme . '://'
							. $redirectUrlObject->host
							. $redirectUrlObject->path
							. (
								!empty($redirectUrlObject->query)
								? '?' . $redirectUrlObject->query
								: ''
							)
						;
						
						
						// Выполняем запрос с новым адресом
						curl_setopt(
							$ch,
							CURLOPT_URL,
							$newUrl
						);
						
						$resultObject->data = curl_exec($ch);
						
						// Get information about the request
						$resultObject->meta->effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
						$resultObject->meta->curlErrorCode = curl_errno($ch);
						$resultObject->meta->curlErrorMessage = curl_error($ch);
						$resultObject->meta->code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
						// If there are no errors or something was received
						$isCurlSuccess =
							$resultObject->meta->curlErrorCode == 0
							|| !empty($resultObject->data)
						;
						// If the HTTP code is not an error
						$isHttpCodeSuccess =
							$resultObject->meta->code < 400
							|| $resultObject->meta->code >= 600
						;
						$resultObject->meta->isSuccess =
							$isCurlSuccess
							&& $isHttpCodeSuccess
						;
						
						// Log errors or debug info
						$this->log([
							'isSuccess' => $resultObject->meta->isSuccess,
							'isHttpCodeSuccess' => $isHttpCodeSuccess,
							'effectiveUrl' => $resultObject->meta->effectiveUrl,
							'httpCode' => $resultObject->meta->code,
							'curlErrorCode' => $resultObject->meta->curlErrorCode,
							'curlErrorMessage' => $resultObject->meta->curlErrorMessage,
							'context' => 'during manual redirect',
						]);	
						
						if (!$isCurlSuccess){
							$resultObject->data = false;
							
							break;
						}
					}else{
						$resultObject->data = $resultData;
						
						break;
					}
				}
			}
			
			// Закрываем сеанс CURL
			curl_close($ch);
		}
		
		// Process result based on outputter->type parameter
		switch ($this->params->outputter->type){
			case 'meta':
				$result = $resultObject->meta;
			break;
			
			case 'metadata':
				$result = $resultObject;
			break;
			
			// case 'data' or default
			default:
				$result = $resultObject->data;
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
	 * log
	 * @version 2.0 (2025-11-19)
	 * 
	 * @param $params {stdClass|arrayAssociative}
	 * @param $params->effectiveUrl {string}
	 * @param $params->httpCode {integer}
	 * @param $params->isSuccess {boolean}
	 * @param $params->isHttpCodeSuccess {boolean}
	 * @param [$params->curlErrorCode=0] {integer}
	 * @param [$params->curlErrorMessage=''] {string}
	 * @param [$params->context=''] {string}
	 * 
	 * @return {void}
	 */
	private function log($params = []): void {
		$params = \DDTools\Tools\Objects::extend([
			'objects' => [
				// Defaults
				(object) [
					'effectiveUrl' => '',
					'httpCode' => 0,
					'curlErrorCode' => 0,
					'curlErrorMessage' => '',
					'isSuccess' => false,
					'isHttpCodeSuccess' => false,
					'context' => '',
				],
				$params,
			],
		]);
		
		if (
			!$params->isSuccess
			|| $this->params->isDebug
		){
			// Compose message title
			$messageTitle = 'Request debug info';
			
			if (!$params->isSuccess){
				$messageTitle =
					!$params->isHttpCodeSuccess
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
						. '<li>URL: <code>' . htmlspecialchars($params->effectiveUrl) . '</code>;</li>'
						. '<li>HTTP code: <code>' . $params->httpCode . '</code>;</li>'
						. (
							$params->curlErrorCode != 0
							? (
								'<li>CURL error code: <code>' . $params->curlErrorCode . '</code>;</li>'
								. '<li>CURL error message: <code>' . htmlspecialchars($params->curlErrorMessage) . '</code>;</li>'
							)
							: ''
						)
						. '<li>Snippet parameters: <pre>' . htmlspecialchars(var_export($this->params, true)) . '</pre>;</li>'
					. '</ul>'
				,
				'eventType' =>
					$params->isSuccess
					? 'information'
					: 'error'
				,
				'source' => 'ddMakeHttpRequest',
			]);
		}
	}
}