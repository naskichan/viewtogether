<?php
require_once("mysqliset.php");
if(!$db) {
    exit("Verbindungsfehler: ".mysqli_connect_error());
}
$id = $_GET['id'];
$stm = "DELETE FROM `entries` WHERE ID = $id";
mysqli_query($db, $stm);
?>