<?php
/**
 * ddMakeHttpRequest.php
 * @version 1.0 (2011-06-16)
 * 
 * @desc Осуществляет POST запрос по заданному URL.
 * 
 * @param $url - Адрес, к которому обращаться.
 * @param $post - Переменные, которые нужно отправить. Пары ключ-значение разделённые '||', разделитель между ключом и значением — '::'.
 * @param $ssl - Соединяемся ли с https.
 * @param $headers - Заголовки, которые нужно отправить. Разделитель между строками — '||'.
 * @param $uagent - Значение HTTP заголовка "User-Agent: "
 * 
 * @copyright 2011, DivanDesign
 * http://www.DivanDesign.biz
 */

if (isset($url)){
	$post = isset($post) ? explode('||', $post) : false;
	$ssl = (isset($ssl) && ($ssl == '1')) ? true : false;
	$headers = isset($headers) ? explode('||', $headers) : false;

	//Инициализируем сеанс CURL
	$ch = curl_init($url);

	//Если необходимо соединиться с https
	if ($ssl){
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	}

	//Результат должен быть возвращен, а не выведен
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	//Запрос будет методом POST типа application/x-www-form-urlencoded (используемый браузерами при отправке форм)
	curl_setopt($ch, CURLOPT_POST, 1);
	//Не включаем полученные заголовки в результат
	curl_setopt($ch, CURLOPT_HEADER, 0);
	//При установке этого параметра в ненулевое значение, при получении HTTP заголовка "Location: " будет происходить перенаправление на указанный этим заголовком URL (это действие выполняется рекурсивно, для каждого полученного заголовка "Location:").
	// 		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	//Если есть переменные для отправки
	if (is_array($post)){
		$_post = Array();
		//Сформируем массив для отправки, предварительно перекодировав
		foreach ($post as $value){
			$value = explode('::', $value);
			$_post[] = $value[0].'='.urlencode($value[1]);
		}
		curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&', $_post));
	}

	//Если заданы какие-то HTTP заголовки
	if (is_array($headers)){curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);}

	//Если задан UserAgent
	if (isset($uagent)){curl_setopt($ch, CURLOPT_USERAGENT, $uagent);}

	//Выполняем запрос
	$result = curl_exec($ch);

	//Если есть ошибки или ничего не получили
	if (curl_errno($ch) != 0 && empty($result)){$result = false;}

	//Закрываем сеанс CURL
	curl_close($ch);

	return $result;
}
?>