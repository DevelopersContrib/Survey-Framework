<?php defined("APP") or die() ?>
<style>
    #container2 p{text-align:justify !important}
</style>
<style>
    .padd-banner{
        padding: 10px 20px;
    }
    .banner-main{
        margin-bottom: 30px;
        padding-bottom:10px;
        border-bottom: 1px solid #fff;
    }
    .banner-main textarea{
        resize:none;
        border-radius: 4px;
    }
    .banner-main:last-child{
        border-bottom: none;
    }
    .banner-header{
        font-weight: 300;
        color: #fff;
        border-bottom: 1px solid #fff;
        line-height: 65px;
        margin:0 0 30px;
    }
    .banner-img-cont{
        margin-bottom: 25px;
    }
    .banner-source{
        color: #fff;
        font-size:18px;
    }
    .banner-info{
        color: #fff;
    }
    /*
    ==============================================
    tossing
    ==============================================
    */

    .tossing{
        animation-name: tossing;
        -webkit-animation-name: tossing;

        animation-duration: 2.5s;
        -webkit-animation-duration: 2.5s;

        animation-iteration-count: infinite;
        -webkit-animation-iteration-count: infinite;
    }

    @keyframes tossing {
        0% {
            transform: rotate(-4deg);
        }
        50% {
            transform: rotate(4deg);
        }
        100% {
            transform: rotate(-4deg);
        }
    }

    @-webkit-keyframes tossing {
        0% {
            -webkit-transform: rotate(-4deg);
        }
        50% {
            -webkit-transform: rotate(4deg);
        }
        100% {
            -webkit-transform: rotate(-4deg);
        }
    }
    /*
    ==============================================
    floating
    ==============================================
    */

    .floating{
        animation-name: floating;
        -webkit-animation-name: floating;

        animation-duration: 1.5s;
        -webkit-animation-duration: 1.5s;

        animation-iteration-count: infinite;
        -webkit-animation-iteration-count: infinite;
    }

    @keyframes floating {
        0% {
            transform: translateY(0%);
        }
        50% {
            transform: translateY(8%);
        }
        100% {
            transform: translateY(0%);
        }
    }

    @-webkit-keyframes floating {
        0% {
            -webkit-transform: translateY(0%);
        }
        50% {
            -webkit-transform: translateY(8%);
        }
        100% {
            -webkit-transform: translateY(0%);
        }
    }
    .text-center{
        text-align:center;
    }
    #container2 p{
        text-align: center !important;
    }

    /* Add New 2 banner */
    .wrap-allbanner{
        background: url(http://d2qcctj8epnr7y.cloudfront.net/images/2013/banner-contrib-728x90-1.png)no-repeat scroll;
        height: 90px;
        width: 728px;
        position: relative;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }
    .bannerType1{
        text-decoration: none;
    }
    .bannerType1, .bannerType1:focus,.bannerType1:hover{
        outline: none;
    }
    .wrap-bannerLeft, .wrap-bannerRight{
        display: inline-block;
        float: left;
    }
    /* Left COntainer */
    .wrap-bannerLeft{
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        height: 90px;
        vertical-align: top;
        padding: 15px 5px 20px 10px;
        width: 245px;
        overflow: hidden;

    }
    /*Link Domain*/
    .ellipsis {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .aBnnrP{
        display: block;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        font-weight: bold;
        font-size: 22px;
        line-height: normal;
        margin: 0;
        color: #0088CC;
        text-align: center;
        text-transform: capitalize;
        text-decoration: none;
    }

    /* Right Container */
    .wrap-bannerRight{
        color: #FFFFFF;
        height: 90px;
        margin-left: 84px;
        width: 397px;
    }
    .content-rightText{
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        width: 350px;
        padding-top: 16px;
        margin: auto;
    }
    .content-rightText span{
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        display: block;
    }
    .content-rightText span, .content-rightText p{
        font-size: 25px;
        text-align: center;
        text-shadow: 2px 1px 1px rgba(0, 0, 0, 0.5);
    }
    .content-rightText p{
        padding: 12px 0 8px;
        margin: 0;
    }
    /*Image*/
    .logo-banners1{
        max-width: 100%;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        max-height: 58px;
    }

    /*Second Bannder*/
    .wrapBanner-2{
        background: url(http://d2qcctj8epnr7y.cloudfront.net/images/jayson/180x150-1.png) no-repeat scroll;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        margin: auto;
        position: relative;
        height: 150px;
        width: 180px;
        overflow: hidden;
    }
    .bannerType2{
        color: #fff;
        text-decoration: none;
    }
    .bannerType2,.bannerType2:hover,.bannerType2:focus{
        outline: none;
    }

    /*Top banner*/
    .wrap-topBanner{
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        position: relative;
        display: block;
        width: 118px;
        margin: 37px auto 0;
    }
    .wrap-contentTop{
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        font-size: 20px;
        letter-spacing: 0.01em;
        line-height: 1.1em;
        text-align: center;
        text-shadow: 2px 1px 1px rgba(0, 0, 0, 0.5);
        text-align: center;
    }
    .wrap-contentTop p{
        margin: 0;
    }
    .wrap-contentTop span{
        display: block;
    }

    /*Down banner*/
    .wrap-downBanner{
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        display: block;
        height: 37px;
        margin: 5px 0 0;
        overflow: hidden;
    }
    .wrap-contentDown{
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        width: 125px;
        height: 35px;
        margin: auto;
        padding: 1px 0;
    }
    .wrap-contentDown img{
        max-width: 100%;
        max-height: 32px;
        text-align:center;
    }
    .wrap-contentDown p{
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        display: block;
        margin: 0;
        color: #0088CC;
    }
</style>
<?php
	global $domain, $domainid;
?>
<?php
	$url = "http://api2.contrib.com/request/getdomainaffiliateid?domain=$domain&key=".md5($domain);
	
	$curl = curl_init();
	curl_setopt ($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec ($curl);
	curl_close ($curl);

	$result = json_decode($result);
	$domain_affiliate_id = $result->data->affiliate_id;

?>
<section>
	<div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <br><br>
                <div class="padd-banner" style="border-radius: 10px;background:rgba(0,0,0,0.8);">
                   <script class="ctb-box" id='referral-script' src='https://www.referrals.com/extension/widget.js?key=356' type='text/javascript'></script>
                    <div class="banner-main">
                        <h3 class="banner-header">Get <?=$domain?> Banners and Make Money</h3>
                       
                    </div>
                    <div class="banner-main">
                        <dl class="dl-horizontal banner-info">
                            <dt>Marketing Group</dt><dd>Contrib</dd>
                            <dt>Banner Size</dt><dd>728 x 90</dd>
                            <dt>Banner Description</dt><dd><?echo ucfirst($domain)?> Banner</dd>
                            <dt>Target URL</dt><dd>http://<?echo $domain?></dd>
                        </dl>

                        <div class="floating text-center banner-img-cont">
                            <div class="wrap-allbanner">
                                <div class="wrap-bannerLeft">
                                    <p href="" class="aBnnrP ellipsis" style="<!--display:none;-->">
                                        <!--wellnesschallenge.com-->
                                        <img class="logo-banners1" src="<?echo $logo?>" alt="<?echo $domain?>" title="<?php echo $domain; ?>">
                                    </p>
                                </div>
                                <div class="wrap-bannerRight ">
                                    <div class="content-rightText ">
                                        <span class="">Follow , Join and</span>
                                        <p class="ellipsis">Partner with Contrib.com</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="text-center banner-source">Source Code - Copy/Paste Into Your Website</p>
                        <textarea class="text-left form-control" rows="3" onclick="this.focus();this.select()" readonly="readonly">
                            <script type="text/javascript" src="http://www.contrib.com/widgets/leadbanner/<?echo $domain?>/<?echo $domainid?>"></script>
                        </textarea>
                    </div>
                    <div class="banner-main">
                        <dl class="dl-horizontal banner-info">
                            <dt>Marketing Group</dt><dd>Contrib</dd>
                            <dt>Banner Size</dt><dd>180 x 150</dd>
                            <dt>Banner Description</dt><dd><?echo ucfirst($domain)?> Banner</dd>
                            <dt>Target URL</dt><dd>http://<?echo $domain?></dd>
                        </dl>

                        <div class="floating text-center banner-img-cont">
                            <div class="wrapBanner-2">
                                <div class="wrap-topBanner ">
                                    <div class="wrap-contentTop">
                                        <span>Follow, Join</span>
                                        <span>and Partner with</span>
                                    </div>
                                </div>
                                <div class="wrap-downBanner">
                                    <div class="wrap-contentDown">
                                        <p href="" class="ellipsis">
                                            <img src="<?echo $logo?>" alt="<?echo $domain?>" title="<?php echo $domain;  ?>">
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="text-center banner-source">Source Code - Copy/Paste Into Your Website</p>
                        <textarea class="text-left form-control" rows="3" onclick="this.focus();this.select()" readonly="readonly">
                            <script type="text/javascript" src="http://www.contrib.com/widgets/roundleadbanner/<?echo $domain?>/<?echo $domainid?>"></script>
                        </textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>