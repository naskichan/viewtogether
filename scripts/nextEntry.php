<?php
require_once('mysqliset.php');
$selplid = $_GET['selplid'];
$curplid = $_GET['curplid'];

$cnt = 0;
if(empty($curplid)) {
    $sql = "SELECT Entryname, VidId FROM entries WHERE FK_playlists_ID = $selplid ";
    $query = mysqli_query($db, $sql);
    while($row = mysqli_fetch_object($query)) {
        if($cnt == 0) {
            echo $row->VidId;
            $cnt = 1;
        }
    }
} else {
    $stm = "SELECT ID, VidId FROM (SELECT * FROM `entries` WHERE FK_playlists_ID = $selplid) AS T WHERE ID = (select min(ID) from entries where ID > (SELECT ID FROM entries WHERE VidId = '$curplid'))";
    echo $stm;
    $que = mysqli_query($db, $stm);
    if(mysql_num_rows($query) == 1) {
        echo "voll";
        $row = mysqli_fetch_object($query);
        echo $row->VidId;
    } else {
        $sql = "SELECT ID, VidId FROM (SELECT * FROM `entries` WHERE FK_playlists_ID = $selplid) AS T ORDER BY ID ASC LIMIT 1";
        echo "leer";
        $query = mysqli_query($db, $sql);
        $row = mysqli_fetch_object($query);
        echo $row->VidId;
    }
}

?>