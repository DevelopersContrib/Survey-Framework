<?php
	include (dirname(dirname(__FILE__))."/config.php");	
	require_once("../includes/Config.php");	

	// Set up some admin variables
	include (ROOT."/includes/Admin.class.php");	
	$admin = new Admin($config,$db);
	$admin->run();	
?>