<?php
//$link = mysql_connect("localhost","artsurve_maida","bing2k");
//$link = mysql_connect("localhost","msurvey_admin","bing2kroy");
$link = mysql_connect("localhost","artsurve","MH4Aytqv");
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
echo 'Connected successfully';

mysql_select_db("artsurve_survey");
$result = mysql_query("SELECT * FROM user",$link);
var_dump($result);
while($row = mysql_fetch_array($result)) {
  echo $row['email'];
  echo "<br>";
}

mysql_close($link);
?>