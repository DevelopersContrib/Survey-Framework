<?php

 global $title, $logo, $desc, $bg_type, $bg_color, $bg_image, $image_style, $about_desc, $domain,$domain_affiliate_link, $partners,$cpanel_username,$cpanel_password;
 function createApiCall($url, $method, $headers, $data = array(),$user=null,$pass=null)
{
        if (($method == 'PUT') || ($method=='DELETE'))
        {
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

        switch($method)
        {
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

 
$headers = array('Accept: application/json');
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
    	$logo = $data_domain['data']['Logo'];
    	$desc = $data_domain['data']['Description'];
    	$account_ga = $data_domain['data']['AccountGA'];
    	$desc = stripslashes(str_replace('\n','<br>',$description));
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
	
	/*
	echo '<pre>';
	print_r($data_cpanel);
	echo '</pre>';
	die();
	//----

if(stristr($sitename, '~') ===FALSE) {
	$sitename = $_SERVER["HTTP_HOST"];								
	$sitename = str_replace("http://","",$sitename);
	$sitename = str_replace("www.","",$sitename);	
}else {
   $key = md5('vnoc.com');
   $d = explode('~',$sitename);
   $user = str_replace('/','',$d[1]);
   $url = $api_url.'getdomainbyusername?username='.$user.'&key='.$key;
   $result =  createApiCall($url, 'GET', $headers, array());
   $data_domain = json_decode($result,true);
   $error = 0;
   $sitename =   $data_domain[0]['domain'];
}



//input sitename without www | http:
$string_tmp = explode(".",$sitename ); 					
$subname = $string_tmp[0];											//gets subdomain name
$subdomain = str_replace($subname.".","",$sitename);				
$domainTMP = substr($subdomain, 0, strpos($subdomain, "."));		//removing .com |.net |.org etc	
				 
if($domainTMP!=""){
	$flag_sub = 1;	//if SUBDOMAIN
	$site_host = $subdomain;
}else{
	$flag_sub = 0;	//if DOMAIN
	$site_host = $sitename;
}

$key = md5($site_host);



$url = $api_url.'getdomaininfo?domain='.$sitename.'&key='.$key; //gets domain|subdomain data
$result =  createApiCall($url, 'GET', $headers, array());

$data_domain = json_decode($result,true);


$error = 0;
if (!isset($data_domain['error']))
{
	$domainid = $data_domain[0]['DomainId'];
	$domainname = $data_domain[0]['DomainName'];
	$title = $data_domain[0]['Title'];
	$logo = $data_domain[0]['Logo'];
	$desc = $data_domain[0]['Description'];
	$account_ga = $data_domain[0]['AccountGA'];
	
}else {
	$error++;
}


$url = $api_url.'getcpanelinfo?domain_id='.$domainid.'&key='.$key;
$result =  createApiCall($url, 'GET', $headers, array());
$data_cpanel = json_decode($result,true);

if (!isset($data_cpanel['error']))
{
	$cpanel_username = $data_cpanel[0]['Username'];
	$cpanel_password = $data_cpanel[0]['Password'];
}else{
	$error++;
}
*/
$sitename =  $domain;
$filename = 'import_sql.php';

if ($site_host!='contrib.com' && !file_exists($filename)) { //already installed
	$host="localhost"; 
	if($flag_sub==1)
		$db=$cpanel_username."_survey_".$subname; //SUBDOMAIN
	else
		$db=$cpanel_username."_survey"; 			//DOMAIN

	$conn = new PDO("mysql:host=$host;dbname=$db", $cpanel_username, $cpanel_password);
 
	$sql = " Update `setting` set `var`=? where `config`=?";
	$q = $conn->prepare($sql);
	$q->execute(array($title,'title')); 

	$sql = " Update `setting` set `var`=? where `config`=?";
	$q = $conn->prepare($sql);
	$q->execute(array($desc,'description')); 

	$sql = " Update `setting` set `var`=? where `config`=?";
	$q = $conn->prepare($sql);
	$q->execute(array($logo,'logo')); 
}

//partners 
	$url = $api_url.'getpartners?domain='.$domain.'&key='.$key;
	$result = createApiCall($url, 'GET', $headers, array());
	$partners_result = json_decode($result,true);
	$partners = array();  
	if ($partners_result['success']){
		$partners = $partners_result;
	}	

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
	 echo "<center><h3>Problem with Api</h3></center>";
	 exit;
}





if ($site_host!='contrib.com' && file_exists($filename)) {

    include('import_sql.php'); //creates DB
	
	unlink($filename); //deletes file
}

$domain = $sitename;


?>