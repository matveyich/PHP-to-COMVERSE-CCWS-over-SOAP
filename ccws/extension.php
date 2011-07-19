<?php
class clsWSSEAuth {
private $Username;
private $Password;
function __construct($username, $password) {
$this->Username=$username;
$this->Password=$password;
}
}

class clsWSSEToken {
private $UsernameToken;
function __construct ($innerVal){
$this->UsernameToken = $innerVal;
}
}

function WSSEHeader()
{
global $username,$password,$strWSSENS;

$_mcrypt = new Mcrypt($password,$_GET['key']);
$password = $_mcrypt->decrypt();

$objSoapVarUser = new SoapVar($username, XSD_STRING, NULL, $strWSSENS, NULL, $strWSSENS);
$objSoapVarPass = new SoapVar($password, XSD_STRING, NULL, $strWSSENS, NULL, $strWSSENS);
$objWSSEAuth = new clsWSSEAuth($objSoapVarUser, $objSoapVarPass);
$objSoapVarWSSEAuth = new SoapVar($objWSSEAuth, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'UsernameToken', $strWSSENS);
$objWSSEToken = new clsWSSEToken($objSoapVarWSSEAuth);
$objSoapVarWSSEToken = new SoapVar($objWSSEToken, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'UsernameToken', $strWSSENS);
$objSoapVarHeaderVal=new SoapVar($objSoapVarWSSEToken, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'Security', $strWSSENS);
$objSoapVarWSSEHeader = new SoapHeader($strWSSENS, 'Security', $objSoapVarHeaderVal,true);
return $objSoapVarWSSEHeader;
}

// абстрактный класс, определяющий набор действий для детей класса
abstract class callCCWS{
    protected $MethodName = null;
    protected $Service = null;
    protected $MethodParams = null;
    protected $MethodPrefix = null;

    // создаем экземпляр набора функций ccws
    public function __construct($wsdl,$opts,$methodPrefix){
        $this->Service = new ccws($wsdl, $opts);
        $this->MethodPrefix = $methodPrefix;
    }

    // абстарктный метод класса настройки метода ccws
    abstract protected function setMethod();

    // вызов метода
    protected function callMethod($parameters){
        // определяем метод, который будем вызывать по глобальной переменной класса
        // глобальная переменная MethodName определяется в классах-детях по каждому из методов ccws
        $Method = $this->MethodName;
        try {
            // пробуем вызвать метод
            $result = $this->Service->$Method($parameters);
        } catch (SoapFault $exception){
            // в тело результата выводим ошибку
            $result = $exception;
        }
        return $result;

    }

    // вывод результатов работы метода ccws
    public function printMethodResults($rootTag = 'IVR') {

        $noErrorNode = array(
                'detail' => array(
                    'ErrorCode' => 0,
                    'ErrorDescription' => ''
                )
                );
        $params = $this->setMethod($this->MethodName);
        $result = $this->callMethod($params);

        $XmlConstruct = new XmlConstruct($rootTag);
        $XmlConstruct->fromObject($result);
        // формируем элемент, в котром сообщаем, что ошибок нет
        $XmlConstruct->fromArray($noErrorNode);

        $XmlConstruct->output();
    }
}

class callRetrieveSubscriberLite extends callCCWS{
    public function __construct($wsdl,$opts,$methodPrefix){
        parent::__construct($wsdl,$opts,$methodPrefix);
    }
    protected function setMethod(){
        $this->MethodName = "RetrieveSubscriberLite";
        $MethodToSet = $this->MethodPrefix.$this->MethodName;

        //инициализируем параметры нужного метода
        $param = new $MethodToSet;
        $param->subscriberID = $_GET['msisdn'];
        $param->identity = NULL;
        return $param;
    }
}

class callRetrieveSubscriberWithIdentityNoHistoryHomeOnly extends callCCWS{
    public function __construct($wsdl,$opts,$methodPrefix){
        parent::__construct($wsdl,$opts,$methodPrefix);
    }
    protected function setMethod(){
        $this->MethodName = "RetrieveSubscriberWithIdentityNoHistory";
        $MethodToSet = $this->MethodPrefix.$this->MethodName;

        //инициализируем параметры нужного метода
        $param = new $MethodToSet;
        $param->subscriberID = $_GET['msisdn'];
        $param->identity = NULL;
        $param->informationToRetrieve = 2;
        return $param;
    }
}
class callRetrieveSubscriberWithIdentityNoHistory extends callCCWS{
    public function __construct($wsdl,$opts,$methodPrefix){
        parent::__construct($wsdl,$opts,$methodPrefix);
    }
    protected function setMethod(){
        $this->MethodName = "RetrieveSubscriberWithIdentityNoHistory";
        $MethodToSet = $this->MethodPrefix.$this->MethodName;

        //инициализируем параметры нужного метода
        $param = new $MethodToSet;
        $param->subscriberID = $_GET['msisdn'];
        $param->identity = NULL;
        $param->informationToRetrieve = 3;
        return $param;
    }
}
?>