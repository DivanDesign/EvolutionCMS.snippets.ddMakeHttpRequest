<?php
/**
 * ddMakeHttpRequest
 * @version 2.4.1 (2026-05-24)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.ru/modx/ddmakehttprequest
 * 
 * @copyright 2011–2026 https://Ronef.me
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