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
		'useCookie' => false,
	];
	
	protected $paramsTypes = [
		'isRawDataEnabled' => 'boolean',
		'headers' => 'objectArray',
		'timeout' => 'integer',
		'useCookie' => 'boolean',
	];
	
	protected $renamedParamsCompliance = [
		'method' => 'metod',
		'userAgent' => 'uagent',
		'data' => ['post', 'postData'],
		'isRawDataEnabled' => 'sendRawPostData',
		'useCookie' => 'cookie',
	];
	
	/**
	 * prepareParams
	 * @version 1.1.4 (2025-11-17)
	 * 
	 * @param $this->params {stdClass|arrayAssociative|stringJsonObject|stringQueryFormatted}
	 * 
	 * @return {void}
	 */
	protected function prepareParams($params = []){
		// Call base method
		parent::prepareParams($params);
		
		$this->params->method = strtolower($this->params->method);
		
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
	 * @version 1.2.3 (2025-11-17)
	 * 
	 * @return {string}
	 */
	public function run(){
		// The snippet must return an empty string even if result is absent
		$result = '';
		
		if (!empty($this->params->url)){
			$manualRedirect = false;
			
			// Разбиваем адрес на компоненты
			$urlArray = parse_url($this->params->url);
			$urlArray['scheme'] =
				isset($urlArray['scheme'])
				? $urlArray['scheme']
				: 'http'
			;
			$urlArray['path'] =
				isset($urlArray['path'])
				? $urlArray['path']
				: ''
			;
			$urlArray['query'] =
				isset($urlArray['query'])
				? '?' . $urlArray['query']
				: ''
			;
			
			// Инициализируем сеанс CURL
			$ch = curl_init(
				$urlArray['scheme'] . '://'
				. $urlArray['host']
				. $urlArray['path']
				. $urlArray['query']
			);
			
			// Выставление таймаута
			curl_setopt(
				$ch,
				CURLOPT_TIMEOUT,
				$this->params->timeout
			);
			
			// Если необходимо соединиться с https
			if ($urlArray['scheme'] === 'https'){
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
			if(isset($urlArray['port'])){
				curl_setopt(
					$ch,
					CURLOPT_PORT,
					$urlArray['port']
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
			if ($this->params->useCookie){
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
			$result = curl_exec($ch);
			
			// Get information about the request
			$curlErrorNo = curl_errno($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			
			// If there are errors or nothing was received
			$isCurlError =
				$curlErrorNo != 0
				&& empty($result)
			;
			$isHttpCodeError =
				$httpCode >= 400
				&& $httpCode < 600
			;
			
			// Log errors
			if (
				$isCurlError
				|| $isHttpCodeError
			){
				$this->log([
					'effectiveUrl' => curl_getinfo($ch, CURLINFO_EFFECTIVE_URL),
					'httpCode' => $httpCode,
					'curlErrorCode' => $curlErrorNo,
					'curlErrorMessage' => curl_error($ch),
				]);
			}
			
			if ($isCurlError){
				$result = '';
			}elseif ($manualRedirect){
				$redirectCount = 10;
				
				while (0 < $redirectCount--){
					// Получаем заголовки, контент и код ответа
					$resultHeader = substr(
						$result,
						0,
						curl_getinfo(
							$ch,
							CURLINFO_HEADER_SIZE
						)
					);
					$resultData = substr(
						$result,
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
						$redirectUrl = parse_url(trim($newUrlStr));
						if (!is_array($redirectUrl)){
							$redirectUrl = [];
						}
						
						
						// Собираем новый url
						$lastUrl = parse_url(curl_getinfo(
							$ch,
							CURLINFO_EFFECTIVE_URL
						));
						
						if (!$redirectUrl['scheme']){
							$redirectUrl['scheme'] = $lastUrl['scheme'];
						}
						if (!$redirectUrl['host']){
							$redirectUrl['host'] = $lastUrl['host'];
						}
						if (!$redirectUrl['path']){
							$redirectUrl['path'] = $lastUrl['path'];
						}
						
						$newUrl =
							$redirectUrl['scheme'] . '://'
							. $redirectUrl['host']
							. $redirectUrl['path']
							. (
								!empty($redirectUrl['query'])
								? '?' . $redirectUrl['query']
								: ''
							)
						;
						
						
						// Выполняем запрос с новым адресом
						curl_setopt(
							$ch,
							CURLOPT_URL,
							$newUrl
						);
						
						$result = curl_exec($ch);
						
						// Get information about the request
						$curlErrorNo = curl_errno($ch);
						$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
						
						// If there are errors or nothing was received
						$isCurlError =
							$curlErrorNo != 0
							&& empty($result)
						;
						$isHttpCodeError =
							$httpCode >= 400
							&& $httpCode < 600
						;
						
						// Log errors
						if (
							$isCurlError
							|| $isHttpCodeError
						){
							$this->log([
								'effectiveUrl' => curl_getinfo($ch, CURLINFO_EFFECTIVE_URL),
								'httpCode' => $httpCode,
								'curlErrorCode' => $curlErrorNo,
								'curlErrorMessage' => curl_error($ch),
							]);	
						}
						
						if ($isCurlError){
							$result = false;
							
							break;
						}
					}else{
						$result = $resultData;
						
						break;
					}
				}
			}
			
			// Закрываем сеанс CURL
			curl_close($ch);
		}
		
		return $result;
	}
	
	/**
	 * log
	 * @version 1.0.0 (2025-11-17)
	 * 
	 * @param $params {stdClass|arrayAssociative}
	 * @param $params->effectiveUrl {string}
	 * @param $params->httpCode {integer}
	 * @param [$params->curlErrorCode=0] {integer}
	 * @param [$params->curlErrorMessage=''] {string}
	 * 
	 * @return {void}
	 */
	private function log($params = []): void {
		$params = $params = \DDTools\Tools\Objects::extend([
			'objects' => [
				// Defaults
				(object) [
					'effectiveUrl' => '',
					'httpCode' => 0,
					'curlErrorCode' => 0,
					'curlErrorMessage' => '',
				],
				$params,
			],
		]);
		
		$isHttpCodeError =
			$params->httpCode >= 400
			&& $params->httpCode < 600
		;
		
		\ddTools::logEvent([
			'message' =>
				'<p>'
					. (
						$isHttpCodeError
						? 'HTTP error response received'
						: 'CURL request failed'
					)
				. ' during manual redirect.</p>'
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
			'eventType' => 'error',
			'source' => 'ddMakeHttpRequest',
		]);
	}
}