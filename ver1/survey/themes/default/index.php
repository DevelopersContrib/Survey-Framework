<?php defined("APP") or die() ?>
 <section>
	<?php /*?>
	<div class="calltoaction">
		<div class="container">
		  <div class="row">
			<div class="col-md-6">
			  <span><?php echo $count["surveys"] ?></span> <?php echo e("Surveys were created and successfully delivered.") ?>
			</div>
			<div class="col-md-6">
			  <span><?php echo $count["votes"] ?></span> <?php echo e("Users have answered questions.") ?>
			</div>            
		  </div>
		</div>
	</div>
	<?php */?>
<br class="clearfix"><br />
	<div class="wrap-polls">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="polls-header">
						<div class="row">
							<div class="col-xs-7">
								Latest Surveys
							</div>
							<div class="col-xs-2 votes-h text-center">
								Votes
							</div>
							<div class="col-xs-3 postby-h">
								Date Posted
							</div>
						</div>
					</div>
					<ul class="list-unstyled ul-polls 1">
						<?php
							foreach($polls_data as $item){
								if(Gibberish::test($item['question']) === false){
						?>
							<li class="">
								<a target="_blank" href="/<?php echo $item['uniqueid']?>" class="link-polls">
									<div class="col-xs-7 ">
										<span class="ellipsis pq-content-ttle">
											<span style="display:inline-block;">
												<img alt="Forum Read" title="Forum Read" src="https://d2qcctj8epnr7y.cloudfront.net/images/jayson/forum_read.png">
											</span>
											<?php echo $item['question']?>
										</span>
									</div>
									<div class="col-xs-2 text-center">
										<span class="pq-votes-con"><?php echo $item['votes']?></span>
									</div>
									<div class="col-xs-3 pq-cont-user">
										<span class="pq-date">
											<?php echo date( 'D M j, Y h:i a', strtotime($item['created']) )?>
										</span>
									</div>
								</a>
							</li>
						<?php
								}
							}
						?>
					</ul>
				</div>
			</div>
		</div>
	</div>
  
  
  <div class="container">
    <div class="row promo">
      <?php echo Main::message() ?>
      <div class="col-md-7">
        <h2 class="promo-heading"><?php echo e("Full-Featured.") ?> <span class="text-muted"><?php echo e("Seriously.") ?></span></h2>
        <p class="lead"><?php echo e("Our polls have many unique features that you will not find anywhere else. This allows you to create the most beautiful yet simple poll. Customize everything to make it your poll and say once and for all bye to those ugly polls.") ?></p>
      </div>
      <div class="col-md-5 promo-icon">
        <div class="row">
          <div class="col-xs-4">
            <span class="glyphicon glyphicon-stats"></span>
            <p><?php echo e("Advanced Statistics") ?></p>
          </div>
          <div class="col-xs-4">
            <span class="glyphicon glyphicon-lock"></span>
            <p><?php echo e("Password Protect") ?></p>
          </div>
          <div class="col-xs-4">
            <span class="glyphicon glyphicon-eye-open"></span>
            <p><?php echo e("Customizable Polls") ?></p>
          </div>
        </div>        
      </div>
    </div>

    <div class="row promo">
      <div class="col-md-5 promo-icon">
        <div class="row">
          <div class="col-xs-4">
            <span class="glyphicon glyphicon-phone"></span>
            <p><?php echo e("Adaptive Design") ?></p>
          </div>
          <div class="col-xs-4">
            <span class="glyphicon glyphicon-flash"></span>
            <p><?php echo e("Blazing-Fast Loading") ?></p>
          </div>
          <div class="col-xs-4">
            <span class="glyphicon glyphicon-hd-video"></span>
            <p><?php echo e("Pixel-Perfect Themes") ?></p>
          </div>
        </div>        
      </div>
      <div class="col-md-7">
        <h2 class="promo-heading"><?php echo e("Responsive.") ?> <span class="text-muted"><?php echo e("Try it yourself.") ?></span></h2>
        <p class="lead"><?php echo e("Our polls are desgined to fit your design and all screens. Each theme will automatically adjust itself to the size of the screen of the user, regardless of the device. We've also made them to work with older browsers, giving you the peace of mind.") ?></p>
      </div>
    </div>

    <div class="row promo">
      <div class="col-md-7">
        <h2 class="promo-heading"><?php echo e("Simple.") ?> <span class="text-muted"><?php echo e("You won't believe it.") ?></span></h2>
        <p class="lead"><?php echo e("We've designed our polls to be as simple as possible by giving you the options to easily customize them as you or your company requires.") ?></p>
      </div>
      <div class="col-md-5 promo-icon">
        <div class="row">
          <div class="col-xs-4">
            <span class="glyphicon glyphicon-align-justify"></span>
            <p><?php echo e("Manage Polls") ?></p>
          </div>
          <div class="col-xs-4">
            <span class="glyphicon glyphicon-user"></span>
            <p><?php echo e("Analyze Data") ?></p>
          </div>
          <div class="col-xs-4">
            <span class="glyphicon glyphicon-download-alt"></span>
            <p><?php echo e("Export Data") ?></p>
          </div>
        </div>        
      </div>
    </div>
  </div><!-- /.container -->   
  <?php
global $related_domains;
?>
 
  
<?php if (count($related_domains)>0):?>
<?php $vertical = str_replace('-',' ',ucfirst($related_domains[0]['slug'])) ?>
	<div class="container survey-sites">
		<div class="row promo">
		  <div class="col-md-12">
			<h4>Other Survey Sites</h4>
			<ul class="list-unstyled">
			<?foreach($related_domains as $rel_domains):?>
				<li class="col-md-3 odd"><a href="https://<?php echo $rel_domains['domain_name']?>"><i class="fa fa-comments-o"></i>&nbsp;<?php echo ucwords($rel_domains['domain_name'])?></a></li>				
			<? endforeach?>
			</ul>
		  </div>
		</div>
	</div>
<?php endif;?>
</section>