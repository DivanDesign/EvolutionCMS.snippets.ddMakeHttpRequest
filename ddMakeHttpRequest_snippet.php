<?php
/**
 * ddMakeHttpRequest
 * @version 2.3.2 (2022-05-25)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.ru/modx/ddmakehttprequest
 * 
 * @copyright 2011–2022 https://Ronef.me
 */

// Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path')
	. 'assets/libs/ddTools/modx.ddtools.class.php'
);

return \DDTools\Snippet::runSnippet([
	'name' => 'ddMakeHttpRequest',
	'params' => $params,
]);
?>