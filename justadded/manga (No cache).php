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
$page = (($_GET['p']*20)-20);
if ($page >= 0){
    //request the html source
    $Mal = file_get_contents("http://myanimelist.net/manga.php?o=9&c[0]=a&c[1]=d&cv=2&q=&sm=0&sd=0&em=0&ed=0&show=".$page);
    
    //remove empty lines
    $Mal = str_replace("\n", "", $Mal);
    $Mal = str_replace("\r", "", $Mal);
    $Mal = trim(preg_replace('/\t+/', '', $Mal));

    //isolate the table
    $Mal = str_replace_first('</table>', '', $Mal);
    $Mal = str_replace_first('</table>', '', $Mal);
    $startsAt = strpos($Mal,'<table border="0" cellpadding="0" cellspacing="0" width="100%">') + strlen('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
    $endsAt = strpos($Mal, '</table>', $startsAt);
    $table = substr($Mal, $startsAt, $endsAt - $startsAt);
    $table = str_replace_first('<tr>', '', $table);
    $table = str_replace_first('</tr>', '', $table);
    
    //get each cell and parse it into json
    for ($i = 1; $i <= 20; $i++) {
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
        if (empty($id)) {
            $type = '';
        }else if (strpos($cell,'Novel</td>') !== false) {
            $type = 'Novel';
        }else if (strpos($cell,'Manga</td>') !== false) {
            $type = 'Manga';
        }else if (strpos($cell,'One Shot</td>') !== false) {
            $type = 'One Shot';
        }else if (strpos($cell,'Doujin</td>') !== false) {
            $type = 'Doujin';
        }else if (strpos($cell,'Manhwa</td>') !== false) {
            $type = 'Manhwa';
        }else if (strpos($cell,'Manhua</td>') !== false) {
            $type = 'Manhua';
        }else if (strpos($cell,'OEL</td>') !== false) {
            $type = 'OEL';
        }
        
        $json = ',{"id":"'.$id.'","title":"'.$title.'","type":"'.$type.'","image_url_short":"'.$image_url.',"image_url":"'.$image_url_large.'}';
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