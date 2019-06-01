<?php
/**
 * ACTION BASE FOR LIBRARY
 * 
 * Please make sure this has a unique name across overall application
 */

$tokenIntervalHour = 2; 

define("AUTHENTICATOR_COUNT_USERS",     'SELECT * FROM `%susers` WHERE username = "%s"');
define("AUTHENTICATOR_COUNT_TOKENS",    'SELECT COUNT(*) AS total FROM `%susers_token` WHERE id = "%d"');
define("AUTHENTICATOR_UPDATE_TOKENS",   'UPDATE `%susers_token` SET token = "%s" WHERE id = "%d"');
define("AUTHENTICATOR_INSERT_TOKENS",   'INSERT INTO `%susers_token` SET token = "%s", id = "%d"');
define("AUTHENTICATOR_CHECK_TOKEN",     'SELECT COUNT(*) as total FROM `%susers_token` WHERE token = "%s" AND timestamp >= DATE_SUB(NOW(), INTERVAL "' . $tokenIntervalHour . '" HOUR)');
define("AUTHENTICATOR_CHECK_PERMISSION",'SELECT * FROM `%susers_permission` p LEFT JOIN `%susers_group` g ON p.group_permission_id = g.id WHERE p.user_id = "%d"');
define("AUTHENTICATOR_GET_USER_ID_BY_TOKEN", 'SELECT id FROM `%susers_token` WHERE token = "%s"');