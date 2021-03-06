<?php defined("APP") or die() ?>
<section class="display_poll">
  <div class="container">
    <div class="centered poll" style="margin-bottom:10px">
      <div class="site_logo">
        <?php if (!empty($this->config["logo"])): ?>
          <?php /*?><a href="<?php echo $this->config["url"] ?>"><img src="<?php echo $this->config["url"] ?>/static/<?php echo $this->config["logo"] ?>" alt="<?php echo $this->config["title"] ?>"></a><?php */?>
		  <img style="100%" src="<?php echo $this->config["logo"] ?>" alt="<?php echo $this->config["title"] ?>">
        <?php else: ?>
          <h1><a href="<?php echo $this->config["url"] ?>"><?php echo $this->config["title"] ?></a></h1>
        <?php endif ?>
      </div>
	  
	  <?php
		//$this->getShareEmbed($poll->id);
	  ?>
	<?php
	$parent_cls = '';
	if(empty($poll->parent_id) && $withChild){
		$parent_cls = 'parent_survey';
	}
	?>

      <?php echo Main::message() ?>
      <?php // Show Password ?>
      <?php if($protected):  ?>
        <div id="poll_widget" class="<?php echo $poll->theme ?> <?php echo $parent_cls;?>">
          <div id="poll_question">
            <h3><?php echo e("Please enter the password") ?></h3>
          </div>          
          <form action="<?php echo Main::href($this->action) ?>" method="post" class="live_form passform">            
              <p><?php echo e("This poll is password protected. Please enter the password to continue.") ?></p>                            
              <div class="input-group">                
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                <input type="text" class="form-control" name="password">
              </div>              
              <?php echo Main::csrf_token(TRUE) ?>
              <button type="submit" class="btn btn-dark"><?php echo e("Submit") ?></button>
          </form>        
        </div><!--#poll_widget-->
      <?php endif; ?>
      
      <?php // Expired ?>
      <?php if($expired): ?>
        <div id="poll_widget" class="<?php echo $poll->theme ?> <?php echo $parent_cls;?>">        
          <div id="poll_question">
            <h3><?php echo e("This poll has either expired or has been closed.") ?></h3>
          </div>                 
        </div><!--#poll_widget-->      
      <?php endif ?>

      <?php // Show Poll ?>
      <?php if(!$protected && !$expired): ?>
        <div id="poll_widget" class="<?php echo $poll->theme ?> <?php echo $parent_cls;?>">
          <?php if(!$poll->visited): ?>          
            <form action="<?php echo Main::href("vote") ?>" method="post" id="poll_form" class="poll_form_widget">
              <?php if($poll->share || $poll->userid==$this->userid): ?>
			  <?php
				if(!empty($poll->parent_id)){
			  ?>
                <!--<a href="#embed" id="poll_embed"><?php echo e("Embed")?></a>-->
				<?php }?>
                <div id="poll_embed_holder" class="live_form">
                  <div class="input-group">
                    <span class="input-group-addon"><?php echo e("Share")?></span>
                    <input type="text" class="form-control" value="<?php echo Main::href($poll->uniqueid) ?>">
                  </div>              
                  <div class="input-group">
                    <span class="input-group-addon"><?php echo e("Embed")?></span>
                    <input type="text" class="form-control" value="&lt;iframe src=&quot;<?php echo Main::href("embed/{$poll->uniqueid}")?>&quot; width=&quot;350&quot; height=&quot;<?php echo $height ?>&quot; scrolling=&quot;0&quot; frameborder=&quot;0&quot;&gt;&lt;/iframe&gt;">
                  </div>
                  <div class="input-group">
                    <a href="https://www.facebook.com/sharer.php?u=<?php echo Main::href($poll->uniqueid) ?>" class="btn btn-transparent" target="_blank"><?php echo e("Share on")?> Facebook</a>
                    <a href="https://twitter.com/share?url=<?php echo Main::href($poll->uniqueid) ?>&amp;text=<?php echo urlencode($poll->question) ?>" class="btn btn-transparent" target="_blank"><?php echo e("Share on")?> Twitter</a>                                
                  </div>            
                </div><!-- /#poll_embed_holder -->             
              <?php endif  ?>
              <div id="poll_question">
                <h1><?php echo $poll->question ?></h1>
				<?php
					if(!empty($poll->image_url)){
					?>
						<img style="width:500px" src="<?php echo $poll->image_url;?>">
					<?php
					}
				  ?>
              </div>
              <ul id="poll_answers">
                <?php $i=1; ?>
                <?php foreach ($poll->answers as $key => $value): ?>
                  <li id="poll-<?php echo $key ?>"> 
                    <label>
                      <?php if($poll->choice): ?>                    
                        <input type="checkbox" name="answer[<?php echo $key ?>]" value="<?php echo $key ?>"<?php echo ($i==1)?" checked":""; ?>> 
                      <?php else: ?>
                        <input type="radio" name="answer" value="<?php echo $key ?>"<?php echo ($i==1)?" checked":""; ?>> 
                      <?php endif ?>
                      <span><?php echo ucfirst($value->answer) ?></span>
                    </label>
                  </li>
                  <?php $i++; ?>
                <?php endforeach ?>
              </ul>
			  <?php
				if(!empty($poll->parent_id)){
			  ?>
              <div id="poll_button">
                <?php echo Main::csrf_token(TRUE) ?>
                <input type="hidden" name="poll_id" id="poll_id" value="<?php echo $poll->uniqueid ?>">
                <input type="submit" class="btn btn-widget" value="Vote">
                <?php if ($poll->results): ?>
                  <button type="button" onclick="javascript:update_results('<?php echo Main::href("results") ?>','<?php echo $poll->uniqueid ?>')" class="btn btn-widget" id="view-results">View Results</button>
                <?php endif ?>
                <?php if($this->logged() && $user->membership!="pro"): ?>
                  <span class="branding pull-right">
                    <?php echo e("Powered by") ?> <a href="<?php echo $this->config["url"] ?>" target="_blank"><?php echo $this->config["title"] ?></a>
                  </span>
                <?php endif ?>
              </div>
			  <?php
				} 
			  ?>
            </form>                    
          <?php else: ?>        
            <?php if(!$poll->results): ?>
              <div id="poll_question">
                <h3><?php echo e("Thank you for voting!")?></h3>
              </div>
              <p id="poll_button"> <a href="<?php echo Main::href("create") ?>" class="btn btn-transparent"><?php echo e("Create your Poll")?></a></p>
            <?php else: ?>
              <?php $this->results($poll->id); ?>
            <?php endif ?>
          <?php endif ?>
        </div><!--#poll_widget-->            
      <?php endif  ?>   
	  <?php /*?>
      <footer>
        <p>&copy; <?php echo date("Y")?> <a href="<?php echo $this->config["url"] ?>"><?php echo $this->config["title"] ?></a></p>
      </footer>
	  <?php */?>
	  
	  
	  
	  
    </div><!--/.centered-->     
  </div>
</section>