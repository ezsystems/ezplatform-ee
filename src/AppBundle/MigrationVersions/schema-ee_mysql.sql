DROP TABLE IF EXISTS `eznotification`;
CREATE TABLE `eznotification` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `is_pending` tinyint(1) NOT NULL DEFAULT '1',
  `type` varchar(128) NOT NULL,
  `created` int(11) NOT NULL,
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `owner_id` (`owner_id`),
  KEY `is_pending` (`is_pending`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Page FieldType
--

DROP TABLE IF EXISTS `ezpage_attributes`;
CREATE TABLE `ezpage_attributes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ezpage_blocks`;
CREATE TABLE `ezpage_blocks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL DEFAULT '',
  `view` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ezpage_map_attributes_blocks`;
CREATE TABLE `ezpage_map_attributes_blocks` (
  `attribute_id` int(11) NOT NULL,
  `block_id` int(11) NOT NULL,
  PRIMARY KEY (`attribute_id`,`block_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ezpage_map_blocks_zones`;
CREATE TABLE `ezpage_map_blocks_zones` (
  `block_id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ezpage_map_zones_pages`;
CREATE TABLE `ezpage_map_zones_pages` (
  `zone_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  PRIMARY KEY (`zone_id`,`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ezpage_pages`;
CREATE TABLE `ezpage_pages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `version_no` int(11) unsigned NOT NULL,
  `content_id` int(11) NOT NULL,
  `language_code` varchar(255) NOT NULL DEFAULT '',
  `layout` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ezpage_zones`;
CREATE TABLE `ezpage_zones` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
