-- Adminer 4.7.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `modules_config`;
CREATE TABLE `modules_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_init` tinyint(11) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `pizza`;
CREATE TABLE `pizza` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1,	'root',	'$2y$10$B5xtA0G2h/t62.U1rwzM1eHLaq5VHiLx0L5ymJGmzpocBYT8W3982'),
(2,	'lamnguyen22',	'$2y$10$SB3CYfxuhG8k/TSxXO5wOuCp3DxyMMisKLHNrlEn0YgPBPG3yd6ii'),
(3,	'lamnguyen223',	'$2y$10$Cv4945cVcXYpFFfhbJ/.4eJ2ZdS9Q45sqnfcABZx1QIIv6mo4r.Na'),
(4,	'lam.nguyen',	'$2y$10$LsWlRQ4IyaXhKAuCl/.7aOEbEh398rexGrJpPDy32a.vB3ckb40t6');

DROP TABLE IF EXISTS `users_group`;
CREATE TABLE `users_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permission` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `users_group` (`id`, `name`, `permission`) VALUES
(1,	'',	''),
(2,	'VIP2',	'{\"api\": [\"post/account/basic-auth/new-group\"]}');

DROP TABLE IF EXISTS `users_permission`;
CREATE TABLE `users_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `group_permission_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `group_permission_id` (`group_permission_id`),
  CONSTRAINT `users_permission_ibfk_8` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `users_permission_ibfk_9` FOREIGN KEY (`group_permission_id`) REFERENCES `users_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `users_permission` (`id`, `user_id`, `group_permission_id`) VALUES
(1,	1,	1);

DROP TABLE IF EXISTS `users_token`;
CREATE TABLE `users_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `id` (`id`),
  CONSTRAINT `users_token_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `users_token` (`id`, `token`, `timestamp`) VALUES
(1,	'685fbfd7df3b9569b483db5f65c566b03e0880b136953dc4f6',	'2019-01-18 09:50:02'),
(2,	'8cfb7f2b990d3e75bbeef218f24f12801ad86c887c7e1f271e',	'2019-01-18 09:50:40'),
(3,	'f7ca3f38182c8aa4095c9bb4098c9f8c26848d641765279fd7',	'2019-01-18 09:51:26'),
(4,	'f8d01e5245ba27de1fd4413ec4aabce1d5be120b4d7e5e6a24',	'2019-01-18 09:56:08');

-- 2019-01-18 09:58:27