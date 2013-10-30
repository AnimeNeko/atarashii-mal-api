<?php

/**
 * @author ratan12
 * @copyright 2013
 */

//get the page numer and confrim

    $a = time();
    //request the html source
    $Mal = file_get_contents("http://myanimelist.net/profile/ratan12/friends");

    $b = (time() - $a);
    echo $b;
    echo '<br><br>'.$Mal;
?>