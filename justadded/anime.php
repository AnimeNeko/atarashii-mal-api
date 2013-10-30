<?php

/**
 * @author ratan12
 * @copyright 2013
 */
 
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

function url($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    $data = curl_exec($curl);
    curl_close($curl);
    return ($data);
}

//connect database
$username="u232407816_cache";
$password="wakkazakka1";
$database="u232407816_cache";

//connect
mysql_connect("mysql.2freehosting.com",$username,$password);
@mysql_select_db($database) or die("Cache error: Unable to get the records!");

//get the page numer and confrim
$page = '';
$page = (($_GET['p']*20)-20);

// Get a specific result from the table
$result = mysql_query("SELECT * FROM Justadded_anime WHERE Page='".(($page+20)/20)."'") or die("Cache error: Unable to check the records!");
$row = mysql_fetch_array($result);
$time = $row['Time'];
$record = $row['Record'];

mysql_close();

if ($page >= 0 && $time!= (date("j").(($page+20)/20))){
    //request the html source
    $Mal = url("http://myanimelist.net/anime.php?o=9&c[0]=a&c[1]=d&cv=2&w=1&q=&tag=&sm=0&sd=0&em=0&ed=0&show=".$page);
    
    if (strpos($Mal,'No anime titles found') !== false) {
        $totaljson = 'Page: '.(($page+20)/20).' has no data!';
    }else{
        //remove empty lines
        $Mal = str_replace("\n", "", $Mal);
        $Mal = str_replace("\r", "", $Mal);
        $Mal = trim(preg_replace('/\t+/', '', $Mal));

        //isolate the table
        $startsAt = strpos($Mal,'<table border="0" cellpadding="0" cellspacing="0" width="100%">') + strlen('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
        $endsAt = strpos($Mal, '</table>', $startsAt);
        $table = substr($Mal, $startsAt, $endsAt - $startsAt);
        $table = str_replace_first('<tr>', '', $table);
        $table = str_replace_first('</tr>', '', $table);
        $totaljson = '[';

        //get each cell and parse it into json
        for ($i = 1; $i <= 20; $i++) {
            $cell = parse($table,'<tr>','</tr>');
            $table = str_replace_first('<tr>', '', $table);
            $table = str_replace_first('</tr>', '', $table);
        
            $id = parse($cell,'selected_series_id=','&hideLayout');
            $title = parse($cell,'<strong>','</strong>');
            $image_url = parse($cell,'src="',' border=');
            $image_url_large = str_replace("t.jpg", "l.jpg", $image_url);
        
            if (strpos($title,'"') !== false) {
                $title = str_replace_first('"', '', $title);
            }
            if (strpos($title,'"') !== false) {
                $title = str_replace_first('"', '', $title);
            }
        
            if (strpos($cell,'TV</td>') !== false) {
                $type = 'TV';
            }else if (strpos($cell,'Movie</td>') !== false) {
                $type = 'Movie';
            }else if (strpos($cell,'OVA</td>') !== false) {   
                $type = 'OVA';
            }else if (strpos($cell,'Special</td>') !== false) { 
                $type = 'Special';
            }else if (strpos($cell,"ONA</td>") !== false) { 
                $type = "ONA";
            }else if (strpos($cell,"Music</td>") !== false) { 
                $type = "Music";
            }
        
        $json = ',{"id":"'.$id.'","title":"'.$title.'","type":"'.$type.'","image_url":"'.$image_url.',"image_url_large":"'.$image_url_large.'}';
        $totaljson = $totaljson.$json;
        }
    
        //remove the first , and show the result to us(json))
        $totaljson = str_replace_first(',', '', $totaljson);
        if (strpos($totaljson,'url_short":","image_url":"}') !== false) {
            $totaljson = str_replace('url_short":","image_url":"}','url_short":"","image_url":""}',$totaljson);
        }
        if (strpos($totaljson,',{"id":"","title":"","type":"","image_url_short":","image_url":","episodes":"","scored":"","members":""}') !== false) {
            $totaljson = str_replace (',{"id":"","title":"","type":"","image_url_short":","image_url":","episodes":"","scored":"","members":""}','',$totaljson);
        }
        $totaljson = $totaljson.']';
    }
    echo $totaljson;
    //connect
    mysql_connect("mysql.2freehosting.com",$username,$password);
    @mysql_select_db($database) or die("Cache error: Unable to get the records!");
    
    if ($record == ""){
        if (strpos($totaljson,"'") !== false) {
            $totaljson = str_replace ("'","#token#",$totaljson);
        }
        mysql_query("INSERT INTO Justadded_anime VALUES ('".date("j").(($page+20)/20)."','".(($page+20)/20)."','".$totaljson."')") or die('');
    }elseif (strpos($totaljson,'[{"id":"","title":"","type":"","image_url_short":"","image_url":""}]') !== false){
        header('Location: http://atarashii.yzi.me/justadded/anime/'.(($page+20)/20));
    }else{
        mysql_query("UPDATE Justadded_anime SET Time='".date("j").(($page+20)/20)."' WHERE Time='".$time."'") or die("");
        mysql_query("UPDATE Justadded_anime SET Record='".$totaljson."' WHERE Record='".$record."'") or die("");  
    }
    mysql_close();
}elseif ($time == date("j").(($page+20)/20)){
    if (strpos($record,"#token#") !== false) {
            $record = str_replace ("#token#","'",$record);
    }
    echo $record;
}else{
    echo "<center><h1>Wrong page!</h1>Error: please enter the page to receive!</center>";
}
?>