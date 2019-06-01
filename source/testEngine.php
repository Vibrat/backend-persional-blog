<?php

/**
 * Test Engine for config application
 */
namespace TestEngine;

use PHPUnit\Framework\TestCase;

trait TestEngine {
    
    protected $app;
    protected $engine;
    protected $dependencies;

    function __construct() {
        TestCase::__construct();
        
        ## inject global $app 
        $this->app = $GLOBALS['app'];
        $this->engine = $this->app->engine;
        $this->dependencies = $this->app->engine->getDependencies();
    }
}