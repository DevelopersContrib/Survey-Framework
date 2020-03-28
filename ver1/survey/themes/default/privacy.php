<section>
	<div class="calltoaction">
		<div class="container">
			<span>Terms of Service</span>
		</div>
	</div>
	<div class="container">
		<div class="row page">
			<div class="col-md-8">
				<div class="post">
					<article>
					<?php
						global $domain;
						$api_content_url = "http://api3.contrib.co/announcement/"; //get privacy
						$url = $api_content_url.'GetFooterContents?domain='.$domain.'&key=5c1bde69a9e783c7edc2e603d8b25023&page=privacy';
						$result = createApiCall($url, 'GET', $headers, array());
						$data_domain = json_decode($result,true);
						
						if (isset($data_domain['data']['content'])){
							$privacy = $data_domain['data']['content'];
						} else {
							$privacy = "";
						}
						
						echo $privacy;
					?>
					</article>
				</div>
			</div>
			<div class="col-md-4 side">
				<?php $this->ads(300) ?>
			</div>
		</div>
	</div>
</section>