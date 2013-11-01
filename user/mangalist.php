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
$result = mysql_query("SELECT * FROM Mangalist WHERE Page='".$pageencrypted."'") or die("Cache error: Unable to check the records!");
$row = mysql_fetch_array($result);
$time = $row['Time'];
$record = $row['Record'];

mysql_close();

if (empty($page) == false && time() > ($time + 300)){
    //request the html source
    $Mal = url("http://myanimelist.net/malappinfo.php?u=".$page."&status=all&type=manga");
    //remove empty lines
    $Mal = str_replace("\n", "", $Mal);
    $Mal = str_replace("\r", "", $Mal);
    $Mal = trim(preg_replace('/\t+/', '', $Mal));

    if (strpos($Mal,'Invalid username') !== false) {
        $totaljson = 'Page: '.$page.' has no data!';
    }else{
		$totaljson = '[';
        $count = substr_count ($Mal,'<manga>');

        //get each cell and parse it into json
        for ($i = 1; $i <= $count; $i++) {
            $cell = parse($Mal,'<manga>','</manga>');
            $Mal = str_replace_first('<manga>', '', $Mal);
            $Mal = str_replace_first('</manga>', '', $Mal);
                
            $id = parse($cell,'<series_mangadb_id>','</series_mangadb_id>');
            $title = parse($cell,'<series_title>','</series_title>');
            $type = parse($cell,'<series_type>','</series_type>');
            If ($type == '1'){
                $type = 'Manga';
            }elseif ($type == '2'){
                $type = 'Novel';
            }elseif ($type == '3'){
                $type = 'One Shot';
            }elseif ($type == '4'){
                $type = 'Doujin';
            }elseif ($type == '5'){
                $type = 'Manwha';
            }elseif ($type == '6'){
                $type = 'Manhua';
            }elseif ($type == '7'){
                $type = 'OEL';
            }
            $episodes = parse($cell,'<series_chapters>','</series_chapters>');
            $volumes = parse($cell,'<series_volumes>','</series_volumes>');
            $status = parse($cell,'<series_status>','</series_status>');
            If ($status == '2'){
                $status = 'finished';
            }elseif ($status == '1'){
                $status = 'publishing';
            }elseif ($status == '3'){
                $status = 'not yet published';
            }
            $startdate = parse($cell,'<series_start>','</series_start>');
            $enddate = parse($cell,'<series_end>','</series_end>');
            $image_url = parse($cell,'<series_image>','</series_image>');
            $episodeswatched = parse($cell,'<my_read_chapters>','</my_read_chapters>');
            $vilumeswatched = parse($cell,'<my_read_volumes>','</my_read_volumes>');
            $mystartdate = parse($cell,'<my_start_date>','</my_start_date>');
            $myenddate = parse($cell,'<my_finish_date>','</my_finish_date>');
            $score = parse($cell,'<my_score>','</my_score>');
            $mystatus = parse($cell,'<my_status>','</my_status>');
            If ($mystatus == '1'){
                $mystatus = 'Reading';
            }elseif ($mystatus == '2'){
                $mystatus = 'Completed';
            }elseif ($mystatus == '3'){
                $mystatus = 'On-hold';
            }elseif ($mystatus == '4'){
                $mystatus = 'Dropped';
            }elseif ($mystatus == '6'){
                $mystatus = 'Plan to Read';
            }
            
            $json = ',{"id":"'.$id.'","title":"'.$title.'","type":"'.$type.'","chapters":"'.$episodes.'","volumes":"'.$volumes.'","status":"'.$status.'","start_date":"'.$startdate.'","end_date":"'.$enddate.'","read_status":"'.$mystatus.'","chapters_read":"'.$episodeswatched.'","volumes_read":"'.$vilumeswatched.'","my_startdate":"'.$mystartdate.'","my_enddate":"'.$myenddate.'","score":"'.$score.'","image_url":"'.$image_url.'"}';
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
        mysql_query("INSERT INTO Mangalist VALUES ('".time()."','".$pageencrypted."','".Encrypt($totaljson)."')") or die("");
    }else{
        mysql_query("UPDATE Mangalist SET Time='".time()."' WHERE Time='".$time."'") or die("");
        mysql_query("UPDATE Mangalist SET Record='".Encrypt($totaljson)."' WHERE Record='".$record."'") or die("");  
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