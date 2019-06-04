<?php
require("mysqliset.php");
$query = mysqli_query($db, "SELECT Name FROM users");
while($row = mysqli_fetch_object($query)) {
    ?> <div class="user"><img class="userimg" src="./img/userImg.png"> <?php echo $row->Name; ?></div> <?php
}
?>