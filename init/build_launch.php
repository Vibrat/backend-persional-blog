<?php
include 'action/init.action.php';

# start our engine
$this->set('engine', new Engine());
$engine = $this->get('engine');

#head SQL
$engine->set(
    'head',
    new MySqliDatabase(
        [
            'username' => USER_NAME,
            'password' => PASSWORD,
            'url' => DATABASE,
            'port' => DB_PORT
        ]
    )
);

#region init database
$num_trials = 0;
while (true) {
    sleep(DB_INTERVAL_CHECK);
    
    $num_trials++;
    
    echo 'Step ' . $num_trials . ' ----------------------' . PHP_EOL;
    echo 'Start connection to database' . PHP_EOL;
    try {

        $query = $engine->head->query(CREATE_DB);

        if (!$query->query) {
            throw new \Exception('Connection not found, restarting' . PHP_EOL);
        }

        break;
    } catch (\Exception $e) {

        echo $e->getMessage();
        if ($num_trials == DB_NUM_TRIALS) {
            echo $e->getMessage();
            exit('Exit: Fail when connects to database' . PHP_EOL);
        }

        continue;
    }
}
#endregion

## init MySqli
$engine->set(
    'db',
    new MySqliDatabase(
        [
            'username' => USER_NAME,
            'password' => PASSWORD,
            'db' => DB_NAME,
            'url' => DATABASE,
            'port' => DB_PORT
        ]
    )
);

$engine->db->query(CREATE_CONFIG_MODULE);

#region 
foreach (glob(DIR_PATH . 'init/*.sql') as $sql_file) {

    $row = $engine->db->query(sprintf(SQL_MODULE_COUNT, DB_PREFIX, basename($sql_file)))->row();
    if (!$row['total']) {

        foreach ($engine->db->queryFile($sql_file) as $command) {
            $engine->db->query($command);
        }

        $query = $engine->db->query(sprintf(INIT_DATABASE, DB_PREFIX, basename($sql_file), 1));
    };
}
#endregion
