# (MODX)EvolutionCMS.snippets.ddMakeHttpRequest changelog


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


<style>ul{list-style:none;}</style>