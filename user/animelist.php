<?php

/**
 * @author ratan12
 * @copyright 2013
 */
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
$result = mysql_query("SELECT * FROM Animelist WHERE Page='".$pageencrypted."'") or die("Cache error: Unable to check the records!");
$row = mysql_fetch_array($result);
$time = $row['Time'];
$record = $row['Record'];

mysql_close();

if (empty($page) == false && time() > ($time + 300)){
    //request the html source
    $Mal = url("http://myanimelist.net/malappinfo.php?u=".$page."&status=all&type=anime");
    //remove empty lines
    $Mal = str_replace("\n", "", $Mal);
    $Mal = str_replace("\r", "", $Mal);
    $Mal = trim(preg_replace('/\t+/', '', $Mal));

    if (strpos($Mal,'Invalid username') !== false) {
        $totaljson = 'Page: '.$page.' has no data!';
    }else{
		$totaljson = '[';
        $count = substr_count ($Mal,'<anime>');

        //get each cell and parse it into json
        for ($i = 1; $i <= $count; $i++) {
            $cell = parse($Mal,'<anime>','</anime>');
            $Mal = str_replace_first('<anime>', '', $Mal);
            $Mal = str_replace_first('</anime>', '', $Mal);
                
            $id = parse($cell,'<series_animedb_id>','</series_animedb_id>');
            $title = parse($cell,'<series_title>','</series_title>');
            $type = parse($cell,'<series_type>','</series_type>');
            If ($type == '1'){
                $type = 'TV';
            }elseif ($type == '2'){
                $type = 'OVA';
            }elseif ($type == '3'){
                $type = 'Movie';
            }elseif ($type == '4'){
                $type = 'Special';
            }elseif ($type == '5'){
                $type = 'ONA';
            }elseif ($type == '6'){
                $type = 'Music';
            }
            $episodes = parse($cell,'<series_episodes>','</series_episodes>');
            $status = parse($cell,'<series_status>','</series_status>');
            If ($status == '1'){
                $status = 'Finished airing';
            }elseif ($status == '2'){
                $status = 'Currently airing';
            }elseif ($status == '3'){
                $status = 'not yet aired';
            }
            $startdate = parse($cell,'<series_start>','</series_start>');
            $enddate = parse($cell,'<series_end>','</series_end>');
            $image_url = parse($cell,'<series_image>','</series_image>');
            $episodeswatched = parse($cell,'<my_watched_episodes>','</my_watched_episodes>');
            $mystartdate = parse($cell,'<my_start_date>','</my_start_date>');
            $myenddate = parse($cell,'<my_finish_date>','</my_finish_date>');
            $score = parse($cell,'<my_score>','</my_score>');
            $mystatus = parse($cell,'<my_status>','</my_status>');
            If ($mystatus == '1'){
                $mystatus = 'Watching';
            }elseif ($mystatus == '2'){
                $mystatus = 'Completed';
            }elseif ($mystatus == '3'){
                $mystatus = 'On-hold';
            }elseif ($mystatus == '4'){
                $mystatus = 'Dropped';
            }elseif ($mystatus == '6'){
                $mystatus = 'Plan to Watch';
            }
            
            $json = ',{"id":"'.$id.'","title":"'.$title.'","type":"'.$type.'","episodes":"'.$episodes.'","status":"'.$status.'","start_date":"'.$startdate.'","end_date":"'.$enddate.'","watched_status":"'.$mystatus.'","watched_episodes":"'.$episodeswatched.'","my_startdate":"'.$mystartdate.'","my_enddate":"'.$myenddate.'","score":"'.$score.'","image_url":"'.$image_url.'"}';
            $totaljson = $totaljson.$json;
        }
    
        //remove the first , and show the result to us(json))
        $totaljson = str_replace_first(',', '', $totaljson);
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
        mysql_query("INSERT INTO Animelist VALUES ('".time()."','".$pageencrypted."','".Encrypt($totaljson)."')") or die("");
    }else{
        mysql_query("UPDATE Animelist SET Time='".time()."' WHERE Time='".$time."'") or die("");
        mysql_query("UPDATE Animelist SET Record='".Encrypt($totaljson)."' WHERE Record='".$record."'") or die("");  
    }
    mysql_close();
}elseif (time() < ($time + 300)){
    if (strpos($record,"#token#") !== false) {
            $record = str_replace ("#token#","'",$record);
    }
    echo Decrypt($record);
}else{
    echo "<center><h1>Wrong page!</h1>Error: please enter the page to receive!</center>";
}