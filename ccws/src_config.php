<?php
$login = 'IVR';
$username = $login;
$password = 'FlaEL9e006boKdvvwelRBE1WtK0/0Oga';

$strWSSENS = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd";


$wsdl = 'external/ccws_wsdl.xml'; // store WSDL file locally

$opts = array(
	'uri' => 'http://10.44.21.1/ccws/ccws.asmx', // address to call methods
    'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
	'trace'=>1,
	'authentication' => SOAP_AUTHENTICATION_BASIC,
	/*'style' => SOAP_RPC,
    'use' => SOAP_ENCODED*/
	'cache_wsdl' => WSDL_CACHE_BOTH // put WSDL file to cache
);
$methodPrefix = 'ccws_';
//$opts = array();
?>
