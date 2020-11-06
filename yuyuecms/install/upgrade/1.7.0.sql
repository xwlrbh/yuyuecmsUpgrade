CREATE TABLE `yuyuecms_news_all` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `yeming` varchar(200) DEFAULT '',
  `guanjianzi` varchar(255) DEFAULT '',
  `description` text DEFAULT '',
  `template` varchar(255) NOT NULL DEFAULT '',
  `tu` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
CREATE TABLE `yuyuecms_product_all` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `yeming` varchar(200) DEFAULT '',
  `guanjianzi` varchar(255) DEFAULT '',
  `description` text DEFAULT '',
  `template` varchar(255) NOT NULL DEFAULT '',
  `tu` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
