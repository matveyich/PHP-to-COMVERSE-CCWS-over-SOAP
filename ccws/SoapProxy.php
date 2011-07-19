<?php 
/**
 * SoapProxy
 *
 * Copyright (c) 2011 Przemek Berezowski (przemek@otn.pl) 
 * All rights reserved.
 *
 * Thanks to Artur Graniszewski (aargoth@boo.pl) for some tips during development

 * @category	  Library
 * @package  	  SoapProxy
 * @copyright     Copyright (c) 2011 Przemek Berezowski (przemek@otn.pl)
 * @version       0.9
 * @license       New BSD License
 */

/**
 * Abstract class for soap calls
 * Base for generated soap operations class
 * @author Przemek Berezowski
 *
 */
abstract class SoapProxy {
	
	/**
	 * SoapClient
	 * @var SoapClient
	 */
	public $soapClient = null;
	private $objSoapVarWSSEHeader = null;
	
	/**
	 * Class constructor
	 * Initialize SoapClient
	 * @param string $wsdl - wsdl address
	 * @param array $opts - SoapClient options see http://php.net/manual/en/soapclient.soapclient.php 
	 */
	public function __construct($wsdlUrl, $opts = array()) {
		if (isset($opts['classmap'])) {
			$opts['classmap'] = array_merge($this->defaultTypeMap, $opts['classmap']);
		} else {
			$opts['classmap'] = $this->defaultTypeMap;
		}
		$this->objSoapVarWSSEHeader = WSSEHeader();
		$this->soapClient = new SoapProxyClient(
			$wsdlUrl,
			$opts
		);
		$this->soapClient->__setSoapHeaders(array($this->objSoapVarWSSEHeader));
	}
}

/**
 * Stub for SoapClient
 * Extends PHP SoapClient to easly modyfiing
 * 
 * @author przemek berezowski
 */
class SoapProxyClient extends SoapClient {
	
	
}