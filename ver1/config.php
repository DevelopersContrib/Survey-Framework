<?php
 global $title, $logo, $desc, $bg_type, $bg_color, $bg_image, 
	$image_style, $about_desc, $domain,$domain_affiliate_link, $partners,$cpanel_username,$cpanel_password;

$headers = array('Accept: application/json');
function createApiCall($url, $method, $headers, $data = array(),$user=null,$pass=null){
	if (($method == 'PUT') || ($method=='DELETE')){
		$headers[] = 'X-HTTP-Method-Override: '.$method;
	}

	$handle = curl_init();
	curl_setopt($handle, CURLOPT_URL, $url);
	curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
	if ($user){
		curl_setopt($handle, CURLOPT_USERPWD, $user.':'.$pass);
	} 

	switch($method){
		case 'GET':
		break;
		case 'POST':
		curl_setopt($handle, CURLOPT_POST, true);
		curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($data));
		break;
		case 'PUT':
		curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($data));
		break;
		case 'DELETE':
		curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
		break;
	}
	$response = curl_exec($handle);
	return $response;
}

$filename = 'import_sql.php';
if ($site_host!='contrib.com' && file_exists($filename) && file_exists('config-framework.php')) { //for rebuild

	unlink('config-framework.php'); //deletes file
	unlink('admin/config-framework.php');
}

if ($_SERVER["HTTP_HOST"]!='contrib.com' && !file_exists('config-framework.php')) {
	$api_url = "http://api2.contrib.com/request/";
	$domain = $_SERVER["HTTP_HOST"]."".$_SERVER['REQUEST_URI'];//input sitename without www

	$key = md5('vnoc.com');

    $api_url = "http://api2.contrib.com/request/";
    $headers = array('Accept: application/json');
    $domain = $_SERVER["HTTP_HOST"]."".$_SERVER['REQUEST_URI'];//input sitename without www
    $error = 0;
    
    if(stristr($domain,'~') ===FALSE) {
    	$domain = $_SERVER["HTTP_HOST"];
      $domain = str_replace("http://","",$domain);
    	$domain = str_replace("www.","",$domain);
    	$key = md5($domain);
    }else {
       $key = md5('vnoc.com');
       $d = explode('~',$domain);
       $user = str_replace('/','',$d[1]);
       
       $url = $api_url.'getdomainbyusername?username='.$user.'&key='.$key;
       $result =  createApiCall($url, 'GET', $headers, array());
       $data_domain = json_decode($result,true);
       $domain =   $data_domain['data']['domain'];
    }
	
    $url = $api_url.'getdomaininfo?domain='.$domain.'&key='.$key;
	
    $result = createApiCall($url, 'GET', $headers, array());
    $data_domain = json_decode($result,true);
    
    if ($data_domain['success']){
    	$domainid = $data_domain['data']['DomainId'];
    	$domainname = $data_domain['data']['DomainName'];
    	$memberid = $data_domain['data']['MemberId'];
    	$title = $data_domain['data']['Title'];
		if(empty($title)) $title = ucwords($domainname);
    	$logo = $data_domain['data']['Logo'];
    	$desc = $data_domain['data']['Description'];
    	$account_ga = $data_domain['data']['AccountGA'];
    	$desc = stripslashes(str_replace('\n','<br>',$desc));
	}else{
		$error++;
	}

	$url = $api_url.'getcpanelinfo?domain_id='.$domainid.'&key='.$key;
	$result =  createApiCall($url, 'GET', $headers, array());
	$data_cpanel = json_decode($result,true);
	
	if($data_cpanel['success']){
		$DomainId = $data_cpanel['data']['DomainId'];
		$cpanel_username = $data_cpanel['data']['Username'];
		$cpanel_password = $data_cpanel['data']['Password'];
		$ServerId = $data_cpanel['data']['ServerId'];
	}else {
    	$error++;
    }
	
	$sitename =  $domain;
	//get monetize ads from vnoc
    $url = $api_url.'getbannercode?d='.$domain.'&p=footer';
    $result = createApiCall($url, 'GET', $headers, array());
    $data_ads = json_decode($result,true);
    $footer_banner = html_entity_decode(base64_decode($data_ads['data']['content']));
    
    //get domain affiliate id
    $url = $api_url.'getdomainaffiliateid?domain='.$domain.'&key='.$key;
    $result = createApiCall($url, 'GET', $headers, array());
    $data_domain_affiliate = json_decode($result,true);
    if ($data_domain_affiliate['success']){
    	$domain_affiliate_id = $data_domain_affiliate['data']['affiliate_id'];
    }else {
    	$domain_affiliate_id = '391'; //contrib.com affiliate id
    }
    $domain_affiliate_link = 'http://referrals.contrib.com/idevaffiliate.php?id='.$domain_affiliate_id.'&url=http://www.contrib.com/signup/firststep?domain='.$domain;

	if ($error > 0){
		echo "<center><h3>Please wait while page is loading...</h3></center>";
		if(!file_exists($filename)){
			echo "<center><h3>Please wait while page is loading...</h3>
				<br><small>Server Time:".date('m/d/Y h:i:s a', time())."</small>
			</center>";
			$page = $_SERVER['PHP_SELF'];
			$sec = "5";
			header("Refresh: $sec; url=$page");
		}
		exit;
	}
	if($flag_sub==1)
		$db=$cpanel_username."_survey_".$subname; //SUBDOMAIN
	else
		$db=$cpanel_username."_survey";  			//DOMAIN
		
	$file='<?php '.
		'$domain="'.$domain.'"; '.
		'$db="'.$db.'"; '.
		'$sitename="'.$sitename.'"; '.
		'$cpanel_username="'.$cpanel_username.'"; '.
		'$cpanel_password="'.$cpanel_password.'"; '.
		'$title="'.$title.'"; '.
		'$account_ga="'.$account_ga.'"; '.
		'$desc="'.$desc.'"; '.
		'$logo="'.$logo.'"; '.
		'$memberid="'.$memberid.'"; '.
		'$domainname="'.$domainname.'"; '.
		'$domainid="'.$domainid.'"; '.
		'$domain_affiliate_link="'.$domain_affiliate_link.'"; '.
		'$footer_banner="'.$footer_banner.'"; '.
		'?>';
	file_put_contents('config-framework.php', $file);
	
	if(!file_exists($filename)){//update only
		$conn = new PDO("mysql:host=$host;dbname=$db", $cpanel_username, $cpanel_password);
	 
		$sql = " Update `setting` set `var`=? where `config`=?";
		$q = $conn->prepare($sql);
		$q->execute(array('http://'.$sitename,'url')); 

		$sql = " Update `setting` set `var`=? where `config`=?";
		$q = $conn->prepare($sql);
		$q->execute(array($title,'title')); 

		$sql = " Update `setting` set `var`=? where `config`=?";
		$q = $conn->prepare($sql);
		$q->execute(array($desc,'description')); 

		$sql = " Update `setting` set `var`=? where `config`=?";
		$q = $conn->prepare($sql);
		$q->execute(array($logo,'logo')); 
		
		if(!empty($account_ga)){
			$sql = " Update `setting` set `var`=? where `config`=?";
			$q = $conn->prepare($sql);			
			$q->execute(array($account_ga,'googleanalytics')); 
		}
	}
}
include 'config-framework.php';

//partners 
$url = $api_url.'getpartners?domain='.$domain.'&key='.$key;
$result = createApiCall($url, 'GET', $headers, array());
$partners_result = json_decode($result,true);
$partners = array();  
if ($partners_result['success']){
	$partners = $partners_result;
}

if ($site_host!='contrib.com' && file_exists($filename)) {
    include('import_sql.php'); //creates DB
	unlink($filename); //deletes file
}


?>