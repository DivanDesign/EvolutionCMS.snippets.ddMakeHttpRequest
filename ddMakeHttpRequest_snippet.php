<?php
/**
 * ddMakeHttpRequest
 * @version 2.3.1 (2021-04-17)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.biz/modx/ddmakehttprequest
 * 
 * @copyright 2011–2021 DD Group {@link https://DivanDesign.biz }
 */

//Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddTools/modx.ddtools.class.php'
);

return \DDTools\Snippet::runSnippet([
	'name' => 'ddMakeHttpRequest',
	'params' => $params
]);
?>