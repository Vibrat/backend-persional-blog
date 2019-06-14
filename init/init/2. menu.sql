SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

USE `rest_api`;

DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(255) DEFAULT '',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `children` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_init` tinyint(11) NOT NULL DEFAULT '0',
  `order` tinyint(11) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `menu` (`category`, `name`, `is_init`, `order`) VALUES ('main', 'About', '1', '1');
INSERT INTO `menu` (`category`, `name`, `is_init`, `order`) VALUES ('main', 'Profile', '1', '1');