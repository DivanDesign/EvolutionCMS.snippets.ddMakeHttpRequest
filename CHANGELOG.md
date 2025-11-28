# (MODX)EvolutionCMS.snippets.ddMakeHttpRequest changelog


## Version 2.4 (2025-11-28)

* \+ Created detailed error logging for CURL errors and HTTP error responses (4xx, 5xx), including URL, HTTP code, CURL error code and message.
* \* Parameters:
	* \+ `isDebug`: The new optional parameter created. Allows to log all requests (including successful ones), not only errors. Useful for debugging and monitoring.
	* \* All request parameters are now grouped into `requester` object and several renamed (backward compatibility preserved, but old parameters are not recommended to use):
		* \* `postData` → `requester->data`.
		* \* `sendRawPostData` → `requester->isRawDataEnabled`.
		* \* `useCookie` → `requester->isCookieUsed`.
		* \* `url` → `requester->url`.
		* \* `method` → `requester->method`.
		* \* `headers` → `requester->headers`.
		* \* `userAgent` → `requester->userAgent`.
		* \* `timeout` → `requester->timeout`.
		* \* `proxy` → `requester->proxy`.
	* \+ `requester->method` → Valid values → `put`, `patch`, `delete`: The new supported methods.
	* \+ `dataProcessor`: The new optional parameter. Allows to process and validate response data with configurable success/failure checking, see README → Parameters, README → Examples.
	* \+ `outputter`: The new optional parameter. Allows to return response data, metadata, or both; and to convert the result to a required format. Metadata contains the following properties:
		* \+ `'isSuccess'` — Whether the request was successful (CURL, HTTP code, and data validation).
		* \+ `'isCurlSuccess'` — Whether CURL execution was successful.
		* \+ `'isHttpCodeSuccess'` — Whether HTTP response code is not an error (< 400 or >= 600).
		* \+ `'isDataValid'` — Whether response data is valid.
		* \+ `'effectiveUrl'` — Effective URL.
		* \+ `'curlErrorCode'` — CURL error code.
		* \+ `'message'` — Message text. Contains CURL error message if CURL failed, or message from response data if `dataProcessor->messagePropName` is set.
		* \+ `'code'` — HTTP code.
* \* Attention! PHP >= 7.4 is required (not tested with older versions).
* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.63 is required.


## Version 2.3.2 (2022-05-25)

* \* Parameters → `postData`: Multidimensional PHP arrays and objects are also supported.


## Version 2.3.1 (2021-04-17)

* \* `\ddMakeHttpRequest\Snippet::run`: Fixed checking of a non-existent array element.


## Version 2.3 (2021-04-13)

* \+ `\ddMakeHttpRequest\Snippet::run`: Improved “manual” redirection.


## Version 2.2 (2021-04-02)

* \* Attention! PHP >= 5.6 is required.
* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.48.2 is required.
* \+ Parameters → `postData`: Can also be set as HJSON or a native PHP object.
* \+ Parameters → `headers`: Can also be set as HJSON.
* \+ You can just call `\DDTools\Snippet::runSnippet` to run the snippet without DB and eval (see README → Examples).
* \+ `\ddMakeHttpRequest\Snippet`: The new class. All snippet code was moved here.
* \+ README:
	* \+ Links.
	* \+ Documentation → Parameters description → `postData`, `headers` → Valid values: Text improvements.
	* \+ Style improvements.
* \+ Composer.json:
	* \+ `homepage`.
	* \+ `support`.
	* \+ `authors`.


## Version 2.1 (2020-02-15)

* \+ Cookie can be used (see the `useCookie` parameter).


## Version 2.0 (2019-09-23)

* \* **Attention!** Backward compatibility is broken. If you want send raw JSON in `postData` you must set `sendRawPostData` equal to `1`.
* \+ `postData` can be set as a JSON object.
* \+ `headers` can be set as a JSON array.
* \+ Added an ability to send raw `postData` (see `sendRawPostData`).


## Version 1.0 (2011-06-16)

* \+ The first release.


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />
<style>ul{list-style:none;}</style>