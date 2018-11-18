<?php
/**
 * ddMakeHttpRequest
 * @version 1.3.1 (2018-11-18)
 * 
 * @desc Makes HTTP request to a given URL.
 * 
 * @uses PHP >= 5.4.
 * @uses MODXEvo.libraries.ddTools >= 0.23.
 * 
 * @param $url {string} — The URL to fetch. @required
 * @param $method {'get'|'post'} — Request type. Default: 'get'.
 * @param $postData {query string|associative array|string} — The full data to post in a HTTP "POST" operation (https://en.wikipedia.org/wiki/Query_string). E. g. 'pladeholder1=value1&pagetitle=My awesome pagetitle!'. Default: —.
 * @param $headers {query string|array} — An array of HTTP header fields to set. E. g. '0=Accept: application/vnd.api+json&1=Content-Type: application/vnd.api+json'. Default: —.
 * @param $userAgent {string} — The contents of the 'User-Agent: ' header to be used in a HTTP request. Default: —.
 * @param $timeout {integer} — The maximum number of seconds for execute request. Default: 60.
 * 
 * @link http://code.divandesign.biz/modx/ddmakehttprequest/1.3.1
 * 
 * @copyright 2011–2018 DivanDesign {@link http://www.DivanDesign.biz }
 */

//Подключаем modx.ddTools
require_once $modx->getConfig('base_path').'assets/libs/ddTools/modx.ddtools.class.php';

//Для обратной совместимости
extract(ddTools::verifyRenamedParams(
	$params,
	[
		'method' => 'metod',
		'userAgent' => 'uagent',
		'postData' => 'post'
	]
));

if (isset($url)){
	$method = ((isset($method) && $method == 'post') || isset($postData)) ? 'post' : 'get';
	
	if (
		isset($headers) &&
		!is_array($headers)
	){
		//If “=” exists
		if (strpos(
			$headers,
			'='
		) !== false){
			//Parse a query string
			parse_str(
				$headers,
				$headers
			);
		}else{
			//The old format
			$headers = ddTools::explodeAssoc($headers);
			$modx->logEvent(
				1,
				2,
				'<p>String separated by “::” && “||” in the “headers” parameter is deprecated. Use a <a href="https://en.wikipedia.org/wiki/Query_string)">query string</a>.</p><p>The snippet has been called in the document with id '.$modx->documentIdentifier.'.</p>',
				$modx->currentSnippet
			);
		}
	}
	
	$timeout = isset($timeout) && is_numeric($timeout) ? $timeout : 60;
	
	$manualRedirect = false;
	
	//Разбиваем адрес на компоненты
	$urlArray = parse_url($url);
	$urlArray['scheme'] = isset($urlArray['scheme']) ? $urlArray['scheme'] : 'http';
	$urlArray['path'] = isset($urlArray['path']) ? $urlArray['path'] : '';
	$urlArray['query'] = isset($urlArray['query']) ? '?'.$urlArray['query'] : '';
	
	//Инициализируем сеанс CURL
	$ch = curl_init($urlArray['scheme'].'://'.$urlArray['host'].$urlArray['path'].$urlArray['query']);
	
	//Выставление таймаута
	curl_setopt(
		$ch,
		CURLOPT_TIMEOUT,
		$timeout
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
		$method == 'post' &&
		isset($postData)
	){
		//Запрос будет методом POST типа application/x-www-form-urlencoded (используемый браузерами при отправке форм)
		curl_setopt(
			$ch,
			CURLOPT_POST,
			1
		);
		
		//Если пост передан строкой в старом формате
		if (
			!is_array($postData) &&
			//Определяем старый формат по наличию «::» (это спорно и неоднозначно, но пока так)
			strpos(
				$postData,
				'::'
			) !== false
		){
			$postData = ddTools::explodeAssoc($postData);
			$modx->logEvent(
				1,
				2,
				'<p>String separated by “::” && “||” in the “post” parameter is deprecated. Use a <a href="https://en.wikipedia.org/wiki/Query_string)">query string</a>.</p><p>The snippet has been called in the document with id '.$modx->documentIdentifier.'.</p>',
				$modx->currentSnippet
			);
		}
		
		//Если он массив — делаем query string
		if (is_array($postData)){
			$postData_mas = [];
			//Сформируем массив для отправки, предварительно перекодировав
			foreach (
				$postData as
				$key => $value
			){
				$postData_mas[] = $key.'='.urlencode($value);
			}
			$postData = implode(
				'&',
				$postData_mas
			);
		}
		
		curl_setopt(
			$ch,
			CURLOPT_POSTFIELDS,
			$postData
		);
	}
	
	//Если заданы какие-то HTTP заголовки
	if (is_array($headers)){
		curl_setopt(
			$ch,
			CURLOPT_HTTPHEADER,
			$headers
		);
	}
	
	//Если задан UserAgent
	if (isset($userAgent)){
		curl_setopt(
			$ch,
			CURLOPT_USERAGENT,
			$userAgent
		);
	}
	
	//Выполняем запрос
	$result = curl_exec($ch);
	
	//Если есть ошибки или ничего не получили
	if (
		curl_errno($ch) != 0 &&
		empty($result)
	){
		$result = false;
	}else if ($manualRedirect){
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
					'/Location:(.*?)\n/',
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
				$newUrl = $redirectUrl['scheme'].'://'.$redirectUrl['host'].$redirectUrl['path'].($redirectUrl['query'] ? '?'.$redirectUrl['query'] : '');
				
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
	
	return $result;
}
?>