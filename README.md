# (MODX)EvolutionCMS.snippets.ddMakeHttpRequest

Makes HTTP request to a given URL.

Supports redirects even if native CURL redirects is unavailable.


## Requires

* PHP >= 7.4
* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.ru/modx/ddtools) >= 0.48.2


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
2. Description: `<b>2.3.2</b> Makes HTTP request to a given URL.`.
3. Category: `Core`.
4. Parse DocBlock: `no`.
5. Snippet code (php): Insert content of the `ddMakeHttpRequest_snippet` file from the archive.


#### 2. Elements → Manage Files

1. Create a new folder `assets/snippets/ddMakeHttpRequest/`.
2. Extract the archive to the folder (except `ddMakeHttpRequest_snippet.php`).


## Parameters description

* `url`
	* Description: The URL to fetch.
	* Valid values: `string`
	* **Required**
	
* `method`
	* Description: Request type.
	* Valid values:
		* `'get'`
		* `'post'`
		* `'put'`
		* `'patch'`
		* `'delete'`
	* Default value: `'get'`
	
* `data`
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
	
* `isRawDataEnabled`
	* Description: Send raw `data`. E. g. if you need JSON in request payload.
	* Valid values:
		* `0`
		* `1`
	* Default value: `0`
	
* `headers`
	* Description: An array of HTTP header fields to set.
	* Valid values:
		* `stringJsonArray` — as [JSON](https://en.wikipedia.org/wiki/JSON)
		* `stringHjsonArray` — as [HJSON](https://hjson.github.io/)
		* `stringQueryFormatted` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
		* It can also be set as a native PHP array (e. g. for calls through `\DDTools\Snippet::runSnippet`):
			* `array`
	* Default value: —
	
* `userAgent`
	* Description: The contents of the `User-Agent: ` header to be used in a HTTP request.
	* Valid values: `string`
	* Default value: —
	
* `timeout`
	* Description: The maximum number of seconds for execute request.
	* Valid values: `integer`
	* Default value: `60`
	
* `proxy`
	* Description: Proxy server in format `[+protocol+]://[+user+]:[+password+]@[+ip+]:[+port+]`. E. g. `http://user:password@11.22.33.44:5555` or `socks5://user:password@11.22.33.44:5555`.
	* Valid values: `string`
	* Default value: —
	
* `useCookie`
	* Description: Enagle cookies. The `assets/cache/ddMakeHttpRequest_cookie.txt` file is used.
	* Valid values:
		* `0`
		* `1`
	* Default value: `0`
	
* `isDebug`
	* Description: Log all requests to event log (including successful ones), not only errors. Useful for debugging.
	* Valid values:
		* `0`
		* `1`
	* Default value: `0`


## Examples


### Simple GET request

```
[[ddMakeHttpRequest?
	&url=`http://www.example.com?name=John&surname=Doe`
]]
```


### Simple POST request

Set data as HJSON:

```
[[ddMakeHttpRequest?
	&url=`http://www.example.com/`
	&data=`{
		name: John
		surname: Doe
	}`
]]
```

Or Query string:

```
[[ddMakeHttpRequest?
	&url=`http://www.example.com/`
	&data=`name=John&surname=Doe`
]]
```


### Run the snippet through `\DDTools\Snippet::runSnippet` without DB and eval

```php
\DDTools\Snippet::runSnippet([
	'name' => 'ddMakeHttpRequest',
	'params' => [
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
]);
```


## Links

* [Home page](https://code.divandesign.ru/modx/ddmakehttprequest)
* [Telegram chat](https://t.me/dd_code)
* [Packagist](https://packagist.org/packages/dd/evolutioncms-snippets-ddmakehttprequest)
* [GitHub](https://github.com/DivanDesign/EvolutionCMS.snippets.ddMakeHttpRequest)


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />