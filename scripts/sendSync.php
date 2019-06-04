<?php
require_once('mysqliset.php');
$stm = "SELECT ID FROM sync WHERE ID = 1";
$query = mysqli_query($db, $stm);
if(mysqli_fetch_object($query) == false) {
    $stm = "INSERT INTO `sync`(`ID`, `Curplid`, `Selplid`, `Time`, `Status`) VALUES (NULL, '$vidid', '$selplid', '$time', '$status')";
    mysqli_query($db, $stm);
} else {
    //If time shall be updated
    if(isset($_GET['selplid'])) {
        $selplid = $_GET['selplid'];
         $stm = "UPDATE `sync` SET `Selplid`='$selplid' WHERE ID= 1";
        mysqli_query($db, $stm);
        echo $_GET['selplid'];
    }
    // If Video shall be updated
    else if(isset($_GET['vidid'])) {
        $vidid = $_GET['vidid'];
        $stm = "UPDATE `sync` SET `Curplid`='$vidid' WHERE ID= 1";
        mysqli_query($db, $stm);
        echo $_GET['vidid'];

    }
    // If Positiontime shall be updated
    else if(isset($_GET['time'])) {
        $time = $_GET['time'];
        $stm = "UPDATE `sync` SET `Time`='$time' WHERE ID= 1";
        mysqli_query($db, $stm);
        echo $_GET['time'];
        
    }
    // If Status shall be updated
    else if(isset($_GET['status'])) {
        $status = $_GET['status'];
        $stm = "UPDATE `sync` SET `Status`='$status' WHERE ID= 1";
        mysqli_query($db, $stm);
        echo $_GET['status'];
        
    }
    else if(isset($_GET['change'])) {
        $change = $_GET['change'];
        $stm = "UPDATE `sync` SET `UpdateChange`='$change' WHERE ID= 1";
        mysqli_query($db, $stm);
        echo $_GET['change'];
    }
}

?>