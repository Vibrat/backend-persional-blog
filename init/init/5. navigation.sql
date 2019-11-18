SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

use `rest_api`;

DROP TABLE IF EXISTS `navigation_all`;
CREATE TABLE `navigation_all` (
    `id` INT(11) UNIQUE NOT NULL AUTO_INCREMENT,
    `menu` VARCHAR(255) UNIQUE NOT NULL,
    `link` VARCHAR(255) DEFAULT '',
    `children` LONGTEXT,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `navigation`;
CREATE TABLE `navigation` (
    `id` INT(11) UNIQUE NOT NULL AUTO_INCREMENT,
    `groupId` INT(11)   NOT NULL,
    `enable` BOOLEAN DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY (`groupId`),
    CONSTRAINT `navigation__users_group` FOREIGN KEY (`groupId`) REFERENCES `users_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `navigation__navigation_all` FOREIGN KEY (`id`) REFERENCES `navigation_all` (`id`) ON DELETE CASCADE ON UPDATE CASCADE 
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `navigation_all` (`menu`, `link`, `children`) VALUES 
('account', '/admin/dashboard/account', ''),
('group', '/admin/dashboard/group', '');