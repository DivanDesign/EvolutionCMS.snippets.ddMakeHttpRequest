# (MODX)EvolutionCMS.snippets.ddMakeHttpRequest

Отправляет HTTP запрос к заданному URL.


## # Requires
* PHP >= 5.4
* [(MODX)EvolutionCMS.libraries.ddTools](http://code.divandesign.biz/modx/ddtools) >= 0.23


## # Документация


### ## Установка
Элементы → Сниппеты: Создать новый сниппет со следующими параметрами:

1. Название сниппета: `ddMakeHttpRequest`.
2. Описание: `<b>2.1</b> Makes HTTP request to a given URL.`.
3. Категория: `Core`.
4. Анализировать DocBlock: `no`.
5. Код сниппета (php): Вставьте содержимое файла `ddMakeHttpRequest_snippet` из архива.


### ## Описание параметров

* `url`
	* Описание: Адрес, к которому обращаться.
	* Допустимые значения: `string`
	* **Обязателен**
	
* `method`
	* Описание: Тип запроса.
	* Допустимые значения:
		* `'get'`
		* `'post'`
	* Значение по умолчанию: `'get'`
	
* `postData`
	* Описание: Данные, которые нужно отправить.
	* Допустимые значения:
		* `string_json` — в виде [JSON](https://en.wikipedia.org/wiki/JSON) object
		* `string_queryFormated` — в виде [Query string](https://en.wikipedia.org/wiki/Query_string)
		* `array_associative`
		* `string`
	* Значение по умолчанию: —
	
* `sendRawPostData`
	* Описание: Отправить `postData` в сыром виде. Например, если нужен JSON in request payload.
	* Допустимые значения:
		* `0`
		* `1`
	* Значение по умолчанию: `0`
	
* `headers`
	* Описание: Заголовки, которые нужно отправить.
	* Допустимые значения:
		* `string_json` — в виде [JSON](https://en.wikipedia.org/wiki/JSON) object
		* `string_queryFormated` — в виде [Query string](https://en.wikipedia.org/wiki/Query_string)
		* `array`
	* Значение по умолчанию: —
	
* `userAgent`
	* Описание: Значение HTTP заголовка `User-Agent: `.
	* Допустимые значения: `string`
	* Значение по умолчанию: —
	
* `timeout`
	* Описание: Максимальное время выполнения запроса в секундах.
	* Допустимые значения: `integer`
	* Значение по умолчанию: `60`
	
* `proxy`
	* Описание: Прокси сервер в формате `[+protocol+]://[+user+]:[+password+]@[+ip+]:[+port+]`. Пример: `http://asan:gd324ukl@11.22.33.44:5555`, `socks5://asan:gd324ukl@11.22.33.44:5555`.
	* Допустимые значения: `string`
	* Значение по умолчанию: —
	
* `useCookie`
	* Desctription: Использовать cookie? Используется файл `assets/cache/ddMakeHttpRequest_cookie.txt`.
	* Valid values:
		* `0`
		* `1`
	* Default value: `0`


### ## Примеры


#### ### Простой GET-запрос
```
[[ddMakeHttpRequest? &url=`http://www.example.com?name=John&surname=Doe`]]
```


#### ### Простой POST-запрос
Передаваемые данные мождно задать в виде JSON
```
[[ddMakeHttpRequest?
	&url=`http://www.example.com/`
	&postData=`{
		"name": "John",
		"surname": "Doe"
	}`
]]
```
Или в виде Query string
```
[[ddMakeHttpRequest?
	&url=`http://www.example.com/`
	&postData=`name=John&surname=Doe`
]]
```


#### ### CMS API
```php
$requestResult = $modx->runSnippet(
	'ddMakeHttpRequest',
	[
		'url' => 'https://www.example.com/',
		'postData' => [
			'name' => 'John',
			'surname' => 'Doe'
		],
		'headers' => [
			'Accept: application/vnd.api+json',
			'Content-Type: application/vnd.api+json'
		],
		'proxy' => 'socks5://asan:gd324ukl@11.22.33.44:5555'
	]
);
```


## Ссылки

* [Home page](https://code.divandesign.ru/modx/ddmakehttprequest)
* [Telegram chat](https://t.me/dd_code)
* [Packagist](https://packagist.org/packages/dd/evolutioncms-snippets-ddmakehttprequest)