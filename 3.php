<?php

/**
 * @author ratan12
 * @copyright 2013
 */

function url($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    $data = curl_exec($ch);
    curl_close($ch);
    return ($data);
}


    $a = time();
    //request the html source
    $Mal = url("myanimelist.net/profile/ratan12/friends");
    $b = (time() - $a);
    echo $b;
    echo '<br><br>'.$Mal;
?>