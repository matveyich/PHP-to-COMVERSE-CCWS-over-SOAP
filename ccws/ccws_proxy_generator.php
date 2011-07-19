<?php 
include('src_config.php');

require('SoapProxy.php');
require('SoapProxyGenerator.php');

$proxyGenerator = new SoapProxyGenerator($wsdl, $opts);
$proxyGenerator->serviceAlias = 'ccws';
$proxyGenerator->typePrefix = 'ccws_';
$proxyGenerator->outputFile = 'output/ccws.php';

$proxyGenerator->generateCode();
