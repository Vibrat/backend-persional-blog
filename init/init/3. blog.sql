SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

USE `rest_api`;

DROP TABLE IF EXISTS `blog`;
CREATE TABLE `blog` (
    `id` INT(11) UNIQUE NOT NULL AUTO_INCREMENT,
    `title` varchar(255) UNIQUE DEFAULT NULL,
    `des` varchar(255),
    `tags` varchar(255) DEFAULT NULL,
    `category` varchar(255) DEFAULT NULL,
    `timestamp` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `seo_title` varchar(255) UNIQUE,
    `seo_des` varchar(255),
    `seo_url` varchar(255) UNIQUE DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY (`title`, `seo_url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;