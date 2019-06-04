<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
include_once('mysqliset.php');
$stm = "SELECT * FROM sync";
$row = mysqli_fetch_object(mysqli_query($db, $stm));
echo "data: ;$row->Curplid;$row->Selplid;$row->Time;$row->Status;$row->UpdateChange \n\n";
flush();
?>