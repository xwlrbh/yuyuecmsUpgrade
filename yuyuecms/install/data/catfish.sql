SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE `catfish_login` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(11) unsigned NOT NULL,
  `loginip` varchar(16) DEFAULT '',
  `logintime` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `logintime` (`logintime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_nav_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL DEFAULT '',
  `target` varchar(25) NOT NULL DEFAULT '_blank',
  `href` varchar(255) NOT NULL DEFAULT '',
  `link` varchar(255) NOT NULL DEFAULT '',
  `icon` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `miaoshu` text DEFAULT '',
  `suolvetu` varchar(255) NOT NULL DEFAULT '',
  `listorder` int(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`),
  KEY `href` (`href`),
  KEY `listorder` (`listorder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_navcat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` int(11) DEFAULT '0',
  `nav_name` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `remark` text DEFAULT '',
  `listorder` int(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `active` (`active`),
  KEY `listorder` (`listorder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_news` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT '0',
  `guanjianzi` varchar(255) DEFAULT '',
  `laiyuan` varchar(500) DEFAULT '',
  `fabushijian` datetime DEFAULT '2000-01-01 00:00:00',
  `alias` varchar(150) DEFAULT '',
  `zhengwen` text DEFAULT '',
  `biaoti` text DEFAULT '',
  `fubiaoti` text DEFAULT '',
  `zhaiyao` text DEFAULT '',
  `review` tinyint(1) DEFAULT '1',
  `comment_status` tinyint(1) DEFAULT '1',
  `gengxinshijian` datetime DEFAULT '2000-01-01 00:00:00',
  `commentime` datetime DEFAULT '2000-01-01 00:00:00',
  `parent_id` int(11) unsigned DEFAULT '0',
  `pinglunshu` int(11) DEFAULT '0',
  `suolvetu` varchar(255) NOT NULL DEFAULT '',
  `tu` varchar(255) NOT NULL DEFAULT '',
  `shipin` varchar(255) NOT NULL DEFAULT '',
  `zutu` text NOT NULL DEFAULT '',
  `wenjianzu` text NOT NULL DEFAULT '',
  `template` varchar(255) NOT NULL DEFAULT '',
  `yuedu` int(11) DEFAULT '0',
  `zan` int(11) DEFAULT '0',
  `cai` int(11) DEFAULT '0',
  `istop` tinyint(1) NOT NULL DEFAULT '0',
  `recommended` tinyint(1) NOT NULL DEFAULT '0',
  `symbol` smallint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `fabushijian` (`fabushijian`),
  KEY `alias` (`alias`),
  KEY `review` (`review`),
  KEY `gengxinshijian` (`gengxinshijian`),
  KEY `commentime` (`commentime`),
  KEY `pinglunshu` (`pinglunshu`),
  KEY `yuedu` (`yuedu`),
  KEY `zan` (`zan`),
  KEY `istop` (`istop`),
  KEY `recommended` (`recommended`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_news_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stid` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `to_uid` int(11) NOT NULL DEFAULT '0',
  `createtime` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `content` text DEFAULT '',
  `comment_type` smallint(1) NOT NULL DEFAULT '1',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `stid` (`stid`),
  KEY `createtime` (`createtime`),
  KEY `parent_id` (`parent_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_news_cate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catename` varchar(200) DEFAULT NULL,
  `alias` varchar(150) DEFAULT '',
  `guanjianzi` varchar(255) DEFAULT '',
  `description` text DEFAULT '',
  `template` varchar(255) NOT NULL DEFAULT '',
  `tu` varchar(255) NOT NULL DEFAULT '',
  `parent_id` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_news_cate_relationships` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stid` int(11) unsigned NOT NULL DEFAULT '0',
  `cateid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cateid` (`cateid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_news_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `conid` int(11) unsigned DEFAULT '0',
  `biaoti` varchar(64) NOT NULL,
  `quantity` int(10) DEFAULT '0',
  `method` varchar(50) NOT NULL DEFAULT '',
  `cateid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `conid` (`conid`),
  KEY `method` (`method`),
  KEY `cateid` (`cateid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_page` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT '0',
  `guanjianzi` varchar(255) DEFAULT '',
  `fabushijian` datetime DEFAULT '2000-01-01 00:00:00',
  `alias` varchar(150) DEFAULT '',
  `zhengwen` text DEFAULT '',
  `biaoti` text DEFAULT '',
  `fubiaoti` text DEFAULT '',
  `zhaiyao` text DEFAULT '',
  `review` tinyint(1) DEFAULT '1',
  `comment_status` tinyint(1) DEFAULT '1',
  `gengxinshijian` datetime DEFAULT '2000-01-01 00:00:00',
  `commentime` datetime DEFAULT '2000-01-01 00:00:00',
  `parent_id` int(11) unsigned DEFAULT '0',
  `pinglunshu` int(11) DEFAULT '0',
  `suolvetu` varchar(255) NOT NULL DEFAULT '',
  `tu` varchar(255) NOT NULL DEFAULT '',
  `shipin` varchar(255) NOT NULL DEFAULT '',
  `zutu` text NOT NULL DEFAULT '',
  `wenjianzu` text NOT NULL DEFAULT '',
  `template` varchar(255) NOT NULL DEFAULT '',
  `yuedu` int(11) DEFAULT '0',
  `zan` int(11) DEFAULT '0',
  `cai` int(11) DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `fabushijian` (`fabushijian`),
  KEY `alias` (`alias`),
  KEY `review` (`review`),
  KEY `gengxinshijian` (`gengxinshijian`),
  KEY `commentime` (`commentime`),
  KEY `pinglunshu` (`pinglunshu`),
  KEY `yuedu` (`yuedu`),
  KEY `zan` (`zan`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_page_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stid` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `to_uid` int(11) NOT NULL DEFAULT '0',
  `createtime` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `content` text DEFAULT '',
  `comment_type` smallint(1) NOT NULL DEFAULT '1',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `stid` (`stid`),
  KEY `createtime` (`createtime`),
  KEY `parent_id` (`parent_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT '0',
  `guanjianzi` varchar(255) DEFAULT '',
  `laiyuan` varchar(500) DEFAULT '',
  `fabushijian` datetime DEFAULT '2000-01-01 00:00:00',
  `alias` varchar(150) DEFAULT '',
  `zhengwen` text DEFAULT '',
  `biaoti` text DEFAULT '',
  `fubiaoti` text DEFAULT '',
  `zhaiyao` text DEFAULT '',
  `yuanjia` decimal(10,2) DEFAULT '0',
  `xianjia` decimal(10,2) DEFAULT '0',
  `review` tinyint(1) DEFAULT '1',
  `comment_status` tinyint(1) DEFAULT '1',
  `gengxinshijian` datetime DEFAULT '2000-01-01 00:00:00',
  `commentime` datetime DEFAULT '2000-01-01 00:00:00',
  `parent_id` int(11) unsigned DEFAULT '0',
  `pinglunshu` int(11) DEFAULT '0',
  `suolvetu` varchar(255) NOT NULL DEFAULT '',
  `tu` varchar(255) NOT NULL DEFAULT '',
  `shipin` varchar(255) NOT NULL DEFAULT '',
  `zutu` text NOT NULL DEFAULT '',
  `wenjianzu` text NOT NULL DEFAULT '',
  `template` varchar(255) NOT NULL DEFAULT '',
  `yuedu` int(11) DEFAULT '0',
  `zan` int(11) DEFAULT '0',
  `cai` int(11) DEFAULT '0',
  `istop` tinyint(1) NOT NULL DEFAULT '0',
  `recommended` tinyint(1) NOT NULL DEFAULT '0',
  `symbol` smallint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `pid` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `fabushijian` (`fabushijian`),
  KEY `alias` (`alias`),
  KEY `xianjia` (`xianjia`),
  KEY `review` (`review`),
  KEY `gengxinshijian` (`gengxinshijian`),
  KEY `commentime` (`commentime`),
  KEY `pinglunshu` (`pinglunshu`),
  KEY `yuedu` (`yuedu`),
  KEY `zan` (`zan`),
  KEY `istop` (`istop`),
  KEY `recommended` (`recommended`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_product_properties` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stid` int(11) unsigned DEFAULT '0',
  `propname` varchar(200) DEFAULT NULL,
  `propvalue` text DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `stid` (`stid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_product_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stid` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `to_uid` int(11) NOT NULL DEFAULT '0',
  `createtime` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `content` text DEFAULT '',
  `comment_type` smallint(1) NOT NULL DEFAULT '1',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `stid` (`stid`),
  KEY `createtime` (`createtime`),
  KEY `parent_id` (`parent_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_product_cate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catename` varchar(200) DEFAULT NULL,
  `alias` varchar(150) DEFAULT '',
  `guanjianzi` varchar(255) DEFAULT '',
  `description` text DEFAULT '',
  `template` varchar(255) NOT NULL DEFAULT '',
  `tu` varchar(255) NOT NULL DEFAULT '',
  `parent_id` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_product_cate_relationships` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stid` int(11) unsigned NOT NULL DEFAULT '0',
  `cateid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cateid` (`cateid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_product_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `conid` int(11) unsigned DEFAULT '0',
  `biaoti` varchar(64) NOT NULL,
  `quantity` int(10) DEFAULT '0',
  `method` varchar(50) NOT NULL DEFAULT '',
  `cateid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `conid` (`conid`),
  KEY `method` (`method`),
  KEY `cateid` (`cateid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `yonghu` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(64) NOT NULL DEFAULT '',
  `nicheng` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `touxiang` varchar(255) DEFAULT '',
  `xingbie` smallint(1) DEFAULT '0',
  `shengri` date DEFAULT NULL,
  `qianming` text DEFAULT '',
  `createtime` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `lip` varchar(255) DEFAULT NULL,
  `ismulti` tinyint(1) NOT NULL DEFAULT '0',
  `activation` varchar(60) NOT NULL DEFAULT '',
  `randomcode` varchar(60) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '1',
  `utype` varchar(50) NOT NULL DEFAULT '',
  `jifen` int(11) NOT NULL DEFAULT '0',
  `jinbi` int(11) NOT NULL DEFAULT '0',
  `shouji` varchar(20) NOT NULL DEFAULT '',
  `xuexiao` varchar(200) NOT NULL DEFAULT '',
  `qq` varchar(200) NOT NULL DEFAULT '',
  `weibo` varchar(200) NOT NULL DEFAULT '',
  `wechat` varchar(200) NOT NULL DEFAULT '',
  `facebook` varchar(200) NOT NULL DEFAULT '',
  `twitter` varchar(200) NOT NULL DEFAULT '',
  `skype` varchar(200) NOT NULL DEFAULT '',
  `line` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `yonghu` (`yonghu`),
  KEY `email` (`email`),
  KEY `jifen` (`jifen`),
  KEY `shouji` (`shouji`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `biaoqian` varchar(64) NOT NULL,
  `aims` varchar(30) NOT NULL,
  `outpos` varchar(30) NOT NULL DEFAULT 'all',
  `isthumb` varchar(30) NOT NULL DEFAULT 'all',
  `remarks` text DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `biaoqian` (`biaoqian`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_properties` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `protemp` varchar(200) NOT NULL DEFAULT '',
  `description` text DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `protemp` (`protemp`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_properties_relationships` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `propid` int(11) unsigned NOT NULL DEFAULT '0',
  `propname` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `propid` (`propid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_options` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(64) NOT NULL,
  `option_value` text NOT NULL,
  `autoload` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `option_name` (`option_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_slide` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mingcheng` varchar(255) NOT NULL,
  `tupian` varchar(255) DEFAULT NULL,
  `lianjie` varchar(255) DEFAULT NULL,
  `miaoshu` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `listorder` int(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `listorder` (`listorder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_slide_cate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catename` varchar(200) DEFAULT NULL,
  `width` int(11) DEFAULT '820',
  `height` int(11) DEFAULT '390',
  `description` text DEFAULT '',
  `listorder` int(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `listorder` (`listorder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_slide_cate_relationships` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slideid` int(11) unsigned NOT NULL DEFAULT '0',
  `cateid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `slideid` (`slideid`),
  KEY `cateid` (`cateid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_links` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `dizhi` varchar(255) NOT NULL,
  `mingcheng` varchar(255) NOT NULL,
  `tubiao` varchar(255) DEFAULT NULL,
  `target` varchar(25) NOT NULL DEFAULT '_blank',
  `miaoshu` text DEFAULT '',
  `shouye` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `listorder` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_user_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `title` text DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `description` text DEFAULT '',
  `createtime` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `url` (`url`),
  KEY `createtime` (`createtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_guestbook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(50) DEFAULT '',
  `email` varchar(100) DEFAULT '',
  `shouji` varchar(100) DEFAULT '',
  `qq` varchar(100) DEFAULT '',
  `wechat` varchar(100) DEFAULT '',
  `title` varchar(255) DEFAULT '',
  `msg` text DEFAULT '',
  `createtime` datetime NOT NULL,
  `parent_id` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `createtime` (`createtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `shijian` datetime DEFAULT '2000-01-01 00:00:00',
  `biaoti` text DEFAULT '',
  `xiangqing` text DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `shijian` (`shijian`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_company` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mingcheng` varchar(300) NOT NULL,
  `dizhi` varchar(500) DEFAULT '',
  `youbian` varchar(20) DEFAULT '',
  `chengli` date DEFAULT NULL,
  `fuzeren` varchar(100) DEFAULT '',
  `lianxiren` varchar(100) DEFAULT '',
  `shouji` varchar(100) DEFAULT '',
  `dianhua` varchar(100) DEFAULT '',
  `chuanzhen` varchar(100) DEFAULT '',
  `wangzhi` varchar(300) DEFAULT '',
  `email` varchar(150) DEFAULT '',
  `jianjie` text DEFAULT '',
  `xingxiang` varchar(300) NOT NULL DEFAULT '',
  `shipin` varchar(300) NOT NULL DEFAULT '',
  `zutu` text DEFAULT '',
  `qq` varchar(200) DEFAULT '',
  `gongzhong` varchar(200) DEFAULT '',
  `erweima` varchar(300) DEFAULT '',
  `zhizhao` varchar(200) DEFAULT '',
  `office` smallint(2) DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `office` (`office`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_home` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `biaoti` text DEFAULT '',
  `zhengwen` text DEFAULT '',
  `tu` varchar(255) NOT NULL DEFAULT '',
  `shipin` varchar(255) NOT NULL DEFAULT '',
  `zutu` text DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_label` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `biaoqian` varchar(64) NOT NULL,
  `outpos` varchar(30) NOT NULL DEFAULT 'all',
  `content` text DEFAULT '',
  `remarks` text DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `biaoqian` (`biaoqian`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `catfish_mlabel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `biaoqian` varchar(64) NOT NULL,
  `outpos` varchar(30) NOT NULL DEFAULT '',
  `quantity` int(10) DEFAULT '0',
  `method` varchar(50) NOT NULL DEFAULT '',
  `homeout` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `biaoqian` (`biaoqian`),
  KEY `outpos` (`outpos`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `catfish_config` (`id`, `biaoqian`, `aims`, `outpos`, `isthumb`, `remarks`) VALUES
(1, 'xinwen', 'news', 'all', 'all', ''),
(2, 'chanpin', 'product', 'all', 'all', '');

INSERT INTO `catfish_news_config` (`id`, `conid`, `biaoti`, `quantity`, `method`, `cateid`) VALUES
(1, 1, '', 6, 'latestRelease', 0);

INSERT INTO `catfish_product_config` (`id`, `conid`, `biaoti`, `quantity`, `method`, `cateid`) VALUES
(1, 2, '', 4, 'latestRelease', 0);

INSERT INTO `catfish_slide_cate` (`id`, `catename`, `width`, `height`, `description`, `listorder`) VALUES
(1, '幻灯1组', 1200, 400, '', 0);

INSERT INTO `catfish_slide` (`id`, `mingcheng`, `tupian`, `lianjie`, `miaoshu`, `status`, `listorder`) VALUES
(1, '', 'data/uploads/20190328/2c8eb1f930e46528635bb5bf0b0e0fb5.jpg', '', '', 1, 0),
(2, '', 'data/uploads/20190328/f09b31cb52daefe76ad269b0220519a3.jpg', '', '', 1, 0);

INSERT INTO `catfish_slide_cate_relationships` (`id`, `slideid`, `cateid`) VALUES
(1, 1, 1),
(2, 2, 1);

INSERT INTO `catfish_nav_cate` (`id`, `cid`, `parent_id`, `label`, `target`, `href`, `link`, `icon`, `status`, `miaoshu`, `suolvetu`, `listorder`) VALUES
(1, 1, 0, '首页', '_self', 'index/Index/index', '', '', 1, '', '', 0);
