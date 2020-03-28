<?php if(!defined("APP")) die()?>
<div class="panel panel-default">
  <div class="panel-heading">
    Application Settings
  </div>      
  <div class="panel-body settings">
  	<div class="row">
  		<div class="col-sm-3 sub-sidebar">
        <ul class="nav tabs">
          <li class="active"><a href="#main">Site Configuration</a></li>
          <li><a href="#payment">Membership Settings</a></li>
          <li><a href="#user">Users Settings</a></li>
          <li><a href="#ads">Advertisement Settings</a></li>
          <li><a href="#tools">Extra Settings</a></li>
        </ul>
  		</div>
  		<div class="col-sm-9">
				<form class="form-horizontal" role="form" id="setting-form" action="<?php echo Main::ahref("settings") ?>" method="post" enctype="multipart/form-data">
					<div id="main" class="tabbed">
						<div class="form-group">
					    <label for="url" class="col-sm-3 control-label">Site URL</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="url" id="url" value="<?php echo $this->config['url'] ?>">
					      <p class="help-block">Please make sure to include http:// (or https://) and remove the last slash</p>
					    </div>
					  </div>				
						<div class="form-group">
					    <label for="title" class="col-sm-3 control-label">Site Title</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="title" id="title" value="<?php echo $this->config['title'] ?>">
					      <p class="help-block">This is your site name as well as the site meta title.</p>
					    </div>
					  </div>				
						<div class="form-group">
					    <label for="description" class="col-sm-3 control-label">Site Description</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="description" id="description" value="<?php echo $this->config['description'] ?>">
					      <p class="help-block">This your site description as well as the site meta description.</p>
					    </div>
					  </div>
						<div class="form-group">
					    <label for="logo" class="col-sm-3 control-label">Logo
					    	<?php if(!empty($this->config["logo"])):  ?>
					    	<span class="help-block"><a href="#" id="remove_logo" class="btn btn-info btn-xs">Remove Logo</a></span>
					    	<?php endif ?>
					    </label>
					    <div class="col-sm-9">
								<?php if(!empty($this->config["logo"])):  ?>
									<img src="<?php echo $this->config["url"] ?>/static/<?php echo $this->config["logo"] ?>" height="80" alt=""> <br />
								<?php endif ?>					    	
					      <input type="file" name="logo_path" id="logo">
					      <p class="help-block">Please make sure that the logo is of adequate size and format.</p>
					    </div>
					  </div>		
						<div class="form-group">
					    <label for="lang" class="col-sm-3 control-label">Default Language</label>
					    <div class="col-sm-9">
					      <select name="lang" id="lang" class="selectized">
					      	<?php echo $lang ?>
					      </select>
					      <p class="help-block">To add a new language, you may use the sample file "sample_lang.php" in includes/languages/ and then rename to a two letter code.</p>
					    </div>
					  </div>					  		  
						<div class="form-group">
					    <label for="theme" class="col-sm-3 control-label">Theme</label>
					    <div class="col-sm-9">
					      <select name="theme" id="theme" class="selectized">
					      	<?php echo $theme ?>
					      </select>
					      <p class="help-block">To install a new theme, upload it in themes/ and it will show up here.</p>
					    </div>
					  </div>
						<div class="form-group">
					    <label class="col-sm-3 control-label">Style</label>
					    <div class="col-sm-9">
						    <ul class="themes">                
	                <li class="dark"><a href="#" data-class="" <?php if($this->config["style"]=="") echo "class='current'" ?>>Dark</a></li>
	                <li class="blue"><a href="#" <?php if($this->config["style"]=="blue") echo "class='current'" ?> data-class="blue">Blue</a></li>                
	                <li class="red"><a href="#" <?php if($this->config["style"]=="red") echo "class='current'" ?> data-class="red">Red</a></li>
	                <li class="green"><a href="#" <?php if($this->config["style"]=="green") echo "class='current'" ?> data-class="green">Green</a></li>
	              </ul> 
					      <input type="hidden" name="style" value="<?php echo $this->config["style"] ?>" id="poll_theme_value"> 
					      <p class="help-block">The default theme supports these styles.</p>
					    </div>
					  </div>						  					  
						<div class="form-group">
					    <label for="fonts" class="col-sm-3 control-label">Fonts</label>
					    <div class="col-sm-9">
					      <textarea class="form-control" name="fonts" id="fonts" rows="10"><?php if(!empty($this->config["fonts"])) echo implode("\n", array_filter(json_decode($this->config['fonts'],TRUE))) ?></textarea>
					      <p class="help-block">Choose the Google fonts you like to offer as an option. One font name per line and please add exactly the name: e.g. if the name is <strong>Open Sans</strong> add <strong>Open Sans</strong> and press enter.<a href="https://www.google.com/fonts" target="_blank">Click here to find some</a>.</p>
					    </div>
					  </div>							  
						<div class="form-group">
					    <label for="email" class="col-sm-3 control-label">Email</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="email" id="email" value="<?php echo $this->config['email'] ?>">
					      <p class="help-block">This email will be used to send emails and to receive emails.</p>
					    </div>
					  </div>					  		  
						<div class="form-group">
					    <label for="captcha" class="col-sm-3 control-label">Captcha</label>
					    <div class="col-sm-9">
					      <select name="captcha" id="catpcha" class="selectized">
					      	<option value="1" <?php if($this->config['captcha']) echo "selected"?>>Enabled</option>
					      	<option value="0" <?php if(!$this->config['captcha']) echo "selected"?>>Disabled</option>
					      </select>
					      <p class="help-block">The Captcha is used to prevent abuse or automatic submission by bots.</p>
					    </div>
					  </div>					  
						<div class="form-group">
					    <label for="captcha_public" class="col-sm-3 control-label">reCaptcha Public Key</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="captcha_public" id="captcha_public" value="<?php echo $this->config['captcha_public'] ?>">
					      <p class="help-block">You can get your public key for free from <a href="https://www.google.com/recaptcha" target="_blank">Google</a></p>
					    </div>
					  </div>				
						<div class="form-group">
					    <label for="captcha_private" class="col-sm-3 control-label">reCaptcha Private Key</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="captcha_private" id="captcha_private" value="<?php echo $this->config['captcha_private'] ?>">
					      <p class="help-block">You can get your private key for free from <a href="https://www.google.com/recaptcha" target="_blank">Google</a></p>
					    </div>
					  </div>										
					</div><!-- /#main.tabbed -->

					<div id="payment" class="tabbed">
						<div class="form-group">
					    <label for="paypal_email" class="col-sm-3 control-label">PayPal Email</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="paypal_email" id="paypal_email" value="<?php echo $this->config['paypal_email'] ?>">
					      <p class="help-block">Payments will be sent to this address. Please make sure that you enable IPN. For more info <a href="https://developer.paypal.com/webapps/developer/docs/classic/products/instant-payment-notification/" target="_blank">click here</a></p>
					    </div>
					  </div>	
						<div class="form-group">
					    <label for="currency" class="col-sm-3 control-label">Currency</label>
					    <div class="col-sm-9">
					      <?php $currencies = Main::currency() ?>
					     <select name="currency" id="currency">
					      <?php foreach ($currencies as $code => $info): ?>
					      	<option value="<?php echo $code ?>" <?php if($this->config["currency"]==$code) echo "selected" ?>><?php echo $info["label"] ?></option>
					      <?php endforeach ?>
					      </select>
					    </div>
					  </div>						  			
						<div class="form-group">
					    <label for="pro_montly" class="col-sm-3 control-label">Pro Monthly Fee</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="pro_monthly" id="pro_monthly" value="<?php echo $this->config['pro_monthly'] ?>">
					      <p class="help-block">Every month users will need to pay this amount to keep their premium account.</p>
					    </div>
					  </div>		
						<div class="form-group">
					    <label for="pro_yearly" class="col-sm-3 control-label">Pro Yearly Fee</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="pro_yearly" id="pro_yearly" value="<?php echo $this->config['pro_yearly'] ?>">
					      <p class="help-block">Every year users will be charged this amount to keep their premium account. Discounts, if any, will be calculated automatically.</p>
					    </div>
					  </div>
						<div class="form-group">
					    <label for="max_count" class="col-sm-3 control-label">Maximum Number of Answers for Pro</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="max_count" id="max_count" value="<?php echo $this->config['max_count'] ?>">
					      <p class="help-block">Free and anonymous users are limited to 10 answers while premium users will be limited to the number above.</p>
					    </div>
					  </div>					  		  					
					</div><!-- /#payment.tabbed -->	
					<div id="user" class="tabbed">
						<div class="form-group">
					    <label for="users" class="col-sm-3 control-label">Users Module</label>
					    <div class="col-sm-9">
					      <select name="users" id="users" class="selectized">
					      	<option value="1" <?php if($this->config['users']) echo "selected"?>>Enabled</option>
					      	<option value="0" <?php if(!$this->config['users']) echo "selected"?>>Disabled</option>
					      </select>
					      <p class="help-block">If enabled, users will be able to register and store their polls.</p>
					    </div>
					  </div>				
						<div class="form-group">
					    <label for="user_activation" class="col-sm-3 control-label">User Activation</label>
					    <div class="col-sm-9">
					      <select name="user_activation" id="user_activation" class="selectized">
					      	<option value="1" <?php if($this->config['user_activation']) echo "selected"?>>Enabled</option>
					      	<option value="0" <?php if(!$this->config['user_activation']) echo "selected"?>>Disabled</option>					      	
					      </select>
					      <p class="help-block">If enabled, users will have to activate their account.</p>
					    </div>
					  </div>			
						<div class="form-group">
					    <label for="export" class="col-sm-3 control-label">Allow Export</label>
					    <div class="col-sm-9">
					      <select name="export" id="export" class="selectized">
					      	<option value="1" <?php if($this->config['export']) echo "selected"?>>Enabled</option>
					      	<option value="0" <?php if(!$this->config['export']) echo "selected"?>>Disabled</option>
					      </select>
					      <p class="help-block">If enabled, <strong>pro</strong> users will be able to export their poll data.</p>
					    </div>
					  </div>						  				
					</div><!-- /#user.tabbed -->											
					<div id="ads" class="tabbed">
						<div class="form-group">
					    <label for="ads" class="col-sm-3 control-label">Advertisement Module</label>
					    <div class="col-sm-9">
					      <select name="ads" id="ads" class="selectized">
					      	<option value="1" <?php if($this->config['ads']) echo "selected"?>>Enabled</option>
					      	<option value="0" <?php if(!$this->config['ads']) echo "selected"?>>Disabled</option>					      	
					      </select>
					      <p class="help-block">If enabled, advertisment will be shown to free users.</p>
					    </div>
					  </div>				
						<div class="form-group">
					    <label for="ad728" class="col-sm-3 control-label">Advertisement (728x90)</label>
					    <div class="col-sm-9">
					      <textarea class="form-control" name="ad728" id="ad728" rows="5"><?php echo $this->config['ad728'] ?></textarea>
					    </div>
					  </div>				
						<div class="form-group">
					    <label for="ad468" class="col-sm-3 control-label">Advertisement (468x60)</label>
					    <div class="col-sm-9">
					      <textarea class="form-control" name="ad468" id="ad468" rows="5"><?php echo $this->config['ad468'] ?></textarea>
					    </div>
					  </div>				
						<div class="form-group">
							<label for="ad300" class="col-sm-3 control-label">Advertisement (300x250)</label>
							<div class="col-sm-9">
							  <textarea class="form-control" name="ad300" id="ad300" rows="5"><?php echo $this->config['ad300'] ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="googleanalytics" class="col-sm-3 control-label">Google Analytics</label>
							<div class="col-sm-9">
							  <textarea class="form-control" name="googleanalytics" id="googleanalytics" rows="5"><?php echo $this->config['googleanalytics'] ?></textarea>
							</div>
						</div>
					</div><!-- /#ads.tabbed -->			
					<div id="tools" class="tabbed">
					  <div class="alert alert-info"><strong>Tip:</strong> SMTP is recommend because it is much more reliable than the system mail module.</div>
						<div class="form-group">
					    <label for="smtp" class="col-sm-3 control-label">SMTP Host</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="smtp[host]" id="smtp" value="<?php echo $this->config['smtp']['host'] ?>">
					    </div>
					  </div>				
						<div class="form-group">
					    <label for="smtp" class="col-sm-3 control-label">SMTP Port</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="smtp[port]" id="smtp" value="<?php echo $this->config['smtp']['port'] ?>">
					    </div>
					  </div>		
						<div class="form-group">
					    <label for="smtp" class="col-sm-3 control-label">SMTP User</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="smtp[user]" id="smtp" value="<?php echo $this->config['smtp']['user'] ?>">
					    </div>
					  </div>		
						<div class="form-group">
					    <label for="smtp" class="col-sm-3 control-label">SMTP Pass</label>
					    <div class="col-sm-9">
					      <input type="password" class="form-control" name="smtp[pass]" id="smtp" value="<?php echo $this->config['smtp']['pass'] ?>">
					    </div>
					  </div>									  					  					  					  			
					</div><!-- /#tools.tabbed -->

				  <div class="form-group">
				    <div class="col-sm-12">
				    	<?php echo Main::csrf_token(TRUE) ?>
				      <button type="submit" class="btn btn-default">Save Settings</button>
				    </div>
				  </div>
				</form>  			
  		</div>
  	</div>
  </div>
</div>