USE `rest_api`;  

DROP TABLE IF EXISTS `file`;
CREATE TABLE `file` (
    `id` INT(11) UNIQUE AUTO_INCREMENT,
    `filename` varchar(255) UNIQUE NOT NULL,
    `path`  varchar(255) NOT NULL,
    `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;