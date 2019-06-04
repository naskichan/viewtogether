<?php
require("mysqliset.php");
$uname = $_GET['username'];
$time = $_GET['time'];
mysqli_query($db, "DELETE FROM users WHERE Name='$uname'");
mysqli_query($db, "UPDATE `sync` SET `Time`='$time' WHERE ID= 1");
?>