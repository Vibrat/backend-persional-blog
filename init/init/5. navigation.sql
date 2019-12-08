SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

use `rest_api`;

DROP TABLE IF EXISTS `navigation_all`;
CREATE TABLE `navigation_all` (
    `id` INT(11) UNIQUE NOT NULL AUTO_INCREMENT,
    `menu` VARCHAR(255) UNIQUE NOT NULL,
    `icon` VARCHAR(255) DEFAULT '',
    `link` VARCHAR(255) DEFAULT '',
    `children` LONGTEXT,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `navigation`;
CREATE TABLE `navigation` (
    `id` INT(11) NOT NULL,
    `groupId` INT(11)   NOT NULL,
    `enable` BOOLEAN DEFAULT 0,
    `order` INT(11) DEFAULT 0,
    KEY (`groupId`, `id`),
    CONSTRAINT `navigation__users_group` FOREIGN KEY (`groupId`) REFERENCES `users_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `navigation__navigation_all` FOREIGN KEY (`id`) REFERENCES `navigation_all` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `navigation_all` (`menu`, `icon`, `link`, `children`) VALUES
('account', 'user', '/admin/dashboard/account', ''),
('group', 'team', '/admin/dashboard/group', ''),
('navigation', 'layout', '/admin/navigation', ''),
('menu',  'menu', '/admin/menu', ''),
('blog', 'read', '/admin/blog', ''),
('file', 'file', '/admin/file', '');

INSERT INTO `navigation` (`id`, `groupId`, `enable`, `order`) VALUES 
('1', '1', '1', '1'),
('2', '1', '1', '1'),
('3', '1', '1', '1'),
('4', '1', '1', '1'),
('5', '1', '1', '1'),
('6', '1', '1', '1');
