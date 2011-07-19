<?php
include('src_config.php');
include('extension.php');
include('mcrypt.php');
require('SoapProxy.php');
require('output/ccws.php');
require_once("xmlobject/class.XmlObject.php");

$MethodToCall = 'call' . $_GET['callMethod'];
$method = new $MethodToCall($wsdl, $opts, $methodPrefix);
$method->printMethodResults();


/*//create service instance
$service = new ccws($wsdl, $opts);

//инициализируем параметры нужного метода
$param = new ccws_RetrieveSubscriberLite();
$param->subscriberID = $_GET['msisdn'];
$param->identity = NULL;
//$param->informationToRetrieve = 7;

//run ws method
try {
    $result = $service->RetrieveSubscriberLite($param);
//    print_r($result);
} catch (SoapFault $exception){
//    print_r($exception);
    $result = $exception;
}
//$result = $service->ChangePassword($param);

$XmlConstruct = new XmlConstruct('IVR');
$XmlConstruct->fromObject($result);
$XmlConstruct->output();

//print_r($result);*/
?>