<?php

/**
 * @author ratan12
 * @copyright 2013
 */

//get the page numer and confrim
    $a = time();
    
    
    function getURL($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $tmp = curl_exec($ch);
        curl_close($ch);
        if ($tmp != false){
            return $tmp;
        }
        echo curl_getinfo ($ch);
    }
    echo getURL('http://myanimelist.net/profile/ratan12/friends');
    $b = (time() - $a);
    echo $b;
?>
