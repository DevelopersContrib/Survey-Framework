<?php 
/**
 * ====================================================================================
 *                           Premium Poll Script (c) KBRmedia
 * ----------------------------------------------------------------------------------
 * @copyright This software is exclusively sold at CodeCanyon.net. If you have downloaded this
 *  from another site or received it from someone else than me, then you are engaged
 *  in an illegal activity. You must delete this software immediately or buy a proper
 *  license from http://codecanyon.net/user/KBRmedia/portfolio?ref=KBRmedia.
 *
 *  Thank you for your cooperation and don't hesitate to contact me if anything :)
 * ====================================================================================
 *
 * @author KBRmedia (http://gempixel.com)
 * @link http://gempixel.com 
 * @license http://gempixel.com/license
 * @package Premium Poll Script
 * @subpackage App Request Handler
 */
require 'Gibberish.class.php';
class App{
	/**
	 * Maximum number of answers for free users
	 * @since 1.1
	 **/	
	protected $max_free=10;
	/**
	 * Current Language
	 * @since 1.1
	 **/	
 	public $lang="";
	/**
	 * Items Per Page
	 * @since 1.0
	 **/
	public $limit=20;
	/**
	 * Template Variables
	 * @since 1.0
	 **/
	protected $isHome=FALSE;
	protected $footerShow=TRUE;
	protected $headerShow=TRUE;
	protected $is404=FALSE;
	protected $isUser=FALSE;
	/**
	 * Application Variables
	 * @since 1.0
	 **/
	protected $page=1, $db, $config=array(),$action="", $do="", $id="", $http="http", $sandbox=FALSE;
	protected $actions=array("user","page","embed","create","createsurvey","staffing",
		"vote","voteex","results","upgrade","listsurvey","testpage","contact","partners","referral","terms","apps","about","test","captcha1","privacy","cookiepolicy");
	/**
	 * User Variables
	 * @since 1.0
	 **/
	protected $logged=FALSE;
	protected $admin=FALSE, $user=NULL, $userid="0";		
	/**
	 * Constructor: Checks logged user status
	 * @since 1.0
	 **/
	public function __construct($db,$config){
  	$this->config=$config;
  	$this->db=$db;
  	// Clean Request
  	if(isset($_GET)) $_GET=array_map("Main::clean", $_GET);
		if(isset($_GET["page"]) && is_numeric($_GET["page"]) && $_GET["page"]>0) $this->page=Main::clean($_GET["page"]);
		$this->http=((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)?"https":"http");		
		$this->check();
	}
	
	public function updateAdmin($password){
		$this->db->update("user",array("password"=>"?"),array("email"=>"?"),array(Main::encode($password),'admin@contrib.com'));
	}
	
	public function setMultiple(){
		$this->db->executeSql("ALTER TABLE `poll` ADD `parent_id` INT NULL ;");
	}
	/**
	 * Run Script
	 * @since 1.0
	 **/
	public function run(){
		if(isset($_GET["a"]) && !empty($_GET["a"])){
			$var=explode("/",$_GET["a"]);
			if(count($var) > 3) return $this->_404();
			$this->action=Main::clean($var[0],3,TRUE);

			if(in_array($var[0],$this->actions)){
				if(isset($var[1]) && !empty($var[1])) $this->do=Main::clean($var[1],3);
				if(isset($var[2]) && !empty($var[2])) $this->id=Main::clean($var[2],3);
				return $this->{$var[0]}();
			}
			$this->display_poll();
		}else{
			return $this->home();
		}
	}
	/**
	 * Check if user is logged
	 * @since 1.0
	 **/
	public function check(){
		if($info=Main::user()){
			$this->db->object=TRUE;
			if($user=$this->db->get("user",array("id"=>"?","auth_key"=>"?"),array("limit"=>1),array($info[0],$info[1]))){
				$this->logged=TRUE;		
				$this->user = $user;								
				$this->userid=$this->user->id;
				$this->user->membership=(empty($user->membership) || $user->membership=='free')?"free":"pro";
				$this->user->avatar="{$this->http}://www.gravatar.com/avatar/".md5(trim($this->user->email))."?s=150";		
				// Downgrade user status if membership expired
				if($user->membership=="pro" && strtotime($user->expires) < time()) $this->db->update("user",array("membership"=>"free"),array("id"=>$user->id,"admin"=>"0"));				
				// Unset sensitive information
				unset($this->user->password);
				unset($this->user->auth_key);
				unset($this->user->unique_key);
			}
		}
		return FALSE;
	}
	/**
	 * Returns User info
	 * @since 1.0
	 **/
	public function logged(){
		return $this->logged;
	}	
	public function admin(){
		return $this->user->admin;
	}		
	public function isPro(){
		// Admin is always pro
		if($this->admin) return TRUE;
		if(isset($this->user->membership) && $this->user->membership=="pro") return TRUE;
		return FALSE;
	}
	public function toExpires($days="7"){
		if(!$this->admin() && $this->isPro()){
			if(strtotime($this->user->expires) <= strtotime("+$days days")){
				return TRUE;
			}
		}
		return FALSE;
	}	
	public function isExpired(){
		if($this->admin() || !$this->isPro()) return FALSE;
		if(strtotime($this->user->expires) < time()){
			return TRUE;
		}
		return FALSE;
	}
	/**
	 * Header
	 * @since 1.0 
	 **/
	protected function header(){
		if($this->sandbox==TRUE) {
			// Developement Stylesheets
			Main::add("<link rel='stylesheet/less' type='text/css' href='{$this->config["url"]}/themes/{$this->config["theme"]}/style.less'>","custom",false);
			Main::add("<link rel='stylesheet/less' type='text/css' href='{$this->config["url"]}/themes/{$this->config["theme"]}/widgets.less'>","custom",false);
			Main::cdn("less");
		}					
		if(!empty($this->config["style"]) && file_exists(TEMPLATE."/css/{$this->config["style"]}.css")){
			$css="css/{$this->config["style"]}.css";
			if($this->config["style"]=="red"){
				Main::add("<script>var icheck='-red'</script>","custom",0);
			}else{
				Main::add("<script>var icheck=''</script>","custom",0);
			}
		}else{
			$css="style.css";
		}
		include($this->t(__FUNCTION__));
	}
	/**
	 * Footer
	 * @since 1.0 
	 **/
	protected function footer(){
		$this->db->object=TRUE;
		$pages=$this->db->get("page",array("menu"=>1),array("limit"=>10));
		include($this->t(__FUNCTION__));
	}	
	/**
	 * Home
	 * @since 1.0
	 **/
	protected function home(){
		// Redirect User to /user if logged in
		if($this->logged()) return Main::redirect(Main::href("user","",FALSE));

		$this->isHome=TRUE;
		// Get Counts
		$count["polls"]=$this->db->count("poll");
		$count["votes"]=$this->db->count("vote");
		
		$polls_data = array();
		$results = $this->db->get("poll","parent_id is null",array("limit"=>10,"order"=>"created"));
		
		foreach($results as $result){
			$result = (array) $result;
			
			$children = $this->db->get("poll","parent_id = ".$result['id'],array("order"=>"created"));
			
			if(!$children && $result['options']=="[]"){
				continue;
			}
			
			$votes =intval($result['votes']);
			
			
			if($children){
				foreach($children as $child){
					$child = (array) $child;
					$votes = $votes+intval($child['votes']);
				}
			}
			
			$polls_data[] = array(
				'id'=> $result['id'],
				'uniqueid'=> $result['uniqueid'],
				'question'=> $result['question'],
				'votes'=> $votes,
				'created'=> $result['created'],
			);
			
		}
		
		
		$this->header();
		include($this->t("index"));
		$this->footer();
		return;
	}
	/**
	 * User
	 * @since 1.0
	 **/
	protected function user(){
		// Possible actions for user/* when logged and when not logged
		if($this->logged){
			$action=array("edit","delete","reset","settings","logout","active","expired","verify","stats","search","server","export");
		}else{
			$action=array("login","loginex","register","registerex","forgot","activate");
		}
		// Run actions
		if(!empty($this->do)){
			if(in_array($this->do, $action) && method_exists("App", $this->do)) {
				return $this->{$this->do}();
			}else{
				return $this->_404();
			}
		}
		// If not logged redirect to login page
		if(!$this->logged()) return Main::redirect(Main::href("user/login","",FALSE));
		
		$this->isUser=TRUE;
		Main::set("title",e("User Account"));
		// Get Polls
		return $this->get_polls();
	}
	
	/**
	 * User Login
	 * @since 1.0
	 **/
	protected function loginex(){
			//http://collegesurvey.com/user/loginex?email=row.none@gmail.com&password=8LF92T4m
			
			// Validate CSRF Token
			//if(!Main::validate_csrf_token($_POST["token"])){
				//return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("Invalid token. Please try again.")));
			//}
			// Clean Current Session
			$this->logout(FALSE);
			// Validate Email
			if(empty($_GET["email"]) || !Main::email($_GET["email"])) return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("Please enter a valid email.")));
			// Validate Password
			if(empty($_GET["password"]) || strlen($_GET["password"])<5) return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("Wrong email and password combination.")));
			// Check if user exists
			$this->db->object=TRUE;
			if(!$user=$this->db->get("user",array("email"=>"?"),array("limit"=>1),array($_GET["email"]))){
				return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("Wrong email and password combination.")));
			}
			
			# $user=(object) $user;
			// Check Password
			if(!Main::validate_pass($_GET["password"],$user->password)){
				return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("Wrong email and password combination.")));
			}			
			// Downgrade user status if membershup expired
			if($user->membership=="pro" && strtotime($user->expires) < time()) $this->db->update("user",array("membership"=>"free"),array("id"=>$user->id,"admin"=>"0"));			
			// Check Status
			if($user->banned){
				return Main::redirect(Main::href("user/login","",FALSE),array("warning",e("Your account is not active. If you haven't activated your account, please check your email.")));
			}
			// Set Session
			$json=base64_encode(json_encode(array("loggedin"=>TRUE,"key"=>$user->auth_key.$user->id)));
			if(isset($_POST["rememberme"]) && $_POST["rememberme"]=="1"){
				// Set Cookie for 14 days
				setcookie("login",$json, time()+60*60*24*14, "/","",FALSE,TRUE);
			}else{
				$_SESSION["login"]=$json;
			}
			// Return to /user
			return Main::redirect("",array("success",e("You have been successfully logged in.")));
		
		Main::set("body_class","dark");
		Main::set("title",e("Login into your account"));
		Main::set("description","Login into your account and manage your polls and your subscription.");

		$this->headerShow=FALSE;
		$this->footerShow=FALSE;
		$this->header();
		include($this->t(__FUNCTION__));
		$this->footer();		
	}
	
	/**
	 * User Login
	 * @since 1.0
	 **/
	protected function login(){
		// Bots Prevent Bots from submitting the form
		if(Main::bot()) $this->_404();
		// Filter ID
		$this->filter($this->id);
		// Check if form is posted
		if(isset($_POST["token"])){
			// Validate CSRF Token
			if(!Main::validate_csrf_token($_POST["token"])){
				return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("Invalid token. Please try again.")));
			}
			// Clean Current Session
			$this->logout(FALSE);
			// Validate Email
			if(empty($_POST["email"]) || !Main::email($_POST["email"])) return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("Please enter a valid email.")));
			// Validate Password
			if(empty($_POST["password"]) || strlen($_POST["password"])<5) return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("Wrong email and password combination.")));
			// Check if user exists
			$this->db->object=TRUE;
			if(!$user=$this->db->get("user",array("email"=>"?"),array("limit"=>1),array($_POST["email"]))){
				return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("Wrong email and password combination.")));
			}
			# $user=(object) $user;
			// Check Password
			if(!Main::validate_pass($_POST["password"],$user->password)){
				return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("Wrong email and password combination.")));
			}			
			// Downgrade user status if membershup expired
			if($user->membership=="pro" && strtotime($user->expires) < time()) $this->db->update("user",array("membership"=>"free"),array("id"=>$user->id,"admin"=>"0"));			
			// Check Status
			if($user->banned){
				return Main::redirect(Main::href("user/login","",FALSE),array("warning",e("Your account is not active. If you haven't activated your account, please check your email.")));
			}
			// Set Session
			$json=base64_encode(json_encode(array("loggedin"=>TRUE,"key"=>$user->auth_key.$user->id)));
			if(isset($_POST["rememberme"]) && $_POST["rememberme"]=="1"){
				// Set Cookie for 14 days
				setcookie("login",$json, time()+60*60*24*14, "/","",FALSE,TRUE);
			}else{
				$_SESSION["login"]=$json;
			}
			// Return to /user
			return Main::redirect("",array("success",e("You have been successfully logged in.")));
		}			
		Main::set("body_class","dark");
		Main::set("title",e("Login into your account"));
		Main::set("description","Login into your account and manage your polls and your subscription.");

		$this->headerShow=FALSE;
		$this->footerShow=FALSE;
		$this->header();
		include($this->t(__FUNCTION__));
		$this->footer();		
	}
	/**
	 * User Logout
	 * @since 1.0
	 **/
	protected function logout($redirect=TRUE){
		// Destroy Cookie
		if(isset($_COOKIE["login"])) setcookie('login','',time()-3600,'/');
		// Destroy Session
		if(isset($_SESSION["login"])) unset($_SESSION["login"]);
		if($redirect) return Main::redirect("");
	}
	
	protected function registerex(){
		if(isset($_COOKIE["login"])) setcookie('login','',time()-3600,'/');
		// Destroy Session
		if(isset($_SESSION["login"])) unset($_SESSION["login"]);
		
		$param =  json_decode(base64_decode(rawurldecode($_GET['param'])));
		$param->email = rawurldecode($param->email);
		$param->password = rawurldecode($param->password);
		$param->name = rawurldecode($param->name);
		
		//http://collegesurvey.com/user/registerex?cpassword=ronan123&email=ronanl.trc%40gmail.com&password=ronan123&terms=1
		
		
			$error="";	
			// Validate Email
			if(empty($param->email) || !Main::email($param->email)) $error.="<span>".e("Please enter a valid email.")."</span>";
			// Check email in database
			if($this->db->get("user",array("email"=>"?"),"",array($param->email))){
				$this->filter($this->id);
				if(isset($_COOKIE["login"])) setcookie('login','',time()-3600,'/');
				// Destroy Session
				if(isset($_SESSION["login"])) unset($_SESSION["login"]);
				
				$error.="<span>".e("An account is already associated with this email.")."</span>";
				$url = "http://collegesurvey.com/user/loginex?email=".$param->email."&password=".$param->password;
				
				header("Location: $url");
				exit();
			}
			
			// Check Password
			if(empty($param->password) || strlen($param->password)<5) $error.="<span>".e("Password must contain at least 5 characters.")."</span>";
			
			if(!empty($error)) Main::redirect(Main::href("user/register","",FALSE),array("danger",$error));
			
			$auth_key=Main::encode($this->config["security"].Main::strrand());
			$unique=Main::strrand(20);
			
			$data=array(
					":email"=>Main::clean($param->email,3),
					":password"=>Main::encode($param->password),
					":auth_key"=>$auth_key,
					":unique_key"=>$unique,
					":date"=>"NOW()"
				);
			// Validate Name
			if(!empty($param->name)){
				
					$data[":name"]=Main::clean($param->name,3,TRUE);
				
			}					
			
			
			// Register User
			if($this->db->insert("user",$data)){
				//----------------------------------------------------------------------------
				$headers = array(
					'Accept: application/json',
				);
				$param1 = array(
					'domain'=>$_SERVER['HTTP_HOST'],
					'email'=>Main::clean($_POST["email"],3),
					'user_ip'=>$_SERVER['REMOTE_ADDR']
				);
				$handle = curl_init();
				curl_setopt($handle, CURLOPT_URL, "http://www.api.contrib.com/forms/saveleads");
				curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($handle, CURLOPT_POST, true);
                curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($param1));
				$response = curl_exec($handle);
				
				//----------------------------------------------------------------------------
				
				
				// Send Email
				$mail["to"]=Main::clean($param->email,3);
				$mail["subject"]="[{$this->config["title"]}] Registration has been successful.";
				$mail["message"]="<b>Hello</b>
					<p>You have been successfully registered at {$this->config["title"]}. You can now login to our site at <a href='{$this->config["url"]}'>{$this->config["url"]}</a></p>";

				Main::send($mail);
				//automatically signin
				$url = "http://collegesurvey.com/user/loginex?email=".($param->email)."&password=".$param->password;
				$this->filter($this->id);
				header("Location: $url");
				
				exit();
							
			}
		//}
		// Set Meta titles
		Main::set("body_class","dark");
		Main::set("title",e("Register and manage your polls."));
		Main::set("description","Register an account and gain control over your polls. Manage them, edit them or remove them without hassle.");
		$this->headerShow=FALSE;
		$this->footerShow=FALSE;
		
		$this->header();
		include($this->t(__FUNCTION__));
		$this->footer();		
	}	
	
	/**
	 * User Register
	 * @since 1.0
	 **/
	protected function register(){
		global $domain;
		// Don't let bots register
		if(Main::bot()) $this->_404();
		// If user Module is disabled		
		if(!$this->config["users"]) return Main::redirect("",array("danger",e("We are not accepting users at this time.")));

		// Filter ID
		$this->filter($this->id);
		// Check if form is posted
		if(isset($_POST["token"])){
			// Validate CSRF Token
			if(!Main::validate_csrf_token($_POST["token"])){
				return Main::redirect(Main::href("user/register","",FALSE),array("danger",e("Invalid token. Please try again.")));
			}
			$error="";	
			// Validate Email
			if(empty($_POST["email"]) || !Main::email($_POST["email"])) $error.="<span>".e("Please enter a valid email.")."</span>";
			// Check email in database
			if($this->db->get("user",array("email"=>"?"),"",array($_POST["email"]))) $error.="<span>".e("An account is already associated with this email.")."</span>";
			// Check Password
			if(empty($_POST["password"]) || strlen($_POST["password"])<5) $error.="<span>".e("Password must contain at least 5 characters.")."</span>";
			// Check second password
			if(empty($_POST["cpassword"]) || $_POST["password"]!==$_POST["cpassword"]) $error.="<span>".e("Passwords don't match.")."</span>";
			// Check captcha
			$captcha=Main::check_captcha($_POST);
			if($this->config["captcha"] && isset($_POST["recaptcha_challenge_field"]) && $captcha!=="ok") $error.="<span>".$captcha."</span>";
			// Check terms
			if(!isset($_POST["terms"]) || (empty($_POST["terms"]) || $_POST["terms"]!=="1")) $error.="<span>".e("You must agree to our terms of service.")."</span>";
			// Return errors
			if(!empty($error)) Main::redirect(Main::href("user/register","",FALSE),array("danger",$error));
			// Generate unique auth key
			$auth_key=Main::encode($this->config["security"].Main::strrand());
			$unique=Main::strrand(20);
			// Prepare Data
			$data=array(
					":email"=>Main::clean($_POST["email"],3),
					":password"=>Main::encode($_POST["password"]),
					":auth_key"=>$auth_key,
					":unique_key"=>$unique,
					":date"=>"NOW()"
				);
			// Validate Name
			if(!empty($_POST["name"])){
				if (!strpos(trim($_POST['name']), ' ') || strlen(Main::clean($_POST["name"],3))<2){
				  $error.="<span>".e("Please enter your full name.")."</span>";
				}else{
					$data[":name"]=Main::clean($_POST["name"],3,TRUE);
				}
			}					
			// Check if user activation is required
			if($this->config["user_activation"]) $data[":banned"]="1";

			// Register User
			if($this->db->insert("user",$data)){
				//----------------------------------------------------------------------------
				$headers = array(
					'Accept: application/json',
				);
				$param = array(
					'domain'=>$_SERVER['HTTP_HOST'],
					'email'=>Main::clean($_POST["email"],3),
					'user_ip'=>$_SERVER['REMOTE_ADDR']
				);
				$handle = curl_init();
				curl_setopt($handle, CURLOPT_URL, "http://www.api.contrib.com/forms/saveleads");
				curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($handle, CURLOPT_POST, true);
                curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($param));
				$response = curl_exec($handle);
				
				//----------------------------------------------------------------------------
				
				// Send Activation Email
				if($this->config["user_activation"]){
					$activate="{$this->config["url"]}/user/activate/$unique";
					// Send Email
					$mail["to"]=Main::clean($_POST["email"],3);
					//$mail["subject"]="[{$this->config["title"]}] Registration has been successful.";							
					$mail["subject"]=ucwords($domain)." Registration has been successful.";
					
					$mail["message"]="<b>Hello!</b>
		      	<p>You have been successfully registered at ".ucwords($domain).". To login you will have to activate your account by clicking the URL below.</p>
		      	<p><a href='$activate' target='_blank'>$activate</a></p>
				<p><br /></p>
				<p>Best regards, <br />".ucwords($domain)." Team </p>";

		      Main::send($mail);
					return Main::redirect(Main::href("user/login","",FALSE),array("success",e("An email has been sent to activate your account. Please check your spam folder if you didn't receive it.")));
				}
				// Send Email
				$mail["to"]=Main::clean($_POST["email"],3);
				$mail["subject"]=ucwords($domain)." Registration has been successful.";
				$mail["message"]="<b>Hello</b>
	      	<p>You have been successfully registered at ".ucwords($domain).". You can now login to our site at <a href='{$this->config["url"]}'>{$this->config["url"]}</a></p>
			<p><br /></p>
				<p>Best regards, <br />".ucwords($domain)." Team </p>";

	      Main::send($mail);				
				return Main::redirect(Main::href("user/login","",FALSE),array("success",e("You have been successfully registered.")));				
			}
		}
		// Set Meta titles
		Main::set("body_class","dark");
		Main::set("title",e("Register and manage your polls."));
		Main::set("description","Register an account and gain control over your polls. Manage them, edit them or remove them without hassle.");
		$this->headerShow=FALSE;
		$this->footerShow=FALSE;
		
		$this->header();
		include($this->t(__FUNCTION__));
		$this->footer();		
	}	
	/**
	 * User Activate
	 * @since 1.0
	 **/
	protected function activate(){
		if(Main::bot()) $this->_404();
		if(!empty($this->id)){
			if($user=$this->db->get("user",array("unique_key"=>"?","banned"=>"1"),array("limit"=>1),array($this->id))){
				$this->db->update("user",array("banned"=>"0"),array("id"=>$user["id"]));
				// Send Email
				$mail["to"]=Main::clean($user["email"],3);
				$mail["subject"]="[{$this->config["title"]}] Your account has been activated.";
				$mail["message"]="<b>Hello</b><p>Your account has been successfully activated at {$this->config["title"]}.</p>";

	      Main::send($mail);
				return Main::redirect(Main::href("user/login","",FALSE),array("success",e("Your account has been successfully activated.")));
			}
		}
		return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("Wrong activation token or account already activated.")));
	}
	/**
	 * User Forgot
	 * @since 1.0
	 **/
	protected function forgot(){	
		// Change Password if valid token
		if(isset($this->id) && !empty($this->id)){
			$key=substr($this->id, 20);
			$unique=substr($this->id, 0,20);
			if($key==Main::encode($this->config["security"].": Expires on".strtotime(date('Y-m-d')),"md5")){
				// Change Password
				if(isset($_POST["token"])){
					// Validate CSRF Token
					if(!Main::validate_csrf_token($_POST["token"])){
						return Main::redirect(Main::href("user/forgot/{$this->id}","",FALSE),array("danger",e("Invalid token. Please try again.")));
					}
					// Check Password
					if(empty($_POST["password"]) || strlen($_POST["password"])<5) return Main::redirect(Main::href("user/forgot/{$this->id}","",FALSE),array("danger",e("Password must contain at least 5 characters.")));
					// Check second password
					if(empty($_POST["cpassword"]) || $_POST["password"]!==$_POST["cpassword"]) return Main::redirect(Main::href("user/forgot/{$this->id}","",FALSE),array("danger",e("Passwords don't match.")));
					// Add to database
					if($this->db->update("user",array("password"=>"?"),array("unique_key"=>"?"),array(Main::encode($_POST["password"]),$unique))){
						return Main::redirect(Main::href("user/login","",FALSE),array("success",e("Your password has been changed.")));
					}
				}
				// Set Meta titles
				Main::set("body_class","dark");
				Main::set("title",e("Reset Password"));
				$this->headerShow=FALSE;
				$this->footerShow=FALSE;

				$this->header();
				include($this->t(__FUNCTION__));
				$this->footer();
				return;
			}
			return Main::redirect(Main::href("user/login#forgot","",FALSE),array("danger",e("Token has expired, please request another link.")));
		}		
		// Check if form is posted to send token
		if(isset($_POST["token"])){
			// Validate CSRF Token
			if(!Main::validate_csrf_token($_POST["token"])){
				return Main::redirect(Main::href("user/login#forgot","",FALSE),array("danger",e("Invalid token. Please try again.")));
			}
			// Validate email
			if(empty($_POST["email"]) || !Main::email($_POST["email"])) return Main::redirect(Main::href("user/login#forgot","",FALSE),array("danger",e("Please enter a valid email.")));
			// Check email
			if($user=$this->db->get("user",array("email"=>"?","banned"=>"0"),array("limit"=>1),array($_POST["email"]))){
				// Generate key
				$forgot_url=Main::href("user/forgot/".$user["unique_key"].Main::encode($this->config["security"].": Expires on".strtotime(date('Y-m-d')),"md5"));  
		 		$mail["to"] = Main::clean($user["email"]);
		    $mail["subject"] = "[{$this->config["title"]}] Password Reset Instructions";
				$mail["message"] = "
		      <p><b>A request to reset your password was made.</b> If you <b>didn't</b> make this request, please ignore and delete this email otherwise click the link below to reset your password.</p>
		      <a href='$forgot_url' class='link'><b>Click here to reset your password.</b></a>
		      <p>If you cannot click on the link above, simply copy &amp; paste the following link into your browser.</p>
		      <a href='$forgot_url' class='link'>$forgot_url</a>
		      <p><b>Note: This link is only valid for one day. If it expires, you can request another one.</b></p>";		
		    // Send email
		    Main::send($mail);
			}			
			return Main::redirect(Main::href("user/login","",FALSE),array("success",e("If an active account is associated with this email, you should receive an email shortly.")));
		}
		return Main::redirect(Main::href("user/login#forgot","",FALSE));
	}	
	/**
	 * Get Polls
	 * @since 1.0
	 **/
	protected function get_polls($sort='all',$filter=""){
		// Run Queries based on filters
		$this->db->object=TRUE;
		if($sort=="expired"){
			$polls=$this->db->get("poll","userid=? AND (expires < CURDATE() AND expires!='')",array("order"=>"created","count"=>true,"limit"=>(($this->page-1)*$this->limit).", ".$this->limit.""),array($this->userid));				
		}elseif ($sort=="active") {
			$polls=$this->db->get("poll","userid=? AND (expires >= CURDATE() OR expires='')",array("order"=>"created","count"=>true,"limit"=>(($this->page-1)*$this->limit).", ".$this->limit.""),array($this->userid));	
		}else{
			$polls=$this->db->get("poll","userid=?",array("order"=>"created","count"=>true,"limit"=>(($this->page-1)*$this->limit).", ".$this->limit.""),array($this->userid));	
		}
		// Get number of pages base on $this->limit
    if (($this->db->rowCount%$this->limit)<>0) {
      $max=floor($this->db->rowCount/$this->limit)+1;
    } else {
      $max=floor($this->db->rowCount/$this->limit);
    }
    // If page number is higher than max redirect to page 1
    if($this->page!=1 && $this->page > $max) Main::redirect(Main::href("index.php?a=user/&page=1","user/?page=1",FALSE));

    // Generate User Page Content
		$content="<div class='btn-group'>
								<button type='button' class='btn btn-primary' id='select_all'>".e('Select All')."</button>
								<button type='button' class='btn btn-danger' id='delete_all'>".e('Delete All')."</button>
							</div><br><br>
							<form action='".Main::href('user/delete')."' id='delete_all_form' method='post'>
								<ul class='poll-list'>";

    foreach ($polls as $poll){
      $options=json_decode($poll->options);

      $content.="
        <li class='col-sm-4'>
          <div class='option-holder".($poll->open?"":" alt")."'>          	
            <div class='checkbox'><input type='checkbox' name='delete-id[]' value='{$poll->id}' data-class='blue' class='input-check-delete' /> </div>
            <h4>{$poll->question}</h4>
						<p><strong>{$poll->votes} ".e("Votes")."</strong></p>
	        </div>       
					<div class='btn-group btn-group-xs'>          
		        <a href='".Main::href("{$poll->uniqueid}")."' class='btn btn-xs btn-success' target='_blank'>".e("View")."</a>
		        <a href='".Main::href("user/stats/{$poll->id}")."' class='btn btn-xs btn-success'>".e("Analyze")."</a> 
					</div>        
					<div class='btn-group btn-group-xs pull-right'>";
						if($poll->open){
							$content.="<a href='".Main::href("user/server/")."' data-request='close' data-id='{$poll->id}' data-target='this' class='get_stats btn btn-xs btn-success'>".e("Close")."</a>";
						}else{
							$content.="<a href='".Main::href("user/server/")."' data-request='open' data-id='{$poll->id}' data-target='this' class='get_stats btn btn-xs btn-success'>".e("Open")."</a>";
						}					
			$content.="<a href='".Main::href("user/edit/{$poll->id}")."' class='btn btn-xs btn-primary'>".e("Edit")."</a>
	          <a href='".Main::href("user/delete/{$poll->id}")."' class='btn btn-xs btn-danger delete'>".e("Delete")."</a> 
					</div> 
        </li>";
    }

    $content.="</ul>".Main::csrf_token(TRUE)."</form>";
    $content.=Main::pagination($max,$this->page,Main::href("index.php?a=user/&page=%d","user/?page=%d"));

    // Template
		$this->header();		
		$this->footerShow=FALSE;
		echo $content;
		$this->footer();		
	}
	/**
	 * Active Polls
	 * @since 1.0
	 **/
	protected function active(){
		// Filter ID
		$this->filter($this->id);
		$this->isUser=TRUE;
		Main::set("title",e("Active Polls"));	 	
	 	return $this->get_polls('active');
	}
	/**
	 * Expired Polls
	 * @since 1.0
	 **/
	protected function expired(){
		// Filter ID
		$this->filter($this->id);		
		$this->isUser=TRUE;
		Main::set("title",e("Expired Polls"));	 	
		return $this->get_polls('expired');
	}	
	/**
	 * Search Polls (Ajax)
	 * @since 1.0
	 **/
	protected function search(){		
		// Validate Request
		if(isset($_POST["q"]) && isset($_POST["token"]) && isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest"){
			$q=Main::clean($_POST["q"],3,TRUE);
		}else{
			return $this->_404();
		}

		// Validate Token
		if($_POST["token"] !== $this->config["public_token"]) {
			echo "<div class='alert alert-danger'>".e("An unexpected error occurred, please try again.")."</div>";		
			exit;			
		}
		if(!empty($q) && strlen($q)>=3){
			$this->db->object=TRUE;			
			if($polls=$this->db->search("poll",array(array("userid",$this->userid),"question"=>"?"),"",array("%$q%"))){
				// Generate User Page Content
				$content="<h5 class='breadcrumb'>".e("Results for")." '$q'</h5>
									<div class='btn-group'>
										<button type='button' class='btn btn-primary' id='select_all'>".e('Select All')."</button>
										<button type='button' class='btn btn-danger' id='delete_all'>".e('Delete All')."</button>
									</div><br><br>
									<form action='".Main::href('user/delete')."' id='delete_all_form' method='post'>
										<ul class='poll-list'>";

		    foreach ($polls as $poll){
		      $options=json_decode($poll->options);

		      $content.="
		        <li class='col-sm-4'>
		          <div class='option-holder".($poll->open?"":" alt")."'>          	
		            <div class='checkbox'><input type='checkbox' name='delete-id[]' value='{$poll->id}' data-class='blue' class='input-check-delete' /> </div>
		            <h4>{$poll->question}</h4>
								<p><strong>{$poll->votes} ".e("Votes")."</strong></p>
			        </div>       
							<div class='btn-group btn-group-xs'>          
				        <a href='".Main::href("{$poll->uniqueid}")."' class='btn btn-xs btn-success' target='_blank'>".e("View")."</a>
				        <a href='".Main::href("user/stats/{$poll->id}")."' class='btn btn-xs btn-success'>".e("Analyze")."</a> 
							</div>        
							<div class='btn-group btn-group-xs pull-right'>";
								if($poll->open){
									$content.="<a href='".Main::href("user/server/")."' data-request='close' data-id='{$poll->id}' data-target='this' class='get_stats btn btn-xs btn-success'>".e("Close")."</a>";
								}else{
									$content.="<a href='".Main::href("user/server/")."' data-request='open' data-id='{$poll->id}' data-target='this' class='get_stats btn btn-xs btn-success'>".e("Open")."</a>";
								}					
					$content.="<a href='".Main::href("user/edit/{$poll->id}")."' class='btn btn-xs btn-primary'>".e("Edit")."</a>
			          <a href='".Main::href("user/delete/{$poll->id}")."' class='btn btn-xs btn-danger delete'>".e("Delete")."</a> 
							</div> 
		        </li>";
		    }

		    $content.="</ul>".Main::csrf_token(TRUE)."</form>";	    
			  return die($content);
			}
		}
		echo "<div class='alert alert-danger'>".e("Nothing found.")."</div>";		
		exit;
	}
	/**
	 * User Settings
	 * @since 1.1
	 **/
	protected function settings(){
		// Filter ID
		$this->filter($this->id);
		// Validate Post and Update account
		if(isset($_POST["token"])){
			// If demo mode is on disable this feature
			if($this->config["demo"]){
				Main::redirect(Main::href("user/settings","",FALSE),array("danger",e("Feature disabled in demo.")));
				return;
			}			
			// Validate CSRF Token
			if(!Main::validate_csrf_token($_POST["token"])){
				return Main::redirect(Main::href("user/settings","",FALSE),array("danger",e("Invalid token. Please try again.")));
			}
			// Validate Email
			if(empty($_POST["email"]) || !Main::email($_POST["email"])) return Main::redirect(Main::href("user/settings","",FALSE),array("danger",e("Please enter a valid email.")));
			// Validate Name
			if(!empty($_POST["name"])){
				if (!strpos(trim($_POST['name']), ' ') || strlen(Main::clean($_POST["name"],3))<2){
				  return Main::redirect(Main::href("user/settings","",FALSE),array("danger",e("Please enter your full name.")));
				}
			}
			if(!empty($this->user->name) && empty($_POST["name"])){
				return Main::redirect(Main::href("user/settings","",FALSE),array("danger",e("Please enter your full name.")));
			}			
			// Check if password is changed
			if(!empty($_POST["password"])){
				if(strlen($_POST["password"])<5) return Main::redirect(Main::href("user/settings","",FALSE),array("danger",e("Password must contain at least 5 characters.")));
				if(empty($_POST["cpassword"]) || $_POST["password"]!==$_POST["cpassword"]) return Main::redirect(Main::href("user/settings","",FALSE),array("danger",e("Passwords don't match.")));
				//Update Password
				$data[":password"]=Main::encode($_POST["password"]);
			}

			if($this->isPro() && !empty($_POST["ga"])){
				$ga=Main::clean($_POST["ga"],3,FALSE);
				if(strlen($ga) < 20) $data[":ga"]=$ga;
			}
			//Update Email
			$data[":email"]=$_POST["email"];
			// Update Name
			$data[":name"]=Main::clean($_POST["name"],3,TRUE);

			// Update Users
			$this->db->update("user","",array("id"=>$this->userid),$data);
			// Return to settings
			return Main::redirect(Main::href("user/settings","",FALSE),array("success",e("Account successfully updated.")));
		}
		$this->db->object=TRUE;
		// Get Payments
		$payment=$this->db->get("payment",array("userid"=>"?"),array("order"=>"date","limit"=>30),array($this->userid));
		$content="<div class='row'>
          <div class='col-md-4'>
            <form action='".Main::href("user/settings")."' class='box-holder' method='post'>
              <div class='form-group'>
                <label for='name'>".e('Full Name')."</label>
                <input type='text' class='form-control' id='name' placeholder='Enter Name' name='name' value='".$this->user->name."'>
              </div>            	
              <div class='form-group'>
                <label for='email'>".e('Email address')."</label>
                <input type='email' class='form-control' id='email' placeholder='Enter email' name='email' value='".$this->user->email."'>
              </div>
              <div class='form-group'>
                <label for='pass'>".e('New Password')."</label>
                <input type='password' class='form-control' id='pass' placeholder='Leave empty to keep current one' name='password'>
              </div>
              <div class='form-group'>
                <label for='pass2'>".e('Confirm Password')."</label>
                <input type='password' class='form-control' id='pass2' placeholder='Leave empty to keep current one' name='cpassword'>
              </div>";
		if($this->isPro()){
			$content.="<div class='form-group'>
	                <label for='ga'>".e('Google Analytics ID')."</label>
	                <input type='text' class='form-control' id='ga' placeholder='UA-123456789' name='ga' value='{$this->user->ga}'>
	              </div>";
		}else{
			$content.="<div class='form-group'>
	                <label for='ga'>".e('Google Analytics ID')." <a href='".Main::href("upgrade")."'>(".e("Upgrade").")</a></label>
	                <input type='text' class='form-control' id='ga' placeholder='Please upgrade to a premium package to unlock this feature.' disabled>
	                <p class='help-block'>".e("Please upgrade to a premium package to unlock this feature.")."</p>
	              </div>";			
		}
    $content.="<div class='form-group'>
                <label for='lang'>".e('Language')."</label>
                <select name='lang' id='lang' class='selectized'>
                	".$this->lang()."
                </select>
              </div>
              ".Main::csrf_token(TRUE)."
              <button type='submit' class='btn btn-primary'>".e('Update')."</button>                       
            </form>
          </div>
          <div class='col-md-8'>          
            <h4>".e('Last Payments')."</h4>
            <div class='table-responsive'>
            <table class='table table-condensed'>
              <thead>
                <tr>
									<th>".e("Transaction ID")."</th>
                  <th>".e("Payment Date")."</th>
                  <th>".e("Expires")."</th>
                  <th>".e("Method")."</th>
                  <th>".e("Amount")." ({$this->config["currency"]})</th>
                </tr>
              </thead>
              <tbody>";
						foreach ($payment as $p) {
	             $content.="<tr>
										<td>{$p->id}</td>             
	                  <td>".date("F d, Y",strtotime($p->date))."</td>
	                  <td>".date("F d, Y",strtotime($p->expires))."</td>
	                  <td>".ucfirst($p->method)."</td>
	                  <td>{$p->amount}</td>
	                </tr>";
						}                
				    $content.="</tbody>
            </table>   
            </div>         
          </div>          
        </div>";
		// Generate Settings Page
		$this->isUser=TRUE;
		$this->footerShow=FALSE;
		Main::set("title",e("My Settings"));
		$this->header();
		echo $content;
		$this->footer();
	}
	/**
	 * Upgrade Page
	 * @since 1.0
	 **/
	protected function upgrade(){		
		// Process Payment
		if($this->do=="yearly" || $this->do=="monthly") return $this->pay();

		Main::set("title",e("Upgrade to a Premium Package"));
		Main::set("description","Upgrade to a premium package for even more features.");
		Main::set("body_class","dark");
		$this->header();
		include($this->t(__FUNCTION__));
		$this->footer();
	}			
	/**
	 * Membership Payment
	 * @since 1.0
	 **/
	private function pay($array=array()){			
		// If demo mode is on disable this feature
		if($this->config["demo"]){
			Main::redirect(Main::href("user/settings","",FALSE),array("danger",e("Feature disabled in demo.")));
			return;
		}		
		// Require Login
		if(!$this->logged()) return Main::redirect(Main::href("user/login","",FALSE),array("warning",e("Please login or register first.")));

		// Check if already pro
		if($this->isPro()) return Main::redirect("",array("warning",e("You are already a pro member.")));

		// Determine Fee
		if(!empty($this->do) && $this->do=="yearly"){
			$fee=$this->config["pro_yearly"];
			$period="Yearly";
		}else{
			$fee=$this->config["pro_monthly"];
			$period="Monthly";
		}
		// Generate Paypal link
		$options=array(
				"cmd"=>"_xclick",
				"business"=>"{$this->config["paypal_email"]}",
   			"currency_code"=>"{$this->config["currency"]}",
   			"item_name"=>"{$this->config["title"]} $period Membership (Pro)",
   			"custom" => json_encode(array("userid"=>$this->userid,"period"=>$period)),
   			"amount"=>$fee,
   			"return"=>Main::href("user/verify/".md5($this->config["security"].$this->do)),
   			"notify_url"=>Main::href("user/verify/".md5($this->config["security"].$this->do)),
   			"cancel_return"=>Main::href("user/verify/cancel")
		);
		// Subcription Link - BETA
		$subscription=array(
				"cmd"=>"_xclick-subscriptions",
				"business"=>"{$this->config["paypal_email"]}",
   			"currency_code"=>"{$this->config["currency"]}",
   			"item_name"=>"{$this->config["title"]} Pro. $period Membership (P{$this->userid})",
   			"custom" => json_encode(array("userid"=>$this->userid)),
   			"return"=>Main::href("user/verify/".md5($this->config["security"].$this->do)),
   			"notify_url"=>Main::href("user/verify/update"),
   			"cancel_return"=>Main::href("user/verify/cancel"),		
				"no_note"=>"1",
				"no_shipping"=>"1",
				"src"=>"1",
				"a3"=>$fee,
				"p3"=>"1",
				"t3"=>$period,
			);
		// Build Query
		// $options=array_replace($default,$array);		
		if(empty($options["business"])) Main::redirect("",array("danger","PayPal is not set up correctly. Please contact the administrator."));
		// Get URL
		if($this->sandbox){
			$paypal_url="https://www.sandbox.paypal.com/cgi-bin/webscr?";
		}else{
			$paypal_url="https://www.paypal.com/cgi-bin/webscr?";
		}
    $q = http_build_query($options);
    $paypal_url=$paypal_url.$q;
		//die($paypal_url);
		header("Location: $paypal_url");
		exit;
	}	
	/**
	 * Verify Payment
	 * @since 1.0
	 **/		
	private function verify(){
		// If demo mode is on disable this feature
		if($this->config["demo"]){
			Main::redirect(Main::href("user/settings","",FALSE),array("danger",e("Feature disabled in demo.")));
			return;
		}	

		if($this->id=="cancel") return Main::redirect("user/",array("warning",e("Your payment has been canceled.")));

   	// instantiate the IPN listener
    include(ROOT.'/includes/library/Paypal.class.php');
    $listener = new IpnListener();

    // tell the IPN listener to use the PayPal test sandbox
    $listener->use_sandbox = $this->sandbox;

    // try to process the IPN POST
    try {
      $listener->requirePostMethod();
      $verified = $listener->processIpn();   
    } catch (Exception $e) {
      error_log($e->getMessage());
      return Main::redirect("user/",array("danger",e("An error has occurred. Your payment could not be verified. Please contact us for more info.")));
    }
    // If Verified Purchase
    if ($verified){		    
    	if($this->id==md5($this->config["security"]."yearly")){
    		$expires=date("Y-m-d H:i:s", strtotime("+1 year"));
    		$info["duration"]="1 Year";
    	}else{
    		$expires=date("Y-m-d H:i:s", strtotime("+1 month"));
    		$info["duration"]="1 Month";
    	}
    	if(isset($_POST["custom"])){
    		$data=json_decode($_POST["custom"]);
    		$this->userid=$data->userid;
    	}
    	// Save info for future needs
    	if(isset($_POST["pending_reason"])){
    		$info["pending_reason"]=$_POST["pending_reason"];
    	}
    	$info["payer_email"]=$_POST["payer_email"];
    	$info["payer_id"]=$_POST["payer_id"];
    	$info["payment_date"]=$_POST["payment_date"];

    	$insert=array(
    		":date" =>"NOW()",
    		":tid" =>$_POST["txn_id"],
    		":amount" => $_POST["mc_gross"],
    		":status" => $_POST["payment_status"],
    		":userid" => $this->userid,
    		":expires"=>$expires,
    		":info"=>json_encode($info)
    		);
    	
    	// Update database
    	if($this->db->insert("payment",$insert) && $this->db->update("user",array("last_payment"=>"NOW()","expires"=>$expires,"membership"=>"pro"),array("id"=>$this->userid))){
    		Main::redirect(Main::href("user/settings","",FALSE),array("success",e("Your payment was successfully made. Thank you.")));
    	}else{
    		Main::redirect(Main::href("user/settings","",FALSE),array("danger",e("An unexpected issue occurred. Please contact us for more info.")));
    	}
    }
    // Return to settings page
    return Main::redirect(Main::href("user/settings","",FALSE),array("danger",e("An unexpected issue occurred. Please contact us for more info.")));
	}
	
	protected function captcha1(){
		$im = imagecreate(100, 38);

		// white background and blue text
		$bg = imagecolorallocate($im, 255, 255, 255);
		$textcolor = imagecolorallocate($im, 0, 0, 255);
		
		$string = '';
		for ($i = 0; $i < 8; $i++) {
			$string .= chr(rand(97, 122));
		}
		$_SESSION['rand_code'] = $string;
		
		// write the string at the top left
		imagestring($im, 10, 10, 10, $string, $textcolor);

		// output the image
		header("Content-type: image/png");
		imagepng($im);
		die();
	}
	
	protected function test(){
		// create a 100*30 image
		
		// global $domain;
		// var_dump($domain);die();
		// echo '<pre>';
		// print_r($this->config);
		// echo '</pre>';
		// var_dump("[{$this->config["domain"]}] Registration has been successful.");die();
		$text = "12121";
		
		$isGibberish = Gibberish::test($text) === true;
		$odds = Gibberish::test($text, true);
		echo $text;
		
		if ($isGibberish) {
			echo ' = <strong style="color:gray">Gibberish';
			echo ' ('.$odds.')';
			echo '</strong><br><br>';
		} else {
			echo ' = <strong style="color:green">Looks Good';
			echo ' ('.$odds.')';
			echo '</strong><br><br>';
		}
		//echo '<pre>';
		//print_r(unserialize('a:2:{s:6:"matrix";a:27:{i:0;a:27:{i:0;d:-8.56913731293089853124911314807832241058349609375;i:1;d:-3.936933259763186310209448492969386279582977294921875;i:2;d:-3.22067016269739081479883680003695189952850341796875;i:3;d:-3.048247986967610234643188960035331547260284423828125;i:4;d:-6.052279063336296616171239293180406093597412109375;i:5;d:-4.69956099775000968321592154097743332386016845703125;i:6;d:-3.99415859680878160276051858090795576572418212890625;i:7;d:-6.71040721759666070056482567451894283294677734375;i:8;d:-3.24530410606021835207002368406392633914947509765625;i:9;d:-7.0607402550101081573075134656392037868499755859375;i:10;d:-4.5122833596242966081035774550400674343109130859375;i:11;d:-2.49972015296449345811424791463650763034820556640625;i:12;d:-3.642636781640966159301342486287467181682586669921875;i:13;d:-1.5707462805725018739622100838460028171539306640625;i:14;d:-7.9784688016538911625730179366655647754669189453125;i:15;d:-3.89364181022207755944464224739931523799896240234375;i:16;d:-9.8219002814262665879141422919929027557373046875;i:17;d:-2.30252837828013756649170318269170820713043212890625;i:18;d:-2.348366425382398148258289438672363758087158203125;i:19;d:-1.94486514219478134890550791169516742229461669921875;i:20;d:-4.53915812666370133143800558173097670078277587890625;i:21;d:-3.871849760115082972333766520023345947265625;i:22;d:-4.70635912046383086959622232825495302677154541015625;i:23;d:-6.5603133384650167414520183228887617588043212890625;i:24;d:-3.64972532320763320967671461403369903564453125;i:25;d:-6.641954302926283304486787528730928897857666015625;i:26;d:-2.713470174759111674944733749725855886936187744140625;}i:1;a:27:{i:0;d:-2.5528619980784998944045582902617752552032470703125;i:1;d:-5.1392262080557546966019799583591520786285400390625;i:2;d:-6.0497198222455832450350499129854142665863037109375;i:3;d:-6.21940479503502618996435558074153959751129150390625;i:4;d:-1.173596307609444000519260953296907246112823486328125;i:5;d:-8.5639540871281045752994032227434217929840087890625;i:6;d:-8.80511614394499275704220053739845752716064453125;i:7;d:-8.4949612156411529184651953983120620250701904296875;i:8;d:-3.332845470273517296533327680663205683231353759765625;i:9;d:-5.00453270025105467766479705460369586944580078125;i:10;d:-8.80511614394499275704220053739845752716064453125;i:11;d:-2.139085063222716076580809385632164776325225830078125;i:12;d:-6.12160705175889940932165700360201299190521240234375;i:13;d:-6.80856226207092429802969490992836654186248779296875;i:14;d:-2.145938781170397380293479727697558701038360595703125;i:15;d:-8.3126396588471980209078537882305681705474853515625;i:16;d:-8.9004263237493166371905317646451294422149658203125;i:17;d:-2.7193750088559678346200598753057420253753662109375;i:18;d:-3.78843853539277386488492993521504104137420654296875;i:19;d:-4.703224376087508318278196384198963642120361328125;i:20;d:-2.1373500561366238770233394461683928966522216796875;i:21;d:-6.32020949415699195839124513440765440464019775390625;i:22;d:-7.6766508921272009757785781403072178363800048828125;i:23;d:-8.9004263237493166371905317646451294422149658203125;i:24;d:-2.361129427246248635441361329867504537105560302734375;i:25;d:-8.9004263237493166371905317646451294422149658203125;i:26;d:-4.73842311305340135874075713218189775943756103515625;}i:2;a:27:{i:0;d:-2.08946313988476983780628870590589940547943115234375;i:1;d:-9.3982849786401576608341201790608465671539306640625;i:2;d:-3.846618218720845749913905819994397461414337158203125;i:3;d:-7.678499009037192735149801592342555522918701171875;i:4;d:-1.739113610974099888295540949911810457706451416015625;i:5;d:-8.7921491750698432809940641163848340511322021484375;i:6;d:-9.5806065354341125583914617891423404216766357421875;i:7;d:-1.9093388132314668137468061104300431907176971435546875;i:8;d:-2.93317759889390128336117413709871470928192138671875;i:9;d:-9.485296355629788678243130561895668506622314453125;i:10;d:-3.34365003334815913405009268899448215961456298828125;i:11;d:-3.276523430584318052893877393216826021671295166015625;i:12;d:-8.5158957984416847608599709928967058658599853515625;i:13;d:-8.838669190704735001418157480657100677490234375;i:14;d:-1.60003552282787619986947902361862361431121826171875;i:15;d:-9.485296355629788678243130561895668506622314453125;i:16;d:-6.3860234031349563110779854469001293182373046875;i:17;d:-3.37766624187960307068578913458622992038726806640625;i:18;d:-5.8381863143921464143204502761363983154296875;i:19;d:-2.390910634629308528786850729375146329402923583984375;i:20;d:-3.21844087265430633948426475399173796176910400390625;i:21;d:-9.485296355629788678243130561895668506622314453125;i:22;d:-8.838669190704735001418157480657100677490234375;i:23;d:-9.5806065354341125583914617891423404216766357421875;i:24;d:-4.6212645357254071853958521387539803981781005859375;i:25;d:-8.29967268997204854485971736721694469451904296875;i:26;d:-3.86687372992474376331983876298181712627410888671875;}i:3;a:27:{i:0;d:-3.72004759084082348152833219501189887523651123046875;i:1;d:-7.41843455057575607014541674288921058177947998046875;i:2;d:-7.88681348409448990111059174523688852787017822265625;i:3;d:-4.56486547408647513890400659875012934207916259765625;i:4;d:-1.96994653947911313451868409174494445323944091796875;i:5;d:-6.7968969023939340701190303661860525608062744140625;i:6;d:-5.4304488376459740806012632674537599086761474609375;i:7;d:-6.75415535401666300430179035174660384654998779296875;i:8;d:-2.462722007718394223729774239473044872283935546875;i:9;d:-6.277375571660389397266044397838413715362548828125;i:10;d:-7.66627071448033792222531701554544270038604736328125;i:11;d:-4.5583264503194182992729110992513597011566162109375;i:12;d:-5.51887851089199354959191623493097722530364990234375;i:13;d:-5.9464847448773721083625787287019193172454833984375;i:14;d:-3.11700972293176281624482726329006254673004150390625;i:15;d:-8.3804713042381155929660963010974228382110595703125;i:16;d:-8.1362743437260736101279690046794712543487548828125;i:17;d:-3.62431653946016041345501434989273548126220703125;i:18;d:-3.6811521539816407511125362361781299114227294921875;i:19;d:-7.19366630353454450386152529972605407238006591796875;i:20;d:-3.994198378682782379911486714263446629047393798828125;i:21;d:-5.5687641654028627868910916731692850589752197265625;i:22;d:-7.0773273114503947311959564103744924068450927734375;i:23;d:-9.9280338129541281233514382620342075824737548828125;i:24;d:-4.61384309724980123945670129614882171154022216796875;i:25;d:-9.7457122561601732257940966519527137279510498046875;i:26;d:-0.5394468422260205731078031021752394735813140869140625;}i:4;a:27:{i:0;d:-3.0956911156796724782225282979197800159454345703125;i:1;d:-6.3279563530093394518871718901209533214569091796875;i:2;d:-3.71139411752934478272436535917222499847412109375;i:3;d:-2.414309096615009142539065578603185713291168212890625;i:4;d:-3.72801015351070663683685779687948524951934814453125;i:5;d:-4.55509580994932061770441578119061887264251708984375;i:6;d:-4.95823695698366151418667868711054325103759765625;i:7;d:-6.34082417762073280442791656241752207279205322265625;i:8;d:-4.46511588446394380724768780055455863475799560546875;i:9;d:-8.0432622665219515312173825805075466632843017578125;i:10;d:-7.01291794898284504000685046776197850704193115234375;i:11;d:-3.439565246328211589599277431261725723743438720703125;i:12;d:-3.74898562949726166237951474613510072231292724609375;i:13;d:-2.38915359506014279844521297491155564785003662109375;i:14;d:-5.281198278652137645394759601913392543792724609375;i:15;d:-4.426598784036354317095174337737262248992919921875;i:16;d:-6.26513162834950332324979171971790492534637451171875;i:17;d:-1.9762712469186809460808262883801944553852081298828125;i:18;d:-2.51922598754776316809511627070605754852294921875;i:19;d:-3.733110644013185375200691851205192506313323974609375;i:20;d:-6.051293675807169591962519916705787181854248046875;i:21;d:-4.11522602804092230144306086003780364990234375;i:22;d:-4.7192493011961147431065910495817661285400390625;i:23;d:-4.45117886715155730570359082776121795177459716796875;i:24;d:-4.53558824103577595820979695417918264865875244140625;i:25;d:-7.73175461565810184794145243358798325061798095703125;i:26;d:-1.1291364595675441595545862583094276487827301025390625;}i:5;a:27:{i:0;d:-2.76344229114557027315868253936059772968292236328125;i:1;d:-7.9114774546442685476677070255391299724578857421875;i:2;d:-7.529542843946298802393357618711888790130615234375;i:3;d:-8.4946237399898851805346566834487020969390869140625;i:4;d:-2.451100566435660876862812074250541627407073974609375;i:5;d:-2.926120215794327794611717763473279774188995361328125;i:6;d:-7.6122345597914122805605074972845613956451416015625;i:7;d:-8.5371833544086808132078658672980964183807373046875;i:8;d:-2.450507446608071404625661671161651611328125;i:9;d:-9.033620240722573413449936197139322757720947265625;i:10;d:-8.728238591171390226008952595293521881103515625;i:11;d:-3.748243385450270803715966394520364701747894287109375;i:12;d:-8.8394642262816152111781775602139532566070556640625;i:13;d:-7.665344385105360203169766464270651340484619140625;i:14;d:-1.9043799278633868343746371465385891497135162353515625;i:15;d:-8.30538174035135767780957394279539585113525390625;i:16;d:-9.370092477343785475341064739041030406951904296875;i:17;d:-2.35581735498397559780414667329750955104827880859375;i:18;d:-5.9296743825283488860122815822251141071319580078125;i:19;d:-3.315653131074415060908222585567273199558258056640625;i:20;d:-3.505893281281679652039429129217751324176788330078125;i:21;d:-9.370092477343785475341064739041030406951904296875;i:22;d:-7.80147655942994067146400993806309998035430908203125;i:23;d:-9.370092477343785475341064739041030406951904296875;i:24;d:-6.13534330331929478319352710968814790248870849609375;i:25;d:-9.370092477343785475341064739041030406951904296875;i:26;d:-0.99767075505541946700560629324172623455524444580078125;}i:6;a:27:{i:0;d:-2.6811173187426220465567894279956817626953125;i:1;d:-8.5602526808766850052734298515133559703826904296875;i:2;d:-8.0833286087863758240246170316822826862335205078125;i:3;d:-6.88627624730501253225156688131392002105712890625;i:4;d:-1.9631827738890723633602419795352034270763397216796875;i:5;d:-7.9242639141566879601441542035900056362152099609375;i:6;d:-4.61975020412667003455453595961444079875946044921875;i:7;d:-2.221364181944155635761717348941601812839508056640625;i:8;d:-2.86129664959741258911662953323684632778167724609375;i:9;d:-9.1480393457788036215561078279279172420501708984375;i:10;d:-8.5602526808766850052734298515133559703826904296875;i:11;d:-3.35807917488155016627615623292513191699981689453125;i:12;d:-6.07534603108868420662247444852255284786224365234375;i:13;d:-3.739971115271647494893159091589041054248809814453125;i:14;d:-2.831958278425978026149323341087438166141510009765625;i:15;d:-7.95411687730636884907653438858687877655029296875;i:16;d:-9.0527291659744779650509372004307806491851806640625;i:17;d:-2.55266875188881936509233128163032233715057373046875;i:18;d:-4.06291519969180825455623562447726726531982421875;i:19;d:-4.94334672638783700904241413809359073638916015625;i:20;d:-3.49449808755934920867503024055622518062591552734375;i:21;d:-9.1480393457788036215561078279279172420501708984375;i:22;d:-7.5794234278649579295006333268247544765472412109375;i:23;d:-9.1480393457788036215561078279279172420501708984375;i:24;d:-5.8822799350117520589265041053295135498046875;i:25;d:-8.617411094716633357393220649100840091705322265625;i:26;d:-1.0302198528344137518075740445055998861789703369140625;}i:7;a:27:{i:0;d:-1.8949866865832032392091832662117667496204376220703125;i:1;d:-7.38720654177458957434510011808015406131744384765625;i:2;d:-8.053104079887159372219684883020818233489990234375;i:3;d:-7.5422784561211688725279600475914776325225830078125;i:4;d:-0.728635365144754576505192744662053883075714111328125;i:5;d:-7.8583157543280748313918593339622020721435546875;i:6;d:-9.589971299486425238001174875535070896148681640625;i:7;d:-8.7790410832700960241936627426184713840484619140625;i:8;d:-1.99513721534594878903590142726898193359375;i:9;d:-10.1007969232524157376928997109644114971160888671875;i:10;d:-7.88522320724799907765145690063945949077606201171875;i:11;d:-6.63506102045268875144756748341023921966552734375;i:12;d:-6.261344610659104858996215625666081905364990234375;i:13;d:-6.7051705866397153243951834156177937984466552734375;i:14;d:-2.561769867428420610622197273187339305877685546875;i:15;d:-9.2534990628652113997532069333828985691070556640625;i:16;d:-10.1007969232524157376928997109644114971160888671875;i:17;d:-4.57202943020773044935367579455487430095672607421875;i:18;d:-6.222675469499950651197650586254894733428955078125;i:19;d:-3.764561336708525107752620897372253239154815673828125;i:20;d:-4.62224350640144532320618964149616658687591552734375;i:21;d:-9.541181135316993078276937012560665607452392578125;i:22;d:-7.3816968859636205024798982776701450347900390625;i:23;d:-10.2831184800463706352502413210459053516387939453125;i:24;d:-5.024581983510177707330512930639088153839111328125;i:25;d:-10.1878083002420449787450706935487687587738037109375;i:26;d:-2.362308800757769500222593705984763801097869873046875;}i:8;a:27:{i:0;d:-3.712244886548000888382148332311771810054779052734375;i:1;d:-4.7174752821302110561418885481543838977813720703125;i:2;d:-2.783984370515816930691244124318473041057586669921875;i:3;d:-3.221601376806764616134159950888715684413909912109375;i:4;d:-3.1683365236496232597573907696641981601715087890625;i:5;d:-3.903825455861240190102989799925126135349273681640625;i:6;d:-3.68079054705895192256548398290760815143585205078125;i:7;d:-9.1193045441002720252754443208687007427215576171875;i:8;d:-6.42298959921648293658336115186102688312530517578125;i:9;d:-10.3232773484262079222162356018088757991790771484375;i:10;d:-5.24083832220096912379858622443862259387969970703125;i:11;d:-3.07952613671373942594300388009287416934967041015625;i:12;d:-3.173687619686372141103447575005702674388885498046875;i:13;d:-1.3126793991869438738007147549069486558437347412109375;i:14;d:-2.6643811758555511204349386389367282390594482421875;i:15;d:-4.92360748761788347138690369320102035999298095703125;i:16;d:-7.8955291124781563638634906965307891368865966796875;i:17;d:-3.409705684122631197396913194097578525543212890625;i:18;d:-2.0515798328281977802589608472771942615509033203125;i:19;d:-2.101193745290548253734641548362560570240020751953125;i:20;d:-6.51107467828027264289403319708071649074554443359375;i:21;d:-3.816746183294980721001365964184515178203582763671875;i:22;d:-9.672689782285058157640378340147435665130615234375;i:23;d:-6.1683081643876729316389173618517816066741943359375;i:24;d:-10.4102887254158371632684065843932330608367919921875;i:25;d:-5.5117707294402880080497197923250496387481689453125;i:26;d:-3.788398987959083807908200469682924449443817138671875;}i:9;a:27:{i:0;d:-2.342760916057565534487139302655123174190521240234375;i:1;d:-6.10240944105970850586118103819899260997772216796875;i:2;d:-6.03787091992213742486228511552326381206512451171875;i:3;d:-6.50787454916787311276493710465729236602783203125;i:4;d:-1.4153520955994334240557463999721221625804901123046875;i:5;d:-6.2455102847003818311577560962177813053131103515625;i:6;d:-6.17140231254666016269538886263035237789154052734375;i:7;d:-6.325552992373918215207595494575798511505126953125;i:8;d:-5.51462277615758988957850306178443133831024169921875;i:9;d:-6.50787454916787311276493710465729236602783203125;i:10;d:-6.2455102847003818311577560962177813053131103515625;i:11;d:-6.41256436936354834443818617728538811206817626953125;i:12;d:-6.325552992373918215207595494575798511505126953125;i:13;d:-6.50787454916787311276493710465729236602783203125;i:14;d:-1.2783714986201963892398225652868859469890594482421875;i:15;d:-6.41256436936354834443818617728538811206817626953125;i:16;d:-6.325552992373918215207595494575798511505126953125;i:17;d:-6.10240944105970850586118103819899260997772216796875;i:18;d:-6.10240944105970850586118103819899260997772216796875;i:19;d:-6.2455102847003818311577560962177813053131103515625;i:20;d:-1.10025444772938651993854364263825118541717529296875;i:21;d:-6.50787454916787311276493710465729236602783203125;i:22;d:-6.2455102847003818311577560962177813053131103515625;i:23;d:-6.50787454916787311276493710465729236602783203125;i:24;d:-6.50787454916787311276493710465729236602783203125;i:25;d:-6.50787454916787311276493710465729236602783203125;i:26;d:-4.85921592358049192483804290532134473323822021484375;}i:10;a:27:{i:0;d:-3.61949335849451347968397385557182133197784423828125;i:1;d:-7.04770353940273697190832535852678120136260986328125;i:2;d:-6.06241993604163109665705633233301341533660888671875;i:3;d:-7.67185784847573160050160367973148822784423828125;i:4;d:-1.2114318817004574579954123692004941403865814208984375;i:5;d:-6.285563487355840806003470788709819316864013671875;i:6;d:-6.824559988088527262561910902149975299835205078125;i:7;d:-3.631148502086262386256976242293603718280792236328125;i:8;d:-1.7851984157338753878008219544426538050174713134765625;i:9;d:-7.81495869211640492579817873775027692317962646484375;i:10;d:-7.489536291681776702944262069649994373321533203125;i:11;d:-3.8876682145574701365831060684286057949066162109375;i:12;d:-6.03610262772425709698609352926723659038543701171875;i:13;d:-2.35439166308893366164056715206243097782135009765625;i:14;d:-3.841768225810271442099974592565558850765228271484375;i:15;d:-7.5466947055217250550640528672374784946441650390625;i:16;d:-7.98201277677957055090018911869265139102935791015625;i:17;d:-5.482068249627030098736213403753936290740966796875;i:18;d:-3.000899921949636617313217357150278985500335693359375;i:19;d:-6.6910285954640045247288071550428867340087890625;i:20;d:-3.738725879837350163370501832105219364166259765625;i:21;d:-7.24441383364879154527216087444685399532318115234375;i:22;d:-5.43826562696863735624219771125353872776031494140625;i:23;d:-8.077322956583895319226940046064555644989013671875;i:24;d:-4.682814563072536628851594286970794200897216796875;i:25;d:-8.077322956583895319226940046064555644989013671875;i:26;d:-1.4288566755523215423551164349191822111606597900390625;}i:11;a:27:{i:0;d:-2.269183388314388150064360161195509135723114013671875;i:1;d:-6.573297799694781673451871029101312160491943359375;i:2;d:-5.74547936544993120833169086836278438568115234375;i:3;d:-2.8596367214634224040992194204591214656829833984375;i:4;d:-1.7841347050080587077758309533237479627132415771484375;i:5;d:-4.14404790090957231285528905573301017284393310546875;i:6;d:-6.84409165411804121248451338033191859722137451171875;i:7;d:-7.74377464959253547505113601800985634326934814453125;i:8;d:-2.175115871017978097512468593777157366275787353515625;i:9;d:-9.70151925629485134550122893415391445159912109375;i:10;d:-4.95658712793160116660828862222842872142791748046875;i:11;d:-2.065933606191924010175853254622779786586761474609375;i:12;d:-5.0603386327837274194507699576206505298614501953125;i:13;d:-6.49269376728015235045177178108133375644683837890625;i:14;d:-2.45088374439617151523407301283441483974456787109375;i:15;d:-5.604400767190025334230085718445479869842529296875;i:16;d:-9.70151925629485134550122893415391445159912109375;i:17;d:-5.64828608231518192184239524067379534244537353515625;i:18;d:-3.8794600407142780795766157098114490509033203125;i:19;d:-3.8645179378524279201201352407224476337432861328125;i:20;d:-3.820986269894151465820186786004342138767242431640625;i:21;d:-5.0913615287957210142621988779865205287933349609375;i:22;d:-5.4662057509475570071799666038714349269866943359375;i:23;d:-9.883840813088806243058570544235408306121826171875;i:24;d:-2.333600268348952067043455826933495700359344482421875;i:25;d:-8.6029069676267422295268261223100125789642333984375;i:26;d:-2.043409772983191263762137168669141829013824462890625;}i:12;a:27:{i:0;d:-1.753994237524768795566387780127115547657012939453125;i:1;d:-3.684119898084507571667245429125614464282989501953125;i:2;d:-6.5593110988036560371483574272133409976959228515625;i:3;d:-8.4396239653731566221495086210779845714569091796875;i:4;d:-1.365444429657459490812243529944680631160736083984375;i:5;d:-6.407584662587904489328138879500329494476318359375;i:6;d:-9.33756555857911507700919173657894134521484375;i:7;d:-8.2389532699110059610347889247350394725799560546875;i:8;d:-2.4389427631602504931151997880078852176666259765625;i:9;d:-9.1705114739159494519071813556365668773651123046875;i:10;d:-8.90224748732126869299463578499853610992431640625;i:11;d:-6.20603174386606237789010265260003507137298583984375;i:12;d:-3.6410823864045251951893078512512147426605224609375;i:13;d:-5.66241629727708062347346640308387577533721923828125;i:14;d:-2.228057525143769534992088665603660047054290771484375;i:15;d:-2.6972144960398214408314743195660412311553955078125;i:16;d:-9.4328757383834389571575229638256132602691650390625;i:17;d:-5.50105010565911367592661918024532496929168701171875;i:18;d:-3.53819745996314960478912325925193727016448974609375;i:19;d:-6.90714709407518423489591441466473042964935302734375;i:20;d:-3.4685254837670296268470337963663041591644287109375;i:21;d:-9.25055418158948583595702075399458408355712890625;i:22;d:-8.0718991852478385595759391435422003269195556640625;i:23;d:-9.4328757383834389571575229638256132602691650390625;i:24;d:-3.424799925470261019455620044027455151081085205078125;i:25;d:-9.4328757383834389571575229638256132602691650390625;i:26;d:-1.896831851189853868078216692083515226840972900390625;}i:13;a:27:{i:0;d:-3.41157713428474007599788819788955152034759521484375;i:1;d:-6.72828520838997601316577856778167188167572021484375;i:2;d:-3.090942743072009335492111858911812305450439453125;i:3;d:-1.74026482568973506204201839864253997802734375;i:4;d:-2.501519051239830648825090975151397287845611572265625;i:5;d:-4.81737190137175819160120227024890482425689697265625;i:6;d:-2.113210530516430818437356720096431672573089599609375;i:7;d:-6.8077493797442230061278678476810455322265625;i:8;d:-3.2957925490940969126540949218906462192535400390625;i:9;d:-6.25279895106929384240856961696408689022064208984375;i:10;d:-4.9177978843369540840058107278309762477874755859375;i:11;d:-4.63276984187907903134373555076308548450469970703125;i:12;d:-5.98877585293416014877720954245887696743011474609375;i:13;d:-4.666651756641289949811834958381950855255126953125;i:14;d:-2.87675951806729290893827055697329342365264892578125;i:15;d:-7.603753945743875419793766923248767852783203125;i:16;d:-6.8644656091860749569377730949781835079193115234375;i:17;d:-7.18723900144912608567437928286381065845489501953125;i:18;d:-3.05233621575503111245097898063249886035919189453125;i:19;d:-2.264747926898596386280360093223862349987030029296875;i:20;d:-4.900158094991045487631708965636789798736572265625;i:21;d:-5.3489595165861789638483969611115753650665283203125;i:22;d:-7.14773655847287958664537654840387403964996337890625;i:23;d:-7.4543765446692749065960015286691486835479736328125;i:24;d:-4.54635184515679124928055898635648190975189208984375;i:25;d:-8.80772675006981131673455820418894290924072265625;i:26;d:-1.4656789800236864618199206233839504420757293701171875;}i:14;a:27:{i:0;d:-5.0822413891388436013585305772721767425537109375;i:1;d:-5.14239363199360166589713116991333663463592529296875;i:2;d:-4.27659835332892424020201360690407454967498779296875;i:3;d:-4.0852168176898668860985708306543529033660888671875;i:4;d:-5.75996838501091357187533503747545182704925537109375;i:5;d:-2.17477361599536411773669897229410707950592041015625;i:6;d:-5.2886215722187106536011924617923796176910400390625;i:7;d:-6.09017411036031486304409554577432572841644287109375;i:8;d:-4.471572330819423513048604945652186870574951171875;i:9;d:-6.906874683037980133804012439213693141937255859375;i:10;d:-4.432658655412264891992890625260770320892333984375;i:11;d:-3.227743106313963661335719734779559075832366943359375;i:12;d:-2.82129235565138447583422021125443279743194580078125;i:13;d:-1.768108839371391294292834572843275964260101318359375;i:14;d:-3.515622423909222415971953523694537580013275146484375;i:15;d:-3.951801656831616771370363494497723877429962158203125;i:16;d:-8.9863162247178163255512117757461965084075927734375;i:17;d:-2.166459079414195354473804400186054408550262451171875;i:18;d:-3.37121318415194171080884188995696604251861572265625;i:19;d:-3.12244833471454175111148288124240934848785400390625;i:20;d:-2.210189388214115258080028070253320038318634033203125;i:21;d:-3.5450716407123703532988656661473214626312255859375;i:22;d:-3.140479750467313646566935858572833240032196044921875;i:23;d:-6.66311184452103422160007539787329733371734619140625;i:24;d:-5.513444385052640228650489007122814655303955078125;i:25;d:-7.70422564112792773727278472506441175937652587890625;i:26;d:-2.21297367900774855087320247548632323741912841796875;}i:15;a:27:{i:0;d:-2.13626534966714398677822828176431357860565185546875;i:1;d:-7.6487499302755264096731480094604194164276123046875;i:2;d:-6.52216378956501063868245182675309479236602783203125;i:3;d:-8.5031652584315935428094235248863697052001953125;i:4;d:-1.734614577170006999295992500265128910541534423828125;i:5;d:-6.50168525822147014281426891102455556392669677734375;i:6;d:-7.78532546528127777918371066334657371044158935546875;i:7;d:-3.64257796057899785324707409017719328403472900390625;i:8;d:-2.6542728486002840071478203753940761089324951171875;i:9;d:-8.4543750942621631594420250621624290943145751953125;i:10;d:-7.86131137225919918165573108126409351825714111328125;i:11;d:-2.38517819861382829316198694868944585323333740234375;i:12;d:-6.3630990949353236629804086987860500812530517578125;i:13;d:-7.294204912594619116816829773597419261932373046875;i:14;d:-2.11620176752729793889784559723921120166778564453125;i:15;d:-2.790753457077338683944844888173975050449371337890625;i:16;d:-9.1963124389915389400584899703972041606903076171875;i:17;d:-1.78687319416069545496839054976589977741241455078125;i:18;d:-3.848728831140584727421583011164329946041107177734375;i:19;d:-3.2942257030347743551601524814032018184661865234375;i:20;d:-3.19712776886841965051644365303218364715576171875;i:21;d:-9.1963124389915389400584899703972041606903076171875;i:22;d:-7.08005692418898746609556837938725948333740234375;i:23;d:-9.1963124389915389400584899703972041606903076171875;i:24;d:-4.98715220234085787609501494443975389003753662109375;i:25;d:-9.1963124389915389400584899703972041606903076171875;i:26;d:-2.93634863884855068505430608638562262058258056640625;}i:16;a:27:{i:0;d:-6.182291496945648390237693092785775661468505859375;i:1;d:-6.182291496945648390237693092785775661468505859375;i:2;d:-6.182291496945648390237693092785775661468505859375;i:3;d:-6.182291496945648390237693092785775661468505859375;i:4;d:-6.182291496945648390237693092785775661468505859375;i:5;d:-6.182291496945648390237693092785775661468505859375;i:6;d:-6.182291496945648390237693092785775661468505859375;i:7;d:-6.182291496945648390237693092785775661468505859375;i:8;d:-6.182291496945648390237693092785775661468505859375;i:9;d:-6.182291496945648390237693092785775661468505859375;i:10;d:-6.182291496945648390237693092785775661468505859375;i:11;d:-6.182291496945648390237693092785775661468505859375;i:12;d:-6.182291496945648390237693092785775661468505859375;i:13;d:-6.182291496945648390237693092785775661468505859375;i:14;d:-6.182291496945648390237693092785775661468505859375;i:15;d:-6.182291496945648390237693092785775661468505859375;i:16;d:-6.182291496945648390237693092785775661468505859375;i:17;d:-6.182291496945648390237693092785775661468505859375;i:18;d:-6.182291496945648390237693092785775661468505859375;i:19;d:-6.182291496945648390237693092785775661468505859375;i:20;d:-0.05717056502499308356934903940782533027231693267822265625;i:21;d:-6.182291496945648390237693092785775661468505859375;i:22;d:-6.182291496945648390237693092785775661468505859375;i:23;d:-6.182291496945648390237693092785775661468505859375;i:24;d:-6.182291496945648390237693092785775661468505859375;i:25;d:-6.182291496945648390237693092785775661468505859375;i:26;d:-5.54043761077325402908400064916349947452545166015625;}i:17;a:27:{i:0;d:-2.57228079524373942632564649102278053760528564453125;i:1;d:-5.99205725679637968283941518166102468967437744140625;i:2;d:-4.31931676455123891855691908858716487884521484375;i:3;d:-3.7284294070555272782030442613177001476287841796875;i:4;d:-1.42254838175564923830052066477946937084197998046875;i:5;d:-5.35723168667141891319261048920452594757080078125;i:6;d:-4.3317609271287889072254984057508409023284912109375;i:7;d:-6.0337579859953240912773253512568771839141845703125;i:8;d:-2.36454174581913800778920631273649632930755615234375;i:9;d:-9.4051832093238090237719006836414337158203125;i:10;d:-4.8504645668318158158172082039527595043182373046875;i:11;d:-4.67355659938315870505221027997322380542755126953125;i:12;d:-3.745004372149740135000683949328958988189697265625;i:13;d:-3.916576116332640911110729575739242136478424072265625;i:14;d:-2.322164557359624126320341019891202449798583984375;i:15;d:-5.41397940602122229591941504622809588909149169921875;i:16;d:-9.04054009573589922865721746347844600677490234375;i:17;d:-3.63686221353003702461137436330318450927734375;i:18;d:-2.901613856951456238419950750540010631084442138671875;i:19;d:-3.23456879658750739992001399514265358448028564453125;i:20;d:-3.981643878022390392601437270059250295162200927734375;i:21;d:-4.90581808223269444368952463264577090740203857421875;i:22;d:-6.25758858646057181118749213055707514286041259765625;i:23;d:-10.139152384404010120988459675572812557220458984375;i:24;d:-3.258597025348527242982754614786244928836822509765625;i:25;d:-8.0701821425914683771907220943830907344818115234375;i:26;d:-1.7281348556024396234676032690913416445255279541015625;}i:18;a:27:{i:0;d:-3.2090119684802527189049214939586818218231201171875;i:1;d:-6.33489952315430304707888353732414543628692626953125;i:2;d:-4.09329906441737367828181959339417517185211181640625;i:3;d:-7.5920350390622797220885331626050174236297607421875;i:4;d:-2.15863457030355476717886631377041339874267578125;i:5;d:-6.1829951939534186777791546774096786975860595703125;i:6;d:-7.8495033329175640801622648723423480987548828125;i:7;d:-2.9152578145891450134286060347221791744232177734375;i:8;d:-2.775471041990636766882971642189659178256988525390625;i:9;d:-9.5502910239398932645826789666898548603057861328125;i:10;d:-4.55102445745039663194120294065214693546295166015625;i:11;d:-4.7071315107863114235442481003701686859130859375;i:12;d:-4.5892723223674494192891870625317096710205078125;i:13;d:-6.24324507340084355888620848418213427066802978515625;i:14;d:-2.968928515714967186767125895130448043346405029296875;i:15;d:-3.8539273046711368664318797527812421321868896484375;i:16;d:-7.00903143760076030588379580876789987087249755859375;i:17;d:-8.243133983378726270530023612082004547119140625;i:18;d:-2.810851494750975287928440593532286584377288818359375;i:19;d:-2.117755763783303546432534858467988669872283935546875;i:20;d:-3.256550330868064779821224874467588961124420166015625;i:21;d:-7.86550367426400587334001102135516703128814697265625;i:22;d:-5.3805970244760050746890556183643639087677001953125;i:23;d:-10.3832001468749961503590384381823241710662841796875;i:24;d:-5.2014165965829111115681371302343904972076416015625;i:25;d:-9.9131965176292613506348061491735279560089111328125;i:26;d:-0.991130059875320323925507182138971984386444091796875;}i:19;a:27:{i:0;d:-3.1682706209020796705999600817449390888214111328125;i:1;d:-8.217706195099385269031699863262474536895751953125;i:2;d:-5.87889983867102206005483822082169353961944580078125;i:3;d:-9.52521967836616312297337572090327739715576171875;i:4;d:-2.338891874978721308053764005308039486408233642578125;i:5;d:-7.17240245988578362101861785049550235271453857421875;i:6;d:-8.4784324575626381914617013535462319850921630859375;i:7;d:-1.1074718716074005708804861569660715758800506591796875;i:8;d:-2.366234149730215730045301825157366693019866943359375;i:9;d:-10.45677788237110661384576815180480480194091796875;i:10;d:-9.6895227296574386599559147725813090801239013671875;i:11;d:-4.41853985524954406827191633055917918682098388671875;i:12;d:-6.0323919738580826788165722973644733428955078125;i:13;d:-7.219608864455580032881698571145534515380859375;i:14;d:-2.336715396119156462617638680967502295970916748046875;i:15;d:-8.161914835470970075448349234648048877716064453125;i:16;d:-10.719142146838596119096109759993851184844970703125;i:17;d:-3.4624215669877802525888910167850553989410400390625;i:18;d:-3.6797447588047447197823203168809413909912109375;i:19;d:-4.02519909174178547317524134996347129344940185546875;i:20;d:-3.900218081563075589457412206684239208698272705078125;i:21;d:-9.5559913370329159221228110254742205142974853515625;i:22;d:-5.18259559155830995536007321788929402828216552734375;i:23;d:-10.131355481936477502813431783579289913177490234375;i:24;d:-4.2047257961667838799257879145443439483642578125;i:25;d:-7.9847746374190133877846164978109300136566162109375;i:26;d:-1.5835253210583506433550837755319662392139434814453125;}i:20;a:27:{i:0;d:-3.69070634749608128544196006259880959987640380859375;i:1;d:-3.73413364092809985095300362445414066314697265625;i:2;d:-3.245472126214963282109238207340240478515625;i:3;d:-4.02060871262984509399984744959510862827301025390625;i:4;d:-3.281297978688058947227546013891696929931640625;i:5;d:-5.0172437658127595483392724418081343173980712890625;i:6;d:-3.19332718495017342519304293091408908367156982421875;i:7;d:-7.796389888598699968724758946336805820465087890625;i:8;d:-3.7311188662600098808752591139636933803558349609375;i:9;d:-9.3545345066452494364739322918467223644256591796875;i:10;d:-6.20822937461188484320473435218445956707000732421875;i:11;d:-2.25767516857242700467622853466309607028961181640625;i:12;d:-3.411297781850523680446940488764084875583648681640625;i:13;d:-2.089862646254398992340384211274795234203338623046875;i:14;d:-6.09643796862376774470249074511229991912841796875;i:15;d:-3.100705695069776890449020356754772365093231201171875;i:16;d:-8.9490693985370857177485959255136549472808837890625;i:17;d:-1.9053759034329840194033067746204324066638946533203125;i:18;d:-1.9722545836234537386388865343178622424602508544921875;i:19;d:-1.964867614061764466981685473001562058925628662109375;i:20;d:-9.274491798971713052424092893488705158233642578125;i:21;d:-6.78319535108494253705657683894969522953033447265625;i:22;d:-9.3545345066452494364739322918467223644256591796875;i:23;d:-7.31765257938420976557836183928884565830230712890625;i:24;d:-7.37353303777866653234696059371344745159149169921875;i:25;d:-5.2671586137392427673376005259342491626739501953125;i:26;d:-3.270035093570078377211984843597747385501861572265625;}i:21;a:27:{i:0;d:-2.467755132731010547786354436539113521575927734375;i:1;d:-8.4660147229718205608151038177311420440673828125;i:2;d:-8.561324902776146217320274445228278636932373046875;i:3;d:-7.8681777222162008200712079997174441814422607421875;i:4;d:-0.51923632855148660336652710611815564334392547607421875;i:5;d:-8.561324902776146217320274445228278636932373046875;i:6;d:-8.2989606383086549357130934367887675762176513671875;i:7;d:-8.4660147229718205608151038177311420440673828125;i:8;d:-1.742947434945225637648036354221403598785400390625;i:9;d:-8.4660147229718205608151038177311420440673828125;i:10;d:-7.919471016603750967988162301480770111083984375;i:11;d:-5.44337499649790590439124571275897324085235595703125;i:12;d:-8.561324902776146217320274445228278636932373046875;i:13;d:-4.56680067583625604044073043041862547397613525390625;i:14;d:-2.789260920703539259335457245470024645328521728515625;i:15;d:-8.561324902776146217320274445228278636932373046875;i:16;d:-8.561324902776146217320274445228278636932373046875;i:17;d:-6.26879014563560144068787849391810595989227294921875;i:18;d:-4.4071403401980280278849022579379379749298095703125;i:19;d:-7.97353823787402671285917676868848502635955810546875;i:20;d:-6.2891990172668084113638542476110160350799560546875;i:21;d:-8.37900334598219131976293283514678478240966796875;i:22;d:-8.0306966517139759531573872664012014865875244140625;i:23;d:-8.4660147229718205608151038177311420440673828125;i:24;d:-5.2581079294741943641611214843578636646270751953125;i:25;d:-8.561324902776146217320274445228278636932373046875;i:26;d:-3.122810905734825670521104257204569876194000244140625;}i:22;a:27:{i:0;d:-1.59679846095761401869594919844530522823333740234375;i:1;d:-7.60014217059567354084492762922309339046478271484375;i:2;d:-7.640964165115928352634000475518405437469482421875;i:3;d:-5.344648685135478416441401350311934947967529296875;i:4;d:-1.8921021056868312371079809963703155517578125;i:5;d:-6.83933634156191327946316960151307284832000732421875;i:6;d:-8.1109677943616649287150721647776663303375244140625;i:7;d:-1.6228272590921337670266666464158333837985992431640625;i:8;d:-1.76287830456499161613237447454594075679779052734375;i:9;d:-8.8731078464085602064415070344693958759307861328125;i:10;d:-7.00130566950696930916819837875664234161376953125;i:11;d:-5.4671598619878079006184634636156260967254638671875;i:12;d:-8.179960665848614809192440588958561420440673828125;i:13;d:-3.218365567177001285159576582373119890689849853515625;i:14;d:-2.52421863647130084729042209801264107227325439453125;i:15;d:-8.293289351155618049915574374608695507049560546875;i:16;d:-9.209580083029774044689474976621568202972412109375;i:17;d:-4.570008470324349758584503433667123317718505859375;i:18;d:-4.2425484264156505531673246878199279308319091796875;i:19;d:-5.65423202154036008693083203979767858982086181640625;i:20;d:-7.5609214574423919685841610771603882312774658203125;i:21;d:-9.209580083029774044689474976621568202972412109375;i:22;d:-7.30747255663285333326939507969655096530914306640625;i:23;d:-9.209580083029774044689474976621568202972412109375;i:24;d:-6.87743618779418408593073763768188655376434326171875;i:25;d:-9.02725852623581914713213336654007434844970703125;i:26;d:-2.172728230713226960091333239688538014888763427734375;}i:23;a:27:{i:0;d:-2.254780968033423871332843191339634358882904052734375;i:1;d:-6.8982098661386057614208766608498990535736083984375;i:2;d:-2.027603216646053141403172048740088939666748046875;i:3;d:-6.49274475803044115451712059439159929752349853515625;i:4;d:-2.4626424645366942201007987023331224918365478515625;i:5;d:-5.83349912914617707571096616447903215885162353515625;i:6;d:-6.8982098661386057614208766608498990535736083984375;i:7;d:-4.31044583091089794635308862780220806598663330078125;i:8;d:-2.073101259785252725720283706323243677616119384765625;i:9;d:-6.8982098661386057614208766608498990535736083984375;i:10;d:-6.8982098661386057614208766608498990535736083984375;i:11;d:-6.42820623689287007351822467171587049961090087890625;i:12;d:-6.7158883093446508638635350507684051990509033203125;i:13;d:-6.8982098661386057614208766608498990535736083984375;i:14;d:-4.455862830769401483621550141833722591400146484375;i:15;d:-1.4955324842663262341346808170783333480358123779296875;i:16;d:-6.367581615076435497257989482022821903228759765625;i:17;d:-4.63644676766481467211633571423590183258056640625;i:18;d:-5.766807754647505390721562434919178485870361328125;i:19;d:-1.8703897472882491381795944107579998672008514404296875;i:20;d:-4.3724812218303501509808484115637838840484619140625;i:21;d:-4.34876469521303388177102533518336713314056396484375;i:22;d:-6.80289968633428099309412573347799479961395263671875;i:23;d:-4.47340714042031084574091437389142811298370361328125;i:24;d:-5.17544326839750201685319552780129015445709228515625;i:25;d:-6.8982098661386057614208766608498990535736083984375;i:26;d:-2.5557039896270072887318747234530746936798095703125;}i:24;a:27:{i:0;d:-3.872874353532437918801178966532461345195770263671875;i:1;d:-6.0798383375596802835616472293622791767120361328125;i:2;d:-5.60595622898537460088164152693934738636016845703125;i:3;d:-6.1495716755743554671198580763302743434906005859375;i:4;d:-2.91810873520068358999424162902869284152984619140625;i:5;d:-5.8331401898365893288200823008082807064056396484375;i:6;d:-6.99872310461088265043372302898205816745758056640625;i:7;d:-7.31717683572941712810688841273076832294464111328125;i:8;d:-3.8266394412494406651603640057146549224853515625;i:9;d:-8.8576218766765659751172279356978833675384521484375;i:10;d:-7.45070822835393986593999215983785688877105712890625;i:11;d:-4.7453828245779146044469598564319312572479248046875;i:12;d:-4.44785848703108488422230948344804346561431884765625;i:13;d:-5.4481256921997154307746313861571252346038818359375;i:14;d:-2.22551031171975655098549395916052162647247314453125;i:15;d:-4.7372306055163644344929707585833966732025146484375;i:16;d:-9.0399434334705208726745695457793772220611572265625;i:17;d:-5.75154154595370936675635675783269107341766357421875;i:18;d:-3.132676045163294009654464389313943684101104736328125;i:19;d:-4.23020108175365461278261136612854897975921630859375;i:20;d:-7.03846343326039658450099523179233074188232421875;i:21;d:-7.6536490723506300781764366547577083110809326171875;i:22;d:-6.23054073810802311328416180913336575031280517578125;i:23;d:-7.70494236673818022609339095652103424072265625;i:24;d:-8.16447469611662057786816149018704891204833984375;i:25;d:-7.7316106138203419817500616773031651973724365234375;i:26;d:-0.3817890191036423797044108141562901437282562255859375;}i:25;a:27:{i:0;d:-2.5311795993314571973087367950938642024993896484375;i:1;d:-6.00314605188181982242667800164781510829925537109375;i:2;d:-5.907835872077495054099927074275910854339599609375;i:3;d:-4.69481323223164093150217013317160308361053466796875;i:4;d:-0.9292230185496455074911636984325014054775238037109375;i:5;d:-5.907835872077495054099927074275910854339599609375;i:6;d:-6.00314605188181982242667800164781510829925537109375;i:7;d:-3.199785670975284990191767064970917999744415283203125;i:8;d:-2.339584405752173523751480388455092906951904296875;i:9;d:-6.00314605188181982242667800164781510829925537109375;i:10;d:-5.6666738152606068723571297596208751201629638671875;i:11;d:-3.886890537079267904374546560575254261493682861328125;i:12;d:-4.00166605167169553425310368766076862812042236328125;i:13;d:-5.0098942788715365992402439587749540805816650390625;i:14;d:-1.7362497244615695546343658861587755382061004638671875;i:15;d:-6.00314605188181982242667800164781510829925537109375;i:16;d:-6.00314605188181982242667800164781510829925537109375;i:17;d:-6.00314605188181982242667800164781510829925537109375;i:18;d:-5.82082449508786492486933639156632125377655029296875;i:19;d:-5.907835872077495054099927074275910854339599609375;i:20;d:-3.118345339035110352909896391793154180049896240234375;i:21;d:-5.6666738152606068723571297596208751201629638671875;i:22;d:-5.907835872077495054099927074275910854339599609375;i:23;d:-6.00314605188181982242667800164781510829925537109375;i:24;d:-4.56806152659249686820430724765174090862274169921875;i:25;d:-3.7731316517226094475745412637479603290557861328125;i:26;d:-3.1699327078256036571701770299114286899566650390625;}i:26;a:27:{i:0;d:-2.15445631830065398304441259824670851230621337890625;i:1;d:-3.132028909232904112514006556011736392974853515625;i:2;d:-3.204240273221434787132011479116044938564300537109375;i:3;d:-3.5547759660800490877363699837587773799896240234375;i:4;d:-3.8320798875631165714139569899998605251312255859375;i:5;d:-3.2625149066892173976839330862276256084442138671875;i:6;d:-4.13184262373052746397661394439637660980224609375;i:7;d:-2.784712279138497503794269505306147038936614990234375;i:8;d:-2.7534204117779434994872644892893731594085693359375;i:9;d:-5.6860742849137100307643777341581881046295166015625;i:10;d:-5.27151820510429391930529163801111280918121337890625;i:11;d:-3.7797926294683339420998891000635921955108642578125;i:12;d:-3.352468498262207940996404431643895804882049560546875;i:13;d:-3.812385935723568319843934659729711711406707763671875;i:14;d:-2.644532475328424947491612329031340777873992919921875;i:15;d:-3.367605730636743288641810067929327487945556640625;i:16;d:-6.2378725360564377666605651029385626316070556640625;i:17;d:-3.680921843486822719881956800236366689205169677734375;i:18;d:-2.70300749751599855841277530998922884464263916015625;i:19;d:-1.8614238674076200030782501926296390593051910400390625;i:20;d:-4.472388530409542539700851193629205226898193359375;i:21;d:-4.91869683048155170723703122348524630069732666015625;i:22;d:-2.80428504058845273760880445479415357112884521484375;i:23;d:-7.78394952828238384512360426015220582485198974609375;i:24;d:-4.7020395584873408978410225245170295238494873046875;i:25;d:-8.486442571260568001889623701572418212890625;i:26;d:-3.2910629924454486427976007689721882343292236328125;}}s:9:"threshold";d:0.027138686673887656153336678244158974848687648773193359375;}'));
		//echo '</pre>';
		//echo "hsl(".rand(0,359).",100%,50%)";
		
		//var_dump($this->getRandomColor());
		//$r = $this->generateCaptchaTextMarkov(10));
		
		// echo $this->contains_gibberish( "ronan" );
		$string = '';
		for ($i = 0; $i < 8; $i++) {
			$string .= chr(rand(97, 122));
		}
		echo '<span style="font-size: 30px;font-weight: bold;color:'.$this->getRandomColor().' ">'.$string.'</span>';
	}
	
	function getRandomColor() {
		$rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
		$color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
		return $color;
	}
	
	function generateCaptchaTextMarkov($length) {
		$transitionMatrix = array(
		0.0001, 0.0218, 0.0528, 0.1184, 0.1189, 0.1277, 0.1450, 0.1458, 0.1914, 0.1915, 0.2028, 0.2792, 0.3131, 0.5293, 0.5304, 0.5448, 0.5448, 0.6397, 0.7581, 0.9047, 0.9185, 0.9502, 0.9600, 0.9601, 0.9982, 1.0000, 
		0.0893, 0.0950, 0.0950, 0.0950, 0.4471, 0.4471, 0.4471, 0.4471, 0.4784, 0.4821, 0.4821, 0.6075, 0.6078, 0.6078, 0.7300, 0.7300, 0.7300, 0.7979, 0.8220, 0.8296, 0.9342, 0.9348, 0.9351, 0.9351, 1.0000, 1.0000, 
		0.1313, 0.1317, 0.1433, 0.1433, 0.3264, 0.3264, 0.3264, 0.4887, 0.5454, 0.5454, 0.5946, 0.6255, 0.6255, 0.6255, 0.8022, 0.8022, 0.8035, 0.8720, 0.8753, 0.9545, 0.9928, 0.9928, 0.9928, 0.9928, 1.0000, 1.0000, 
		0.0542, 0.0587, 0.0590, 0.0840, 0.3725, 0.3837, 0.3879, 0.3887, 0.5203, 0.5208, 0.5211, 0.5390, 0.5435, 0.5550, 0.8183, 0.8191, 0.8191, 0.8759, 0.9376, 0.9400, 0.9629, 0.9648, 0.9664, 0.9664, 1.0000, 1.0000, 
		0.0860, 0.0877, 0.1111, 0.2533, 0.3017, 0.3125, 0.3183, 0.3211, 0.3350, 0.3355, 0.3378, 0.4042, 0.4381, 0.5655, 0.5727, 0.5842, 0.5852, 0.7817, 0.8718, 0.9191, 0.9201, 0.9530, 0.9652, 0.9792, 0.9998, 1.0000, 
		0.1033, 0.1037, 0.1050, 0.1057, 0.2916, 0.3321, 0.3324, 0.3324, 0.4337, 0.4337, 0.4337, 0.4912, 0.4912, 0.4912, 0.7237, 0.7274, 0.7274, 0.8545, 0.8569, 0.9150, 0.9986, 0.9986, 0.9990, 0.9990, 1.0000, 1.0000, 
		0.1014, 0.1017, 0.1024, 0.1028, 0.2725, 0.2729, 0.2855, 0.4981, 0.5770, 0.5770, 0.5770, 0.6184, 0.6191, 0.6384, 0.7783, 0.7797, 0.7797, 0.9249, 0.9663, 0.9688, 0.9923, 0.9923, 0.9937, 0.9937, 1.0000, 1.0000, 
		0.2577, 0.2579, 0.2580, 0.2581, 0.6967, 0.6970, 0.6970, 0.6970, 0.8648, 0.8648, 0.8650, 0.8661, 0.8667, 0.8670, 0.9397, 0.9397, 0.9397, 0.9509, 0.9533, 0.9855, 0.9926, 0.9926, 0.9929, 0.9929, 1.0000, 1.0000, 
		0.0324, 0.0478, 0.0870, 0.1267, 0.1585, 0.1908, 0.2182, 0.2183, 0.2193, 0.2193, 0.2309, 0.2859, 0.3426, 0.6110, 0.6501, 0.6579, 0.6583, 0.6923, 0.8211, 0.9764, 0.9781, 0.9948, 0.9949, 0.9965, 0.9965, 1.0000, 
		0.1276, 0.1276, 0.1276, 0.1276, 0.4286, 0.4286, 0.4286, 0.4286, 0.4337, 0.4337, 0.4337, 0.4337, 0.4337, 0.4337, 0.6684, 0.6684, 0.6684, 0.6684, 0.6684, 0.6684, 1.0000, 1.0000, 1.0000, 1.0000, 1.0000, 1.0000, 
		0.0033, 0.0059, 0.0100, 0.0109, 0.5401, 0.5443, 0.5477, 0.5485, 0.7149, 0.7149, 0.7149, 0.7316, 0.7333, 0.9247, 0.9264, 0.9273, 0.9273, 0.9289, 0.9791, 0.9816, 0.9824, 0.9824, 0.9833, 0.9833, 1.0000, 1.0000, 
		0.0850, 0.0865, 0.0874, 0.1753, 0.3439, 0.3725, 0.3744, 0.3746, 0.5083, 0.5083, 0.5192, 0.6784, 0.6840, 0.6848, 0.8088, 0.8128, 0.8128, 0.8147, 0.8326, 0.8511, 0.8743, 0.8817, 0.9054, 0.9054, 1.0000, 1.0000, 
		0.1562, 0.1760, 0.1774, 0.1776, 0.5513, 0.5517, 0.5517, 0.5520, 0.6352, 0.6352, 0.6352, 0.6369, 0.6486, 0.6499, 0.7717, 0.8230, 0.8230, 0.8337, 0.8697, 0.8703, 0.9376, 0.9376, 0.9378, 0.9378, 1.0000, 1.0000, 
		0.0255, 0.0265, 0.0682, 0.2986, 0.4139, 0.4204, 0.6002, 0.6009, 0.6351, 0.6360, 0.6507, 0.6672, 0.6679, 0.6786, 0.7718, 0.7723, 0.7732, 0.7873, 0.8364, 0.9715, 0.9753, 0.9797, 0.9803, 0.9804, 0.9997, 1.0000, 
		0.0050, 0.0089, 0.0183, 0.0379, 0.0410, 0.1451, 0.1494, 0.1514, 0.1654, 0.1656, 0.1866, 0.2171, 0.2821, 0.4272, 0.4761, 0.4926, 0.4927, 0.6434, 0.6722, 0.7195, 0.9126, 0.9332, 0.9913, 0.9925, 0.9999, 1.0000, 
		0.1596, 0.1688, 0.1688, 0.1688, 0.3799, 0.3799, 0.3799, 0.4011, 0.4827, 0.4827, 0.4833, 0.6081, 0.6087, 0.6090, 0.7353, 0.7953, 0.7953, 0.8804, 0.9181, 0.9584, 0.9952, 0.9952, 0.9952, 0.9952, 1.0000, 1.0000, 
		0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1.0000, 1.0000, 1.0000, 1.0000, 1.0000, 1.0000, 
		0.0902, 0.0938, 0.1003, 0.1555, 0.4505, 0.4606, 0.4705, 0.4740, 0.5928, 0.5928, 0.6018, 0.6201, 0.6402, 0.6605, 0.7619, 0.7666, 0.7671, 0.8125, 0.8645, 0.9029, 0.9226, 0.9298, 0.9319, 0.9319, 0.9996, 1.0000, 
		0.0584, 0.0598, 0.0903, 0.0912, 0.2850, 0.2870, 0.2883, 0.3902, 0.5057, 0.5058, 0.5165, 0.5271, 0.5400, 0.5447, 0.6525, 0.6762, 0.6792, 0.6792, 0.7512, 0.9370, 0.9843, 0.9851, 0.9953, 0.9953, 0.9999, 1.0000, 
		0.0416, 0.0419, 0.0466, 0.0467, 0.1673, 0.1696, 0.1697, 0.6314, 0.7003, 0.7003, 0.7003, 0.7142, 0.7150, 0.7160, 0.8626, 0.8626, 0.8627, 0.9023, 0.9255, 0.9498, 0.9746, 0.9746, 0.9812, 0.9812, 0.9998, 1.0000, 
		0.0141, 0.0308, 0.0668, 0.0877, 0.1241, 0.1282, 0.1874, 0.1874, 0.2191, 0.2192, 0.2210, 0.3626, 0.3794, 0.4618, 0.4632, 0.5097, 0.5097, 0.6957, 0.8373, 0.9949, 0.9949, 0.9961, 0.9963, 0.9982, 0.9984, 1.0000, 
		0.0740, 0.0740, 0.0740, 0.0740, 0.8423, 0.8423, 0.8423, 0.8423, 0.9486, 0.9486, 0.9486, 0.9486, 0.9486, 0.9491, 0.9836, 0.9836, 0.9836, 0.9849, 0.9849, 0.9849, 0.9907, 0.9907, 0.9907, 0.9907, 1.0000, 1.0000, 
		0.2785, 0.2789, 0.2795, 0.2823, 0.4088, 0.4118, 0.4118, 0.6070, 0.7774, 0.7774, 0.7782, 0.7840, 0.7840, 0.8334, 0.9704, 0.9704, 0.9704, 0.9861, 0.9996, 1.0000, 1.0000, 1.0000, 1.0000, 1.0000, 1.0000, 1.0000, 
		0.0741, 0.0741, 0.1963, 0.1963, 0.2519, 0.2741, 0.2741, 0.3333, 0.4000, 0.4000, 0.4000, 0.4000, 0.4000, 0.4000, 0.4037, 0.6741, 0.7667, 0.7667, 0.7667, 0.9667, 0.9963, 0.9963, 0.9963, 0.9963, 1.0000, 1.0000, 
		0.0082, 0.0130, 0.0208, 0.0225, 0.1587, 0.1608, 0.1613, 0.1686, 0.2028, 0.2028, 0.2032, 0.2322, 0.2391, 0.2417, 0.8232, 0.8314, 0.8314, 0.8409, 0.9529, 0.9965, 0.9965, 0.9965, 0.9991, 0.9996, 1.0000, 1.0000, 
		0.0678, 0.0678, 0.0763, 0.0763, 0.7373, 0.7373, 0.7373, 0.7458, 0.8729, 0.8729, 0.8729, 0.8814, 0.8814, 0.8814, 0.9237, 0.9237, 0.9237, 0.9237, 0.9237, 0.9407, 0.9492, 0.9492, 0.9492, 0.9492, 0.9492, 1.0000
		);

		$chars = 'abcdefghijklmnopqrstuvwxyz';
		$captchaText = '';
		$char = rand(0, 25);

		for ($i = 0; $i < $length; $i++) {
			$captchaText .= chr($char + 65 + 32);

			// Look up next char in transition matrix
			$next = rand(0, 10000) / 10000;
			for ($j = 0; $j < 26; $j++) {
				if ($next < $transitionMatrix[$char * 26 + $j]) {
					$char = $j;
					break;
				}
			}

		}

		return $captchaText;
	}
	
	protected function createsurvey(){
		// Filter Do and ID
		$this->filter();	
		// Check if form is posted
		if(isset($_POST["token"])){
			// Kill the bots
			if(Main::bot()) die();
			// Validate Token
			if(!Main::validate_csrf_token($_POST["token"])){
				return Main::redirect(Main::href("createsurvey","",FALSE),array("danger",e("Invalid token. Please try again.")));
			}
			
			if($_POST["captcha1"]!=$_SESSION['rand_code']){
				return Main::redirect(Main::href("createsurvey","",FALSE),array("danger",e("Invalid human code. Please try again.")));
			}
			
			// Check if question length is higher than 5 chars
			if(strlen(Main::clean($_POST["question"],3,TRUE,FALSE)) < 5) return Main::redirect(Main::href("createsurvey","",FALSE),array("danger",e("That is not a valid question.")));
			
			if(preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $_POST["question"]) //valid chars check
				&& preg_match("/^.{1,253}$/", $_POST["question"]) //overall length check
				&& preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $_POST["question"])   ) {
				return Main::redirect(Main::href("createsurvey","",FALSE),array("danger",e("That is not a valid question.")));
			}
			
			if(Gibberish::test(Main::clean($_POST["question"],3,TRUE,FALSE)) === true) return Main::redirect(Main::href("create?pid=".$_REQUEST['pid'],"",FALSE),array("danger",e("That is not a valid question.")));
			
			// Validate answers and skip empty ones - maximum number of question is 20
			$options=array();
			$i=1;
			if($this->isPro()){
				$max=$this->config["max_count"];
			}else{
				$max=$this->max_free;
			}
			
			if(!empty($_POST["option"])){
				foreach ($_POST["option"] as $q) {				
					if($i>=$max) break;
					$q=Main::clean($q,3,TRUE);
					if(empty($q)) continue;			
					$options[$i]=array("answer"=>$q,"count"=>0);
					$i++;
				}
			}
			// Check if at least one answer is set
			///if(empty($options[1])) return Main::redirect(Main::href("createsurvey","",FALSE),array("danger",e("That is not a valid choice of answers.")));
			// JSON encode questions
			$options=json_encode($options);			
			// Generate custom info
			$custom=array(
				"background" => Main::clean($_POST["background"],3),
				"font" => in_array($_POST["font"], json_decode(strtolower(str_replace(" ","_",$this->config["fonts"])),TRUE))?$_POST["font"]:"null"
				);
			// Validate Post info
			$unique=$this->uniqueid();
			if(in_array($_POST["expires"], array("1h","5h","1d","5d","1w","5w","0"))){
				$to=array(
					"1h"=>"1 hours",
				  "5h"=>"5 hours",
				  "1d"=>"1 day",
				  "5d"=>"5 days",
				  "1w"=>"1 weeks",
				  "5w"=>"5 weeks",
				  "0"=>""
				  );
				if($_POST["expires"]=="0"){
					$expires="";
				}else{
					$expires=date("Y-m-d G:i:s",strtotime("+".$to[$_POST["expires"]]));
				}
			}
			// Prepare data
			$data=array(
				":userid" => $this->userid,
				":question" => Main::clean($_POST["question"],3,TRUE),
				":image_url" => Main::clean($_POST["image_url"],3,TRUE),
				//":parent" => $expires,
				":options" => $options,
				":share" => in_array($_POST["share"],array("1","0"))?$_POST["share"]:"0",
				":choice" => in_array($_POST["choice"],array("1","0"))?$_POST["choice"]:"0",
				":theme" => str_replace(" ", "_", Main::clean($_POST["theme"],3,TRUE)),
				":custom" => json_encode($custom),
				":created" => "NOW()",
				":expires" => $expires,
				":results" => in_array($_POST["results"],array("1","0"))?$_POST["results"]:"0",
				":uniqueid" => $unique
				);		
			// Pro Features
			if($this->isPro()){
				$data[":pass"] = Main::clean($_POST["pass"],3,TRUE); 
				$data[":count"] = in_array($_POST["vote"],array("month","day","off"))?$_POST["vote"]:"off";
			}			
			// Insert to database
			if($this->db->insert("poll",$data)){
				return Main::redirect(Main::href("create?pid=".$this->db->lastInsertId(),"",FALSE));
				//return Main::redirect(Main::href($unique,"",FALSE));
			}
			return Main::redirect("",array("danger",e("An unexpected error occurred, please try again.")));
		}		
		Main::set("body_class","dark");
		Main::set("title",e("Create your poll for free"));
		// Add Google Fonts
		if(!empty($this->config["fonts"])){
			$fonts=str_replace(" ","+",implode("|",  array_filter(json_decode($this->config["fonts"],TRUE))));
			Main::add("<link href='http://fonts.googleapis.com/css?family=$fonts' rel='stylesheet' type='text/css'>","custom",0);	
		}
		
		$this->header();
		include($this->t(__FUNCTION__));
		$this->footer();	
	}
	
	/**
	 * Create Poll
	 * @since 1.0
	 **/
	protected function create(){
		if(empty($_REQUEST['pid'])){
			$this->createsurvey();
			exit();
		}
		// Filter Do and ID
		$this->filter();	
		// Check if form is posted
		if(isset($_POST["token"])){
			// Kill the bots
			if(Main::bot()) die();
			// Validate Token
			if(!Main::validate_csrf_token($_POST["token"])){
				return Main::redirect(Main::href("create?pid=".$_REQUEST['pid'],"",FALSE),array("danger",e("Invalid token. Please try again.")));
			}
			
			if($_POST["captcha1"]!=$_SESSION['rand_code']){
				return Main::redirect(Main::href("create?pid=".$_REQUEST['pid'],"",FALSE),array("danger",e("Invalid human code. Please try again.")));
			}
			
			// Check if question length is higher than 5 chars
			if(strlen(Main::clean($_POST["question"],3,TRUE,FALSE)) < 5) return Main::redirect(Main::href("create?pid=".$_REQUEST['pid'],"",FALSE),array("danger",e("That is not a valid question.")));
			
			if(preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $_POST["question"]) //valid chars check
				&& preg_match("/^.{1,253}$/", $_POST["question"]) //overall length check
				&& preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $_POST["question"])   ) {
				return Main::redirect(Main::href("createsurvey","",FALSE),array("danger",e("That is not a valid question.")));
			}
			
			if(Gibberish::test(Main::clean($_POST["question"],3,TRUE,FALSE)) === true) return Main::redirect(Main::href("create?pid=".$_REQUEST['pid'],"",FALSE),array("danger",e("That is not a valid question.")));
			
			// Validate answers and skip empty ones - maximum number of question is 20
			$options=array();
			$i=1;
			if($this->isPro()){
				$max=$this->config["max_count"];
			}else{
				$max=$this->max_free;
			}
			foreach ($_POST["option"] as $q) {				
				if($i>=$max) break;
				$q=Main::clean($q,3,TRUE);
				if(empty($q)) continue;			
				$options[$i]=array("answer"=>$q,"count"=>0);
				$i++;
			}
			// Check if at least one answer is set
			if(empty($options[1])) return Main::redirect(Main::href("create?pid=".$_REQUEST['pid'],"",FALSE),array("danger",e("That is not a valid choice of answers.")));
			// JSON encode questions
			$options=json_encode($options);			
			// Generate custom info
			$custom=array(
				"background" => Main::clean($_POST["background"],3),
				"font" => in_array($_POST["font"], json_decode(strtolower(str_replace(" ","_",$this->config["fonts"])),TRUE))?$_POST["font"]:"null"
				);
			// Validate Post info
			$unique=$this->uniqueid();
			if(in_array($_POST["expires"], array("1h","5h","1d","5d","1w","5w","0"))){
				$to=array(
					"1h"=>"1 hours",
				  "5h"=>"5 hours",
				  "1d"=>"1 day",
				  "5d"=>"5 days",
				  "1w"=>"1 weeks",
				  "5w"=>"5 weeks",
				  "0"=>""
				  );
				if($_POST["expires"]=="0"){
					$expires="";
				}else{
					$expires=date("Y-m-d G:i:s",strtotime("+".$to[$_POST["expires"]]));
				}
			}
			// Prepare data
			$data=array(
				":userid" => $this->userid,
				":parent_id" => $_POST["pid"],
				":question" => Main::clean($_POST["question"],3,TRUE),
				":image_url" => Main::clean($_POST["image_url"],3,TRUE),
				":options" => $options,
				":share" => in_array($_POST["share"],array("1","0"))?$_POST["share"]:"0",
				":choice" => in_array($_POST["choice"],array("1","0"))?$_POST["choice"]:"0",
				":theme" => str_replace(" ", "_", Main::clean($_POST["theme"],3,TRUE)),
				":custom" => json_encode($custom),
				":created" => "NOW()",
				":expires" => $expires,
				":results" => in_array($_POST["results"],array("1","0"))?$_POST["results"]:"0",
				":uniqueid" => $unique
				);		
			// Pro Features
			if($this->isPro()){
				$data[":pass"] = Main::clean($_POST["pass"],3,TRUE); 
				$data[":count"] = in_array($_POST["vote"],array("month","day","off"))?$_POST["vote"]:"off";
			}			
			// Insert to database
			
			if($this->db->insert("poll",$data)){
			
				if(!empty($_POST['another'])){			
					return Main::redirect(Main::href("create?pid=".$_POST['pid'],"",FALSE));
				}else{
					if(!empty($_POST['pid'])){
						$this->db->object=TRUE;
						// Filter Do and ID
						$this->filter();				
						$poll=$this->db->get("poll","`id`=?",array("limit"=>1),array($_POST['pid']));
						return Main::redirect(Main::href($poll->uniqueid,"",FALSE));
					}else{
						return Main::redirect(Main::href($unique,"",FALSE));
					}
				}
			}
			return Main::redirect("",array("danger",e("An unexpected error occurred, please try again.")));
		}else{
			$this->db->object=TRUE;
			// Filter Do and ID
			$this->filter();				
			$poll=$this->db->get("poll","`id`=?",array("limit"=>1),array($_REQUEST['pid']));
		}
		Main::set("body_class","dark");
		Main::set("title",e("Create your poll for free"));
		// Add Google Fonts
		if(!empty($this->config["fonts"])){
			$fonts=str_replace(" ","+",implode("|",  array_filter(json_decode($this->config["fonts"],TRUE))));
			Main::add("<link href='http://fonts.googleapis.com/css?family=$fonts' rel='stylesheet' type='text/css'>","custom",0);	
		}
		
		$this->header();
		include($this->t(__FUNCTION__));
		$this->footer();		
	}	
	/**
	 * Display Poll
	 * @since 1.1
	 **/
	protected function display_poll(){
		// Get data
		$this->db->object=TRUE;
		// Filter Do and ID
		$this->filter();				
		$poll=$this->db->get("poll","BINARY `uniqueid`=?",array("limit"=>1),array($this->action));
		if(!$poll){
			return $this->_404();
		}
		// Check if current visitor has voted
		if($poll->count=="day"){						
			$poll->visited=($this->db->get("vote","pollid='$poll->id' AND ip=? AND DAY(date) = DAY(CURDATE()) AND YEAR(date)=YEAR(CURDATE())",array("limit"=>1),array(Main::ip())));
		}elseif($poll->count=="month"){
			$poll->visited=($this->db->get("vote","pollid='$poll->id' AND ip=? AND MONTH(date) = MONTH(CURDATE()) AND YEAR(date)=YEAR(CURDATE())",array("limit"=>1),array(Main::ip())));
		}else{
			$poll->visited=($this->db->get("vote",array("pollid"=>$poll->id,"ip"=>"?"),array("limit"=>1),array(Main::ip())) || isset($_COOKIE[$poll->uniqueid]));
		}
		
		
		// Get user's owner information
		if($poll->userid!=0){
			$user=$this->db->get(array("count"=>"id,membership,ga,expires","table"=>"user"),array("id"=>"?"),array("limit"=>1),array($poll->userid));
			// Downgrade user status if membershup expired
			if($user->membership=="pro" && strtotime($user->expires) < time()) $this->db->update("user",array("membership"=>"free"),array("id"=>$user->id,"admin"=>"0"));
		}
		
		// Decode encoded information
		$poll->answers=json_decode($poll->options);
		$height=round(138+count((array) $poll->answers)*68,0)+20;
		$poll->custom=json_decode($poll->custom);	
		// Validate Theme
		if($poll->theme=="0"){
			$poll->theme="";
		}else{
			$poll->theme=str_replace("_"," ",Main::clean($poll->theme,3));
		}
		// Get Background and validate information
		if(!empty($poll->custom->background)){
			if(Main::is_url($poll->custom->background)) Main::add("<style type='text/css'>#poll_widget{background-image: url('{$poll->custom->background}')}</style>","custom",0);
		}
		// Validate Font
		if($poll->custom->font!=="null"){
			$font=ucwords(str_replace("_", " ", $poll->custom->font));
			if(in_array($font, json_decode($this->config["fonts"],TRUE))){

				Main::add("<link href='http://fonts.googleapis.com/css?family=".str_replace(" ","+",$font)."' rel='stylesheet' type='text/css'>","custom",0);				
				Main::add("<style type='text/css'>#poll_widget{font-family:'$font'}</style>","custom",0);
			}
		}	
	
		if(!$poll->results && $poll->userid=$this->userid){
			$poll->results=1;
		}
		// Check if Poll Expired
		$expired=FALSE;
		if(!$poll->open && $poll->userid!=$this->userid){
			$expired=TRUE;
		}
		if(($poll->expires!=="never" || !empty($poll->expires)) && strtotime($poll->expires) > 0 && time() > strtotime($poll->expires) && $poll->userid!=$this->userid){
			$expired=TRUE;
		}

		$protected=FALSE;
		// Check if Password Protected
		if(!empty($poll->pass) && $poll->userid!=$this->userid){
			$protected=TRUE;
			// Validate Password
			if(isset($_POST["token"]) && !isset($_SESSION["access"])){
				// Validate CSRF Token
				if(!Main::validate_csrf_token($_POST["token"])){
					return Main::redirect(Main::href($this->action,"",FALSE),array("danger",e("Invalid token. Please try again.")));
				}				
				if($_POST["password"]==$poll->pass) {
					$_SESSION["access"]=md5($_POST["password"]);
					$protected=FALSE;
				}else{
					return Main::redirect(Main::href($this->action,"",FALSE),array("danger",e("Access denied. The password is not valid.")));
				}
			}			
			if(isset($_SESSION["access"]) && $_SESSION["access"]==md5($poll->pass)){
				$protected=FALSE;
			}
		}		
		// Add Google analytics code if pro
		if(isset($user->membership) && $user->membership=="pro" && !empty($user->ga)){
			$user->ga=trim($user->ga);
			Main::add("<script type='text/javascript'>
					(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');ga('create', '{$user->ga}');ga('send', 'pageview');
				</script>","custom",FALSE);
		}
		// Meta Title
		if(!$protected && !$expired){			
			Main::set("title",$poll->question);
			Main::set("description","Our current poll is: $poll->question. Feel free to answer it.");			
			Main::set("url",Main::href($poll->uniqueid));
		}else{
			Main::set("title","Poll is closed");
			Main::set("description","Thank you for your interest but this poll has been closed or expired.");						
		}

		Main::set("body_class","dark");
		$this->headerShow=FALSE;
		$this->footerShow=FALSE;
		$this->header();
		$withChild = false;
		if(empty($poll->parent_id)){
			$questions=$this->db->get("poll","`parent_id`=?","",array($poll->id));
			$withChild = (count($questions)>0);
		}
		include($this->t(__FUNCTION__));
		
		
		echo '<section class="display_question"><div class="container">';
		
		if(empty($poll->parent_id)){
			
			if(count($questions)>0){

				echo '<ul class="nav nav-tabs" role="tablist" id="myTab">';
				$x=1;
				
				foreach($questions as $q){
					$cls = '';
					if($x==1){
						$cls = 'active';
					}
					echo '<li role="presentation" class="'.$cls.'"><a class="tabs" data-id="tab'.$q->id.'"  href="#tab'.$q->id.'" aria-controls="tab'.$q->id.'" role="tab" data-toggle="tab">Question #'.$x.'</a></li>';
					$x++;
				}
				  
				echo '</ul>';
			
				echo '<div class="tab-content">';
				$x = 1;
				foreach($questions as $q){
					$cls = '';
					if($x==1){
						$cls = 'display:block;';
					}
					$this->question_display($q->id,$cls);
					$x++;
				}
				echo '</div>';
				
				
			}
		}
		echo '</div></div>';
		echo '<section class=""><div class="container">';
		$this->getShareEmbed($poll->id);
		echo '</div></div>';
		$this->footer();
		return;
	}	
	
	protected function question_display($id, $style='')
	{
		$question=$this->db->get("poll","`id`=?",array("limit"=>1),array($id));
		if($question->count=="day"){						
			$question->visited=($this->db->get("vote","pollid='$question->id' AND ip=? AND DAY(date) = DAY(CURDATE()) AND YEAR(date)=YEAR(CURDATE())",array("limit"=>1),array(Main::ip())));
		}elseif($question->count=="month"){
			$question->visited=($this->db->get("vote","pollid='$question->id' AND ip=? AND MONTH(date) = MONTH(CURDATE()) AND YEAR(date)=YEAR(CURDATE())",array("limit"=>1),array(Main::ip())));
		}else{
			//var_dump(Main::ip());
			//var_dump(isset($_COOKIE[$question->uniqueid]));
			$question->visited=($this->db->get("vote",array("pollid"=>$question->id,"ip"=>"?"),array("limit"=>1),array(Main::ip())) || isset($_COOKIE[$question->uniqueid]));
			//$question->visited=($this->db->get("vote",array("pollid"=>$question->id,"ip"=>"?"),array("limit"=>1),array(Main::ip())));
		}
		
		//$question->visited = false;
		
		// Get user's owner information
		if($question->userid!=0){
			$user=$this->db->get(array("count"=>"id,membership,ga,expires","table"=>"user"),array("id"=>"?"),array("limit"=>1),array($question->userid));
			// Downgrade user status if membershup expired
			if($user->membership=="pro" && strtotime($user->expires) < time()) $this->db->update("user",array("membership"=>"free"),array("id"=>$user->id,"admin"=>"0"));
		}
		
		// Decode encoded information
		$question->answers=json_decode($question->options);
		$height=round(138+count((array) $question->answers)*68,0)+20;
		$question->custom=json_decode($question->custom);	
		// Validate Theme
		if($question->theme=="0"){
			$question->theme="";
		}else{
			$question->theme=str_replace("_"," ",Main::clean($question->theme,3));
		}
		// Get Background and validate information
		if(!empty($question->custom->background)){
			if(Main::is_url($question->custom->background)) Main::add("<style type='text/css'>#poll_widget{background-image: url('{$question->custom->background}')}</style>","custom",0);
		}
		// Validate Font
		if($question->custom->font!=="null"){
			$font=ucwords(str_replace("_", " ", $question->custom->font));
			if(in_array($font, json_decode($this->config["fonts"],TRUE))){

				Main::add("<link href='http://fonts.googleapis.com/css?family=".str_replace(" ","+",$font)."' rel='stylesheet' type='text/css'>","custom",0);				
				Main::add("<style type='text/css'>#poll_widget{font-family:'$font'}</style>","custom",0);
			}
		}		
		if(!$question->results && $question->userid=$this->userid){
			//$question->results=1;
		}
		// Check if Poll Expired
		$expired=FALSE;
		if(!$question->open && $question->userid!=$this->userid){
			$expired=TRUE;
		}
		if(($question->expires!=="never" || !empty($question->expires)) && strtotime($question->expires) > 0 && time() > strtotime($question->expires) && $question->userid!=$this->userid){
			$expired=TRUE;
		}

		$protected=FALSE;
		// Check if Password Protected
		if(!empty($question->pass) && $question->userid!=$this->userid){
			$protected=TRUE;
			// Validate Password
			if(isset($_POST["token"]) && !isset($_SESSION["access"])){
				// Validate CSRF Token
				if(!Main::validate_csrf_token($_POST["token"])){
					return Main::redirect(Main::href($this->action,"",FALSE),array("danger",e("Invalid token. Please try again.")));
				}				
				if($_POST["password"]==$question->pass) {
					$_SESSION["access"]=md5($_POST["password"]);
					$protected=FALSE;
				}else{
					return Main::redirect(Main::href($this->action,"",FALSE),array("danger",e("Access denied. The password is not valid.")));
				}
			}			
			if(isset($_SESSION["access"]) && $_SESSION["access"]==md5($question->pass)){
				$protected=FALSE;
			}
		}
		include($this->t('display_question'));
	}
	
	/**
	 * Vote
	 * @since 1.1
	 **/
	protected function vote(){
		return $this->voteex();
		/*
		// Kill the Bots and validate request
		if(Main::bot()) die("Access Denied: We do not support bots, unfortunately.");
		if(!isset($_POST["poll_id"]) || !isset($_SERVER["HTTP_X_REQUESTED_WITH"]) || $_SERVER["HTTP_X_REQUESTED_WITH"]!=="XMLHttpRequest") die("Access Denied: Invalid request.");
		if(!isset($_POST["token"])) die("Access Denied: Token mismatch.");

    $this->db->object=TRUE;
		$poll=$this->db->get("poll","BINARY `uniqueid`=?",array("limit"=>1),array($_POST["poll_id"]));
		$options=json_decode($poll->options,TRUE);
		$max=count($options);
		if(is_array($_POST["answer"])){
			$vote=implode(",",Main::clean($_POST["answer"]));
			foreach ($_POST["answer"] as $key => $value) {
				$options[$key]["count"]=$options[$key]["count"]+1;
			}
		}else{
			if(!is_numeric($_POST["answer"]) || $_POST["answer"]>$max) return FALSE;
			$options[$_POST["answer"]]["count"]=$options[$_POST["answer"]]["count"]+1;
			$vote=$_POST["answer"];
		}
		$options=json_encode($options);
		$update=array(
				":votes"=>($poll->votes + 1),
				":options"=>$options
			);
		if(isset($_POST["referrer"]) && !empty($_POST["referrer"])){
			$source=Main::clean($_POST["referrer"],3,TRUE);
		}else{
			$source="";
		}
		$insert=array(
				":pollid" => $poll->id,
				":polluserid" => $poll->userid,
				":vote" => $vote,
				":ip" => Main::ip(),
				":country" => $this->country(),
				":source" => $source
			);
			if($this->db->insert("vote",$insert) && $this->db->update("poll","",array("id"=>$poll->id),$update)){
				if($poll->count=="month"){
					Main::cookie($poll->uniqueid,time(),60*60*24*30);
				}elseif ($poll->count=="day") {
					Main::cookie($poll->uniqueid,time(),60*60*24);
				}else{
					Main::cookie($poll->uniqueid,time(),60*60*24*365);
				}
				if(isset($_POST["embed"])){
					$this->results($poll->id,"",TRUE);
				}else{
					$this->results($poll->id);
				}
				return;
			}
		return;*/
	}
	
	protected function voteex(){
		// Kill the Bots and validate request
		if(Main::bot()) die("Access Denied: We do not support bots, unfortunately.");
		if(!isset($_POST["poll_id"]) || !isset($_SERVER["HTTP_X_REQUESTED_WITH"]) || $_SERVER["HTTP_X_REQUESTED_WITH"]!=="XMLHttpRequest") die("Access Denied: Invalid request.");
		if(!isset($_POST["token"])) die("Access Denied: Token mismatch.");

    $this->db->object=TRUE;
		$poll=$this->db->get("poll","BINARY `uniqueid`=?",array("limit"=>1),array($_POST["poll_id"]));
		$options=json_decode($poll->options,TRUE);
		$max=count($options);
		if(is_array($_POST["answer"])){
			$vote=implode(",",Main::clean($_POST["answer"]));
			foreach ($_POST["answer"] as $key => $value) {
				$options[$key]["count"]=$options[$key]["count"]+1;
			}
		}else{
			if(!is_numeric($_POST["answer"]) || $_POST["answer"]>$max) return FALSE;
			$options[$_POST["answer"]]["count"]=$options[$_POST["answer"]]["count"]+1;
			$vote=$_POST["answer"];
		}
		$options=json_encode($options);
		$update=array(
				":votes"=>($poll->votes + 1),
				":options"=>$options
			);
		if(isset($_POST["referrer"]) && !empty($_POST["referrer"])){
			$source=Main::clean($_POST["referrer"],3,TRUE);
		}else{
			$source="";
		}
		$insert=array(
				":pollid" => $poll->id,
				":polluserid" => $poll->userid,
				":vote" => $vote,
				":ip" => Main::ip(),
				":country" => $this->country(),
				":source" => $source
			);
			if($this->db->insert("vote",$insert) && $this->db->update("poll","",array("id"=>$poll->id),$update)){
				if($poll->count=="month"){
					Main::cookie($poll->uniqueid,time(),60*60*24*30);
				}elseif ($poll->count=="day") {
					Main::cookie($poll->uniqueid,time(),60*60*24);
				}else{
					Main::cookie($poll->uniqueid,time(),60*60*24*365);
				}
				//if(isset($_POST["embed"])){
				//	$this->results($poll->id,"",TRUE);
				//}else{
				//	$this->results($poll->id);
				//}
				
				
				$r = '<div id="poll_question">
				<h3>'.$poll->question.'</h3>
				<h5>Thank you for voting!</h5>
				</div>
				<p id="poll_button" style="display:none" > <button type="button" class="btn btn-widget view-results-trigger">View Results</button></p>
				<p id="poll_button" style="display:none"> <button type="button" onclick="javascript:update_results(\''.Main::href("results").'\',\''.$poll->uniqueid.'\')" class="btn btn-widget view-results" id="view-results">View Results</button></p>
				';
				echo $r;
				return;
			}
		return;
	}	
	/**
	 * Results
	 * @since 1.1
	 **/
	protected function results($pollid="",$vote_id="",$embed=FALSE){
		// Get results
		$this->db->object=TRUE;
		if(empty($pollid)) {
			$poll=$this->db->get("poll","BINARY `uniqueid`=?",array("limit"=>1),array($_POST["poll_id"]));
		}else{
			$poll=$this->db->get("poll",array("id"=>"?"),array("limit"=>1),array($pollid));
		}
		if(!$poll->results) {
			echo '<div id="poll_question">
                <h3>'.e("Thank you for voting!").'</h3>
              </div><p id="poll_button"> <a href="'.Main::href("create").'" class="btn btn-transparent">'.e("Create your poll").'</a></p>';
			return;
		}
		$user=$this->db->get(array("count"=>"membership,ga","table"=>"user"),array("id"=>"?"),array("limit"=>1),array($poll->userid));
		$options=json_decode($poll->options,TRUE);
		$return="";
		
		if(!empty($poll->parent_id)){
			$parent=$this->db->get("poll","`id`=?",array("limit"=>1),array($poll->parent_id));
			$link = $parent->uniqueid;
		}else{
			$link = $poll->uniqueid;
		}
		/*////03.26.2015
    if($poll->share && $embed==TRUE){
		
		
		
    	$height=round(138+count($options)*68,0)+20;
      $return.='<a href="#embed" id="poll_embed">'.e("Embed").'</a>
      <div id="poll_embed_holder" class="live_form">
        <div class="input-group">
          <span class="input-group-addon">'.e("Share").'</span>
          <input type="text" class="form-control onclick-select" value="'.Main::href($link).'">
        </div>              
        <div class="input-group">
          <span class="input-group-addon">'.e("Embed").'</span>
          <input type="text" class="form-control form-embed-code onclick-select" value="&lt;iframe src=&quot;'.Main::href("embed/{$link}").'&quot; width=&quot;400&quot; height=&quot;'.$height.'&quot; frameborder=&quot;0&quot;&gt;">
        </div>
        <div class="input-group">
          <a href="https://www.facebook.com/sharer.php?u='.Main::href($link).'" class="btn btn-transparent" target="_blank">'.e("Share").' Facebook</a>
          <a href="https://twitter.com/share?url='.Main::href($link).'&amp;text='.urlencode($poll->question).'" class="btn btn-transparent" target="_blank">'.e("Share on").' on Twitter</a>                                
        </div>            
      </div><!-- /#poll_embed_holder -->';            
   	}		
	*/
		$return.="<div class='poll_results' data-action='".Main::href("results")."'  data-id='{$link}'> 
							<div id='poll_question'> 
								<h3>{$poll->question}</h3>
							</div>
							<ul class='results'>";		
		foreach ($options as $i => $vote) {
			$p=round($vote["count"]*100/($poll->votes=="0"?1:$poll->votes),0);
 			/*'.($i==$vote_id?'<span class="current">'.e('You').'</span>':'').'*/
			$return.='<li>
			        <div class="holder">'.$vote["answer"].'</div>
              <div class="row">
                <div class="col-xs-9">
                  <div class="progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow="'.$p.'">
                      '.$p.'%
                    </div>
                  </div>                      
                </div>
                <div class="col-xs-3">'.$vote["count"].' Votes</div>
              </div>
            </li>';
		}
		if($poll->choice){
			$return.="</ul><h4><span>{$poll->votes} Users</span>";		
		}else{
			$return.="</ul><h4><span>{$poll->votes} Votes</span>";		
		}
		if($poll->userid!=0 && $user->membership!="pro"){
			$return.=' <div class="branding pull-right">
                    <a href="'.$this->config["url"].'" target="_blank" style="color:#fff">'.$this->config["title"].'</a>
                  </div></h4>';
		}else{
			$return.="</h4>";
		}
		/*////03.26.2015
		if($poll->share && $embed==FALSE){
			$height=round(138+count($options)*68,0)+20;
			$return.="<div class='box-holder slidedown none'>
									<div class='input-group'>
		                <span class='input-group-addon'>Share</span>
		                <input type='text' class='form-control onclick-select' value='".Main::href($link)."'>
		              </div>              
		              <div class='input-group'>
		                <span class='input-group-addon'>Embed</span>
		                <input type='text' class='form-control onclick-select' value='&lt;iframe id=&quot;poll{$link}&quot; src=&quot;".Main::href("embed/{$link}")."&quot; width=&quot;350&quot; height=&quot;$height&quot; allowTransparency=&quot;true&quot; frameborder=&quot;0&quot; scrolling=&quot;no&quot; &gt;&lt;/iframe&gt;'>
		              </div>       
					        <div class='input-group'>
					          <a href='https://www.facebook.com/sharer.php?u=".Main::href($link)."' class='btn btn-transparent btn-xs' target='_blank'>".e('Share on Facebook')." </a> &nbsp; &nbsp;         
					          <a href='https://twitter.com/share?url=".Main::href($link)."&amp;text=".urlencode($poll->question)."' class='btn btn-transparent btn-xs' target='_blank'>".e('Share on')." Twitter</a>
					        </div>   
			      		</div>";
		}
		*/
		$return.="</div><!--.poll-result-->";
		echo $return;
	}
	
	//03.26.2015
	protected function getShareEmbed($pollid=""){
		// Get results
		$return = '';
		$this->db->object=TRUE;
		if(empty($pollid)) {
			$poll=$this->db->get("poll","BINARY `uniqueid`=?",array("limit"=>1),array($_POST["poll_id"]));
		}else{
			$poll=$this->db->get("poll",array("id"=>"?"),array("limit"=>1),array($pollid));
		}
		
		$user=$this->db->get(array("count"=>"membership,ga","table"=>"user"),array("id"=>"?"),array("limit"=>1),array($poll->userid));
		$options=json_decode($poll->options,TRUE);
		if(!empty($poll->parent_id)){
			$parent=$this->db->get("poll","`id`=?",array("limit"=>1),array($poll->parent_id));
			$link = $parent->uniqueid;
		}else{
			$link = $poll->uniqueid;
		}
		//if($poll->share){
			$height=round(138+count($options)*68,0)+20;
			$return .='<div id="poll_widget" class=" parent_survey">';
			$return.="<div class='poll_results' data-action='".Main::href("results")."'  data-id='{$link}'> ";
			$return.="<div class='box-holder slidedown '>
									<div class='input-group'>
		                <span class='input-group-addon'>Share</span>
		                <input type='text' class='form-control onclick-select' value='".Main::href($link)."'>
		              </div>              
		              <div class='input-group'>
		                <span class='input-group-addon'>Embed</span>
		                <input type='text' class='form-control onclick-select' value='&lt;iframe id=&quot;poll{$link}&quot; src=&quot;".Main::href("embed/{$link}")."&quot; width=&quot;350&quot; height=&quot;$height&quot; allowTransparency=&quot;true&quot; frameborder=&quot;0&quot; scrolling=&quot;no&quot; &gt;&lt;/iframe&gt;'>
		              </div>       
					        <div class='input-group'>
					          <a href='https://www.facebook.com/sharer.php?u=".Main::href($link)."' class='btn btn-transparent btn-xs' target='_blank'>".e('Share on Facebook')." </a> &nbsp; &nbsp;         
					          <a href='https://twitter.com/share?url=".Main::href($link)."&amp;text=".urlencode($poll->question)."' class='btn btn-transparent btn-xs' target='_blank'>".e('Share on')." Twitter</a>
					        </div>   
			      		</div>";
			$return.="</div><!--.poll-result-->";
			$return.="</div>";
		//}
		
		echo $return;
	}
	
	protected function testpage(){
		$this->header();        
		include $this->t('testpage');
		$this->footer();
	}
	protected function referral(){
		$this->header();        
		include $this->t('referral'); 
		$this->footer();
	}
	protected function terms(){
		$this->header();        
		include $this->t('terms');
		$this->footer();
	}
	protected function privacy(){
		$this->header();        
		include $this->t('privacy');
		$this->footer();
	}
	protected function cookiepolicy(){
		$this->header();        
		include $this->t('cookiepolicy');
		$this->footer();
	}
	protected function partners(){
		$this->header();        
		include $this->t('partners');
		$this->footer();
	}
	protected function staffing(){
		$this->header();        
		include $this->t('staffing');
		$this->footer();
	}
	protected function contact(){
		$this->header();        
		include $this->t('contact');
		$this->footer();
	}
	protected function apps(){
		$this->header();        
		include $this->t('apps');
		$this->footer();
	}
	protected function about(){
		$this->header();
		include $this->t('about');
		$this->footer();
	}
	
	protected function listsurvey(){
		header("Content-type: application/json");
		$return = array();
		$results = $this->db->get("poll","",array("order"=>"created"));
		foreach($results as $result){
			$result = (array) $result;
			$user=$this->db->get("user",array("id"=>"?"),array("limit"=>1),array($result['userid']));
			$user = (array) $user;
			if(!empty($user['email'])){ 
				$email = $user['email'];
				$return[] = array(
					'id'=> $result['id'],
					'uniqueid'=> $result['uniqueid'],
					'question'=> $result['question'],
					'email'=> $email,
					'votes'=>$result['votes']
				);
			}
		}		
		echo json_encode(array('status'=>1,'data'=>$return,'total_polls'=>count($return),'total_users'=>$this->db->count("user")));
	}
	
	/**
	 * Embed Poll
	 * @since 1.0
	 **/
	protected function embed(){
		$this->db->object=TRUE;
		// Filter  ID
		$this->filter($this->id);				
		$poll=$this->db->get("poll","BINARY `uniqueid`=?",array("limit"=>1),array($this->do));
		if(!$poll->share) die("Sorry this cannot be embedded.");
		// Check if current visitor has voted
		$poll->visited=($this->db->get("vote",array("pollid"=>$poll->id,"ip"=>"?"),array("limit"=>1),array(Main::ip())) || isset($_COOKIE[$poll->uniqueid]));
		// Get user's owner information
		if($poll->userid!=0){
			$user=$this->db->get(array("count"=>"membership,ga","table"=>"user"),array("id"=>"?"),array("limit"=>1),array($poll->userid));
		}
		// Decode encoded information
		$poll->answers=json_decode($poll->options);
		$poll->custom=json_decode($poll->custom);	
		$height=round(138+count((array) $poll->answers)*68,0)+20;
		// Validate Theme
		if($poll->theme=="0"){
			$poll->theme="";
		}else{
			$poll->theme=str_replace("_"," ",Main::clean($poll->theme,3));
		}
		// Get Background and validate information
		if(!empty($poll->custom->background)){
			if(Main::is_url($poll->custom->background)) Main::add("<style type='text/css'>#poll_widget{background-image: url('{$poll->custom->background}')}</style>","custom",0);
		}
		// Validate Font
		if($poll->custom->font!=="null"){
			$font=ucwords(str_replace("_", " ", $poll->custom->font));
			if(in_array($font, json_decode($this->config["fonts"],TRUE))){
				Main::add("<link href='http://fonts.googleapis.com/css?family=".str_replace(" ","+",$font)."' rel='stylesheet' type='text/css'>","custom",0);				
				Main::add("<style type='text/css'>#poll_widget{font-family:'$font'}</style>","custom",0);
			}
		}		
		// Check if Poll Expired
		$expired=FALSE;
		if(!$poll->open && $poll->userid!=$this->userid){
			$expired=TRUE;
		}
		if(($poll->expires!=="never" || !empty($poll->expires)) && strtotime($poll->expires) > 0 && time() > strtotime($poll->expires) && $poll->userid!=$this->userid){
			$expired=TRUE;
		}


		$protected=FALSE;
		// Check if Password Protected
		if(!empty($poll->pass) && $poll->userid!=$this->userid){
			$protected=TRUE;
			// Validate Password
			if(isset($_POST["token"]) && !isset($_SESSION["access"])){
				// Validate CSRF Token
				if(!Main::validate_csrf_token($_POST["token"])){
					return Main::redirect(Main::href($this->action,"",FALSE),array("danger",e("Invalid token. Please try again.")));
				}				
				if($_POST["password"]==$poll->pass) {
					$_SESSION["access"]=md5($_POST["password"]);
					$protected=FALSE;
				}else{
					return Main::redirect(Main::href($this->action,"",FALSE),array("danger",e("Access denied. The password is not valid.")));
				}
			}			
			if(isset($_SESSION["access"]) && $_SESSION["access"]==md5($poll->pass)){
				$protected=FALSE;
			}
		}		
		// Add Google analytics code if pro
		if($user->membership=="pro" && !empty($user->ga)){
			$user->ga=trim($user->ga);
			Main::add("<script type='text/javascript'>
					(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');ga('create', '{$user->ga}');ga('send', 'pageview');
				</script>","custom",FALSE);
		}
		// Meta Title
		if(!$protected && !$expired){			
			Main::set("title",$poll->question);
			Main::set("description","Our current poll is: $poll->question. Feel free to answer it.");
		}
		Main::set("body_class","transparent");		
		$this->headerShow=FALSE;
		$this->footerShow=FALSE;
		$this->header();
		include($this->t(__FUNCTION__));
		$this->footer();
		return;
	}	
	/**
	 * Poll Edit
	 * @since 1.0
	 **/
	protected function edit(){
		// Get Poll
		$this->db->object=TRUE;
		if(!$poll=$this->db->get("poll",array("userid"=>"?","id"=>"?"),array("limit"=>1),array($this->userid,$this->id))){
			return $this->_404();
		}		
		# $poll=(object) $poll;
		$options=json_decode($poll->options);		

		// Update Poll
		if(isset($_POST["token"])){	
			if(!Main::validate_csrf_token($_POST["token"])){
				return Main::redirect(Main::href("user/edit/{$this->id}","",FALSE),array("danger",e("Invalid token. Please try again.")));
			}			
			if(strlen(Main::clean($_POST["question"],3,TRUE,FALSE)) < 5) return Main::redirect(Main::href("user/edit/{$this->id}","",FALSE),array("danger",e("That is not a valid question.")));
			
			if(preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $_POST["question"]) //valid chars check
				&& preg_match("/^.{1,253}$/", $_POST["question"]) //overall length check
				&& preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $_POST["question"])   ) {
				return Main::redirect(Main::href("user/edit/{$this->id}","",FALSE),array("danger",e("That is not a valid question.")));
			}
			
			if(Gibberish::test(Main::clean($_POST["question"],3,TRUE,FALSE)) === true) return Main::redirect(Main::href("user/edit/{$this->id}","",FALSE),array("danger",e("That is not a valid question.")));
			
			// Get Count
			foreach ($options as $key => $value) {
				$key=$key;
				$count[$key]=$value->count;
			}
			$new_options=array();
			if($this->isPro()){
				$max=$this->config["max_count"];
			}else{
				$max=$this->max_free;
			}
			$i=1;
			foreach ($_POST["option"] as $key=>$q) {	
				if($i>=$max) break;
				$q=Main::clean($q,3,TRUE);
				if(empty($q)) continue;
				$new_options[]=array("answer"=>$q,"count"=>(isset($count[$key])?$count[$key]:0));
				$i++;
			}
			
			if(!empty($_POST['parent_id'])){
				if(empty($new_options[1]["answer"])) return Main::redirect(Main::href("user/edit/{$this->id}","",FALSE),array("danger",e("That is not a valid choice of answers.")));
			}
			
			$custom=array(
				"background" => Main::clean($_POST["background"],3),
				"font" => in_array($_POST["font"], json_decode(strtolower(str_replace(" ","_",$this->config["fonts"])),TRUE))?$_POST["font"]:"null"
				);
			$data=array(
				":userid" => $this->userid,
				":question" => Main::clean($_POST["question"],3,TRUE),
				":options" => json_encode($new_options),
				":share" => in_array($_POST["share"],array("1","0"))?$_POST["share"]:"0",
				":choice" => in_array($_POST["choice"],array("1","0"))?$_POST["choice"]:"0",
				":theme" => str_replace(" ", "_", Main::clean($_POST["theme"],3)),
				":custom" => json_encode($custom),
				":expires" => (!Main::validatedate($_POST["expires"],"Y-m-d")?"":Main::clean($_POST["expires"],3)),
        ":results"=> in_array($_POST["results"],array("1","0"))?$_POST["results"]:"0",				
				);				
				// Pro Features
				if($this->isPro()){
					$data[":pass"] = Main::clean($_POST["pass"],3,TRUE); 
					$data[":count"] = in_array($_POST["vote"],array("month","day","off"))?$_POST["vote"]:"off";
				}

				if($this->db->update("poll","",array("id"=>$this->id),$data))				{
					return Main::redirect(Main::href("user/edit/{$this->id}","",FALSE),array("success",e("Poll has been edited.")));
				}
		}

    
		$custom=json_decode($poll->custom);
		$poll->font=$custom->font;
		$poll->expires=empty($poll->expires)?e("Never"):date("Y-m-d",strtotime($poll->expires));
		$poll->background=$custom->background;
		$poll->theme=str_replace("_"," ",$poll->theme);
		
		$h1 = '';
		if(!empty($poll->parent_id)){
			$parent=$this->db->get("poll","`id`=?",array("limit"=>1),array($poll->parent_id));
			$h1 = "<h1>".$parent->question."</h1>";
		}
		
		$caption = !empty($poll->parent_id)?'Question':'Survey';
		$parent_id = !empty($poll->parent_id)?"<input name='parent_id' type='hidden' value='".$poll->parent_id."' />":"";
		$content="<div class='row'>
          <form action='".Main::href("user/edit/{$poll->id}")."' method='post'>     $parent_id             
            <div class='col-md-8'>
              <div class='box-holder live_form'>                  
			  $h1
                <h4>".e($caption)."</h4>
                <div class='form-group'>
                  <input type='text' class='form-control' placeholder='Question' name='question' value='{$poll->question}'>
                </div>  ";
				if(!empty($parent_id)){
					$content.="<h4>".e('Answers')."</h4>";
				}
				$content.="
                  <ul id='sortable'>";
                  foreach ($options as $key => $value) {
                  	$content.= "<li id='poll_sort_".($key)."'>
                      <div class='row'>
                        <div class='col-md-9'>
                          <div class='input-group'>
                            <span class='input-group-addon'><i class='glyphicon glyphicon-move'></i></span>
                            <input type='text' class='form-control' name='option[".($key)."]' value='{$value->answer}'>
                          </div>                          
                        </div>
                        <div class='col-md-3'>
                          <div class='input-group'>
                            <span class='input-group-addon'><i class='glyphicon glyphicon-user'></i></span>
                            <input type='text' class='form-control' value='{$value->count}' disabled>
                          </div>                          
                        </div>
                      </div>                
                    </li>   ";
                  }                           
    $content.="     </ul>    ";
	if(!empty($parent_id)){
	$content.="
                  <p>
                    <a href='#' id='add-field' class='btn btn-transparent'><small>".e('Add Field')."</small></a>
                  </p>        
				  ";
	}
$content.="				  
              </div>       
              <p><br>
              	<button type='submit' class='btn btn-primary btn-lg'>".e('Update')."</button>";
if(empty($poll->parent_id)){
			$content.='&nbsp;<a target="_blank" class="btn btn-warning btn-lg " href="http://collegesurvey.com/create?pid='.$poll->id.'">Add Question</a>';
}

if(!empty($poll->parent_id)){
			$parent=$this->db->get("poll","`id`=?",array("limit"=>1),array($poll->parent_id));
			$link = $parent->uniqueid;
		}else{
			$link = $poll->uniqueid;
		}

$content.="				  				
              	<a href='".Main::href($link)."' class='btn btn-success btn-lg pull-right' target='_blank'>".e('View')."</a>
              </p> 
            </div>";
			if(!empty($poll->parent_id)){
				
			
            $content.="<div class='col-md-4'>  
						<ul class='nav nav-pills'>
						  <li class='active'><a href='#' data-id='options' class='tabs'>".e("Options")."</a></li>
						  <li><a href='#' data-id='theme-options' class='tabs'>".e("Design")."</a></li>
						</ul>         
						<br>   
             <div id='options' class='tabbed'>
              <div class='form-group'>
                <label for='share'>".e('Sharing')."</label>
                <select id='share' name='share'>
                  <option value='1' ".($poll->share?"selected":"").">".e('Enabled')."</option>
                  <option value='0' ".(!$poll->share?"selected":"").">".e('Disabled')."</option>                
                </select>
              </div>    
              <div class='form-group'>
                <label for='results'>".e('Show Results')."</label>
                <select id='results' name='results'>
                  <option value='1' ".($poll->results?"selected":"").">".e('Enabled')."</option>
                  <option value='0' ".(!$poll->results?"selected":"").">".e('Disabled')."</option>                
                </select>
              </div>                  
              <div class='form-group'>
                <label for='font'>".e('Multiple Choices')."</label>
                <select id='choice' name='choice'>
                  <option value='1' ".($poll->choice?"selected":"").">".e('Enabled')."</option>
                  <option value='0' ".(!$poll->choice?"selected":"").">".e('Disabled')."</option>                
                </select>
              </div>";      
      if($this->isPro()){
        $content.="<div class='form-group'>
                <label for='vote'>".e('Multiple Votes')."</label>
                <select id='vote' name='vote'>
                  <option value='month' ".($poll->count=="month"?"selected":"").">".e('Monthly')."</option>
                  <option value='day' ".($poll->count=="day"?"selected":"").">".e('Daily')."</option>
                  <option value='0' ".($poll->count=="off"?"selected":"").">".e('Disabled')."</option>                
                </select>
              </div>              
              <div class='form-group'>
                <label for='pass'>Password</label>
                <input type='text' class='form-control' id='pass' name='pass' value='{$poll->pass}'>
              </div> ";
      }
      $content.="<div class='form-group'>
                <label for='expires'>".e('Expires')."</label>
       					<input type='text' class='form-control' id='expires' name='expires' value='{$poll->expires}'>
              </div> 
             </div> 
             <div id='theme-options' class='tabbed'>
							<h5>".e("Simple")."</h5>
							<ul class='themes'>                
							  <li class='dark'><a href='#' ".($poll->theme=="dark"?"class='current'":"")." data-class='dark'>Dark</a></li>
							  <li class='light'><a href='#'  ".($poll->theme=="light"?"class='current'":"")." data-class='light'>Light</a></li>
							  <li class='blue'><a href='#' ".($poll->theme=="blue" || empty($poll->theme)?"class='current'":"")." data-class='blue'>Blue</a></li>                
							  <li class='red'><a href='#' ".($poll->theme=="red"?"class='current'":"")." data-class='red'>Red</a></li>
							  <li class='green'><a href='#'  ".($poll->theme=="green"?"class='current'":"")." data-class='green'>Green</a></li>
							  <li class='yellow'><a href='#' ".($poll->theme=="yellow"?"class='current'":"")." data-class='yellow'>Yellow</a></li>
							</ul> 
							<h5>".e("Boxed")."</h5>
							<ul class='themes'>                
							  <li class='dark '><a href='#' ".($poll->theme=="bs dark"?"class='current'":"")." data-class='bs dark'>Dark</a></li>
							  <li class='light'><a href='#'  ".($poll->theme=="bs light"?"class='current'":"")." data-class='bs light'>Light</a></li>
							  <li class='blue '><a href='#' ".($poll->theme=="bs blue"?"class='current'":"")." data-class='bs blue'>Blue</a></li>                
							  <li class='red'><a href='#' ".($poll->theme=="bs red"?"class='current'":"")." data-class='bs red'>Red</a></li>
							  <li class='green'><a href='#'  ".($poll->theme=="bs green"?"class='current'":"")." data-class='bs green'>Green</a></li>
							  <li class='yellow'><a href='#' ".($poll->theme=="bs yellow"?"class='current'":"")." data-class='bs yellow'>Yellow</a></li>
							</ul>     
							<h5>".e("Inline")."</h5>
							<ul class='themes'>                
							  <li class='dark '><a href='#' ".($poll->theme=="is dark"?"class='current'":"")." data-class='is dark'>Dark</a></li>
							  <li class='light'><a href='#'  ".($poll->theme=="is light"?"class='current'":"")." data-class='is light'>Light</a></li>
							  <li class='blue '><a href='#' ".($poll->theme=="is blue"?"class='current'":"")." data-class='is blue'>Blue</a></li>                
							  <li class='red'><a href='#' ".($poll->theme=="is red"?"class='current'":"")." data-class='is red'>Red</a></li>
							  <li class='green'><a href='#'  ".($poll->theme=="is green"?"class='current'":"")." data-class='is green'>Green</a></li>
							  <li class='yellow'><a href='#' ".($poll->theme=="is yellow"?"class='current'":"")." data-class='is yellow'>Yellow</a></li>
							</ul>                  
							<input type='hidden' name='theme' value='{$poll->theme}' id='poll_theme_value'>    
							<br>             
              <div class='form-group'>
                <label for='font'>".e('Font')."</label>
                <select id='font' name='font' class='choose_font'>
                  <option value='null' ".($poll->font=="null"?"selected":"").">Default</option>";
                  $fonts=json_decode($this->config["fonts"],TRUE);
                  foreach ($fonts as $font){
                      $content.="<option value='".str_replace(" ","_", strtolower($font))."' ".($poll->font==str_replace(" ","_", strtolower($font))?'selected':'').">$font</option>";
                  }                
                                     
            $content.="</select>
              </div>              
              <div class='form-group'>
                <label for='background'>".e('Custom Image Background')."</label>
                <input type='text' class='form-control' name='background' value='{$poll->background}' id='background' placholder='e.g. http://mysite.com/background.png'>
              </div>                  
            </div>";
			
			}
			
            $content.="".Main::csrf_token(TRUE)."
          </form>
        </div>";
		// Show Template		
		$this->isUser=TRUE;
		$this->footerShow=FALSE;				
		Main::set("title","Edit Poll: {$poll->question}");
		$this->header();        
		echo $content;
		$this->footer();		
	}	
	/**
	 * Poll Delete
	 * @since 1.0
	 **/
	protected function delete(){
		if(isset($_POST["token"]) && isset($_POST["delete-id"]) && is_array($_POST["delete-id"])){
			if(!Main::validate_csrf_token($_POST["token"])){
				return Main::redirect(Main::href("user","",FALSE),array("danger",e("Invalid token. Please try again.")));
			}			
			$query="(";
			$query2="(";
			$c=count($_POST["delete-id"]);
			$p="";
			$i=1;
			foreach ($_POST["delete-id"] as $id) {
				if($i>=$c){
					$query.="`id` = :id$i";
					$query2.="`pollid` = :id$i";
				}else{
					$query.="`id` = :id$i OR ";
					$query2.="`pollid` = :id$i OR ";
				}
				$p[":id$i"]=$id;
				$i++;
			}
			$query.=") AND `userid`=:user";
			$query2.=") AND `polluserid`=:user";			
			$p[":user"]=$this->userid;
			$this->db->delete("poll",$query,$p);
			$this->db->delete("vote",$query2,$p);
			return Main::redirect(Main::href("user","",FALSE),array("success",e("Polls have been deleted.")));
		}

		if(!empty($this->id) && is_numeric($this->id)){
			$id=$this->id;
			$this->db->delete("poll",array("id"=>"?","userid"=>"?"),array($id,$this->userid));
			$this->db->delete("vote",array("pollid"=>"?","polluserid"=>"?"),array($id,$this->userid));
			return Main::redirect(Main::href("user","",FALSE),array("success",e("Poll has been deleted.")));
		} 
		return Main::redirect(Main::href("user","",FALSE),array("danger",e("An unexpected error occurred, please try again.")));
	}
	/**
	 * Poll Results
	 * @since 1.0
	 **/
	protected function stats(){
		// Check if is Pro
		if(!$this->isPro()) return Main::redirect(Main::href("upgrade","",FALSE),array("warning",e("Please upgrade to a premium package to unlock this feature.")));
    // Check if user owns poll or poll exists
    $this->db->object=TRUE;
		if(!$poll=$this->db->get("poll",array("userid"=>"?","id"=>"?"),array("limit"=>1),array($this->userid,$this->id))) return $this->_404();
		// Get Ip
		$ips=$this->db->get(array("count"=>"COUNT(ip) as count,ip as ip","table"=>"vote"),array("pollid"=>"?","polluserid"=>"?"),array("limit"=>24,"group"=>"ip","order"=>"date"),array($this->id,$this->userid));		
		// Get Source
		$refs=$this->db->get(array("count"=>"SUBSTR(source, 1 , IF(LOCATE('/', source, 8), LOCATE('/', source, 8)-1,LENGTH(source))) as domain, COUNT(source) as count", "table"=>"vote"),array("pollid"=>"?","polluserid"=>"?"),array("limit"=>24,"group"=>"domain","order"=>"count"),array($this->id,$this->userid));			
		// Generate Chart
		$this->charts();
		// Get Countries
		$topcountries=$this->countries();
		// Set Meta data
		Main::set("title",e("Stats for")." {$poll->question}");
		$this->isUser=TRUE;
		$this->footerShow=FALSE;		
		$this->header();
		include($this->t(__FUNCTION__));
		$this->footer();
	}
    /**
     *  Dashboard Country Function
     *  @since 1.0
     */     
     protected function countries(){
     		$this->db->object=FALSE;
        $countries=$this->db->get(array("count"=>"COUNT(country) as count, country as country","table"=>"vote"),array("polluserid"=>"?","pollid"=>"?"),array("group"=>"country","order"=>"count","limit"=>15),array($this->userid,$this->id));
        $i=0;
        $top_countries=array();
        $country=array();
        foreach ($countries as $c) {
          $country[strtoupper($c["country"])]=$c["count"];
          if($i<=10){
            if(!empty($c["country"])) $top_countries[Main::ccode($c["country"])]=$c["count"];
          }
        }
				Main::add("{$this->config["url"]}/static/js/jvector.css","style",FALSE);        
        Main::add("{$this->config["url"]}/static/js/jvector.js","script");
        Main::add("{$this->config["url"]}/static/js/jvector.world.js","script");
        Main::add("<script type='text/javascript'>
        var c=".json_encode($country).";
        $('#country-map').vectorMap({
          map: 'world_mill_en',
          backgroundColor: 'transparent',
          series: {
            regions: [{
              values: c,
              scale: ['#74CBFA', '#0da1f5'],
              normalizeFunction: 'polynomial'
            }]
          },
          onRegionLabelShow: function(e, el, code){
            if(typeof c[code]!='undefined') el.html(el.html()+' ('+c[code]+' Votes)');
          }     
        });</script>","custom");
        return $top_countries;
     }  
  /**
   *  Dashboard Chart Data Function
   *  @since 1.0
   */   
    protected function charts($span=15){
    	$this->db->object=FALSE;
      $votes=$this->db->get(array("count"=>"COUNT(DATE(date)) as count, DATE(date) as date","table"=>"vote"),"(date >= CURDATE() - INTERVAL $span DAY) AND pollid=? AND polluserid=?",array("group_custom"=>"DAY(date)","limit"=>"0 , $span"),array($this->id,$this->userid));      
      $new_date=array();  
      foreach ($votes as $votes[0] => $data) {
        $new_date[date("d M",strtotime($data["date"]))]=$data["count"];
      }
      $timestamp = time();
      for ($i = 0 ; $i < $span ; $i++) {
          $array[date('d M', $timestamp)]=0;
          $timestamp -= 24 * 3600;
      }
      $date=""; $var=""; $i=0; 

      foreach ($array as $key => $value) {
        $i++;
        if(isset($new_date[$key])){
          $var.="[".($span-$i).", ".$new_date[$key]."], ";
          $date.="[".($span-$i).",\"$key\"], ";
        }else{
          $var.="[".($span-$i).", 0], ";
          $date.="[".($span-$i).", \"$key\"], ";
        } 
      }
      $data=array($var,$date);
      Main::add("{$this->config["url"]}/static/js/flot.js","js");
      Main::add("<script type='text/javascript'>var options = {
            series: {
              lines: { show: true, lineWidth: 2,fill: true},
              //bars: { show: true,lineWidth: 1 },  
              points: { show: true, lineWidth: 2 }, 
              shadowSize: 0
            },
            grid: { hoverable: true, clickable: true, tickColor: 'transparent', borderWidth:0 },
            colors: ['#FFFFFF', '#F11010', '#1F2227'],
            xaxis: {ticks:[{$data[1]}], tickDecimals: 0, color: '#fff'},
            yaxis: {ticks:3, tickDecimals: 0, color: '#fff'},
            xaxes: [ { mode: 'time'} ]
        }; 
        var data = [{
            data: [{$data[0]}]
        }];
        $.plot('#vote-chart', data ,options);</script>",'custom',TRUE);        
    }       
   /**
     * Get In-depth Stats
     * @since 1.0
     **/  	
    protected function server(){
     	if(!$this->logged()) return $this->_404();

     	if(!isset($_POST["request"]) || empty($_POST["request"]) || !isset($_POST["token"]) || $_POST["token"]!==$this->config["public_token"]) return die("");
     	$html="";
     	$this->db->object=TRUE;     	
     	// Get Countries
     	if($_POST["request"]=="country"){
     		if(!$this->isPro()) return die("notpro");
	     	if(!$votes=$this->db->get(array("count"=>"COUNT(*) as count, vote as vote","table"=>"vote"),array("pollid"=>"?","polluserid"=>"?","country"=>"?"),array("group"=>"vote"),array(Main::clean($_POST["id"],3,TRUE),$this->userid,Main::clean(strtolower($_POST["value"]),3,TRUE)))){
	     		die("0");
	     	}
	     	$html.="<h4>".e("Vote Distribution for")." ".Main::ccode($_POST["value"])."</h4>
	     					<ol>";
	     	$poll=$this->db->get(array("table"=>"poll","count"=>"options,votes"),array("id"=>"?","userid"=>"?"),array("limit"=>1),array(Main::clean($_POST["id"],3,TRUE),$this->userid));        	  
	     	$options=json_decode($poll->options,TRUE);
	     	$count=array();
	     	$total="0";
	     	foreach ($votes as $vote) {
	     		if(!is_numeric($vote->vote)){
	     			$array=explode(",", $vote->vote);
	     			$total=$total+count($array);
	     			foreach ($array as $new) {
	     				if(!isset($count[$new])) {
	     					$count[$new]["answer"]=$options[$new]["answer"];
	     					$count[$new]["count"]=$options[$new]["count"];
	     				}else{
	     					$count[$new]["answer"]=$options[$new]["answer"];
	     					$count[$new]["count"]=$count[$new]["count"]+$options[$new]["count"];
	     				}	     				
	     			}	     			
	     		}else{
	     			$html.="<li>".$options[$vote->vote]["answer"]." <i>".round($vote->count*100/$poll->votes,0)."%</i> <span class='label label-primary pull-right'>{$vote->count}</span></li>";	
	     		}	     		
	     	}
	     	if(!empty($count)){
	     		foreach ($count as $v) {
	     			$html.="<li>".$v["answer"]." <i>".round($v["count"]*100/$total,0)."%</i> <span class='label label-primary pull-right'>{$vote->count}</span></li>";	
	     		}
	     	}
	     	$html.="</ol>";     		
     	}
     	if($_POST["request"]=="source"){
     		if(!$this->isPro()) return die("notpro");
     		$sources=$this->db->search(array("table"=>"vote","count"=>"source as url"),"source LIKE ? AND (pollid=? AND polluserid=?)",array("limit"=>15,"group"=>"url"),array("%".Main::clean($_POST["value"],3,TRUE)."%",Main::clean($_POST["id"],3,TRUE),$this->userid));
				
				$html.="<ol>";     		
	     	foreach ($sources as $source) {
	     		$html.="<li>{$source->url}</li>";
	     	}
	     	$html.="</ol>";     				
     	}
     	if($_POST["request"]=="close"){
     		$this->db->update("poll",array("open"=>0),array("id"=>"?","userid"=>"?"),array(Main::clean($_POST["id"],TRUE,3),$this->userid));
     		$html="<a href='".Main::href("user/server")."' data-request='open' data-id='".Main::clean($_POST["id"],TRUE,3)."' data-target='this' class='get_stats btn btn-xs btn-success'>".e("Open")."</a>";
     	}
     	if($_POST["request"]=="open"){
     		$this->db->update("poll",array("open"=>1),array("id"=>"?","userid"=>"?"),array(Main::clean($_POST["id"],TRUE,3),$this->userid));
     		$html="<a href='".Main::href("user/server")."' data-request='close' data-id='".Main::clean($_POST["id"],TRUE,3)."' data-target='this' class='get_stats btn btn-xs btn-success'>".e("Close")."</a>";
     	}     	
     	// Return HTML
     	return die($html);
    }
	/**
  * Export Data
  * @since 1.0
  **/   
  protected function export(){
  	// Check if enabled
  	if(!$this->config["export"]) return $this->_404();
		 // Check if is Pro
		if(!$this->isPro()) return Main::redirect(Main::href("upgrade","",FALSE),array("warning",e("Please upgrade to a premium package to unlock this feature.")));

		$this->db->object=TRUE;
		if($poll=$this->db->get("poll",array("id"=>"?","userid"=>"?"),array("limit"=>1),array($this->id,$this->userid))){
			$votes=$this->db->get("vote",array("pollid"=>"?"),array("order"=>"date"),array($poll->id));			
      // Export Payments
      $option=json_decode($poll->options,TRUE);
			header('Content-Type: text/csv');
      header("Content-Disposition: attachment;filename=ExportData_{$poll->id}.csv");
      echo "Vote Date,Voter Choice,Voter IP,Voter Country,Voter Referrer\n";
      foreach ($votes as $vote) {
      	if(isset($option[$vote->vote])){
      		$choice=$option[$vote->vote]["answer"];
      	}else{
      		$choice="{$vote->vote} (Multiple Choice)";
      	}
        echo "{$vote->date},$choice,{$vote->ip},".Main::ccode($vote->country,TRUE).",".Main::clean($vote->source)."\n";
      }			      
      return;
		}
		return $this->_404();
  }
	/**
	 * Custom Pages
	 * @since 1.0
	 **/
	protected function page(){
		// Filter ID
		// 
		$this->filter($this->id);
		$this->db->object=TRUE;
		if($page=$this->db->get("page",array("slug"=>"?"),array("limit"=>1),array($this->do))){
			Main::set("body_class","alt");
			$this->header();
				include($this->t(__FUNCTION__));
			$this->footer();
			return;
		}

		return $this->_404();
	}

	/**
	 * 404 Page
	 * @since 1.0
	 **/
	protected function _404(){
		// 404 Header
		header('HTTP/1.0 404 Not Found');
		// Set Meta Tags
		Main::set("title",e("Page not found"));
		Main::set("description","The page you are looking for cannot be found anywhere.");
		Main::set("body_class","dark");

		$this->header();
		include($this->t("404"));
		$this->footer();
	}
	/* 
		=== Helper Functions ===
	*/
	/**
	 * Get Template
	 * @since 1.0
	 **/
	protected function t($template){
    if(!file_exists(TEMPLATE."/$template.php")) $template="404";
    return TEMPLATE."/$template.php";
 	}
 	/**
 	 * Generate Unique ID
 	 * @since 1.0
 	 **/
  protected function uniqueid(){
    $l=5; 
    $i=0; 
    while(1) {
      if($i >='100') { $i=0; $l=$l+1; };
      $unique=Main::strrand($l);
      if(!$this->db->get("poll",array("uniqueid"=>$unique))) {
        return $unique;
        break;
      }
      $i++;
    }   
  }    
	/**
	 * Get country from IP now with GeoIP - must be downloaded separately.
	 * @since v1.0
	 */	
	public function country($ip=NULL,$api=''){
		if(is_null($ip)) $ip=Main::ip();
		// Get it from database first
		if(file_exists(ROOT."/includes/library/GeoIP.dat") && file_exists(ROOT."/includes/library/geoip.inc")){
			require_once(ROOT."/includes/library/geoip.inc");
			$gi = geoip_open(ROOT."/includes/library/GeoIP.dat",GEOIP_STANDARD);
			$country=geoip_country_code_by_addr($gi,$ip);
			geoip_close($gi);	
			return $country;
		}
		return "";
	}
	/**
	 * Filter
	 * @since 1.0 
	 **/
	private function filter($filter=null){
		if(is_null($filter)){
			if(!empty($this->do) || !empty($this->id)) die($this->_404());
		}else{
			if(!empty($filter)) die($this->_404());
		}
	}
/**
 * Ads
 * @since 1.0
 **/	
	private function ads($size,$pro=FALSE){
		if(!empty($this->config["ad$size"])) {
			if($this->isPro()) return;
			echo "<div class='ads ad$size'>{$this->config["ad$size"]}</div>";
		}
	}
/**
 * Languages
 * @since 1.0
 **/	
  private function lang($form=TRUE){
		if($form){
			$lang="<option value='en'".(($this->lang=="" || $this->lang=="en")?"selected":"").">English</option>";
		}else{
			$lang="<a href='?lang=en'>English</a>";
		}
    foreach (new RecursiveDirectoryIterator(ROOT."/includes/languages/") as $path){
      if(!$path->isDir() && $path->getFilename()!=="." && $path->getFilename()!==".." && $path->getFilename()!=="sample_lang.php" && $path->getFilename()!=="index.php" && Main::extension($path->getFilename())==".php"){  
          $data=token_get_all(file_get_contents($path));
          $data=$data[1][1];
          if(preg_match("~Language:\s(.*)~", $data,$name)){
            $name="".strip_tags(trim($name[1]))."";
          }        
        $code=str_replace(".php", "" , $path->getFilename());
        if($form){
					$lang.="<option value='".$code."'".($this->lang==$code?"selected":"").">$name</option>";
        }else{
					$lang.="<a href='?lang=$code'>$name</a>";	
        }
      }
    }  
    return $lang;	
  }
}
?>