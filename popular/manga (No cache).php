<?php

/**
 * @author ratan12
 * @copyright 2013
 */
 
 $totaljson = '[';
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

//get the page numer and confrim
$page = '';
$page = (($_GET['p']*30)-30);
if ($page >= 0){
    //request the html source
    $Mal = file_get_contents("http://myanimelist.net/topmanga.php?type=bypopularity&limit=".$page);
    
    //remove empty lines
    $Mal = str_replace("\n", "", $Mal);
    $Mal = str_replace("\r", "", $Mal);
    $Mal = trim(preg_replace('/\t+/', '', $Mal));

    //isolate the table
    $startsAt = strpos($Mal,'<table border="0" cellpadding="0" cellspacing="0" width="100%">') + strlen('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    $endsAt = strpos($Mal, '</table>', $startsAt);
    $table = substr($Mal, $startsAt, $endsAt - $startsAt);

    //get each cell and parse it into json
    for ($i = 1; $i <= 30; $i++) {
        $cell = parse($table,'<tr>','</tr>');
        $table = str_replace_first('<tr>', '', $table);
        $table = str_replace_first('</tr>', '', $table);
        
        $id = parse($cell,'selected_manga_id=','&hideLayout');
        $title = parse($cell,'<strong>','</strong>');
        $image_url = parse($cell,'src="',' border=');
        $image_url_large = str_replace("t.jpg", "l.jpg", $image_url);
        
        if (strpos($title,'"') !== false) {
            $title = str_replace_first('"', '', $title);
        }
        if (strpos($title,'"') !== false) {
            $title = str_replace_first('"', '', $title);
        }
        
        $volumes = parse($cell,'"spaceit_pad">',' volumes');
        $scored = parse($cell,'scored ','<br /><span');
        $members = parse($cell,'<br /><span class="lightLink">',' members');
        $json = ',{"id":"'.$id.'","title":"'.$title.'","image_url_short":"'.$image_url.',"image_url":"'.$image_url_large.',"volumes":"'.$volumes.'","scored":"'.$scored.'","members":"'.$members.'"}';
        $totaljson = $totaljson.$json;
    }
    
    //remove the first , and show the result to us(json))
    $totaljson = str_replace_first(',', '', $totaljson);
    if (strpos($totaljson,'url_short":","image_url":"}') !== false) {
        $totaljson = str_replace ('url_short":","image_url":"}','url_short":"","image_url":""}',$totaljson);
    }
    echo $totaljson.']';
}else{
    echo "<center><h1>Wrong page!</h1>Error: please enter the page to receive!</center>";
}
?>