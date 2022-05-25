<?php
namespace ddMakeHttpRequest;

class Snippet extends \DDTools\Snippet {
	protected
		$version = '2.3.2',
		
		$params = [
			//Defaults
			'url' => null,
			'method' => 'get',
			'postData' => null,
			'sendRawPostData' => false,
			'headers' => [],
			'userAgent' => null,
			'timeout' => 60,
			'proxy' => null,
			'useCookie' => false
		],
		
		$paramsTypes = [
			'sendRawPostData' => 'boolean',
			'headers' => 'objectArray',
			'timeout' => 'integer',
			'useCookie' => 'boolean'
		],
		
		$renamedParamsCompliance = [
			'method' => 'metod',
			'userAgent' => 'uagent',
			'postData' => 'post',
			'useCookie' => 'cookie'
		]
	;
	
	/**
	 * prepareParams
	 * @version 1.1 (2021-04-01)
	 * 
	 * @param $this->params {stdClass|arrayAssociative|stringJsonObject|stringQueryFormatted}
	 * 
	 * @return {void}
	 */
	protected function prepareParams($params = []){
		//Call base method
		parent::prepareParams($params);
		
		$this->params->method = strtolower($this->params->method);
		
		if (is_object($this->params->postData)){
			$this->params->postData = (array) $this->params->postData;
		}
		
		if (!empty($this->params->postData)){
			$this->params->method = 'post';
			
			if (
				//Если отправляемые данные переданы строкой
				!is_array($this->params->postData) &&
				//И обрабатывать её можно
				!$this->params->sendRawPostData
			){
				$this->params->postData = \DDTools\ObjectTools::convertType([
					'object' => $this->params->postData,
					'type' => 'objectArray'
				]);
			}
		}
	}
	
	/**
	 * run
	 * @version 1.1.2 (2022-05-25)
	 * 
	 * @return {string}
	 */
	public function run(){
		//The snippet must return an empty string even if result is absent
		$result = '';
		
		if (!empty($this->params->url)){
			$manualRedirect = false;
			
			//Разбиваем адрес на компоненты
			$urlArray = parse_url($this->params->url);
			$urlArray['scheme'] =
				isset($urlArray['scheme']) ?
				$urlArray['scheme'] :
				'http'
			;
			$urlArray['path'] =
				isset($urlArray['path']) ?
				$urlArray['path'] :
				''
			;
			$urlArray['query'] =
				isset($urlArray['query']) ?
				'?' . $urlArray['query'] :
				''
			;
			
			//Инициализируем сеанс CURL
			$ch = curl_init(
				$urlArray['scheme'] . '://' .
				$urlArray['host'] .
				$urlArray['path'] .
				$urlArray['query']
			);
			
			//Выставление таймаута
			curl_setopt(
				$ch,
				CURLOPT_TIMEOUT,
				$this->params->timeout
			);
			
			//Если необходимо соединиться с https
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
			
			//Устанавливаем порт, если задан
			if(isset($urlArray['port'])){
				curl_setopt(
					$ch,
					CURLOPT_PORT,
					$urlArray['port']
				);
			}
			
			//Результат должен быть возвращен, а не выведен
			curl_setopt(
				$ch,
				CURLOPT_RETURNTRANSFER,
				1
			);
			
			//Не включаем полученные заголовки в результат
			
			if (
				ini_get('open_basedir') != '' ||
				ini_get('safe_mode')
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
				//При установке этого параметра в ненулевое значение, при получении HTTP заголовка "Location: " будет происходить перенаправление на указанный этим заголовком URL (это действие выполняется рекурсивно, для каждого полученного заголовка "Location:").
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
			
			//Если есть переменные для отправки
			if (
				$this->params->method == 'post' &&
				!empty($this->params->postData)
			){
				//Запрос будет методом POST типа application/x-www-form-urlencoded (используемый браузерами при отправке форм)
				curl_setopt(
					$ch,
					CURLOPT_POST,
					1
				);
				
				//Если он массив — делаем query string
				if (is_array($this->params->postData)){
					$this->params->postData = http_build_query($this->params->postData);
				}
				
				curl_setopt(
					$ch,
					CURLOPT_POSTFIELDS,
					$this->params->postData
				);
			}
			
			//Если заданы какие-то HTTP заголовки
			if (is_array($this->params->headers)){
				curl_setopt(
					$ch,
					CURLOPT_HTTPHEADER,
					$this->params->headers
				);
			}
			
			//Если задан UserAgent
			if (!empty($this->params->userAgent)){
				curl_setopt(
					$ch,
					CURLOPT_USERAGENT,
					$this->params->userAgent
				);
			}
			
			//Если задано использование печенек
			if ($this->params->useCookie){
				curl_setopt(
					$ch,
					CURLOPT_COOKIEFILE,
					(
						\ddTools::$modx->getConfig('base_path') .
						'assets/cache/ddMakeHttpRequest_cookie.txt'
					)
				);
				curl_setopt(
					$ch,
					CURLOPT_COOKIEJAR,
					(
						\ddTools::$modx->getConfig('base_path') .
						'assets/cache/ddMakeHttpRequest_cookie.txt'
					)
				);
			}
			
			//Если задан прокси-сервер
			if(!empty($this->params->proxy)){
				curl_setopt(
					$ch,
					CURLOPT_PROXY,
					$this->params->proxy
				);
			}
			
			//Выполняем запрос
			$result = curl_exec($ch);
			
			//Если есть ошибки или ничего не получили
			if (
				curl_errno($ch) != 0 &&
				empty($result)
			){
				$result = '';
			}elseif ($manualRedirect){
				$redirectCount = 10;
				
				while (0 < $redirectCount--){
					//Получаем заголовки, контент и код ответа
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
					
					//Проверяем код на редирект
					if (intval($resultResponseCode / 100) == 3){
						//Ищем новый url в заголовках
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
						
						
						//Парсим url
						$redirectUrl = parse_url(trim($newUrlStr));
						if (!is_array($redirectUrl)){
							$redirectUrl = [];
						}
						
						
						//Собираем новый url
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
							$redirectUrl['scheme'] . '://' .
							$redirectUrl['host'] .
							$redirectUrl['path'] .
							(
								!empty($redirectUrl['query']) ?
								'?' . $redirectUrl['query'] :
								''
							)
						;
						
						
						//Выполняем запрос с новым адресом
						curl_setopt(
							$ch,
							CURLOPT_URL,
							$newUrl
						);
						
						$result = curl_exec($ch);
						
						if (
							curl_errno($ch) != 0 &&
							empty($result)
						){
							$result = false;
							
							break;
						}
					}else{
						$result = $resultData;
						
						break;
					}
				}
			}
			
			//Закрываем сеанс CURL
			curl_close($ch);
		}
		
		return $result;
	}
}