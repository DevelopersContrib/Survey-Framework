<?php global $footer_html,$social_twitter,$social_linkedin,$social_fb,$social_gtube,$social_gplus; ?>
<style>
.footer-dark-1 {
    color: #FFF;
    padding: 25px 0px 10px;
    background-color: #333;
}
.footer-dark-1 h3 {
	font-size: 18px;
margin-top: 0px;
margin-bottom: 15px;
text-transform:uppercase;
}
.footer-dark-1 h3 a {
	text-transform:uppercase;
}
.footer-dark-1 .fa {
	font-size:30px;
}
.footer-dark-1 a {
	color:#dedede !important;
	text-transform:capitalize;
}
.footer-dark-2 {
    color: #FFF;
    padding: 20px 0px 10px 0px;
    background-color: #222;
	margin-bottom:-40px;
	font-size:12px;
}
.footer-dark-2 a {
	color:#dedede !important;
	font-size:12px;
	text-transform:capitalize;
}
</style>
<?php defined("APP") or die() ?>
<?php
global $footer_banner;
?>
  <?php if ($this->footerShow): ?>
  <section style="display:none">
    <div class="container">
      <footer class="row">
        <div class="pull-right footer"> 
			<a href="/partners">Partner with us</a>
			<a href="/referral">Referral</a>
			<a href="/terms">Terms of Service</a>
          <?php foreach ($pages as $page): ?>
              <a href="<?php echo Main::href("page/{$page->slug}") ?>"><?php echo $page->title ?></a>
          <?php endforeach ?>
        <div class="languages">
          <a href="#lang" class="active" id="show-language"><i class="glyphicon glyphicon-globe"></i> <?php echo e("Language") ?></a>
          <div class="langs">
            <?php echo $this->lang(0) ?>
          </div>          
        </div>    
        </div>
        <p>
			&copy; <?php echo date("Y")?> <a href="<?php echo $this->config["url"] ?>"><?php echo $this->config["title"] ?></a>
			&nbsp; &#8212; &nbsp;
			<a target="_blank" href="https://contrib.com">POWERED BY CONTRIB.COM</a>
		</p>
    <p align="center"><?php echo $footer_banner?></p>
    
      </footer>
    </div><!-- /.container -->     
  </section>

<!-- footer -->
        <footer>
    
           <div class="footer-dark-1">
				<div class="container">
					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-3">
									<h3 class="fnt-bold text-uppercase">
										<a href="/"><?php echo $this->config["title"] ?></a>
									</h3>
									<p>
										Join our exclusive community of like minded people on <?php echo $this->config["title"] ?>.
									</p>
								</div>
								<div class="col-md-3">
									<h3 class="fnt-bold text-uppercase">
										get started
									</h3>
									<ul class="list-unstyled f-a-links">
										<li>
											<a href="/partners" class="text-capitalize">
												Partner with us
											</a>
										</li>
										<li>
											<a href="/staffing" class="text-capitalize">
												Apply now
											</a>
										</li>
										<li>
											<a href="/referral" class="text-capitalize">
												referral
											</a>
										</li>
										<li>
											<a href="/create" class="text-capitalize">
												create survey
											</a>
										</li>										
									</ul>
								</div>
								<div class="col-md-3">
									<h3 class="fnt-bold text-uppercase">
										company
									</h3>
									<ul class="list-unstyled f-a-links f-a-links-mrgBtm">
										<li>
											<a href="/about" class="text-capitalize">
												About us
											</a>
										</li>
										<li>
											<a href="/terms" class="text-capitalize">
												Terms
											</a>
										</li>
										<li>
											<a href="/privacy" class="text-capitalize">
											privacy
											</a>
										</li>
										<li>
											<a href="/cookiepolicy" class="text-capitalize">
											cookie policy
											</a>
										</li>
										<li>
											<a href="/contact" class="text-capitalize">Contact us</a>
										</li>
										<li>
											<a href="/apps" class="text-capitalize">Apps</a>
										</li>
										<li>
											<a href="https://www.domaindirectory.com/policypage/unsubscribe?domain=<?=$_SERVER['HTTP_HOST']?>" class="text-capitalize" target="_blank">
												unsubscribe
											</a>
										</li>
									</ul>
								</div>
								<div class="col-md-3">
									<!-- <h3 class="fnt-bold text-uppercase">
										partners
									</h3>
									<p>
										<a href="https://www.rackspace.com">
											<img style="height:45px;" title="Rackspace" alt="Rackspace" src="https://c15162226.r26.cf2.rackcdn.com/Rackspace_Cloud_Company_Logo_clr_300x109.jpg">
										</a>
									</p> -->
									<h3 class="fnt-bold text-uppercase">
										partners
									</h3>
									<p>
									 <?if($footer_html != ""):?>

        					<?//echo base64_decode($footer_html)?>

        					 <?php
								$footer_html = str_replace('http:','https:',base64_decode($footer_html));
								$footer_html = str_replace('https://referrals.contrib.com/banners/codero-logo-HostingOnDemand.png','https://cdn.vnoc.com/banner/codero-logo-HostingOnDemand.png',$footer_html);
								echo $footer_html;
							?>
        					<?php else:?>
        					<a href="https://goo.gl/WpfyJC" target="_blank"><img style="border:0px" src="https://referrals.contrib.com/banners/codero-logo-HostingOnDemand.png" width="205" height="58" alt="Dedicated Servers, Cloud and Hybrid Hosting Services " title="Dedicated Servers, Cloud and Hybrid Hosting Services "></a>
        				<?endif;?>
									</p>
									<h3 class="fnt-bold text-uppercase">
										Socials
									</h3>
					

									<ul class="list-inline socials-ul">
										<li>
											<a title="twitter" class="icon-button twitter" target="_blank" href="<?php echo $social_twitter; ?>">
												<i class="fa fa-twitter-square"></i>
												<span></span>
											</a>
										</li>
										<li>
											<a title="facebook" class="icon-button facebook" target="_blank" href="<?php echo $social_fb; ?>">
												<i class="fa fa-facebook-square"></i>
												<span></span>
											</a>
										</li>										
										<li>
											<a title="linkedin" class="icon-button linkedin" target="_blank" href="<?php echo $social_linkedin; ?>">
												<i class="fa fa-linkedin-square"></i>
												<span></span>
											</a>
										</li>
									</ul>
									
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="footer-dark-2">
				<div class="container">
					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-6 f-a-links">
									© <?php echo date("Y"); ?> <a href="" class="text-capitalize "><?php echo $this->config["title"] ?></a>. All Rights Reserved. 
								</div>
								<div class="col-md-6" style="display:none">
									<ul class="list-inline text-right f-a-links">
										<li>
											<a href="/about" class="text-capitalize">
												<i class="fa fa-bookmark-o"></i>
												About us
											</a>
										</li>
										<li>
											<a href="/terms" class="text-capitalize">
												<i class="fa fa-book"></i>
												Terms
											</a>
										</li>
										<li>
											<a href="/privacy" class="text-capitalize">
												<i class="fa fa-cube"></i>
												privacy
											</a>
										</li>
										<li>
											<a href="contactus.php" class="text-capitalize">
												<i class="fa fa-phone-square"></i>
												contact us
											</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
               
        </footer>
        <!-- end footer -->
  
  <?php else:  /* Close Container + User Panel Footer */?>
        </div><!--/.col -->        
      </div><!--/.row -->
    </div><!--/.container -->
  </section>
  <?php endif ?>  
  <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/application.js?v=1.0"></script>   
	<?php Main::enqueue('footer') ?>
	</body>
</html>