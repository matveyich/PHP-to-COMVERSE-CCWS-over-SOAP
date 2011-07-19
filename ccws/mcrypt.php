<?php
/**
 * Created by JetBrains PhpStorm.
 * User: illya.matvienko
 * Date: 15.06.11
 * Time: 10:04
 * To change this template use File | Settings | File Templates.
 */

class Mcrypt{
    protected $iv;
    protected $key;
    protected $string;
    protected $cipher;

    function __construct($_sting, $_key){
        $this->key = $_key;
        $this->iv = '12345678';
        $this->string = $_sting;
        $this->cipher = mcrypt_module_open(MCRYPT_BLOWFISH,'','cbc','');
        mcrypt_generic_init($this->cipher, $this->key, $this->iv);
    }

    function __destruct(){
        mcrypt_generic_deinit($this->cipher);
    }

    // Encrypting
    public function encrypt() {
    $enc = mcrypt_generic($this->cipher,$this->string);
    return base64_encode($enc);
    }

    // Decrypting
    public function decrypt() {
    $this->string = trim(base64_decode($this->string));
    $dec = mdecrypt_generic($this->cipher,$this->string);
    return $dec;
    }
}

?>
