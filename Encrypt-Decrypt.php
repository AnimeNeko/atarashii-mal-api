<?php

/**
 * @author ratan12
 * @copyright 2013
 */

function Encrypt($sValue){
    return rtrim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,"Atarashii19 codec18 password12",$sValue,MCRYPT_MODE_ECB,mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256,MCRYPT_MODE_ECB),MCRYPT_RAND))), "\0");
}

function Decrypt($sValue){
    return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256,"Atarashii19 codec18 password12", base64_decode($sValue), MCRYPT_MODE_ECB,mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256,MCRYPT_MODE_ECB),MCRYPT_RAND)), "\0");
}

?>