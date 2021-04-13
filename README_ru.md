# (MODX)EvolutionCMS.snippets.ddMakeHttpRequest

Отправляет HTTP запрос к заданному URL.

Поддерживает редиректы даже если нативные редиректы CURL недоступны.


## Requires

* PHP >= 5.6
* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.biz/modx/ddtools) >= 0.48.2


## Документация


### Установка


#### 1. Элементы → Сниппеты: Создайте новый сниппет со следующими параметрами

1. Название сниппета: `ddMakeHttpRequest`.
2. Описание: `<b>2.3</b> Makes HTTP request to a given URL.`.
3. Категория: `Core`.
4. Анализировать DocBlock: `no`.
5. Код сниппета (php): Вставьте содержимое файла `ddMakeHttpRequest_snippet` из архива.


#### 2. Элементы → Управление файлами

1. Создайте новую папку `assets/snippets/ddMakeHttpRequest/`.
2. Извлеките содержимое архива в неё (кроме файла `ddMakeHttpRequest_snippet.php`).


### Описание параметров

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
		* `stringJsonObject` — в виде [JSON](https://en.wikipedia.org/wiki/JSON) object
		* `stringHjsonObject` — в виде [HJSON](https://hjson.github.io/)
		* `stringQueryFormated` — в виде [Query string](https://en.wikipedia.org/wiki/Query_string)
		* `string`
		* Также может быть задан, как нативный PHP объект или массив (например, для вызовов через `\DDTools\Snippet::runSnippet`).
			* `arrayAssociative`
			* `object`
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
		* `stringJsonArray` — в виде [JSON](https://en.wikipedia.org/wiki/JSON)
		* `stringHjsonArray` — в виде [HJSON](https://hjson.github.io/)
		* `stringQueryFormated` — в виде [Query string](https://en.wikipedia.org/wiki/Query_string)
		* Также может быть задан, как нативный PHP массив (например, для вызовов через `\DDTools\Snippet::runSnippet`).
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


### Примеры


#### Простой GET-запрос

```
[[ddMakeHttpRequest?
	&url=`http://www.example.com?name=John&surname=Doe`
]]
```


#### Простой POST-запрос

Передаваемые данные мождно задать в виде JSON:

```
[[ddMakeHttpRequest?
	&url=`http://www.example.com/`
	&postData=`{
		"name": "John",
		"surname": "Doe"
	}`
]]
```

Или в виде Query string:

```
[[ddMakeHttpRequest?
	&url=`http://www.example.com/`
	&postData=`name=John&surname=Doe`
]]
```


#### Запустить сниппет без DB и eval через `\DDTools\Snippet::runSnippet`

```php
\DDTools\Snippet::runSnippet([
	'name' => 'ddMakeHttpRequest',
	'params' => [
		'url' => 'https://www.example.com/',
		'postData' => [
			'name' => 'John',
			'surname' => 'Doe'
		],
		'headers' => [
			'Accept: application/vnd.api+json',
			'Content-Type: application/vnd.api+json'
		],
		'proxy' => 'socks5://user:password@11.22.33.44:5555'
	]
]);
```


## Ссылки

* [Home page](https://code.divandesign.ru/modx/ddmakehttprequest)
* [Telegram chat](https://t.me/dd_code)
* [Packagist](https://packagist.org/packages/dd/evolutioncms-snippets-ddmakehttprequest)


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />