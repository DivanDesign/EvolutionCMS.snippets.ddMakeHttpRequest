<?php
/**
 * ddMakeHttpRequest.php
 * @version 1.1 (2011-09-28)
 * 
 * @desc Осуществляет запрос по заданному URL.
 * 
 * @param $url - Адрес, к которому обращаться.
 * @param $metod - Тип запроса (get или post, get по умолчанию).
 * @param $post - Переменные, которые нужно отправить. Пары ключ-значение разделённые '||', разделитель между ключом и значением — '::'.
 * @param $ssl - Соединяемся ли с https.
 * @param $headers - Заголовки, которые нужно отправить. Разделитель между строками — '||'.
 * @param $uagent - Значение HTTP заголовка "User-Agent: "
 * 
 * @copyright 2011, DivanDesign
 * http://www.DivanDesign.biz
 */

if (isset($url)){
	if (!is_array($post)) $post = isset($post) ? $post : false;
	$metod = ((isset($metod) && $mettod == 'post') || is_array($post)) ? 'post' : 'get';
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
	//Не включаем полученные заголовки в результат
	curl_setopt($ch, CURLOPT_HEADER, 0);
	//При установке этого параметра в ненулевое значение, при получении HTTP заголовка "Location: " будет происходить перенаправление
	//на указанный этим заголовком URL (это действие выполняется рекурсивно, для каждого полученного заголовка "Location:").
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	//Если есть переменные для отправки
	if ($metod == 'post' && isset($post)){
		//Запрос будет методом POST типа application/x-www-form-urlencoded (используемый браузерами при отправке форм)
		curl_setopt($ch, CURLOPT_POST, 1);

		//Если пост передан строкой, то преобразовываем в массив
		if (!is_array($post)){
			$post = explode('||', $post);
			$temp = $post;
			$post = array();
			foreach ($temp as $value){
				$value = explode('::', $value);
				$post[$value[0]] = $value[1];
			}
		}

		if (is_array($post)){
			$post_mas = Array();
			//Сформируем массив для отправки, предварительно перекодировав
			foreach ($post as $key=>$value){
				$post_mas[] = $key.'='.urlencode($value);
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&', $post_mas));
		}
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