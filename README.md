# (MODX)EvolutionCMS.snippets.ddMakeHttpRequest

Makes HTTP request to a given URL.

* Supports redirects even if native CURL redirects is unavailable.
* Automatic response validation with configurable success/failure checking.
* Flexible data conversion and output formatting.
* All HTTP methods (GET, POST, PUT, PATCH, DELETE).
* Cookies, proxy, custom headers.


## Requires

* PHP >= 7.4
* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.ru/modx/ddtools) >= 0.67


## Installation


### Using [(MODX)EvolutionCMS.libraries.ddInstaller](https://github.com/DivanDesign/EvolutionCMS.libraries.ddInstaller)

Just run the following PHP code in your sources or [Console](https://github.com/vanchelo/MODX-Evolution-Ajax-Console):

```php
// Include (MODX)EvolutionCMS.libraries.ddInstaller
require_once(
	$modx->getConfig('base_path')
	. 'assets/libs/ddInstaller/require.php'
);

// Install (MODX)EvolutionCMS.snippets.ddMakeHttpRequest
\DDInstaller::install([
	'url' => 'https://github.com/DivanDesign/EvolutionCMS.snippets.ddMakeHttpRequest',
]);
```

* If `ddMakeHttpRequest` is not exist on your site, `ddInstaller` will just install it.
* If `ddMakeHttpRequest` is already exist on your site, `ddInstaller` will check it version and update it if needed.


### Manually


#### 1. Elements → Snippets: Create a new snippet with the following data

1. Snippet name: `ddMakeHttpRequest`.
2. Description: `<b>2.4</b> Makes HTTP request to a given URL.`.
3. Category: `Core`.
4. Parse DocBlock: `no`.
5. Snippet code (php): Insert content of the `ddMakeHttpRequest_snippet` file from the archive.


#### 2. Elements → Manage Files

1. Create a new folder `assets/snippets/ddMakeHttpRequest/`.
2. Extract the archive to the folder (except `ddMakeHttpRequest_snippet.php`).


## Parameters description


### General parameters

* `isDebug`
	* Description: Log all requests to event log (including successful ones), not only errors. Useful for debugging.
	* Valid values:
		* `0`
		* `1`
	* Default value: `0`


### Request parameters

* `requester`
	* Description: Request parameters.
	* Valid values:
		* `stringJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON) object
		* `stringHjsonObject` — as [HJSON](https://hjson.github.io/)
		* `stringQueryFormatted` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
		* It can also be set as a native PHP object or array (e. g. for calls through `\DDTools\Snippet::runSnippet`):
			* `arrayAssociative`
			* `object`
	* Default value: —
	
* `requester->url`
	* Description: The URL to fetch.
	* Valid values: `string`
	* **Required**
	
* `requester->method`
	* Description: Request type.
	* Valid values:
		* `'get'`
		* `'post'`
		* `'put'`
		* `'patch'`
		* `'delete'`
	* Default value: `'get'`
	
* `requester->data`
	* Description: The full data to send in request body. Can be used with POST, PUT, PATCH, DELETE methods.
	* Valid values:
		* `stringJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON) object
		* `stringHjsonObject` — as [HJSON](https://hjson.github.io/)
		* `stringQueryFormatted` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
		* `string`
		* It can also be set as a native PHP object or array (e. g. for calls through `\DDTools\Snippet::runSnippet`):
			* `arrayAssociative`
			* `object`
	* Default value: —
	
* `requester->isRawDataEnabled`
	* Description: Send raw `data`. E. g. if you need JSON in request payload.
	* Valid values:
		* `0`
		* `1`
	* Default value: `0`
	
* `requester->headers`
	* Description: An array of HTTP header fields to set.
	* Valid values:
		* `stringJsonArray` — as [JSON](https://en.wikipedia.org/wiki/JSON)
		* `stringHjsonArray` — as [HJSON](https://hjson.github.io/)
		* `stringQueryFormatted` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
		* It can also be set as a native PHP array (e. g. for calls through `\DDTools\Snippet::runSnippet`):
			* `array`
	* Default value: —
	
* `requester->userAgent`
	* Description: The contents of the `User-Agent: ` header to be used in a HTTP request.
	* Valid values: `string`
	* Default value: —
	
* `requester->timeout`
	* Description: The maximum number of seconds for execute request.
	* Valid values: `integer`
	* Default value: `60`
	
* `requester->proxy`
	* Description: Proxy server in format `[+protocol+]://[+user+]:[+password+]@[+ip+]:[+port+]`. E. g. `http://user:password@11.22.33.44:5555` or `socks5://user:password@11.22.33.44:5555`.
	* Valid values: `string`
	* Default value: —
	
* `requester->isCookieUsed`
	* Description: Enable cookies. The `assets/cache/ddMakeHttpRequest_cookie.txt` file is used.
	* Valid values:
		* `0`
		* `1`
	* Default value: `0`


### Data processing parameters

* `dataProcessor`
	* Description: Response data processing and validation parameters for automatic success/failure checking.
	* Valid values:
		* `stringJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON) object
		* `stringHjsonObject` — as [HJSON](https://hjson.github.io/)
		* `stringQueryFormatted` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
		* It can also be set as a native PHP object or array (e. g. for calls through `\DDTools\Snippet::runSnippet`):
			* `arrayAssociative`
			* `object`
	* Default value: — (see below)
	
* `dataProcessor->checkValue`
	* Description: Value considered as success/failure (depends on `dataProcessor->isCheckForSuccess`).
	* Valid values: `mixed` (string, number, boolean, etc.)
	* Default value: `''` (empty string)
	
* `dataProcessor->isCheckForSuccess`
	* Description: Whether to check for success or failure.
	* Valid values:
		* `true` — check for success (e. g. response: `{"success": true}`)
		* `false` — check for failure (e. g. response: `{"error": true}`)
	* Default value: `false`
	
* `dataProcessor->checkPropName`
	* Description: Name of the response property to check for success/failure status.
		* Use only if the response is an object.
		* You can also use `.` to get nested properties. Examples:
			* `error`, `ok`, `success`, `status` — get first-level property
			* `sms.status` — get second-level property
	* Valid values:
		* `null` — check whole response data (usually if response is not an object)
		* `string` — property name for checking (if response is object)
	* Default value: `null`
	
* `dataProcessor->messagePropName`
	* Description: Name of the response property that contains message text (success or error message).
		* Use only if the response is an object.
		* You can also use `.` to get nested properties. Examples:
			* `description`, `title`, `message` — get first-level property
			* `error.message` — get second-level property
	* Valid values:
		* `null` — do not extract message
		* `string` — property name with message
	* Default value: `null`
	
* `dataProcessor->convertTo`
	* Description: Convert response data to specified type.
		* **Important:** This converts only the `data` part of the response. If you need to convert the whole snippet result (which can be `data`, `meta`, or `metaData`), use `outputter->convertTo` instead.
		* If parameter is set, data is always returned in specified format, regardless of success/failure status and even if data is invalid (you don't need to do extra checks yourself).
		* Values are case insensitive (the following values are equal: `'stringjsonauto'`, `'stringJsonAuto'`, `'STRINGJSONAUTO'`, etc).
	* Valid values:
		* `''` (empty value) — do not convert (default value)
		* The snippet can convert data to primitive types:
			* `'string'`
			* `'integer'`/`'int'`
			* `'float'`
			* `'boolean'`/`'bool'`
		* The snippet can convert data to string:
			* `'stringJsonAuto'` — `stringJsonObject` or `stringJsonArray` depends on data type
			* `'stringJsonObject'`
			* `'stringJsonArray'`
			* `'stringQueryFormatted'` — [Query string](https://en.wikipedia.org/wiki/Query_string)
		* The snippet can also convert data to native PHP object or array (it is convenient to call through `\DDTools\Snippet`):
			* `'objectAuto'` — `stdClass` or `array` depends on data type
			* `'objectStdClass'` — `stdClass`
			* `'objectArray'` — `array`
	* Default value: — (without conversion)


### Output parameters

* `outputter`
	* Description: Output parameters.
	* Valid values:
		* `stringJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON) object
		* `stringHjsonObject` — as [HJSON](https://hjson.github.io/)
		* `stringQueryFormatted` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
		* It can also be set as a native PHP object or array (e. g. for calls through `\DDTools\Snippet::runSnippet`):
			* `arrayAssociative`
			* `object`
	* Default value:
		```hjson
		{
			type: data
			convertTo: ""
		}
		```
	
* `outputter->type`
	* Description: What to return as snippet result.
		* Values are case insensitive (the following values are equal: `'metaData'`, `'metadata'`, `'METADATA'`, etc).
	* Valid values:
		* `'data'` — response body only
		* `'meta'` — only metadata, the following properties are available:
			* `'isSuccess'` — Whether the request was successful (CURL, HTTP code, and data validation)
			* `'isCurlSuccess'` — Whether CURL execution was successful
			* `'isHttpCodeSuccess'` — Whether HTTP response code is not an error (< 400 or >= 600)
			* `'isDataValid'` — Whether response data is valid
			* `'effectiveUrl'` — Effective URL
			* `'curlErrorCode'` — CURL error code
			* `'message'` — Message text. Contains CURL error message if CURL failed, or message from response data if `dataProcessor->messagePropName` is set
			* `'code'` — HTTP code
		* `'metaData'` — both response body and metadata as JSON object with `data` and `meta` properties
	* Default value: `'data'`
	
* `outputter->convertTo`
	* Description: Convert the whole snippet result to specified format.
		* **Important:** This converts the complete snippet result (which can be `data`, `meta`, or `metaData` depending on `outputter->type`). If you need to convert only the response `data`, use `dataProcessor->convertTo` instead.
		* Values are case insensitive (the following values are equal: `'stringjsonauto'`, `'stringJsonAuto'`, `'STRINGJSONAUTO'`, etc).
	* Valid values:
		* `''` (empty value) — return as is, without conversion (default value)
		* The snippet can return object as string:
			* `'stringJsonAuto'` — `stringJsonObject` or `stringJsonArray` depends on result object
			* `'stringJsonObject'`
			* `'stringJsonArray'`
			* `'stringQueryFormatted'` — [Query string](https://en.wikipedia.org/wiki/Query_string)
		* The snippet can also return object as a native PHP object or array (it is convenient to call through `\DDTools\Snippet`).
			* `'objectAuto'` — `stdClass` or `array` depends on result object
			* `'objectStdClass'` — `stdClass`
			* `'objectArray'` — `array`
	* Default value: — (without conversion)


## Examples


### Simple GET request

```
[[ddMakeHttpRequest?
	&requester=`{
		url: http://www.example.com?name=John&surname=Doe
	}`
]]
```


### Simple POST request

Set data as HJSON:

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

Or Query string:

```
[[ddMakeHttpRequest?
	&requester=`{
		url: http://www.example.com/
		data: name=John&surname=Doe
	}`
]]
```


### Run the snippet through `\DDTools\Snippet::runSnippet` without DB and eval

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


### Get only meta

```php
$responseMeta = \DDTools\Snippet::runSnippet([
	'name' => 'ddMakeHttpRequest',
	'params' => [
		'requester' => [
			'url' => 'https://example.com/',
		],
		'outputter' => [
			'type' => 'meta',
		],
	],
]);

// Check if request was successful
if ($responseMeta->isSuccess){
	// Success
}else{
	// Error
	error_log('HTTP request failed: ' . $responseMeta->message);
}
```


### Get both data and meta

```php
$result = \DDTools\Snippet::runSnippet([
	'name' => 'ddMakeHttpRequest',
	'params' => [
		'requester' => [
			'url' => 'https://api.example.com/users',
		],
		'outputter' => [
			'type' => 'metaData',
		],
	],
]);

if ($result->meta->isSuccess){
	// Process response data
	$users = json_decode($result->data);
}
```


### Convert result to JSON

```php
// Get result as JSON string
$jsonString = \DDTools\Snippet::runSnippet([
	'name' => 'ddMakeHttpRequest',
	'params' => [
		'requester' => [
			'url' => 'https://api.example.com/users',
		],
		'outputter' => [
			'type' => 'metaData',
			'convertTo' => 'stringJsonAuto',
		],
	],
]);

// Now you can use it in JavaScript or save to file
```


### Convert result to array

```php
// Get response meta as PHP array
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

// Access as array
if ($metaArray['isSuccess']){
	echo 'HTTP code: ' . $metaArray['code'];
}
```

### Response data validation with `dataProcessor`


#### Validate response with simple value (e. g. string or number)

API returns simple value and if it equals to `OK` — it's success, otherwise — failure.

```php
$result = \DDTools\Snippet::runSnippet([
	'name' => 'ddMakeHttpRequest',
	'params' => [
		'requester' => [
			'url' => 'https://api.example.com/check',
		],
		'dataProcessor' => [
			// Response contains data about success status
			'isCheckForSuccess' => true,
			// If value equals to `OK` — it's success, otherwise — failure
			'checkValue' => 'OK',
		],
		'outputter' => [
			'type' => 'meta',
		],
	],
]);

// Now isSuccess will be true only if:
// * CURL request succeeded
// * HTTP code is 2xx
// * Response equals `OK`
if ($result->isSuccess){
	// All good!
}
```


#### Validate response as object and check for success status

API response is an object and contains `success` property with value `true` (e. g. `{"success": true}`) — it's success, otherwise — failure.

```php
$result = \DDTools\Snippet::runSnippet([
	'name' => 'ddMakeHttpRequest',
	'params' => [
		'requester' => [
			'url' => 'https://api.example.com/send',
			'method' => 'post',
			'data' => [
				'message' => 'Hello',
			],
		],
		'dataProcessor' => [
			// Response contains data about success status
			'isCheckForSuccess' => true,
			// Use `success` property to check for success status
			'checkPropName' => 'success',
			// Value to check for success status
			'checkValue' => true,
		],
		'outputter' => [
			'type' => 'metaData',
		],
	],
]);

// Now meta->isSuccess will be true only if:
// * CURL request succeeded
// * HTTP code is 2xx
// * Response is an object and contains `success` property with value `true` (e. g. `{"success": true}`)
if ($result->meta->isSuccess){
	// All good!
}else{
	// Something went wrong
}
```


#### Validate response as object, check for failure status and extract error message

API response is an object and contains `status` property with value `fail` (e. g. `{"status": "fail"}`) — it's failure, otherwise — success.

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
			// Response contains data about failure status (e. g. `{"status": "fail"}`)
			'isCheckForSuccess' => false,
			// Use `status` property to check for failure status
			'checkPropName' => 'status',
			// Failure status value
			'checkValue' => 'fail',
			// Extract message from response
			'messagePropName' => 'message',
		],
		'outputter' => [
			'type' => 'metaData',
		],
	],
]);

if (!$result->meta->isSuccess){
	// Log error with message from API
	error_log('API error: ' . ($result->meta->message ?? 'Unknown error'));
}
```


### Convert data and output simultaneously with `dataProcessor->convertTo` and `outputter->convertTo`

API returns JSON string `'{"userId": "123", "userName": "John"}'`. We want to:
1. Convert response data from JSON string to PHP object (using `dataProcessor->convertTo`)
2. Return both data and meta (using `outputter->type = 'metaData'`)
3. Convert the whole result to JSON for frontend (using `outputter->convertTo`)

```php
$jsonResult = \DDTools\Snippet::runSnippet([
	'name' => 'ddMakeHttpRequest',
	'params' => [
		'requester' => [
			'url' => 'https://api.example.com/user',
		],
		'dataProcessor' => [
			// Convert JSON string to PHP object before validation
			'convertTo' => 'objectStdClass',
		],
		'outputter' => [
			// Return both data and meta
			'type' => 'metaData',
			// Convert whole result to JSON
			'convertTo' => 'stringJsonObject',
		],
	],
]);

// Now $jsonResult is JSON string like:
// {"data": {"userId": "123", "userName": "John"}, "meta": {"isSuccess": true, ...}}
// Perfect for AJAX responses or saving to file
```


## Links

* [Home page](https://code.divandesign.ru/modx/ddmakehttprequest)
* [Telegram chat](https://t.me/dd_code)
* [Packagist](https://packagist.org/packages/dd/evolutioncms-snippets-ddmakehttprequest)
* [GitHub](https://github.com/DivanDesign/EvolutionCMS.snippets.ddMakeHttpRequest)


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />