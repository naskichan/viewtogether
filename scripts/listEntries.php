<?php
$selplid = $_GET['selplid'];
require_once("mysqliset.php");
if(!$db) {
    exit("Verbindungsfehler: ".mysqli_connect_error());
}
$stm = "SELECT * FROM entries WHERE FK_playlists_ID=$selplid";
$query = mysqli_query($db, $stm);
while($row = mysqli_fetch_object($query)) {
    echo '<div class=entry id='.$row->VidId.'> <div class=aligner onClick=play("'.$row->VidId.'",1,0) cursor=> <img class="imglist entryelem" onerror=this.src="img/ph.png" src=http://img.youtube.com/vi/'.$row->imglink.'/0.jpg> <div class="text entryelem" >'.$row->Entryname.' </div> </div> </a> <a class=delete href=javascript:void(0) onclick=delentry("'.$row->ID.'")><div class=cross>&times</div></a> </div>';
}

?>