<div id="wrap">
<div id="header">
<?php if ($logo!=""){?>
     <img src="<?php echo $logo?>" style="height:90px;width:350px;padding:5px;">
<?php }else {?>
   <h1><?php echo ucfirst($sitename)?></h1>
<?php }?>
</div>

<div id="topmenu">
<div class="search">
<input type="text" style="width:220px" />
   <input type="submit" value="Search" />
</div>
<ul>
<li><a href="index.html">Home</a></li>
<li><a href="about.html">About Us</a></li>
<li><a href="contact.html">Contact Us</a></li>
</ul>
</div>