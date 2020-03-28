<?php defined("APP") or die() ?>
<div role="tabpanel" class="tab-pane tabbed" style="<?php echo $style;?>" id="tab<?php echo $question->id?>">
<section>
  <div class="container">
    <div class="centered poll">
		<?php /*?>
      <div class="site_logo">
        <?php if (!empty($this->config["logo"])): ?>
		  <img style="100%" src="<?php echo $this->config["logo"] ?>" alt="<?php echo $this->config["title"] ?>">
        <?php else: ?>
          <h3><a href="<?php echo $this->config["url"] ?>"><?php echo $this->config["title"] ?></a></h3>
        <?php endif ?>
      </div>
	  <?php */?>
      <?php echo Main::message() ?>
      <?php // Show Password ?>
      <?php if($protected):  ?>
        <div id="poll_widget" class="<?php echo $question->theme ?> poll_widget<?php echo $question->uniqueid?>">        
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
        <div id="poll_widget" class="<?php echo $question->theme ?> poll_widget<?php echo $question->uniqueid?>">        
          <div id="poll_question">
            <h3><?php echo e("This poll has either expired or has been closed.") ?></h3>
          </div>                 
        </div><!--#poll_widget-->      
      <?php endif ?>

      <?php // Show Poll ?>
      <?php if(!$protected && !$expired): ?>
        <div id="poll_widget" class="<?php echo $question->theme ?> poll_widget<?php echo $question->uniqueid?>">
          <?php if(!$question->visited): ?>          
            <form action="<?php echo Main::href("vote") ?>" method="post" id="poll_form" class="poll_form_widget">
              <?php if($question->share || $question->userid==$this->userid): ?>
                <!--<a href="#embed" id="poll_embed"><?php echo e("Embed")?></a>-->
                <div id="poll_embed_holder" class="live_form">
                  <div class="input-group">
                    <span class="input-group-addon"><?php echo e("Share")?></span>
                    <input type="text" class="form-control" value="<?php echo Main::href($question->uniqueid) ?>">
                  </div>              
                  <div class="input-group">
                    <span class="input-group-addon"><?php echo e("Embed")?></span>
                    <input type="text" class="form-control" value="&lt;iframe src=&quot;<?php echo Main::href("embed/{$question->uniqueid}")?>&quot; width=&quot;350&quot; height=&quot;<?php echo $height ?>&quot; scrolling=&quot;0&quot; frameborder=&quot;0&quot;&gt;&lt;/iframe&gt;">
                  </div>
                  <div class="input-group">
                    <a href="https://www.facebook.com/sharer.php?u=<?php echo Main::href($question->uniqueid) ?>" class="btn btn-transparent" target="_blank"><?php echo e("Share on")?> Facebook</a>
                    <a href="https://twitter.com/share?url=<?php echo Main::href($question->uniqueid) ?>&amp;text=<?php echo urlencode($question->question) ?>" class="btn btn-transparent" target="_blank"><?php echo e("Share on")?> Twitter</a>                                
                  </div>            
                </div><!-- /#poll_embed_holder -->             
              <?php endif  ?>
              <div id="poll_question">
                <h3><?php echo $question->question ?></h3>
				<?php
					if(!empty($question->image_url)){
					?>
						<img style="width:500px" src="<?php echo $question->image_url;?>">
					<?php
					}
				  ?>
              </div>
              <ul id="poll_answers">
                <?php $i=1; ?>
                <?php foreach ($question->answers as $key => $value): ?>
                  <li id="poll-<?php echo $key ?>"> 
                    <label>
                      <?php if($question->choice): ?>                    
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
				if(!empty($question->parent_id)){
			  ?>
              <div id="poll_button">
                <?php echo Main::csrf_token(TRUE) ?>
                <input type="hidden" name="poll_id" id="poll_id" value="<?php echo $question->uniqueid ?>">
                <input type="submit" class="btn btn-widget" value="Vote">
                <?php if ($question->results): ?>
				<?php /*//03.26.15 ?>
                  <button type="button" onclick="javascript:update_results('<?php echo Main::href("results") ?>','<?php echo $question->uniqueid ?>')" class="btn btn-widget" id="view-results">View Results</button>
                <?php */?>
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
		  <?php //var_dump($question->results); ?>
            <?php if(!$question->results): ?>
				<div id="poll_question">
			  <h3><?php echo $question->question?></h3>
                <h5><?php echo e("Thank you for voting!")?></h5>
              </div>
			  
              <p id="poll_button" style="display:none" > <button type="button" class="btn btn-widget view-results">View Results</button></p>
              <?php /*?>
			  <div id="poll_question">
                <h3><?php echo e("Thank you for voting!")?></h3>
              </div>
			  <p id="poll_button"> <a href="<?php echo Main::href("create") ?>" class="btn btn-transparent"><?php echo e("Create your Poll")?></a></p>
			  <?php */?>
            <?php else: ?>
              <?php //$this->results($question->id); ?>
			  <?php //*/// 03.26.15?>
			  
			  <div id="poll_question">
			  <h3><?php echo $question->question?></h3>
                <h5><?php echo e("Thank you for voting!")?></h5>
              </div>
			  <p id="poll_button" style="display:none" > <button type="button" class="btn btn-widget view-results-trigger">View Results</button></p>
			  <p id="poll_button" style="display:none"> <button type="button" onclick="javascript:update_results('<?php echo Main::href("results") ?>','<?php echo $question->uniqueid ?>')" class="btn btn-widget view-results" id="view-results">View Results</button></p>			  
			  <?php /*?>
              <p id="poll_button"> <a href="<?php echo Main::href("create") ?>" class="btn btn-transparent"><?php echo e("Create your Poll")?></a></p>
			  <?php */?>
			  <?php //*/?>
			  
            <?php endif ?>
          <?php endif ?>
        </div><!--#poll_widget-->            
      <?php endif  ?>   
      
    </div><!--/.centered-->     
  </div>
</section>
</div>