<?php defined("APP") or die() ?>
<link rel="stylesheet" media="screen" type="text/css" href="/themes/default/serviceforms/css/partner.css">
<style type="text/css">
	.partnerMainCont {
		background: none repeat scroll 0 0 #fff;
		border-radius: 10px;
		color: black;
		float: left;
		font-family: 'Open Sans',sans-serif;
		height: 466px;
		margin-left: 40px;
		padding: 5px 10px;
		text-align: left;
		width: 400px;
	}
	#partner_step1{
		text-align:center;
	}
	.blckBckgrnd{
		background: rgba(0,0,0,0.5);
		padding: 20px 15px;
		margin-bottom: 10px;
		box-shadow: 0 0 3px rgba(255, 255, 255, 0.4)
	}
	.blckBckgrnd:before,.blckBckgrnd:after{
		display: table;
		content: "";
	}
	.blckBckgrnd:after{
		clear: both;
	}
	.padd-banner p{
		color: #fff;
	}
	.padd-banner h3 > a{
		color: #fff;
	}
	.padd-banner h3 > a:hover{
		color: #f0f0f0;
	}
	.animated {
        -webkit-animation-duration: 1s;
        animation-duration: 1s;
        -webkit-animation-fill-mode: both;
        animation-fill-mode: both;
    }
    @-webkit-keyframes rotateIn {
        0% {
            -webkit-transform-origin: center center;
            transform-origin: center center;
            -webkit-transform: rotate(-200deg);
            transform: rotate(-200deg);
            opacity: 0;
        }

        100% {
            -webkit-transform-origin: center center;
            transform-origin: center center;
            -webkit-transform: rotate(0);
            transform: rotate(0);
            opacity: 1;
        }
    }

    @keyframes rotateIn {
        0% {
            -webkit-transform-origin: center center;
            -ms-transform-origin: center center;
            transform-origin: center center;
            -webkit-transform: rotate(-200deg);
            -ms-transform: rotate(-200deg);
            transform: rotate(-200deg);
            opacity: 0;
        }

        100% {
            -webkit-transform-origin: center center;
            -ms-transform-origin: center center;
            transform-origin: center center;
            -webkit-transform: rotate(0);
            -ms-transform: rotate(0);
            transform: rotate(0);
            opacity: 1;
        }
    }
    .rotateIn {
        -webkit-animation-name: rotateIn;
        animation-name: rotateIn;
    }
    .r-d{
        -webkit-animation-delay: 2.5s;
        -moz-animation-delay: 2.5s;
        -ms-animation-delay: 2.5s;
        -o-animation-delay: 2.5s;
        animation-delay: 2.5s;
    }
    .arrw-rela {
        position: relative;
    }
    .arrw-point-white {
        background: url("http://d2qcctj8epnr7y.cloudfront.net/contrib/arrow-1-medium.png") no-repeat scroll 0 0 rgba(0, 0, 0, 0);
        height: 92px;
        left: -130px;
        position: absolute;
        top: -75px;
        width: 100px;
    }
    .badge-postn {
	    left: 90px;
	    position: absolute;
	    top: -21px;
	    z-index: 10;
	}
    /* Landscape phones and down */
    @media (max-width: 480px) {
        .badge-postn{
            position: absolute;
            right: 1px;
            top: 2px;
            width: 40px;
            z-index: 10;
        }
    }
    .logo-img{
    	display: inline-block;
    }
	
	select, textarea, input[type="text"], input[type="password"]{
		background-color: #ffffff;
		border: 1px solid #cccccc;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
		transition: border 0.2s linear 0s, box-shadow 0.2s linear 0s
		border-radius: 4px;
		color: #555555;
		display: inline-block;
		font-size: 14px;
		height: 20px;
		line-height: 20px;
		margin-bottom: 10px;
		padding: 4px 6px;
		vertical-align: middle;
		box-sizing: border-box;
		min-height: 30px;
		width: 100%;
	}
</style>
<?php
global $title, $logo, $desc, $bg_type, $bg_color, $bg_image, $image_style, $about_desc, $domain, $partners;
?>
<section>
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<?$partners_list = $partners['data'];?>
				<div class="wrap-ad">
					<div class="overflow-ad">
						<div style="position:relative;">
							<div class="animated rotateIn r-d badge-postn">
								<a href="<?=$domain_affiliate_link;?>" target="_blank" alt="Contrib">
									<img class="img-responsive" src="http://d2qcctj8epnr7y.cloudfront.net/images/2013/badge-contrib-3.png">
								</a>
							</div>
						</div>
						<div class="content-ad" style="text-align: justify;">
							<div class="text-center">
								<? if($logo!=''){ ?>
								<a href="http://<?=$domain?>"><img class="img-responsive logo-img" src="<?=$logo?>" alt="<?=$title?>" title="<?=$domain?>" style="max-width:500px" border="0" /></a>
								<? }else{ ?>
								<h1><?=ucwords($domain)?></h1>
								<? } ?>
								<h4>Learn more about Joining our Partner Network</h4>
							</div>
							<a name="top"></a>
							<div class="text-center">
								<button type="button" id="show_partner_dialog" class="btn btn-large btn-primary" data-toggle="modal" data-target="#form-container">
									Join Our Partner Network
								</button>
								<br/>
							</div>
							<br/>
							<div class="padd-banner">
								<div class="row-fluid">
									<div class="col-lg-8 col-lg-offset-2">
										<div class="blckBckgrnd">
											<div class="col-lg-4">
												<a href="http://contrib.com">
													<img class="img-responsive" src="http://d2qcctj8epnr7y.cloudfront.net/images/2013/logo-contrib-green13.png" alt="Contrib.com" />
												</a>
											</div><!-- span4 -->
											<div class="col-lg-8">
												<h3><a href="http://contrib.com">Contrib.com</a></h3>
												<p>
													Our network of Contributors power our domains. Browse through our Marketplace of People, Partnerships,Proposals and Brands and find your next great opportunity. Join Free Today.
												</p>
											</div><!-- span8 -->
										</div>

										<div class="blckBckgrnd">
											<div class="col-lg-4">
												<a href="http://globalventures.com">
													<img class="img-responsive" src="http://d2qcctj8epnr7y.cloudfront.net/images/lucille/logo-gv-re283x35.png" alt="GlobalVentures.com" />
												</a>
											</div><!-- span4 -->
											<div class="col-lg-8">
												<h3><a href="http://globalventures.com">GlobalVentures.com</a></h3>
												<p>
													Global Ventures owns a premium network of 20,000 websites and powerful tools to help you build successful companies quickly.
													Some of the things we offer you include a great domain name with targeted traffic, unique business model, equity ownership, and flexible, performance based compensation. You just need to bring your knowledge, passion and work smart.
												</p>
												<p>
													With over 17 years of internet experience, we built a network of over 20,000 websites and created dozens of successful businesses. We would love to work on the next cutting-edge projects with great companies and talented people.
												</p>
											</div><!-- span8 -->
										</div>

										<div class="blckBckgrnd">
											<div class="col-lg-4">
												<a href="http://ifund.com">
													<img class="img-responsive" src="http://www.contrib.com/uploads/logo/ifund.png" alt="iFund.com" />
												</a>
											</div><!-- span4 -->
											<div class="col-lg-8">
												<h3><a href="http://ifund.com">iFund.com</a></h3>
												<p>
													iFund is a software as a service crowdfunding platform. iFund is not a registered broker-dealer and does not offer investment
													advice or advise on the raising of capital through securities offerings. iFund does not recommend or otherwise suggest that any
													investor make an investment in a particular company, or that any company offer securities to a particular investor. iFund takes no part in the negotiation or execution of transactions for the purchase or sale of securities, and at no time has possession of funds or securities. No securities transactions are executed or negotiated on or through the iFund platform.
													iFund receives no compensation in connection with the purchase or sale of securities.
												</p>
											</div><!-- span8 -->
										</div>

										<div class="blckBckgrnd">
											<div class="col-lg-4">
												<a href="http://ichallenge.com">
													<img class="img-responsive" src="http://d2qcctj8epnr7y.cloudfront.net/images/2013/logo-ichallenge1.png" alt="iChallenge.com" />
												</a>
											</div><!-- span4 -->
											<div class="col-lg-8">
												<h3><a href="http://ichallenge.com">iChallenge.com</a></h3>
												<p>
													The best internet challenges. Solve and win online prizes.
												</p>
											</div><!-- span8 -->
										</div>
										<!-- start dynmic partners -->
										<?if(isset($partners_list)){?>
										<?foreach($partners_list AS $partner_detail):?>
										<div class="blckBckgrnd">
											<div class="col-lg-4">
												<a href="<?echo $partner_detail['url'];?>">
													<img class="img-responsive" src="<?echo $partner_detail['image'];?>" alt="<?echo $partner_detail['company_name'];?>">
												</a>
											</div><!-- span4 -->
											<div class="col-lg-8">
												<h3><a href="<?echo $partner_detail['url'];?>"><?echo $partner_detail['company_name'];?></a></h3>
												<p><?echo $partner_detail['summary'];?></p>
												<p><?echo $partner_detail['description'];?></p>
											</div><!-- span8 -->
										</div>
										<?endforeach;?>
										<?}?>
										<!-- dynamic partners -->
									</div>
								</div><!-- -->
							</div><!-- padd-banner -->
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

	
	<style type="text/css">
	.modal-body {
		max-height: 480px !important;
		padding-left:20% !important;
	}
</style>
<?php 
	include('serviceforms/variables.php'); 
?>
<div class="modal hide fade" id="form-container">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="form-header"></h3>
  </div>
  <div class="modal-body">
    <div id="form-container-partner" style="display:none;">
		<?include('serviceforms/partner.php')?>
	</div>
	<div id="form-container-inquire" style="display:none;">
		<?include('serviceforms/contact_us.php')?>
	</div>
	<div id="form-container-staffing" style="display:none;">
		<?include('serviceforms/staffing.php')?>
	</div>
  </div>
  <div class="modal-footer">
    &nbsp;
  </div>
</div>	


	<script type="text/javascript">
	$(function(){
		$('button#show_partner_dialog, a#_partner').click(function(){
			hideOtherForms();
			$('#form-header').text("Submit Partnership Application");
			$('#form-container-partner').css('display','block');
			$(this).modal({content:$('#form-container .modal-body').html(),title:'Partner',confimation:1});
		});
		
		$('a#_contactus').click(function(){
			hideOtherForms();
			$('#form-header').text("Send Inquiry");
			$('#form-container-inquire').css('display','block');
		});
		
		$('a#_apply').click(function(){
			hideOtherForms();
			$('#form-header').text("Submit Team Application");
			$('#form-container-staffing').css('display','block');
		});
			
	});
	
	function hideOtherForms(){
		$('#form-container-partner').css('display','none');
		$('#form-container-inquire').css('display','none');
		$('#form-container-staffing').css('display','none');
	}

</script>	