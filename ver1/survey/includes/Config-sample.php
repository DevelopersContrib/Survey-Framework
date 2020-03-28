<?php 

// Database Configuration
  $dbinfo= array (
    "host" => 'RHOST',        // Your mySQL Host (usually Localhost)
    "db" => 'RDB',            // The database where you have dumped the included sql file
    "user" => 'RUSER',        // Your mySQL username
    "password" => 'RPASS',    //  Your mySQL Password 
    "prefix" => 'RPRE'      // Prefix for your tables e.g. short_ if you are using same db for multiple scripts
  );

  $config = array(
    // Timezone
    "timezone" => "RTZ",

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
  "security" => 'RKEY',	 // !!!! DON'T CHANGE THIS IF YOUR SITE IS LIVE
  "public_token" => 'RPUB', // Please don't change this, this is randomly generated

  "debug" => 0,   // Enable debug mode (outputs errors) - 0 = OFF, 1 = Error message, 2 = Error + Queries (Don't enable this if your site is live!)
  "demo" => 0 // Demo mode
  );

// Include core.php
include ('Core.php');
?>