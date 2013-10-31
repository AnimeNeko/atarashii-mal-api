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

include('../Requesthandler.php');

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
$result = mysql_query("SELECT * FROM Upcoming_anime WHERE Page='".(($page+20)/20)."'") or die("Cache error: Unable to check the records!");
$row = mysql_fetch_array($result);
$time = $row['Time'];
$record = $row['Record'];

mysql_close();

if ($page >= 0 && $time!= (date("j").(($page+20)/20))){
    //request the html source
    $Mal = url("http://myanimelist.net/anime.php?sd=".date("j")."&sm=".date("n")."&sy=".date("y")."&em=0&ed=0&ey=0&o=2&w=&c[0]=a&c[1]=d&cv=1&q=&tag=&show=".$page);
    
    if (strpos($Mal,'No titles that matched your query were found.') !== false) {
        $totaljson = 'Page: '.(($page+20)/20).' has no data!';
    }else{
        //remove empty lines
        $Mal = str_replace("\n", "", $Mal);
        $Mal = str_replace("\r", "", $Mal);
        $Mal = trim(preg_replace('/\t+/', '', $Mal));

        //isolate the table
        $Mal = trim(preg_replace('/\t+/', '', $Mal));
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
            $json = ',{"id":"'.$id.'","title":"'.$title.'","type":"'.$type.'","image_url_short":"'.$image_url.',"image_url":"'.$image_url_large.'}';
            $totaljson = $totaljson.$json;
        }
    
        //remove the first , and show the result to us(json))
        $totaljson = str_replace_first(',', '', $totaljson);
        if (strpos($totaljson,'url_short":","image_url":"}') !== false) {
            $totaljson = str_replace('url_short":","image_url":"}','url_short":"","image_url":""}',$totaljson);
        }
        if (strpos($totaljson,',{"id":"","title":"","type":"","image_url_short":"","image_url":""}') !== false) {
            $totaljson = str_replace (',{"id":"","title":"","type":"","image_url_short":"","image_url":""}','',$totaljson);
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
        mysql_query("INSERT INTO Upcoming_anime VALUES ('".date("j").(($page+20)/20)."','".(($page+20)/20)."','".$totaljson."')") or die("");
    }elseif (strpos($totaljson,'[{"id":"","title":"","type":"","image_url_short":"","image_url":""}]') !== false){
        header('Location: http://atarashii.yzi.me/upcoming/anime/'.(($page+20)/20));
    }else{
        mysql_query("UPDATE Upcoming_anime SET Time='".date("j").(($page+20)/20)."' WHERE Time='".$time."'") or die("");
        mysql_query("UPDATE Upcoming_anime SET Record='".$totaljson."' WHERE Record='".$record."'") or die("");  
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