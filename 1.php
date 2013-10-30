<?php

/**
 * @author ratan12
 * @copyright 2013
 */
 

//get the page numer and confrim
    $a = time();
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "myanimelist.net/profile/ratan12/friends");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $data = curl_exec($curl);
    curl_close($curl);
    return ($data);
    
    
    //request the html sourc
    $b = (time() - $a);
    echo $b;
    echo '<br><br>'.$data;
    echo $code;
?>