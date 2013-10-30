<?php

/**
 * @author ratan12
 * @copyright 2013
 */

function url($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($curl);
    curl_close($curl);
    return ($data);
}


    $a = time();
    //request the html source
    $Mal = url("myanimelist.net/profile/ratan12/friends");
    $b = (time() - $a);
    echo $b;
    echo '<br><br>'.$Mal;
?>