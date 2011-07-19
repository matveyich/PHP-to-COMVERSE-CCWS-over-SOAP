<?php
/**
 * SoapProxy
 *
 * Copyright (c) 2011 Przemek Berezowski (przemek@otn.pl) 
 * All rights reserved.
 * 
 * Thanks to Artur Graniszewski (aargoth@boo.pl) for some tips during development

 * @category      Library
 * @package       SoapProxy
 * @copyright     Copyright (c) 2011 Przemek Berezowski (przemek@otn.pl)
 * @version       0.9
 * @license       New BSD License
 */


/**
 * Class for generating PHP client code representation for webservice 
 * @author przemek berezowski
 *
 */ 
class SoapProxyGenerator {

	/**
	 * Alias for webservice code representation
	 * 
	 * @var string
	 */
	public $serviceAlias = 'Service';
	
	/**
	 * Prefix for soap types
	 * @var string
	 */
	public $typePrefix = '';
	
	/**
	 * Location where generated class will be saved
	 * If empty result will be displayed on screen 
	 * 
	 * @var string
	 */
	public $outputFile = '';

	
	protected $types = array();
	
	protected $methods = array();

	protected $nativeTypes = array(
		'int',
		'integer',
		'string',
		'date',
		'datetime',
		'bool',
		'boolean',
		'float',
		'decimal',
	);
	
	/**
	 * Class constructor
	 *  
	 * @param string $wsdl - wsdl address
	 * @param array $opts - SoapClient options see http://php.net/manual/en/soapclient.soapclient.php  
	 */
	public function __construct($wsdl, $opts) {

		$client = new SoapProxyClient($wsdl, $opts);
		$this->types = array_unique($client->__getTypes());
		$this->methods = array_unique($client->__getFunctions());

	}

	/**
	 * Runs code generation over wsdl 
	 */
	public function generateCode() {
		
		$soapMethods = $this->parseMethods();
		$soapTypes = $this->parseTypes();

		$classStart = "<?php".PHP_EOL;
		$classStart .= $this->getComment();
		$classStart .= "class $this->serviceAlias extends SoapProxy {".PHP_EOL;
		
		$class = '';

		foreach($soapMethods as $method) {
			$class .= "\t/**".PHP_EOL;
			$class .= "\t* Genarated webservice method ".$method['method'].PHP_EOL;
			$class .= "\t*".PHP_EOL;
			

			
			$methodParams = array();
			$methodParamsVals = array();
			foreach($method['params'] as $param) {
				if (in_array($param['type'], $this->nativeTypes)) {
					$methodParams[] = $param['name'];
					$class .= "\t* @param ".$param['type'].' '.$param['name'].PHP_EOL;
				} else {
					$methodParams[] = $this->typePrefix.$param['type'].' '.$param['name'];
					$class .= "\t* @param ".$this->typePrefix.$param['type'].' '.$param['name'].PHP_EOL;
				}
				$methodParamsVals[] = $param['name'];
				
			}
			
			$class .= "\t* @return ".$this->typePrefix.$method['return'].PHP_EOL;
			$class .= "\t*/".PHP_EOL;
			
			$class .= "\tpublic function ".$method['method']."(";
			$class .= implode(' ', $methodParams);
			$class .= ") {".PHP_EOL;
			$class .= "\t\t".'return $this->soapClient->'.$method['method'].'('.implode(' ', $methodParamsVals).');'.PHP_EOL;
			$class .= "\t}".PHP_EOL.PHP_EOL;

		}

		$class .= PHP_EOL.PHP_EOL.'} //end generated proxy class'.PHP_EOL.PHP_EOL;

		$classMap = "\t".'protected $defaultTypeMap = array('.PHP_EOL;
		$classMapArray = array();
		$types = PHP_EOL.'/**********SOAP TYPES***********/'.PHP_EOL.PHP_EOL;
		foreach ($soapTypes as $type) {
			$classMapArray[] = "\t\t".'"'.$type['name'].'" => "'.$this->typePrefix.$type['name'].'"';
			$types .= $this->generateType($type);
		}

		$classMap .= implode(','.PHP_EOL, $classMapArray);
		$classMap .= PHP_EOL."\t);".PHP_EOL.PHP_EOL;

		$class = $classStart.$classMap.$class;
		
		if (!empty($this->outputFile)) {
			file_put_contents($this->outputFile, $class.$types);
		} else {
			highlight_string($class.$types);
		}
	}

	
	protected function parseTypes() {
		$soapTypes = array();
		foreach($this->types as $soapType) {
			$struct = explode(' ', str_replace(array("\n", "\t", " {", "{", "}", ";", '[', ']'), '', $soapType));
			$soapTypeName = $struct[0];
			$typeName = $struct[1];
			array_shift($struct);
			array_shift($struct);

			$fields = array();
			$index = 0;
			foreach ($struct as $k => $vars) {
				if ($k%2 == 0) { //variable type
					$fields[$index]['type'] = $vars;
				} else { //variable name
					$fields[$index]['name'] = $vars;
					$index++;
				}
			}

			$soapTypes[] = array(
				'type' => $soapTypeName,
				'name' => $typeName,
				'fields' => $fields
			);
		}
		return $soapTypes;
	}

	protected function parseMethods() {
		$soapMethods = array();
		foreach($this->methods as $method) {
			$struct = explode(' ', trim(str_replace(array("(", ")"), ' ', $method)));
			$returnType = $struct[0];
			$methodName = $struct[1];
			array_shift($struct);
			array_shift($struct);

			$params = array();
			$index = 0;
			foreach ($struct as $k=>$param) {
				if ($k%2 == 0) { //param type
					$params[$index]['type'] = $param;
				} else { //param name
					$params[$index]['name'] = $param;
					$index++;
				}
			}

			$soapMethods[] = array (
				'return' => $returnType,
				'method' => $methodName,
				'params' => $params
			);
		}

		return $soapMethods;
	}

	protected function generateType($typeInfo) {

		$txt = '/**'.PHP_EOL;
		$txt .= '* Generated data proxy class for '.$typeInfo['type'].' '.$typeInfo['name'].PHP_EOL;
		$txt .= '*'.PHP_EOL;
		$txt .= '*/'.PHP_EOL;
		$txt .= 'class '.$this->typePrefix.$typeInfo['name'].' {'.PHP_EOL.PHP_EOL;
		foreach ($typeInfo['fields'] as $field) {
			$txt .= "\t/**".PHP_EOL;
			$txt .= "\t* @var ".$field['type'].' $'.$field['name'].PHP_EOL;
			$txt .= "\t*/".PHP_EOL;
			$txt .= "\tpublic $".$field['name'].';'.PHP_EOL.PHP_EOL;
		}
		$txt .= '}'.PHP_EOL.PHP_EOL;

		return $txt;
	}
	
	private function getComment() {
		$txt = '/**'.PHP_EOL;
		$txt .- '*'.PHP_EOL;
		$txt .= '* Class to handle requests to '.$this->serviceAlias.' webservice.'.PHP_EOL;
		$txt .= '* This code was generated by using SoapProxy tool by przemek@otn.pl'.PHP_EOL;
		$txt .= '* Please do not modify it by hand.'.PHP_EOL;
		$txt .= '*'.PHP_EOL;
		$txt .= '*/'.PHP_EOL;
		return $txt;
	}
	

}