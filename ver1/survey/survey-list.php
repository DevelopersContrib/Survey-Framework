<div id="menu">

<h3>Other Surveys</h3>
<ul>
 <?php if (count($surv)>0):?>
     <?php for ($i=0;$i<count($surv);$i++):?>
        <li><a href="index.php?sid=<?php echo $surv[$i]['link']?>"><?php echo $surv[$i]['title']?></a></li>
        <?php endfor;?>
 <?php endif;?>
</ul>



<h3><?php echo ucfirst($sitename)?> News</h3>
<ul>
<?php 
$search = "https://api.twitter.com/1/statuses/user_timeline.rss?screen_name=domaindirectory";
$tw = curl_init();
curl_setopt($tw, CURLOPT_URL, $search);
curl_setopt($tw, CURLOPT_RETURNTRANSFER, TRUE);
$twi = curl_exec($tw);
$search_res = new SimpleXMLElement($twi);
$j=0;
foreach($search_res->channel->item as $twit1) {
	if ($j<10){
	?>
	  <li><a href="<?php echo $twit1->guid?>" target="_blank"><?php echo str_replace("Domaindirectory:","", substr(ucfirst($twit1->title),0, 40))  . "...";?></a></li>
	<?php 
	}
	$j++;
}

?>
</ul>
</div>

