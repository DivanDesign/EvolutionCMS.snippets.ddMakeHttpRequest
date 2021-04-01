# (MODX)EvolutionCMS.snippets.ddMakeHttpRequest

Makes HTTP request to a given URL.


## Requires

* PHP >= 5.6
* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.biz/modx/ddtools) >= 0.48.2


## Documentation


### Installation

Elements → Snippets: Create a new snippet with the following data:

1. Snippet name: `ddMakeHttpRequest`.
2. Description: `<b>2.1</b> Makes HTTP request to a given URL.`.
3. Category: `Core`.
4. Parse DocBlock: `no`.
5. Snippet code (php): Insert content of the `ddMakeHttpRequest_snippet` file from the archive.


### Parameters description

* `url`
	* Desctription: The URL to fetch.
	* Valid values: `string`
	* **Required**
	
* `method`
	* Desctription: Request type.
	* Valid values:
		* `'get'`
		* `'post'`
	* Default value: `'get'`
	
* `postData`
	* Desctription: The full data to post in a HTTP “POST” operation.
	* Valid values:
		* `string_json` — as [JSON](https://en.wikipedia.org/wiki/JSON) object
		* `string_queryFormated` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
		* `array_associative`
		* `string`
	* Default value: —
	
* `sendRawPostData`
	* Desctription: Send raw `postData`. E. g. if you need JSON in request payload.
	* Valid values:
		* `0`
		* `1`
	* Default value: `0`
	
* `headers`
	* Desctription: An array of HTTP header fields to set.
	* Valid values:
		* `string_json` — as [JSON](https://en.wikipedia.org/wiki/JSON) object
		* `string_queryFormated` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
		* `array`
	* Default value: —
	
* `userAgent`
	* Desctription: The contents of the `User-Agent: ` header to be used in a HTTP request.
	* Valid values: `string`
	* Default value: —
	
* `timeout`
	* Desctription: The maximum number of seconds for execute request.
	* Valid values: `integer`
	* Default value: `60`
	
* `proxy`
	* Desctription: Proxy server in format `[+protocol+]://[+user+]:[+password+]@[+ip+]:[+port+]`. E. g. `http://asan:gd324ukl@11.22.33.44:5555` or `socks5://asan:gd324ukl@11.22.33.44:5555`.
	* Valid values: `string`
	* Default value: —
	
* `useCookie`
	* Desctription: Enagle cookies. The `assets/cache/ddMakeHttpRequest_cookie.txt` file is used.
	* Valid values:
		* `0`
		* `1`
	* Default value: `0`


### Examples


#### Simple GET request

```
[[ddMakeHttpRequest?
	&url=`http://www.example.com?name=John&surname=Doe`
]]
```


#### Simple POST request

Set data as JSON:

```
[[ddMakeHttpRequest?
	&url=`http://www.example.com/`
	&postData=`{
		"name": "John",
		"surname": "Doe"
	}`
]]
```

Or Query string:

```
[[ddMakeHttpRequest?
	&url=`http://www.example.com/`
	&postData=`name=John&surname=Doe`
]]
```


#### CMS API

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


## Links

* [Home page](https://code.divandesign.biz/modx/ddmakehttprequest)
* [Telegram chat](https://t.me/dd_code)
* [Packagist](https://packagist.org/packages/dd/evolutioncms-snippets-ddmakehttprequest)


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />