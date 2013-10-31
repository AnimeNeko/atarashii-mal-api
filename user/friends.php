<?php

/**
 * @author ratan12
 * @copyright 2013
 */
 
include('../Requesthandler.php');
include('../Methodes.php');

//connect database
$username="u232407816_cache";
$password="wakkazakka1";
$database="u232407816_cache";

//connect
mysql_connect("mysql.2freehosting.com",$username,$password);
@mysql_select_db($database) or die("Cache error: Unable to get the records!");

//get the page numer and confrim
$page = '';
$page = ($_GET['p']);

$pageencrypted = Encrypt($page);

// Get a specific result from the table
$result = mysql_query("SELECT * FROM Friendlist WHERE Page='".$pageencrypted."'") or die("Cache error: Unable to check the records!");
$row = mysql_fetch_array($result);
$time = $row['Time'];
$record = $row['Record'];

mysql_close();

if (empty($page) == false && time() > ($time + 120)){
    //request the html source
    $Mal = url("http://myanimelist.net/profile/".$page."/friends");
    
    //remove empty lines
    $Mal = str_replace("\n", "", $Mal);
    $Mal = str_replace("\r", "", $Mal);
    $Mal = trim(preg_replace('/\t+/', '', $Mal));

    if (strpos($Mal,'Failed to find the specified user, please try again') !== false) {
        $totaljson = 'Page: '.$page.' has no data!';
    }else{
		$totaljson = '[';
        //isolate the table
        $startsAt = strpos($Mal,'<div class="majorPad">') + strlen('<div class="majorPad">');
        $endsAt = strpos($Mal, '</div></div></div>  </td>', $startsAt);
        $table = substr($Mal, $startsAt, $endsAt - $startsAt);
        $table = str_replace('</div><div><a href=', '<a href=', $table);
        $table = str_replace('</a></div><div>', '</a>', $table);
        $table = str_replace_first('friendHolder', '', $table);
        $table = $table.'</div></div>friendHolder';
    
        $count = substr_count ($table,'friendBlock');

        //get each cell and parse it into json
        for ($i = 1; $i <= $count; $i++) {
            $cell = parse($table,'<div class="friendBlock">','friendHolder');
            $table = str_replace_first('<div class="friendBlock">', '', $table);
            $table = str_replace_first('friendHolder', '', $table);
                
            $name = parse($cell,'<strong>','</strong>');
            $last_online = parse($cell,'</strong></a>','</div><div>');
            if (strpos($cell,'since') !== false) {
                $since = parse($cell,'Friends since ','</div></div></div>');
            }else{
                $since = "Unknown";
            }
            $image_url = parse($cell,'src="',' border=');
            $image_url_large = str_replace("thumbs/", "", $image_url);
            $image_url_large = str_replace("_thumb", "", $image_url_large);
        
            $json = ',{"name":"'.$name.'","last_online":"'.$last_online.'","since":"'.$since.'","image_url_short":"'.$image_url.',"image_url":"'.$image_url_large.'}';
            $totaljson = $totaljson.$json;
        }
    
        //remove the first , and show the result to us(json))
        $totaljson = str_replace_first(',', '', $totaljson);
        if (strpos($totaljson,'url_short":","image_url":"}') !== false) {
            $totaljson = str_replace ('url_short":","image_url":"}','url_short":"","image_url":""}',$totaljson);
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
        mysql_query("INSERT INTO Friendlist VALUES ('".time()."','".$pageencrypted."','".Encrypt($totaljson)."')") or die("");
    }elseif (strpos($totaljson,'[{"id":"","title":"","type":"","image_url_short":"","image_url":""}]') !== false){
        header('Location: http://atarashii.yzi.me/upcoming/manga/'.$page);
    }else{
        mysql_query("UPDATE Friendlist SET Time='".time()."' WHERE Time='".$time."'") or die("");
        mysql_query("UPDATE Friendlist SET Record='".Encrypt($totaljson)."' WHERE Record='".$record."'") or die("");  
    }
    mysql_close();
}elseif (time() < ($time + 120)){
    if (strpos($record,"#token#") !== false) {
            $record = str_replace ("#token#","'",$record);
    }
    echo Decrypt($record);
}else{
    echo "<center><h1>Wrong page!</h1>Error: please enter the page to receive!</center>";
}