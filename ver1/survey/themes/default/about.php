<?php defined("APP") or die() ?>
<style>
    #container2 p{text-align:justify !important}
</style>
<style> 
footer {
	position: absolute;
	bottom: -10px;
	width: 100%;
	background-color: #f5f5f5;
}
.post {
	background: rgb(245, 245, 245) none repeat scroll 0% 0%;
	padding: 20px 40px 40px;
	margin-top: 50px;
} 
</style>
<?php
	global $domain, $domainid;
?>
<?php
	$url = "http://api2.contrib.com/request/getdomainaffiliateid?domain=$domain&key=".md5($domain);
	
	$curl = curl_init();
	curl_setopt ($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec ($curl);
	curl_close ($curl);

	$result = json_decode($result);
	$domain_affiliate_id = $result->data->affiliate_id;
?>
<section>
	<div class="container">
       <div class="col-md-8 col-md-offset-2">
        <div class="post">
          <h3>About <?php echo $this->config["domain"] ?></h3>
          <article>
            <p><?php echo ucfirst($domain) ?> Survey Platform is part of the Global Ventures Network.</p><p>Founded in 1996, Global Ventures is the worlds largest virtual Domain Development Incubator on the planet.</p><p>We create and match great domain platforms like the survey platform with talented people, applications and resources to build successful, value driven, web-based businesses quickly. Join the fastest growing Virtual Business Network and earn Equity and Cowork with other great people making a difference by joining us here at <?php echo ucfirst($domain)?>.</p>          </article>
        </div>
      </div>
    </div>
</section>