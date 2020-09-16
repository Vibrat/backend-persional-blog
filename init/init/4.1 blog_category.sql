SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

USE `rest_api`;
DROP TABLE IF EXISTS  `blog_category`;
CREATE TABLE `blog_category` (
  `id`  INT(11) UNIQUE AUTO_INCREMENT,
  `name` VARCHAR(255) UNIQUE,
  `children` TEXT(65535) DEFAULT NULL,
  `order` INT(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
