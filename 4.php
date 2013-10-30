<?php

/**
 * @author ratan12
 * @copyright 2013
 */
 
function url($url) {
    $ch1= curl_init();
    curl_setopt ($ch1, CURLOPT_URL, $url );
    curl_setopt($ch1, CURLOPT_HEADER, 0);
    curl_setopt($ch1,CURLOPT_VERBOSE,1);
    curl_setopt($ch1, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)');
    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch1,CURLOPT_POST,0);
    $data = curl_exec($ch1);
    curl_close($ch1);
    return ($data);
}


    $a = time();
    //request the html source
    $Mal = url("myanimelist.net/profile/ratan12/friends");
    
    $b = (time() - $a);
    echo $b;
    echo '<br><br>'.$Mal;
?>