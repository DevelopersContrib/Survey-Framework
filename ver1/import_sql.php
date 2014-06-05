<?php
$host="localhost"; 
$root=$cpanel_username; 
$root_password=$cpanel_password; 

$user=$cpanel_username."_maida";
$pass='bing2k';

if($flag_sub==1)
	$db=$cpanel_username."_".$subname; //SUBDOMAIN
else
	$db=$cpanel_username; 			//DOMAIN



    try {
        $dbh = new PDO("mysql:host=$host", $cpanel_username, $cpanel_password);

        $dbh->exec("CREATE DATABASE IF NOT EXISTS `$db`;
                CREATE USER '$user'@'localhost' IDENTIFIED BY '$pass';
                GRANT ALL ON `$db`.* TO '$user'@'localhost';
                FLUSH PRIVILEGES;
             "); 
        
        //or die(print_r($dbh->errorInfo(), true));
        
       system("mysql -u$cpanel_username -p$cpanel_password $db < wp.sql");     

    } catch (PDOException $e) {
        die("DB ERROR: ". $e->getMessage());
    }
    
    
       $conn = new PDO("mysql:host=$host;dbname=$db", $cpanel_username, $cpanel_password);

	 
			$sql = " Update `setting` set var=? where config=?";
			$q = $conn->prepare($sql);
			$q->execute(array('http://'.$_SERVER['HTTP_HOST'],'url')); 
		   
			$sql = " Update `setting` set var=? where config=?";
			$q = $conn->prepare($sql);
			$q->execute(array($title,'title')); 
	
			$sql = " Update `setting` set var=? where config=?";
			$q = $conn->prepare($sql);
			$q->execute(array($desc,'description')); 
	  
			$sql = " Update `setting` set var=? where config=?";
			$q = $conn->prepare($sql);
			$q->execute(array($logo,'logo')); 
?>