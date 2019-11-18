SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

use `rest_api`;

DROP TABLE IF EXISTS `navigation`;
CREATE TABLE `navigation` (
    `id` INT(11) UNIQUE NOT NULL AUTO_INCREMENT,
    `groupId` INT(11)   NOT NULL,
    `menu` VARCHAR(255) UNIQUE NOT NULL,
    `children` LONGTEXT,
    `enable` BOOLEAN DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY (`groupId`, `menu`),
    CONSTRAINT `navigation__users_group` FOREIGN KEY (`groupId`) REFERENCES `users_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE  
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;