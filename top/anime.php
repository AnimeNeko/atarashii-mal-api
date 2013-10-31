<?php

/**
 * @author ratan12
 * @copyright 2013
 */
 
include('../Methodes.php');
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
$page = (($_GET['p']*30)-30);

// Get a specific result from the table
$result = mysql_query("SELECT * FROM Top_anime WHERE Page='".(($page+30)/30)."'") or die("Cache error: Unable to check the records!");
$row = mysql_fetch_array($result);
$time = $row['Time'];
$record = $row['Record'];

mysql_close();

if ($page >= 0 && $time!= (date("j").(($page+30)/30))){
    //request the html source
    $Mal = url("http://myanimelist.net/topanime.php?type=&limit=".$page);
    
    if (strpos($Mal,'No anime titles found') !== false) {
        $totaljson = 'Page: '.(($page+30)/30).' has no data!';
    }else{
        //remove empty lines
        $Mal = str_replace("\n", "", $Mal);
        $Mal = str_replace("\r", "", $Mal);
        $Mal = trim(preg_replace('/\t+/', '', $Mal));

        //isolate the table
        $startsAt = strpos($Mal,'<table border="0" cellpadding="0" cellspacing="0" width="100%">') + strlen('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
        $endsAt = strpos($Mal, '</table>', $startsAt);
        $table = substr($Mal, $startsAt, $endsAt - $startsAt);
        $totaljson = '[';

        //get each cell and parse it into json
        for ($i = 1; $i <= 30; $i++) {
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
        
            if (strpos($cell,'TV,') !== false) {
                $type = 'TV';
            }else if (strpos($cell,'Movie,') !== false) {
                $type = 'Movie';
            }else if (strpos($cell,'OVA,') !== false) {   
                $type = 'OVA';
            }else if (strpos($cell,'Special,') !== false) { 
                $type = 'Special';
            }else if (strpos($cell,"ONA,") !== false) { 
                $type = "ONA";
            }else if (strpos($cell,"Music,") !== false) { 
                $type = "Music";
            }
            $episodes = parse($cell,$type.', ',' eps,');
            $scored = parse($cell,'scored ','<br /><span');
            $members = parse($cell,'<br /><span class="lightLink">',' members');
            $json = ',{"id":"'.$id.'","title":"'.$title.'","type":"'.$type.'","image_url_short":"'.$image_url.',"image_url":"'.$image_url_large.',"episodes":"'.$episodes.'","scored":"'.$scored.'","members":"'.$members.'"}';
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
        mysql_query("INSERT INTO Top_anime VALUES ('".date("j").(($page+30)/30)."','".(($page+30)/30)."','".$totaljson."')") or die("");
    }elseif (strpos($totaljson,'[{"id":"","title":"","type":"","image_url_short":"","image_url":""}]') !== false){
        header('Location: http://atarashii.yzi.me/top/anime/'.(($page+30)/30));
    }else{
        mysql_query("UPDATE Top_anime SET Time='".date("j").(($page+30)/30)."' WHERE Time='".$time."'") or die("");
        mysql_query("UPDATE Top_anime SET Record='".$totaljson."' WHERE Record='".$record."'") or die("");  
    }
    mysql_close();
}elseif ($time == date("j").(($page+30)/30)){
    if (strpos($record,"#token#") !== false) {
            $record = str_replace ("#token#","'",$record);
    }
    echo $record;
}else{
    echo "<center><h1>Wrong page!</h1>Error: please enter the page to receive!</center>";
}
?>