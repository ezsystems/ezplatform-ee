DROP TABLE IF EXISTS `ezflexworkflow`;
CREATE TABLE `ezflexworkflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `message_id` int(11) DEFAULT NULL,
  `content_id` int(11) NOT NULL,
  `content_owner_id` int(11) NOT NULL,
  `previous_owner_id` int(11) NOT NULL,
  `new_owner_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL,
  `version` int(11) NOT NULL,
  `content_type_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `message_id` (`message_id`),
  KEY `content_id` (`content_id`),
  KEY `content_owner_id` (`content_owner_id`),
  KEY `previous_owner_id` (`previous_owner_id`),
  KEY `new_owner_id` (`new_owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ezflexworkflow_message`;
CREATE TABLE `ezflexworkflow_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `message` blob DEFAULT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

DROP TABLE IF EXISTS `ezdatebasedpublisher_scheduled_version`;
CREATE TABLE `ezdatebasedpublisher_scheduled_version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `version_id` int(11) NOT NULL,
  `version_number` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `publication_date` int(11) NOT NULL,
  `url_root` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index4` (`content_id`,`version_number`),
  KEY `index2` (`content_id`),
  KEY `index3` (`version_id`)
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

DROP TABLE IF EXISTS `ezpage_blocks_design`;
CREATE TABLE `ezpage_blocks_design` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `block_id` INT(11) NOT NULL,
  `style` TEXT DEFAULT NULL,
  `compiled` TEXT DEFAULT NULL,
  `class` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ezpage_blocks_visibility`;
CREATE TABLE `ezpage_blocks_visibility` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `block_id` INT(11) NOT NULL,
  `since` DATETIME DEFAULT NULL,
  `till` DATETIME DEFAULT NULL,
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
