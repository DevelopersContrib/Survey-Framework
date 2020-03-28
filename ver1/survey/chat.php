<?php include "header.php"?>
<?php include "header-top.php"?>

<?php
	 $ac_link = mysql_connect("localhost","mychanne_maida","bing2k") or die("Unable to connect to database.");
	 $ac_link_r = mysql_select_db("mychanne_page",$ac_link) or die("Could not select database.");
	
	$topic = ucfirst(str_replace('.com','',$sitename));
	
	$query = MYSQL_QUERY("SELECT * FROM ac_rooms WHERE roomname = '$topic' ") OR DIE(MYSQL_ERROR());
	if(MYSQL_NUM_ROWS($query) == 0){
		MYSQL_QUERY("INSERT INTO ac_rooms(roomname) VALUES('$topic')") OR DIE(MYSQL_ERROR());
	}
?>

<div id="content">
<h2>Chat with <?php echo ucfirst($sitename)?> </h2>
<iframe name="ajchat" src="http://ajchat.mychannel.com/schat/<?=$topic?>?des=" width="450px" height="400px" scrolling="yes" frameborder="0" style="border: 1px solid #4260BF;"></iframe>			

<div class="clear"> </div>

</div>
<?php include "survey-list.php"?>
<div class="clear"> </div>

<?php include "footer.php"?>