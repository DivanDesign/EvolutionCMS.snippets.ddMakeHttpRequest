<?php
namespace ddMakeHttpRequest;

/**
 * DataProcessor
 * 
 * @desc Processes and validates HTTP response data
 */
class DataProcessor {
	/**
	 * @property $params {stdClass} — Processing parameters
	 * @property $params->checkValue {mixed} — Value to check for success/failure
	 * @property $params->isCheckForSuccess {boolean} — Check for success (true) or failure (false)
	 * @property $params->checkPropName {string|null} — Property name to check (null = check whole data)
	 * @property $params->messagePropName {string|null} — Property name with message
	 * @property $params->convertTo {string} — Convert data to specified type
	 */
	private $params = [
		'checkValue' => '',
		'isCheckForSuccess' => false,
		'checkPropName' => null,
		'messagePropName' => null,
		'convertTo' => '',
	];
	
	/**
	 * __construct
	 * @version 1.1 (2025-11-25)
	 * 
	 * @param $params {stdClass|arrayAssociative} — Processing parameters, see $this->params property
	 */
	public function __construct($params = []){
		$this->params = \DDTools\Tools\Objects::extend([
			'objects' => [
				(object) $this->params,
				$params,
			],
		]);
		
		// Make convertTo case insensitive
		$this->params->convertTo = strtolower($this->params->convertTo);
	}
	
	/**
	 * process
	 * @version 1.1 (2025-11-25)
	 * 
	 * @desc Processes and validates response data
	 * 
	 * @param $theResultInstance {\ddMakeHttpRequest\Result} — Result instance to process
	 * 
	 * @return {void}
	 */
	public function process($theResultInstance): void {
		// Validate data only if CURL and HTTP are successful
		if (
			$theResultInstance->meta->isCurlSuccess
			&& $theResultInstance->meta->isHttpCodeSuccess
		){
			// If property name for checking is specified, consider data as object
			$isDataObject = !is_null($this->params->checkPropName);
			
			$data = $theResultInstance->data;
			// Check by raw data by default
			$data_checkValue = $data;
			
			if ($isDataObject){
				// Convert data to object
				$data = \DDTools\Tools\Objects::convertType([
					'object' => $data,
					'type' => 'objectStdClass',
				]);
				
				// Get required property value for checking
				$data_checkValue = \DDTools\Tools\Objects::getPropValue([
					'object' => $data,
					'propName' => $this->params->checkPropName,
				]);
			}
			
			// Determine if response is valid
			$theResultInstance->meta->isDataValid =
				$this->params->isCheckForSuccess
				// Looking for success value
				? $data_checkValue == $this->params->checkValue
				// Looking for absence of failure value
				: $data_checkValue != $this->params->checkValue
			;
			
			// Extract message if property is specified
			if (
				$isDataObject
				&& !is_null($this->params->messagePropName)
			){
				$theResultInstance->meta->message = \DDTools\Tools\Objects::getPropValue([
					'object' => $data,
					'propName' => $this->params->messagePropName,
				]);
			}
		}
		
		// Calculate final success flag
		$theResultInstance->meta->isSuccess =
			$theResultInstance->meta->isCurlSuccess
			&& $theResultInstance->meta->isHttpCodeSuccess
			&& $theResultInstance->meta->isDataValid
		;
		
		// Convert data if needed (always, regardless of validation)
		if (!empty($this->params->convertTo)){
			switch ($this->params->convertTo){
				case 'string':
					$theResultInstance->data = (string) $theResultInstance->data;
				break;
				
				case 'integer':
				case 'int':
					$theResultInstance->data = (int) $theResultInstance->data;
				break;
				
				case 'float':
					$theResultInstance->data = (float) $theResultInstance->data;
				break;
				
				case 'boolean':
				case 'bool':
					$theResultInstance->data = (bool) $theResultInstance->data;
				break;
				
				// Object-like types
				default:
					$theResultInstance->data = \DDTools\Tools\Objects::convertType([
						'object' => $theResultInstance->data,
						'type' => $this->params->convertTo,
					]);
			}
		}
	}
}
?>
