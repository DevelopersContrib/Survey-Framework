-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 09, 2014 at 05:48 AM
-- Server version: 5.5.36-cll
-- PHP Version: 5.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `msurvey_survey`
--

-- --------------------------------------------------------

--
-- Table structure for table `page`
--

CREATE TABLE IF NOT EXISTS `page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `menu` int(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `page`
--

INSERT INTO `page` (`id`, `title`, `slug`, `content`, `date`, `menu`) VALUES
(1, 'About', 'about', '<p>Msurvey.com Survey Platform is part of the Global Ventures Network.</p>\r\n\r\n<p>Founded in 1996, Global Ventures is the worlds largest virtual Domain Development Incubator on the planet.</p>\r\n\r\n<p>We create and match great domain platforms like the survey platform with talented people, applications and resources to build successful, value driven, web-based businesses quickly. Join the fastest growing Virtual Business Network and earn Equity and Cowork with other great people making a difference by joining us here at Msurvey.com.</p>\r\n', '2014-06-05 02:09:53', 1);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE IF NOT EXISTS `payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` varchar(255) NOT NULL,
  `userid` bigint(20) NOT NULL,
  `method` varchar(255) NOT NULL DEFAULT 'paypal',
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(255) NOT NULL,
  `expires` datetime NOT NULL,
  `date` datetime NOT NULL,
  `info` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `poll`
--

CREATE TABLE IF NOT EXISTS `poll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` mediumint(9) NOT NULL DEFAULT '0',
  `open` int(1) NOT NULL DEFAULT '1',
  `question` text NOT NULL,
  `options` text NOT NULL,
  `results` int(1) NOT NULL DEFAULT '1',
  `choice` int(1) NOT NULL DEFAULT '0',
  `share` int(1) NOT NULL DEFAULT '1',
  `pass` varchar(255) NOT NULL,
  `theme` varchar(255) NOT NULL,
  `custom` text NOT NULL,
  `count` enum('day','month','off') DEFAULT 'off',
  `created` datetime NOT NULL,
  `expires` varchar(255) NOT NULL,
  `uniqueid` varchar(8) NOT NULL,
  `votes` bigint(20) NOT NULL DEFAULT '0',
  `image_url` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE IF NOT EXISTS `setting` (
  `config` varchar(255) NOT NULL,
  `var` text,
  UNIQUE KEY `config` (`config`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`config`, `var`) VALUES
('url', 'http://msurvey.com'),
('title', 'Msurvey.com'),
('description', 'Join our exclusive community of like minded people on msurvey.com'),
('captcha', '1'),
('captcha_public', '6Lcng_QSAAAAAKLeFe7cfR-9MUhuecw3wdbdAPdf '),
('captcha_private', '6Lcng_QSAAAAAJhAyUqrtLILM5q4RYNeYVC0B9zO '),
('paypal_email', 'chad@ecorp.com'),
('pro_monthly', '1'),
('pro_yearly', '12'),
('currency', 'USD'),
('max_count', '1000'),
('users', '1'),
('user_activation', '1'),
('smtp', '{"host":"","port":"","user":"social@msurvey.com","pass":"mschool3030"}'),
('export', '1'),
('logo', 'http://d2qcctj8epnr7y.cloudfront.net/images/2013/logo-msurvey1.png'),
('email', 'social@msurvey.com'),
('ads', '1'),
('ad728', '<!--/* Revive Adserver Javascript Tag v3.0.0 */-->\r\n\r\n<script type=''text/javascript''><!--//<![CDATA[\r\n   document.MAX_ct0 =''INSERT_ENCODED_CLICKURL_HERE'';\r\n\r\n   var m3_u = (location.protocol==''https:''?''https://ecorp.com/adserver/www/delivery/ajs.php'':''http://ecorp.com/adserver/www/delivery/ajs.php'');\r\n   var m3_r = Math.floor(Math.random()*99999999999);\r\n   if (!document.MAX_used) document.MAX_used = '','';\r\n   document.write ("<scr"+"ipt type=''text/javascript'' src=''"+m3_u);\r\n   document.write ("?zoneid=4");\r\n   document.write (''&amp;cb='' + m3_r);\r\n   if (document.MAX_used != '','') document.write ("&amp;exclude=" + document.MAX_used);\r\n   document.write (document.charset ? ''&amp;charset=''+document.charset : (document.characterSet ? ''&amp;charset=''+document.characterSet : ''''));\r\n   document.write ("&amp;loc=" + escape(window.location));\r\n   if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));\r\n   if (document.context) document.write ("&context=" + escape(document.context));\r\n   if ((typeof(document.MAX_ct0) != ''undefined'') && (document.MAX_ct0.substring(0,4) == ''http'')) {\r\n       document.write ("&amp;ct0=" + escape(document.MAX_ct0));\r\n   }\r\n   if (document.mmm_fo) document.write ("&amp;mmm_fo=1");\r\n   document.write ("''><\\/scr"+"ipt>");\r\n//]]>--></script><noscript><a href=''http://ecorp.com/adserver/www/delivery/ck.php?n=a0257597&amp;cb=INSERT_RANDOM_NUMBER_HERE'' target=''_blank''><img src=''http://ecorp.com/adserver/www/delivery/avw.php?zoneid=4&amp;cb=INSERT_RANDOM_NUMBER_HERE&amp;n=a0257597&amp;ct0=INSERT_ENCODED_CLICKURL_HERE'' border=''0'' alt='''' /></a></noscript>\r\n'),
('ad468', '<!--/* Revive Adserver Javascript Tag v3.0.0 */-->\r\n\r\n<script type=''text/javascript''><!--//<![CDATA[\r\n   document.MAX_ct0 =''INSERT_ENCODED_CLICKURL_HERE'';\r\n\r\n   var m3_u = (location.protocol==''https:''?''https://ecorp.com/adserver/www/delivery/ajs.php'':''http://ecorp.com/adserver/www/delivery/ajs.php'');\r\n   var m3_r = Math.floor(Math.random()*99999999999);\r\n   if (!document.MAX_used) document.MAX_used = '','';\r\n   document.write ("<scr"+"ipt type=''text/javascript'' src=''"+m3_u);\r\n   document.write ("?zoneid=7&target=_blank");\r\n   document.write (''&cb='' + m3_r);\r\n   if (document.MAX_used != '','') document.write ("&exclude=" + document.MAX_used);\r\n   document.write (document.charset ? ''&charset=''+document.charset : (document.characterSet ? ''&charset=''+document.characterSet : ''''));\r\n   document.write ("&loc=" + escape(window.location));\r\n   if (document.referrer) document.write ("&referer=" + escape(document.referrer));\r\n   if (document.context) document.write ("&context=" + escape(document.context));\r\n   if ((typeof(document.MAX_ct0) != ''undefined'') && (document.MAX_ct0.substring(0,4) == ''http'')) {\r\n       document.write ("&ct0=" + escape(document.MAX_ct0));\r\n   }\r\n   if (document.mmm_fo) document.write ("&mmm_fo=1");\r\n   document.write ("''></scr"+"ipt>");\r\n//]]>--></script><noscript><a href=''http://ecorp.com/adserver/www/delivery/ck.php?n=ab27ecff&cb=INSERT_RANDOM_NUMBER_HERE'' target=''_blank''><img src=''http://ecorp.com/adserver/www/delivery/avw.php?zoneid=7&cb=INSERT_RANDOM_NUMBER_HERE&n=ab27ecff&ct0=INSERT_ENCODED_CLICKURL_HERE'' border=''0'' alt='''' /></a></noscript>\r\n'),
('ad300', '<!--/* Revive Adserver Javascript Tag v3.0.0 */-->\r\n\r\n<script type=''text/javascript''><!--//<![CDATA[\r\n   document.MAX_ct0 =''INSERT_ENCODED_CLICKURL_HERE'';\r\n\r\n   var m3_u = (location.protocol==''https:''?''https://ecorp.com/adserver/www/delivery/ajs.php'':''http://ecorp.com/adserver/www/delivery/ajs.php'');\r\n   var m3_r = Math.floor(Math.random()*99999999999);\r\n   if (!document.MAX_used) document.MAX_used = '','';\r\n   document.write ("<scr"+"ipt type=''text/javascript'' src=''"+m3_u);\r\n   document.write ("?zoneid=9");\r\n   document.write (''&amp;cb='' + m3_r);\r\n   if (document.MAX_used != '','') document.write ("&amp;exclude=" + document.MAX_used);\r\n   document.write (document.charset ? ''&amp;charset=''+document.charset : (document.characterSet ? ''&amp;charset=''+document.characterSet : ''''));\r\n   document.write ("&amp;loc=" + escape(window.location));\r\n   if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));\r\n   if (document.context) document.write ("&context=" + escape(document.context));\r\n   if ((typeof(document.MAX_ct0) != ''undefined'') && (document.MAX_ct0.substring(0,4) == ''http'')) {\r\n       document.write ("&amp;ct0=" + escape(document.MAX_ct0));\r\n   }\r\n   if (document.mmm_fo) document.write ("&amp;mmm_fo=1");\r\n   document.write ("''><\\/scr"+"ipt>");\r\n//]]>--></script><noscript><a href=''http://ecorp.com/adserver/www/delivery/ck.php?n=a3e86b74&amp;cb=INSERT_RANDOM_NUMBER_HERE'' target=''_blank''><img src=''http://ecorp.com/adserver/www/delivery/avw.php?zoneid=9&amp;cb=INSERT_RANDOM_NUMBER_HERE&amp;n=a3e86b74&amp;ct0=INSERT_ENCODED_CLICKURL_HERE'' border=''0'' alt='''' /></a></noscript>\r\n'),
('theme', 'default'),
('style', ''),
('lang', ''),
('fonts', '[""]'),
('googleanalytics', '<script>\r\n  (function(i,s,o,g,r,a,m){i[''GoogleAnalyticsObject'']=r;i[r]=i[r]||function(){\r\n  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),\r\n  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)\r\n  })(window,document,''script'',''//www.google-analytics.com/analytics.js'',''ga'');\r\n\r\n  ga(''create'', ''UA-47677208-2'', ''msurvey.com'');\r\n  ga(''send'', ''pageview'');\r\n\r\n</script>');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `banned` int(1) NOT NULL DEFAULT '0',
  `membership` enum('free','pro') NOT NULL DEFAULT 'free',
  `date` datetime NOT NULL,
  `last_payment` datetime NOT NULL,
  `expires` datetime NOT NULL,
  `admin` int(1) NOT NULL DEFAULT '0',
  `ga` varchar(50) NOT NULL,
  `auth_key` varchar(255) NOT NULL,
  `unique_key` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `banned`, `membership`, `date`, `last_payment`, `expires`, `admin`, `ga`, `auth_key`, `unique_key`) VALUES
(1, '', 'admin@contrib.com', '$2a$08$M9WZDbsMaJUrcu4ih3P4VeWE3xSidoolIJ0wTSXr6qnO8cK6P.OEe', 0, 'pro', '2014-06-02 02:24:26', '0000-00-00 00:00:00', '2114-06-01 19:24:26', 1, '', '$2a$08$TQ0sdmYABO3ug.5qF6QipOgw2gUAOsOhCLmhNZTZIabUc/gYp0b8G', 'AFTUey9iWNSBXgI2kx7v'),
(2, '', 'maidabarrientos@gmail.com', '$2a$08$Eu41/XlLR8.BjnNaySpyBOsVeMClkENjGJhNzOskIoZ.ui8RVoaEe', 0, 'free', '2014-06-02 02:51:21', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '$2a$08$CCWrkDgVifDs0NNgLNEMOeTgEvOomk.SBkFxw0XtzA.83A86Cw2fG', 'Mko8dnIvqCiAMa5fR5AP'),
(3, '', 'ronan.asia@yahoo.com', '$2a$08$mDcvaMBGsfq7fgeGBfSPa.CTGPRjQAscAvJ3VECQY0223kF9tKvn6', 0, 'free', '2014-06-03 06:35:44', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '$2a$08$tDlLwMVqSAJ.6qRJaFrT5.BCAs5hwHb91VKjW2pKbK7WFt7U32R9a', 'H0BQH2L5fQDvYLHXJMMQ');

-- --------------------------------------------------------

--
-- Table structure for table `vote`
--

CREATE TABLE IF NOT EXISTS `vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pollid` mediumint(9) NOT NULL,
  `polluserid` mediumint(9) NOT NULL DEFAULT '0',
  `vote` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `source` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
