<?php
/**
 * ddMakeHttpRequest
 * @version 2.4 (2025-11-28)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.ru/modx/ddmakehttprequest
 * 
 * @copyright 2011–2025 https://Ronef.me
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