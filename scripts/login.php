<?php
require("mysqliset.php");
$uname = $_GET["username"];
$go = 0;
$name = "";
if($uname == "guest") {
    while($go == 0) {
        $rand = substr(md5(microtime()),rand(0,26),5);
        $name = "User-".$rand;
        $query = mysqli_query($db, "SELECT ID FROM users WHERE Name='$name'");
        if(mysqli_fetch_object($query) == false) {
            $go = 1;
        }
    }
} else {
    $name = $uname;
}
mysqli_query($db, "INSERT INTO `users`(`ID`, `Name`) VALUES (NULL, '$name')");
echo $name;

?>