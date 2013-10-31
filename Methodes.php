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

function parse($string,$first,$second){
    $startsAt = strpos($string, $first);
    $endsAt = strpos($string, $second, $startsAt);
    $parse = substr($string, $startsAt, $endsAt - $startsAt);
    $parsed = str_replace($first, '', $parse);
    return($parsed);
}

function str_replace_first($search, $replace, $subject) {
    $pos = strpos($subject, $search);
    if ($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}

?>