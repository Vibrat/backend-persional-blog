<?php

/**
 * Database Initialization
 * This will generate any basic config for usage
 * 
 * @var Application $this
 */ 

$db = $this->engine->db;

$sql_count  = 'SELECT COUNT(*) as total FROM `%smodules_config` WHERE name = "%s"';
$sql_insert = 'INSERT INTO `%smodules_config` SET name = "%s", is_init = "%d"';

## Create root password
$sql_create_root    = 'INSERT INTO `%susers` SET username = "%s", password = "%s", id = 1'; 
$sql_root_init      = 'SELECT COUNT(*) as total FROM `%susers` WHERE username = "root"';

if (!$db->query(sprintf($sql_root_init, DB_PREFIX))->row('total')) {
    $db->query(sprintf($sql_create_root, DB_PREFIX, 'root', ROOT_PASSWORD));
}

foreach (glob(DB_DIR . '*.sql') as $sql_file) {
 
    $row = $db->query(sprintf($sql_count, DB_PREFIX, basename($sql_file)))->row();
   
    if(!$row['total']) {
        ## Run sql files
        $sql_init = file_get_contents($sql_file);

        $db->query(basename($sql_init));
        $query = $db->query(sprintf($sql_insert, DB_PREFIX, basename($sql_file), 1));
    };  
}


