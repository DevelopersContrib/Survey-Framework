<?php
$host="localhost"; 
$root=$cpanel_username; 
$root_password=$cpanel_password; 

$user=$cpanel_username."_maida";
$pass='bing2k';

if($flag_sub==1)
		$db=$cpanel_username."_survey_".$subname; //SUBDOMAIN
else
	$db=$cpanel_username."_survey";  			//DOMAIN
	
/*echo 'cpanel_username ';var_dump($cpanel_username);echo '<br>';
echo 'cpanel_password ';var_dump($cpanel_password);echo '<br>';
echo 'db ';var_dump($db);echo '<br>';
echo 'user ';var_dump($user);echo '<br>';
echo 'pass ';var_dump($pass);echo '<br>';
*/
try {
	$dbh = new PDO("mysql:host=$host", $cpanel_username, $cpanel_password);
	$dbh->exec("CREATE DATABASE IF NOT EXISTS `$db`;
			CREATE USER '$user'@'localhost' IDENTIFIED BY '$pass';
			GRANT ALL ON `$db`.* TO '$user'@'localhost';
			FLUSH PRIVILEGES;
		 "); 
	 
	//or die(print_r($dbh->errorInfo(), true));
	$output = shell_exec("mysql -u$cpanel_username -p$cpanel_password $db < msurvey.sql");
	//include "execute_sql_file.php"; //temp 
	
} catch (PDOException $e) {
	die("DB ERROR: ". $e->getMessage());
}

 //die();
$conn = new PDO("mysql:host=$host;dbname=$db", $cpanel_username, $cpanel_password);
 
$sql = " Update `setting` set `var`=? where `config`=?";
$q = $conn->prepare($sql);
$q->execute(array('http://'.$sitename,'url')); 

$sql = " Update `setting` set `var`=? where `config`=?";
$q = $conn->prepare($sql);
$q->execute(array($title,'title')); 

$sql = " Update `setting` set `var`=? where `config`=?";
$q = $conn->prepare($sql);
$q->execute(array($desc,'description')); 

$sql = " Update `setting` set `var`=? where `config`=?";
$q = $conn->prepare($sql);
$q->execute(array($logo,'logo')); 
?>