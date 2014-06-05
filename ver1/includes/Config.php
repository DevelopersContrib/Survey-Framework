<?php 

// Database Configuration
  $dbinfo= array (
    "host" => 'localhost',        // Your mySQL Host (usually Localhost)
    "db" => 'msurvey_survey',            // The database where you have dumped the included sql file
    "user" => 'msurvey_admin',        // Your mySQL username
    "password" => 'bing2kroy',    //  Your mySQL Password 
    "prefix" => ''      // Prefix for your tables e.g. short_ if you are using same db for multiple scripts
  );

  $config = array(
    // Timezone
    "timezone" => "America/Los_Angeles",

    // Enable mode_rewrite? e.g. user/login instead of index.php?action=user&do=login
    "mod_rewrite" => TRUE,

    // Enable Compression? Makes your website faster
    "gzip" => TRUE,
    /*
     ====================================================================================
     *  Security Key & Token - Please don't change this if your site is live.
     * ----------------------------------------------------------------------------------
     *  - Setup a security phrase - This is used to encode some important user 
     *    information such as password. The longer the key the more secure they are.
     *
     *  - If you change this, many things such as user login and even admin login will 
     *    fail.
     ====================================================================================
    */
  "security" => '55320y5l9vvC1s1pCezD7pewi1cxdUwQ',	 // !!!! DON'T CHANGE THIS IF YOUR SITE IS LIVE
  "public_token" => 'fbe45103ad4b1ddc0ef5465bd1e1d716', // Please don't change this, this is randomly generated

  "debug" => 0,   // Enable debug mode (outputs errors) - 0 = OFF, 1 = Error message, 2 = Error + Queries (Don't enable this if your site is live!)
  "demo" => 0 // Demo mode
  );

// Include core.php
include ('Core.php');
?>