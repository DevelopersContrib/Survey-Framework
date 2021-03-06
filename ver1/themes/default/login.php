<?php defined("APP") or die() ?>
<section>
	<div class="container">    
		<div class="centered form">
      
      <div class="site_logo">
        <?php if (!empty($this->config["logo"])): ?>
          <a href="<?php echo $this->config["url"] ?>">
		  <img style="width:400px" src="<?php echo $this->config["logo"] ?>" alt="<?php echo $this->config["title"] ?>"></a>
        <?php else: ?>
          <a class="navbar-brand" href="<?php echo $this->config["url"] ?>"><?php echo $this->config["title"] ?></a>
        <?php endif ?>
      </div> 

      <?php echo Main::message() ?>
      <form role="form" class="live_form form" id="login_form" method="post" action="<?php echo Main::href("index.php?a=user/login","user/login")?>">
        <div class="form-group">
          <label for="email"><?php echo e("Email address")?> 
            <?php if($this->config["users"]): ?>
              <a href="<?php echo Main::href("index.php?a=user/register","user/register")?>" class="pull-right">(<?php echo e("Create account")?>)</a>
            <?php endif ?>
          </label>
          <input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
        </div>
        <div class="form-group">
          <label for="pass"><?php echo e("Password")?> <a href="#forgot" class="pull-right" id="forgot-password">(<?php echo e("Forgot Password")?>)</a></label>
          <input type="password" class="form-control" id="pass" placeholder="Password" name="password">
        </div>         
        <div class="form-group">
          <label>
              <input type="checkbox" name="rememberme" value="1" data-class="blue">  
              <span class="check-box"><?php echo e("Remember me")?></span>
          </label>
        </div>                  
        <?php echo Main::csrf_token(TRUE) ?>
        <button type="submit" class="btn btn-primary"><?php echo e("Login")?></button>
      </form>  

      <form role="form" class="live_form" id="forgot_form" method="post" action="<?php echo Main::href("index.php?a=user/forgot","user/forgot")?>">
        <div class="form-group">
          <label for="email1"><?php echo e("Email address")?></label>
          <input type="email" class="form-control" id="email1" placeholder="Enter email" name="email">
        </div>        
        <?php echo Main::csrf_token(TRUE) ?>
        <button type="submit" class="btn btn-primary"><?php echo e("Reset Password")?></button>
        <a href="<?php echo Main::href("user/login") ?>" class="pull-right">(<?php echo e("Back to login")?>)</a>
      </form>        
		</div>
	</div>
</section>