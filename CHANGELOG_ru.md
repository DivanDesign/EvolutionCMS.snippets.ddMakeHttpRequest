# (MODX)EvolutionCMS.snippets.ddMakeHttpRequest changelog


## Версия 2.4.1 (2026-05-24)

* \+ `\ddMakeHttpRequest\Requester::parseUrlStrToObject` → Параметры → `$params->defaults->query`: Добавлено пропущенное значение по умолчанию для необязательного параметра.


## Версия 2.4 (2025-11-28)

* \+ Добавлено подробное логирование ошибок CURL и HTTP (4xx, 5xx), включая URL, HTTP код, код ошибки CURL и сообщение.
* \* Параметры:
	* \+ `isDebug`: Новый необязательный параметр. Позволяет логировать все запросы (включая успешные), не только ошибки. Полезен для отладки и мониторинга.
	* \* Все параметры запроса теперь сгруппированы в объект `requester` и несколько переименованы (обратная совместимость сохранена, но старое не рекомендуется использовать):
		* \* `postData` → `requester->data`.
		* \* `sendRawPostData` → `requester->isRawDataEnabled`.
		* \* `useCookie` → `requester->isCookieUsed`.
		* \* `url` → `requester->url`.
		* \* `method` → `requester->method`.
		* \* `headers` → `requester->headers`.
		* \* `userAgent` → `requester->userAgent`.
		* \* `timeout` → `requester->timeout`.
		* \* `proxy` → `requester->proxy`.
	* \+ `requester->method` → Допустимые значения → `put`, `patch`, `delete`: Новые поддерживаемые методы.
	* \+ `dataProcessor`: Новый необязательный параметр. Позволяет обрабатывать и валидировать данные ответа с настраиваемой проверкой успешности/неуспешности, см. README → Параметры, README → Примеры.
	* \+ `outputter`: Новый необязательный параметр. Позволяет возвращать данные ответа, метаданные, или оба; и преобразовывать результат в требуемый формат. Метаданные содержат следующие свойства:
		* \+ `'isSuccess'` — Был ли запрос успешным (CURL, HTTP код, и валидация данных).
		* \+ `'isCurlSuccess'` — Был ли успешен выполнение CURL.
		* \+ `'isHttpCodeSuccess'` — Был ли HTTP код не ошибкой (< 400 или >= 600).
		* \+ `'isDataValid'` — Были ли данные валидны.
		* \+ `'effectiveUrl'` — Эффективный URL.
		* \+ `'curlErrorCode'` — Код ошибки CURL.
		* \+ `'message'` — Текст сообщения. Содержит сообщение об ошибке CURL, если CURL не выполнился, или сообщение из данных ответа, если `dataProcessor->messagePropName` установлено.
		* \+ `'code'` — HTTP код.
* \* Внимание! Требуется PHP >= 7.4 (не тестировалось с более старыми версиями).
* \* Внимание! Требуется (MODX)EvolutionCMS.libraries.ddTools >= 0.63.


## Версия 2.3.2 (2022-05-25)

* \* Параметры → `postData`: Многомерные массивы и объекты PHP также поддерживаются.


## Версия 2.3.1 (2021-04-17)

* \* `\ddMakeHttpRequest\Snippet::run`: Исправлена проверка несуществующего элемента массива.


## Версия 2.3 (2021-04-13)

* \+ `\ddMakeHttpRequest\Snippet::run`: Улучшен «ручной» редирект.


## Версия 2.2 (2021-04-02)

* \* Внимание! Требуется PHP >= 5.6.
* \* Внимание! Требуется (MODX)EvolutionCMS.libraries.ddTools >= 0.48.2.
* \+ Параметры → `postData`: Также может быть задан, как HJSON или нативный PHP объект.
* \+ Параметры → `headers`: Также может быть задан, как HJSON.
* \+ Запустить сниппет без DB и eval можно через `\DDTools\Snippet::runSnippet` (см. примеры в README).
* \+ `\ddTypograph\Snippet`: Новый класс. Весь код сниппета перенесён туда.
* \+ README:
	* \+ Ссылки.
	* \+ Документация → Описание параметров → `postData`, `headers` → Допустимые значения: Текст улучшен.
	* \+ Улучшения стиля.
* \+ Composer.json:
	* \+ `homepage`.
	* \+ `support`.
	* \+ `authors`.


## Версия 2.1 (2020-02-15)

* \+ Добавлена возможность использовать cookie (см. параметр `useCookie`).


## Версия 2.0 (2019-09-23)

* \* **Внимание!** Обратная совместимость нарушена. Если вы хотите отправить сырой JSON в `postData`, выставьте параметр `sendRawPostData` в `1`.
* \+ Параметр `postData` может быть задан, как JSON-объект.
* \+ Параметр `headers` может быть задан, как JSON-массив.
* \+ Параметр `sendRawPostData`.


## Версия 1.0 (2011-06-16)

* \+ Первый релиз.


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />
<style>ul{list-style:none;}</style>