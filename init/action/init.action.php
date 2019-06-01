<?php

define('CREATE_DB', 'CREATE DATABASE IF NOT EXISTS `rest_api` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci');
define('CREATE_CONFIG_MODULE', 'CREATE TABLE IF NOT EXISTS `modules_config` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `is_init` tinyint(11) NOT NULL DEFAULT 0,
    `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
define('INIT_DATABASE', 'INSERT INTO `%smodules_config` SET name = "%s", is_init = "%d"');
define('SQL_MODULE_COUNT', 'SELECT COUNT(*) as total FROM `%smodules_config` WHERE name = "%s"');