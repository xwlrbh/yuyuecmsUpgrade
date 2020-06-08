ALTER TABLE `yuyuecms_news_comments` ADD `topid` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `parent_id` , ADD INDEX ( `topid` ) ;

ALTER TABLE `yuyuecms_page_comments` ADD `topid` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `parent_id` , ADD INDEX ( `topid` ) ;

ALTER TABLE `yuyuecms_product_comments` ADD `topid` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `parent_id` , ADD INDEX ( `topid` ) ;
