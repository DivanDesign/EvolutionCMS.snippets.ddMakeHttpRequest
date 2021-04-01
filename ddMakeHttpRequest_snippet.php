<?php
/**
 * ddMakeHttpRequest
 * @version 2.1 (2020-02-15)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.biz/modx/ddmakehttprequest
 * 
 * @copyright 2011–2020 DivanDesign {@link http://www.DivanDesign.biz }
 */

//Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddTools/modx.ddtools.class.php'
);


//Renaming params with backward compatibility
$params = \ddTools::verifyRenamedParams(
	'params' => $params,
	'compliance' => [
		'method' => 'metod',
		'userAgent' => 'uagent',
		'postData' => 'post',
		'useCookie' => 'cookie'
	],
	'returnCorrectedOnly' => false
);

//Defaults
$params = \DDTools\ObjectTools::extend([
	'objects' => [
		(object) [
			//Required
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
		$params
	]
]);

$params->method = strtolower($params->method);

if (!empty($params->postData)){
	$params->method = 'post';
	
	if (
		//Если отправляемые данные переданы строкой
		!is_array($params->postData) &&
		//И обрабатывать её можно
		!$params->sendRawPostData
	){
		$params->postData = \ddTools::encodedStringToArray($params->postData);
	}
}

if (!is_array($params->headers)){
	$params->headers = \ddTools::encodedStringToArray($params->headers);
}

$params->timeout = intval($params->timeout);

$params->useCookie = boolval($params->useCookie);


//The snippet must return an empty string even if result is absent
$snippetResult = '';

if (!empty($params->url)){
	$manualRedirect = false;
	
	//Разбиваем адрес на компоненты
	$urlArray = parse_url($params->url);
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
		$params->timeout
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
		$params->method == 'post' &&
		!empty($params->postData)
	){
		//Запрос будет методом POST типа application/x-www-form-urlencoded (используемый браузерами при отправке форм)
		curl_setopt(
			$ch,
			CURLOPT_POST,
			1
		);
		
		//Если он массив — делаем query string
		if (is_array($params->postData)){
			$postData_mas = [];
			//Сформируем массив для отправки, предварительно перекодировав
			foreach (
				$params->postData as
				$key =>
				$value
			){
				$postData_mas[] =
					$key .
					'=' .
					urlencode($value)
				;
			}
			$params->postData = implode(
				'&',
				$postData_mas
			);
		}
		
		curl_setopt(
			$ch,
			CURLOPT_POSTFIELDS,
			$params->postData
		);
	}
	
	//Если заданы какие-то HTTP заголовки
	if (is_array($params->headers)){
		curl_setopt(
			$ch,
			CURLOPT_HTTPHEADER,
			$params->headers
		);
	}
	
	//Если задан UserAgent
	if (!empty($params->userAgent)){
		curl_setopt(
			$ch,
			CURLOPT_USERAGENT,
			$params->userAgent
		);
	}
	
	//Если задано использование печенек
	if ($params->useCookie){
		curl_setopt(
			$ch,
			CURLOPT_COOKIEFILE,
			(
				$modx->getConfig('base_path') .
				'assets/cache/ddMakeHttpRequest_cookie.txt'
			)
		);
		curl_setopt(
			$ch,
			CURLOPT_COOKIEJAR,
			(
				$modx->getConfig('base_path') .
				'assets/cache/ddMakeHttpRequest_cookie.txt'
			)
		);
	}
	
	//Если задан прокси-сервер
	if(!empty($params->proxy)){
		curl_setopt(
			$ch,
			CURLOPT_PROXY,
			$params->proxy
		);
	}
	
	//Выполняем запрос
	$snippetResult = curl_exec($ch);
	
	//Если есть ошибки или ничего не получили
	if (
		curl_errno($ch) != 0 &&
		empty($snippetResult)
	){
		$snippetResult = '';
	}elseif ($manualRedirect){
		$redirectCount = 10;
		while (0 < $redirectCount--){
			//Получаем заголовки, контент и код ответа
			$resultHeader = substr(
				$snippetResult,
				0,
				curl_getinfo(
					$ch,
					CURLINFO_HEADER_SIZE
				)
			);
			$resultData = substr(
				$snippetResult,
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
				$newUrl =
					$redirectUrl['scheme'] . '://' .
					$redirectUrl['host'] .
					$redirectUrl['path'] .
					(
						$redirectUrl['query'] ?
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
				$snippetResult = curl_exec($ch);
				if (
					curl_errno($ch) != 0 &&
					empty($snippetResult)
				){
					$snippetResult = false;
					
					break;
				}
			}else{
				$snippetResult = $resultData;
				
				break;
			}
		}
	}
	
	//Закрываем сеанс CURL
	curl_close($ch);
}

return $snippetResult;
?>