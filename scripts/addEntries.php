 <?php 
require_once("mysqliset.php");
if(!$db) {
    exit("Verbindungsfehler: ".mysqli_connect_error());
}
$vidid = $_GET['vidid'];
$selplid = $_GET['selplid'];
$url = "https://www.youtube.com/watch?v=".$vidid;
$page = file_get_contents($url); 
$doc = new DOMDocument(); 
$doc->loadHTML($page);  
$title_div = $doc->getElementById('eow-title'); 
$title = $title_div->nodeValue;
$stm = "SELECT ID FROM entries WHERE VidId = $vidid";
$query = mysqli_query($db, $stm);
if(mysqli_fetch_object($query) == false) {
    $stm = "INSERT INTO `entries`(`ID`, `FK_playlists_ID`, `Entryname`, `VidId`, `imglink`) VALUES (NULL, '$selplid', '$title', '$vidid', '$vidid')";
    $query = mysqli_query($db, $stm);
}
?>