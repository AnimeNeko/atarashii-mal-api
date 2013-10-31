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
$page = (($_GET['p']*20)-20);

// Get a specific result from the table
$result = mysql_query("SELECT * FROM Justadded_manga WHERE Page='".(($page+20)/20)."'") or die("Cache error: Unable to check the records!");
$row = mysql_fetch_array($result);
$time = $row['Time'];
$record = $row['Record'];

mysql_close();

if ($page >= 0 && $time!= (date("j").(($page+20)/20))){
    //request the html source
    $Mal = url("http://myanimelist.net/manga.php?o=9&c[0]=a&c[1]=d&cv=2&q=&sm=0&sd=0&em=0&ed=0&show=".$page);
    
    if (strpos($Mal,'No manga titles found') !== false) {
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
            $totaljson = $totaljson.$json.']';
        }
        echo $totaljson;
        //connect
        mysql_connect("mysql.2freehosting.com",$username,$password);
        @mysql_select_db($database) or die("Cache error: Unable to get the records!");
    
        if ($record == ""){
            if (strpos($totaljson,"'") !== false) {
                $totaljson = str_replace ("'","#token#",$totaljson);
            }
            mysql_query("INSERT INTO Justadded_manga VALUES ('".date("j").(($page+20)/20)."','".(($page+20)/20)."','".$totaljson."')") or die('');
        }elseif (strpos($totaljson,'[{"id":"","title":"","type":"","image_url_short":"","image_url":""}]') !== false){
            header('Location: http://atarashii.yzi.me/justadded/manga/'.(($page+20)/20));
        }else{
            mysql_query("UPDATE Justadded_manga SET Time='".date("j").(($page+20)/20)."' WHERE Time='".$time."'") or die("");
            mysql_query("UPDATE Justadded_manga SET Record='".$totaljson."' WHERE Record='".$record."'") or die("");  
        }
        mysql_close();
    }
}elseif ($time == date("j").(($page+20)/20)){
    if (strpos($record,"#token#") !== false) {
            $record = str_replace ("#token#","'",$record);
    }
    echo $record;
}else{
    echo "<center><h1>Wrong page!</h1>Error: please enter the page to receive!</center>";
}
?>