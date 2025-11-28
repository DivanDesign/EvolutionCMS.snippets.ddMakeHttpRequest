# (MODX)EvolutionCMS.snippets.ddMakeHttpRequest

Отправляет HTTP запрос к заданному URL.

* Поддерживает редиректы даже если нативные редиректы CURL недоступны.
* Автоматическая валидация ответов с настраиваемой проверкой успеха/неудачи.
* Гибкая конвертация данных и форматирование вывода.
* Все HTTP методы (GET, POST, PUT, PATCH, DELETE).
* Cookies, прокси, пользовательские заголовки.


## Requires

* PHP >= 7.4
* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.ru/modx/ddtools) >= 0.67


## Установка


### Используя [(MODX)EvolutionCMS.libraries.ddInstaller](https://github.com/DivanDesign/EvolutionCMS.libraries.ddInstaller)

Просто вызовите следующий код в своих исходинках или модуле [Console](https://github.com/vanchelo/MODX-Evolution-Ajax-Console):

```php
// Подключение (MODX)EvolutionCMS.libraries.ddInstaller
require_once(
	$modx->getConfig('base_path')
	. 'assets/libs/ddInstaller/require.php'
);

// Установка (MODX)EvolutionCMS.snippets.ddMakeHttpRequest
\DDInstaller::install([
	'url' => 'https://github.com/DivanDesign/EvolutionCMS.snippets.ddMakeHttpRequest',
]);
```

* Если `ddMakeHttpRequest` отсутствует на вашем сайте, `ddInstaller` просто установит его.
* Если `ddMakeHttpRequest` уже есть на вашем сайте, `ddInstaller` проверит его версию и обновит, если нужно. 


### Вручную


#### 1. Элементы → Сниппеты: Создайте новый сниппет со следующими параметрами

1. Название сниппета: `ddMakeHttpRequest`.
2. Описание: `<b>2.4</b> Отправляет HTTP запрос к заданному URL.`.
3. Категория: `Core`.
4. Анализировать DocBlock: `no`.
5. Код сниппета (php): Вставьте содержимое файла `ddMakeHttpRequest_snippet` из архива.


#### 2. Элементы → Управление файлами

1. Создайте новую папку `assets/snippets/ddMakeHttpRequest/`.
2. Извлеките содержимое архива в неё (кроме файла `ddMakeHttpRequest_snippet.php`).


## Описание параметров


### Общие параметры

* `isDebug`
	* Описание: Логировать все запросы в журнал событий (включая успешные), а не только ошибки. Полезно для отладки.
	* Допустимые значения:
		* `0`
		* `1`
	* Значение по умолчанию: `0`


### Параметры запроса

* `requester`
	* Описание: Параметры запроса.
	* Допустимые значения:
		* `stringJsonObject` — в виде [JSON](https://ru.wikipedia.org/wiki/JSON) объекта
		* `stringHjsonObject` — в виде [HJSON](https://hjson.github.io/)
		* `stringQueryFormatted` — в виде [Query string](https://ru.wikipedia.org/wiki/Query_string)
		* Также можно задать нативным PHP объектом или массивом (например, при вызове через `\DDTools\Snippet::runSnippet`):
			* `arrayAssociative`
			* `object`
	* Значение по умолчанию: —
	
* `requester->url`
	* Описание: Адрес, к которому обращаться.
	* Допустимые значения: `string`
	* **Обязателен**
	
* `requester->method`
	* Описание: Тип запроса.
	* Допустимые значения:
		* `'get'`
		* `'post'`
		* `'put'`
		* `'patch'`
		* `'delete'`
	* Значение по умолчанию: `'get'`
	
* `requester->data`
	* Описание: Данные, которые нужно отправить. Можно использовать с методами POST, PUT, PATCH, DELETE.
	* Допустимые значения:
		* `stringJsonObject` — в виде [JSON](https://en.wikipedia.org/wiki/JSON) object
		* `stringHjsonObject` — в виде [HJSON](https://hjson.github.io/)
		* `stringQueryFormatted` — в виде [Query string](https://en.wikipedia.org/wiki/Query_string)
		* `string`
		* Также может быть задан, как нативный PHP объект или массив (например, для вызовов через `\DDTools\Snippet::runSnippet`).
			* `arrayAssociative`
			* `object`
	* Значение по умолчанию: —
	
* `requester->isRawDataEnabled`
	* Описание: Отправить `data` в сыром виде. Например, если нужен JSON в payload запроса.
	* Допустимые значения:
		* `0`
		* `1`
	* Значение по умолчанию: `0`
	
* `requester->headers`
	* Описание: Заголовки, которые нужно отправить.
	* Допустимые значения:
		* `stringJsonArray` — в виде [JSON](https://en.wikipedia.org/wiki/JSON)
		* `stringHjsonArray` — в виде [HJSON](https://hjson.github.io/)
		* `stringQueryFormatted` — в виде [Query string](https://en.wikipedia.org/wiki/Query_string)
		* Также может быть задан, как нативный PHP массив (например, для вызовов через `\DDTools\Snippet::runSnippet`).
			* `array`
	* Значение по умолчанию: —
	
* `requester->userAgent`
	* Описание: Значение HTTP заголовка `User-Agent: `.
	* Допустимые значения: `string`
	* Значение по умолчанию: —
	
* `requester->timeout`
	* Описание: Максимальное время выполнения запроса в секундах.
	* Допустимые значения: `integer`
	* Значение по умолчанию: `60`
	
* `requester->proxy`
	* Описание: Прокси сервер в формате `[+protocol+]://[+user+]:[+password+]@[+ip+]:[+port+]`. Пример: `http://asan:gd324ukl@11.22.33.44:5555`, `socks5://asan:gd324ukl@11.22.33.44:5555`.
	* Допустимые значения: `string`
	* Значение по умолчанию: —
	
* `requester->isCookieUsed`
	* Описание: Использовать cookie? Используется файл `assets/cache/ddMakeHttpRequest_cookie.txt`.
	* Допустимые значения:
		* `0`
		* `1`
	* Значение по умолчанию: `0`


### Параметры обработки данных

* `dataProcessor`
	* Описание: Параметры обработки и валидации данных ответа для автоматической проверки успеха/неудачи.
	* Допустимые значения:
		* `stringJsonObject` — в виде [JSON](https://ru.wikipedia.org/wiki/JSON)
		* `stringHjsonObject` — в виде [HJSON](https://hjson.github.io/)
		* `stringQueryFormatted` — в виде [Query string](https://en.wikipedia.org/wiki/Query_string)
		* Также может быть задан, как нативный PHP объект или массив (например, при вызове через `\DDTools\Snippet::runSnippet`):
			* `arrayAssociative`
			* `object`
	* Значение по умолчанию: — (см. ниже)
	
* `dataProcessor->checkValue`
	* Описание: Значение, которое считается успехом/неудачей (зависит от `dataProcessor->isCheckForSuccess`).
	* Допустимые значения: `mixed` (строка, число, логическое значение и т. д.)
	* Значение по умолчанию: `''` (пустая строка)
	
* `dataProcessor->isCheckForSuccess`
	* Описание: Проверять на успех или неудачу.
	* Допустимые значения:
		* `true` — проверка на успех (например, ответ: `{"success": true}`)
		* `false` — проверка на неудачу (например, ответ: `{"error": true}`)
	* Значение по умолчанию: `false`
	
* `dataProcessor->checkPropName`
	* Описание: Имя свойства в ответе для проверки статуса успеха/неудачи.
		* Используйте только если ответ является объектом.
		* Можно также использовать `.` для получения вложенных свойств. Примеры:
			* `error`, `ok`, `success`, `status` — получить свойство первого уровня
			* `sms.status` — получить свойство второго уровня
	* Допустимые значения:
		* `null` — проверять весь ответ целиком (обычно, если ответ не является объектом)
		* `string` — имя свойства для проверки (если ответ является объектом)
	* Значение по умолчанию: `null`
	
* `dataProcessor->messagePropName`
	* Описание: Имя свойства в ответе, которое содержит текст сообщения (об успехе или об ошибке).
		* Используйте только если ответ является объектом.
		* Можно также использовать `.` для получения вложенных свойств. Примеры:
			* `description`, `title`, `message` — получить свойство первого уровня
			* `error.message` — получить свойство второго уровня
	* Допустимые значения:
		* `null` — не извлекать сообщение
		* `string` — имя свойства с сообщением
	* Значение по умолчанию: `null`
	
* `dataProcessor->convertTo`
	* Описание: Конвертировать данные ответа в указанный тип.
		* **Важно:** Конвертируется только часть `data` ответа. Если необходимо конвертировать весь результат сниппета (который может быть `data`, `meta` или `metaData`), используйте `outputter->convertTo`.
		* Если параметр задан, данные всегда возвращаются в указанном формате, независимо от статуса успешности/неудачи и даже если данные пришли невалидные (вам не нужно делать лишних проверок у себя).
		* Значения регистронезависимы (следующие значения равны: `stringjsonauto`, `stringJsonAuto`, `STRINGJSONAUTO` и т. п.).
	* Допустимые значения:
		* `''` (пустое значение) — не конвертировать (значение по умолчанию)
		* Сниппет умеет конвертировать данные в примитивные типы:
			* `'string'`
			* `'integer'`/`'int'`
			* `'float'`
			* `'boolean'`/`'bool'`
		* Сниппет умеет конвертировать данные в строку:
			* `'stringJsonAuto'` — автоматиески будет выбран `stringJsonObject` или `stringJsonArray`, в зависимости от типа данных
			* `'stringJsonObject'`
			* `'stringJsonArray'`
			* `'stringQueryFormatted'` — [Query string](https://ru.wikipedia.org/wiki/Query_string)
		* Сниппет также умеет конвертировать данные в нативный PHP объект или массив (удобно при вызове через `\DDTools\Snippet`):
			* `'objectAuto'` — `stdClass` или `array` в зависимости от типа данных
			* `'objectStdClass'` — `stdClass`
			* `'objectArray'` — `array`
	* Значение по умолчанию: — (без конвертации)


### Параметры вывода

* `outputter`
	* Описание: Параметры вывода.
	* Допустимые значения:
		* `stringJsonObject` — как [JSON](https://ru.wikipedia.org/wiki/JSON) объект
		* `stringHjsonObject` — как [HJSON](https://hjson.github.io/)
		* `stringQueryFormatted` — как [Query string](https://ru.wikipedia.org/wiki/Query_string)
		* Также можно задать нативным PHP объектом или массивом (например, при вызове через `\DDTools\Snippet::runSnippet`):
			* `arrayAssociative`
			* `object`
	* Значение по умолчанию:
		```hjson
		{
			type: data
			convertTo: ""
		}
		```
	
* `outputter->type`
	* Описание: Что возвращать в качестве результата сниппета.
		* Значения регистронезависимы (следующие значения равны: `metaData`, `metadata`, `METADATA` и т. п.).
	* Допустимые значения:
		* `'data'` — только тело ответа
		* `'meta'` — только метаданные, доступны следующие свойства:
			* `'isSuccess'` — Запрос был успешным (CURL, HTTP код и валидация данных)
			* `'isCurlSuccess'` — Выполнение CURL было успешным
			* `'isHttpCodeSuccess'` — HTTP код ответа не является ошибкой (< 400 или >= 600)
			* `'isDataValid'` — Данные ответа валидны
			* `'effectiveUrl'` — Финальный URL
			* `'curlErrorCode'` — Код ошибки CURL
			* `'message'` — Текст сообщения. Содержит сообщение об ошибке CURL, если CURL failed, или сообщение из данных ответа, если указан `dataProcessor->messagePropName`
			* `'code'` — HTTP код
		* `'metaData'` — и тело ответа, и метаданные в виде JSON-объекта со свойствами `data` и `meta`
	* Значение по умолчанию: `'data'`
	
* `outputter->convertTo`
	* Описание: Конвертировать весь результат сниппета в указанный формат.
		* **Важно:** Конвертируется полный результат сниппета (который может быть `data`, `meta` или `metaData` в зависимости от `outputter->type`). Если необходимо конвертировать только данные ответа `data`, используйте `dataProcessor->convertTo`.
		* Значения регистронезависимы (следующие значения равны: `stringjsonauto`, `stringJsonAuto`, `STRINGJSONAUTO` и т. п.).
	* Допустимые значения:
		* `''` (пустое значение) — возвращать как есть, без конвертации (значение по умолчанию)
		* Сниппет умеет возвращать объект в виде строки:
			* `'stringJsonAuto'` — автоматиески будет выбран `stringJsonObject` или `stringJsonArray`, в зависимости от результата
			* `'stringJsonObject'`
			* `'stringJsonArray'`
			* `'stringQueryFormatted'` — [Query string](https://ru.wikipedia.org/wiki/Query_string)
		* Сниппет также умеет возвращать объект в виде нативного PHP объекта или массива (удобно при вызове через `\DDTools\Snippet`):
			* `'objectAuto'` — `stdClass` или `array` в зависимости от результата
			* `'objectStdClass'` — `stdClass`
			* `'objectArray'` — `array`
	* Значение по умолчанию: — (без конвертации)


## Примеры


### Простой GET-запрос

```
[[ddMakeHttpRequest?
	&requester=`{
		url: http://www.example.com?name=John&surname=Doe
	}`
]]
```


### Простой POST-запрос

Передаваемые данные мождно задать в виде HJSON:

```
[[ddMakeHttpRequest?
	&requester=`{
		url: http://www.example.com/
		data: {
			name: John
			surname: Doe
		}
	}`
]]
```

Или в виде Query string:

```
[[ddMakeHttpRequest?
	&requester=`{
		url: http://www.example.com/
		data: name=John&surname=Doe
	}`
]]
```


### Запустить сниппет без DB и eval через `\DDTools\Snippet::runSnippet`

```php
\DDTools\Snippet::runSnippet([
	'name' => 'ddMakeHttpRequest',
	'params' => [
		'requester' => [
			'url' => 'https://www.example.com/',
			'data' => [
				'name' => 'John',
				'surname' => 'Doe',
			],
			'headers' => [
				'Accept: application/vnd.api+json',
				'Content-Type: application/vnd.api+json',
			],
			'proxy' => 'socks5://user:password@11.22.33.44:5555',
		],
	],
]);
```


### Получить только метаданные ответа

```php
$responseMeta = \DDTools\Snippet::runSnippet([
	'name' => 'ddMakeHttpRequest',
	'params' => [
		'requester' => [
			'url' => 'https://www.example.com/',
		],
		'outputter' => [
			'type' => 'meta',
		],
	],
]);

// Проверяем, успешен ли запрос
if ($responseMeta->isSuccess){
	// Успех
}else{
	// Ошибка
	error_log('HTTP запрос не удался: ' . $responseMeta->message);
}
```


### Получить и данные, и метаданные

```php
$result = \DDTools\Snippet::runSnippet([
	'name' => 'ddMakeHttpRequest',
	'params' => [
		'requester' => [
			'url' => 'https://api.example.com/users',
		],
		'outputter' => [
			'type' => 'metadata',
		],
	],
]);

if ($result->meta->isSuccess){
	// Обрабатываем данные ответа
	$users = json_decode($result->data);
}
```


### Конвертировать результат в JSON

```php
// Получаем результат в виде JSON-строки
$jsonString = \DDTools\Snippet::runSnippet([
	'name' => 'ddMakeHttpRequest',
	'params' => [
		'requester' => [
			'url' => 'https://api.example.com/users',
		],
		'outputter' => [
			'type' => 'metadata',
			'convertTo' => 'stringJsonAuto',
		],
	],
]);

// Теперь можно использовать в JavaScript или сохранить в файл
```


### Конвертировать результат в массив

```php
// Получаем метаданные ответа в виде PHP-массива
$metaArray = \DDTools\Snippet::runSnippet([
	'name' => 'ddMakeHttpRequest',
	'params' => [
		'requester' => [
			'url' => 'https://api.example.com/status',
		],
		'outputter' => [
			'type' => 'meta',
			'convertTo' => 'objectArray',
		],
	],
]);

// Обращаемся как к массиву
if ($metaArray['isSuccess']){
	echo 'HTTP код: ' . $metaArray['code'];
}
```


### Валидация данных ответа с помощью `dataProcessor`


#### Валидация ответа с простым значением (например, строка или число)

API возвращает простое значение и если оно равно `OK` — это успех, иначе — неудача.

```php
$result = \DDTools\Snippet::runSnippet([
	'name' => 'ddMakeHttpRequest',
	'params' => [
		'requester' => [
			'url' => 'https://api.example.com/check',
		],
		'dataProcessor' => [
			// Ответ содержит статус успешности
			'isCheckForSuccess' => true,
			// Если значение равно `OK` — это успех, иначе — неудача
			'checkValue' => 'OK',
		],
		'outputter' => [
			'type' => 'meta',
		],
	],
]);

// Теперь isSuccess будет true только если:
// * CURL-запрос успешен
// * HTTP-код 2xx
// * Ответ равен `OK`
if ($result->isSuccess){
	// Всё хорошо!
}
```


#### Валидация ответа как объекта и проверка на успех

Ответ API является объектом и содержит свойство `success` со значением `true` (например, `{"success": true}`) — это успех, иначе — неудача.

```php
$result = \DDTools\Snippet::runSnippet([
	'name' => 'ddMakeHttpRequest',
	'params' => [
		'requester' => [
			'url' => 'https://api.example.com/send',
			'method' => 'post',
			'data' => [
				'message' => 'Привет',
			],
		],
		'dataProcessor' => [
			// Ответ содержит статус успешности
			'isCheckForSuccess' => true,
			// Используем свойство `success` для проверки статуса успешности
			'checkPropName' => 'success',
			// Значение для проверки статуса успешности
			'checkValue' => true,
		],
		'outputter' => [
			'type' => 'metaData',
		],
	],
]);

// Теперь meta->isSuccess будет true только если:
// * CURL-запрос успешен
// * HTTP-код 2xx
// * Ответ является объектом и содержит свойство `success` со значением `true` (например, `{"success": true}`)
if ($result->meta->isSuccess){
	// Всё хорошо!
}else{
	// Что-то пошло не так
}
```


#### Валидация ответа как объекта, проверка на неудачу и извлечение сообщения об ошибке

Ответ API является объектом и содержит свойство `status` со значением `fail` (например, `{"status": "fail"}`) — это неудача, иначе — успех.

```php
$result = \DDTools\Snippet::runSnippet([
	'name' => 'ddMakeHttpRequest',
	'params' => [
		'requester' => [
			'url' => 'https://api.example.com/send-sms',
			'method' => 'post',
			'data' => [
				'phone' => '+1234567890',
			],
		],
		'dataProcessor' => [
			// Ответ содержит данные о статусе неудачи (например, `{"status": "fail"}`)
			'isCheckForSuccess' => false,
			// Используем свойство `status` для проверки статуса неудачи
			'checkPropName' => 'status',
			// Значение статуса неудачи
			'checkValue' => 'fail',
			// Извлекаем сообщение из ответа
			'messagePropName' => 'message',
		],
		'outputter' => [
			'type' => 'metaData',
		],
	],
]);

if (!$result->meta->isSuccess){
	// Логируем ошибку с сообщением от API
	error_log('Ошибка API: ' . ($result->meta->message ?? 'Неизвестная ошибка'));
}
```


### Конвертация данных и результата одновременно с помощью `dataProcessor->convertTo` и `outputter->convertTo`

API возвращает JSON-строку `'{"userId": "123", "userName": "John"}'`. Нам нужно:
1. Конвертировать данные ответа из JSON-строки в PHP-объект (используя `dataProcessor->convertTo`)
2. Вернуть и данные, и мета (используя `outputter->type = 'metaData'`)
3. Конвертировать весь результат в JSON для фронтенда (используя `outputter->convertTo`)

```php
$jsonResult = \DDTools\Snippet::runSnippet([
	'name' => 'ddMakeHttpRequest',
	'params' => [
		'requester' => [
			'url' => 'https://api.example.com/user',
		],
		'dataProcessor' => [
			// Конвертируем JSON-строку в PHP-объект перед валидацией
			'convertTo' => 'objectStdClass',
		],
		'outputter' => [
			// Возвращаем и данные, и мета
			'type' => 'metaData',
			// Конвертируем весь результат в JSON
			'convertTo' => 'stringJsonObject',
		],
	],
]);

// Теперь $jsonResult это JSON-строка вида:
// {"data": {"userId": "123", "userName": "John"}, "meta": {"isSuccess": true, ...}}
// Идеально для AJAX-ответов или сохранения в файл
```


## Ссылки

* [Home page](https://code.divandesign.ru/modx/ddmakehttprequest)
* [Telegram chat](https://t.me/dd_code)
* [Packagist](https://packagist.org/packages/dd/evolutioncms-snippets-ddmakehttprequest)
* [GitHub](https://github.com/DivanDesign/EvolutionCMS.snippets.ddMakeHttpRequest)


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />