CREATE TABLE `glpi_plugin_avisos_avisos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment` longtext COLLATE utf8_unicode_ci,
  `color` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Formato hexadecimal',
  `date_mod` datetime DEFAULT NULL,
  `is_recursive` tinyint(1) NOT NULL DEFAULT '0',
  `entities_id` int(11) NOT NULL DEFAULT '0',
  `query` longtext COLLATE utf8_unicode_ci,
  `itemtype` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `make` tinyint(1) NOT NULL DEFAULT '0',
  `show` tinyint(1) NOT NULL DEFAULT '1',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `entities_id` (`entities_id`),
  KEY `is_recursive` (`is_recursive`),
  KEY `is_deleted` (`is_deleted`),
  KEY `date_mod` (`date_mod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `glpi_plugin_avisos_avisos_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_avisos_avisos_id` int(11) NOT NULL DEFAULT '0',
  `groups_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unicity` (`plugin_avisos_avisos_id`,`groups_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `glpi_plugin_avisos_configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cabecera` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'AVISO',
  `color` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '#339966' COMMENT 'Formato hexadecimal',
  `size` int(11) NOT NULL DEFAULT '4',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `glpi_plugin_avisos_configs` (`id`, `cabecera`, `color`, `size`) 
VALUES ('1', 'ATENCIÓN', '#339966', '4');
