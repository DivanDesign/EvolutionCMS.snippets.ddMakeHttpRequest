<?php
/**
 * ddMakeHttpRequest.php
 * @version 1.1.4 (2014-07-17)
 * 
 * @desc Осуществляет запрос по заданному URL.
 * 
 * @uses The library modx.ddTools 0.13.
 * 
 * @param $url {string} - Адрес, к которому обращаться. @required
 * @param $method {'get'; 'post'} - Тип запроса. Default: 'get'.
 * @param $post {separated string} - Переменные, которые нужно отправить. Формат: строка, разделённая '::' между парой ключ-значение и '||' между парами. Default: —.
 * @param $headers {separated string} - Заголовки, которые нужно отправить. Разделитель между строками — '||'. Default: —.
 * @param $userAgent {string} - Значение HTTP заголовка 'User-Agent: '. Default: —.
 * @param $timeout {integer} - Максимальное время выполнения запроса в секундах. Default: 60.
 * 
 * @copyright 2014, DivanDesign
 * http://www.DivanDesign.biz
 */

//Подключаем modx.ddTools
require_once $modx->getConfig('base_path').'assets/snippets/ddTools/modx.ddtools.class.php';

//Для обратной совместимости
extract(ddTools::verifyRenamedParams($params, array(
	'method' => 'metod',
	'userAgent' => 'uagent'
)));

if (isset($url)){
	if (!isset($post) || !is_array($post)){
		$post = isset($post) ? $post : false;
	}
	$method = ((isset($method) && $method == 'post') || is_array($post)) ? 'post' : 'get';
	$headers = isset($headers) ? explode('||', $headers) : false;
	$timeout = isset($timeout) && is_numeric($timeout) ? $timeout : 60;
	
	$manualRedirect = false;
	
	//Разбиваем адрес на компоненты
	$urlArray = parse_url($url);
	$urlArray['scheme'] = isset($urlArray['scheme'])? $urlArray['scheme']: 'http';
	$urlArray['path'] = isset($urlArray['path'])? $urlArray['path']: '';
	$urlArray['query'] = isset($urlArray['query'])? '?'.$urlArray['query']: '';
	
	//Инициализируем сеанс CURL
	$ch = curl_init($urlArray['scheme'].'://'.$urlArray['host'].$urlArray['path'].$urlArray['query']);
	
	//Выставление таймаута
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	
	//Если необходимо соединиться с https
	if ($urlArray['scheme'] === 'https'){
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	}
	
	//Устанавливаем порт, если задан
	if(isset($urlArray['port'])){
		curl_setopt($ch, CURLOPT_PORT, $urlArray['port']);
	}
	
	//Результат должен быть возвращен, а не выведен
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	//Не включаем полученные заголовки в результат
	
	if (ini_get('open_basedir') != '' || ini_get('safe_mode')){
		curl_setopt($ch, CURLOPT_HEADER, 1);
		$manualRedirect = true;
	}else{
		curl_setopt($ch, CURLOPT_HEADER, 0);
		//При установке этого параметра в ненулевое значение, при получении HTTP заголовка "Location: " будет происходить перенаправление на указанный этим заголовком URL (это действие выполняется рекурсивно, для каждого полученного заголовка "Location:").
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	}
	
	curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
	
	//Если есть переменные для отправки
	if ($method == 'post' && isset($post)){
		//Запрос будет методом POST типа application/x-www-form-urlencoded (используемый браузерами при отправке форм)
		curl_setopt($ch, CURLOPT_POST, 1);
		
		//Если пост передан строкой, то преобразовываем в массив
		if (!is_array($post)){$post = ddTools::explodeAssoc($post);}
		
		if (is_array($post)){
			$post_mas = Array();
			//Сформируем массив для отправки, предварительно перекодировав
			foreach ($post as $key => $value){
				$post_mas[] = $key.'='.urlencode($value);
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&', $post_mas));
		}
	}
	
	//Если заданы какие-то HTTP заголовки
	if (is_array($headers)){
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	}
	
	//Если задан UserAgent
	if (isset($userAgent)){
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
	}
	
	//Выполняем запрос
	$result = curl_exec($ch);
	
	//Если есть ошибки или ничего не получили
	if (curl_errno($ch) != 0 && empty($result)){
		$result = false;
	}else if ($manualRedirect){
		$redirectCount = 10;
		while (0 < $redirectCount--){
			//Получаем заголовки, контент и код ответа
			$resultHeader = substr($result, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
			$resultData = substr($result, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
			$resultResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			
			//Проверяем код на редирект
			if (intval($resultResponseCode / 100) == 3){
				//Ищем новый url в заголовках
				$matches = array();
				preg_match('/Location:(.*?)\n/', $resultHeader, $matches);
				$newUrlStr = '';
				if (count($matches)){
					$newUrlStr = array_pop($matches);
				}
				
				//Парсим url
				$redirectUrl = parse_url(trim($newUrlStr));
				if (!is_array($redirectUrl)){
					$redirectUrl = array();
				}
				
				//Собираем новый url
				$lastUrl = parse_url(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
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
				curl_setopt($ch, CURLOPT_URL, $newUrl);
				$result = curl_exec($ch);
				if (curl_errno($ch) != 0 && empty($result)){
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