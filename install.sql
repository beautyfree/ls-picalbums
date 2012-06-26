CREATE TABLE IF NOT EXISTS `prefix_picalbums_album` (
  `album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(64) NOT NULL,
  `url` varchar(256) NOT NULL,
  `description` varchar(256) NOT NULL,
  `date_add` datetime NOT NULL,
  `date_modify` datetime NOT NULL,
  `cover_picture_id` int(11) unsigned DEFAULT NULL,
  `visibility` int(11) unsigned NOT NULL DEFAULT '0',
  `adduser_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `needmoder` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`album_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_picalbums_blacklist` (
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_picalbums_category` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(64) NOT NULL,
  `position` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_picalbums_heart` (
  `heart_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `target_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`heart_id`),
  UNIQUE KEY `target_id` (`target_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_picalbums_note` (
  `note_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `left` varchar(256) NOT NULL,
  `top` varchar(256) NOT NULL,
  `height` varchar(256) NOT NULL,
  `width` varchar(256) NOT NULL,
  `dateadd` datetime NOT NULL,
  `note` varchar(256) NOT NULL,
  `link` varchar(256) DEFAULT NULL,
  `picture_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `user_mark_id` int(11) DEFAULT NULL,
  `is_confirm` int(1) unsigned NOT NULL,
  PRIMARY KEY (`note_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_picalbums_picture` (
  `picture_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` int(11) unsigned NOT NULL,
  `description` varchar(64) NOT NULL,
  `url` varchar(256) NOT NULL,
  `picpath` varchar(512) NOT NULL,
  `picminiaturepath` varchar(512) NOT NULL,
  `picblockpath` varchar(512) NOT NULL,
  `originalpath` varchar(512) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `exif` text,
  `position` int(11) NOT NULL DEFAULT '0',
  `adduser_id` int(11) DEFAULT NULL,
  `ismoder` tinyint(1) unsigned NOT NULL DEFAULT '1',
PRIMARY KEY (`picture_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_picalbums_related` (
  `related_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `target_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`related_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_picalbums_settings` (
  `user_id` int(11) unsigned NOT NULL,
  `comment_notify` int(1) unsigned NOT NULL,
  `mark_notify` int(1) unsigned NOT NULL DEFAULT '0',
  `is_used_ajax` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_picalbums_tag` (
  `tag_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `target_id` int(11) unsigned NOT NULL,
  `tag_text` varchar(64) NOT NULL,
  PRIMARY KEY (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;