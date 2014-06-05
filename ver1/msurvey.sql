-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 03, 2014 at 08:43 AM
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `poll`
--

INSERT INTO `poll` (`id`, `userid`, `open`, `question`, `options`, `results`, `choice`, `share`, `pass`, `theme`, `custom`, `count`, `created`, `expires`, `uniqueid`, `votes`) VALUES
(3, 0, 1, 'Do you understand how gamification affects your community?', '{"1":{"answer":"no","count":1},"2":{"answer":"yes","count":0}}', 1, 0, 1, '', 'bs_red', '{"background":"","font":"null"}', 'off', '2014-06-02 02:35:10', '', '8ZySp', 1),
(5, 1, 1, 'If you are going to fund a new startup? What will be the criteria for you to join the startup?', '[{"answer":"revenue model","count":1},{"answer":"creativity","count":0},{"answer":"usefullness","count":0}]', 1, 1, 1, '', 'bs_green', '{"background":"","font":"null"}', 'month', '2014-06-02 03:17:44', '', 'sdWTr', 1),
(6, 0, 1, 'What is the best cms open source application or framework?', '{"1":{"answer":"Wordpress","count":0},"2":{"answer":"Joomla","count":0}}', 1, 1, 0, '', 'bs_blue', '{"background":"","font":"null"}', 'off', '2014-06-03 05:51:50', '', '5gCSs', 0),
(4, 2, 1, 'Can you promote a Facebook page and get 1000 likes in two weeks?', '{"1":{"answer":"Yes","count":1},"2":{"answer":"No","count":0},"3":{"answer":"Maybe","count":0}}', 1, 0, 1, '', 'bs_green', '{"background":"","font":"null"}', 'off', '2014-06-02 02:56:27', '', 'L1leF', 1),
(7, 3, 1, 'NBA Champion', '{"1":{"answer":"Miami Heat","count":0},"2":{"answer":"San Antonio Spurs","count":1},"3":{"answer":"Oklahoma","count":0},"4":{"answer":"Indiana Pacers","count":0}}', 1, 0, 1, '', '', '{"background":"","font":"null"}', 'off', '2014-06-03 06:50:59', '', 'YEh7X', 1);

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
('title', 'Msurvey - Create free surveys today'),
('description', 'Create, Embed and Get Survey Results Today'),
('captcha', '1'),
('captcha_public', '6Lcng_QSAAAAAKLeFe7cfR-9MUhuecw3wdbdAPdf '),
('captcha_private', '6Lcng_QSAAAAAJhAyUqrtLILM5q4RYNeYVC0B9zO '),
('paypal_email', 'bizmaida@gmail.com'),
('pro_monthly', '1'),
('pro_yearly', '12'),
('currency', 'USD'),
('max_count', '1000'),
('users', '1'),
('user_activation', '1'),
('smtp', '{"host":"","port":"","user":"social@msurvey.com","pass":"mschool3030"}'),
('export', '1'),
('logo', 'auto_site_logo.png'),
('email', 'social@msurvey.com'),
('ads', '1'),
('ad728', '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>\r\n<!-- survey framework - 728 x 90 -->\r\n<ins class="adsbygoogle"\r\n     style="display:inline-block;width:728px;height:90px"\r\n     data-ad-client="ca-pub-0390821261465417"\r\n     data-ad-slot="5078150302"></ins>\r\n<script>\r\n(adsbygoogle = window.adsbygoogle || []).push({});\r\n</script>'),
('ad468', '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>\r\n<!-- survey framework - 468 x 60 -->\r\n<ins class="adsbygoogle"\r\n     style="display:inline-block;width:468px;height:60px"\r\n     data-ad-client="ca-pub-0390821261465417"\r\n     data-ad-slot="6554883504"></ins>\r\n<script>\r\n(adsbygoogle = window.adsbygoogle || []).push({});\r\n</script>'),
('ad300', '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>\r\n<!-- survey framework 300 x 250 -->\r\n<ins class="adsbygoogle"\r\n     style="display:inline-block;width:300px;height:250px"\r\n     data-ad-client="ca-pub-0390821261465417"\r\n     data-ad-slot="8031616703"></ins>\r\n<script>\r\n(adsbygoogle = window.adsbygoogle || []).push({});\r\n</script>'),
('theme', 'default'),
('style', 'green'),
('lang', ''),
('fonts', '[""]');

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
(1, '', 'social@msurvey.com', '$2a$08$M9WZDbsMaJUrcu4ih3P4VeWE3xSidoolIJ0wTSXr6qnO8cK6P.OEe', 0, 'pro', '2014-06-02 02:24:26', '0000-00-00 00:00:00', '2114-06-01 19:24:26', 1, '', '$2a$08$TQ0sdmYABO3ug.5qF6QipOgw2gUAOsOhCLmhNZTZIabUc/gYp0b8G', 'AFTUey9iWNSBXgI2kx7v'),
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `vote`
--

INSERT INTO `vote` (`id`, `pollid`, `polluserid`, `vote`, `ip`, `country`, `source`, `date`) VALUES
(4, 5, 1, '1', '120.28.195.174', 'PH', '', '2014-06-03 03:11:32'),
(2, 4, 2, '1', '202.137.120.23', 'PH', '', '2014-06-02 03:12:27'),
(3, 3, 0, '1', '202.137.120.23', 'PH', '', '2014-06-02 03:14:39'),
(5, 7, 3, '2', '120.28.195.174', 'PH', '', '2014-06-03 07:10:38');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;