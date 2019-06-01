<?php 
use beFunc\beFunc as Befunc;
use phpDocumentor\Reflection\Types\Integer;

class Router {
    private $baseDir;
    private $url;
    private $params = [];
    private $loadFiles = [];
    private $engine = [];
    private $env;
    
    function __construct(
        String $baseController,
        String $caller, 
        String $baseDir,
        int    $env = APPLICATION) 
        {
        
        ## Switching env for app and test
        $this->env = $env;

        ## set baseDir for implement code
        $this->baseDir = $baseDir;

        ## build params & unset empty last element
        $params = preg_split("/[\/\\\]+/", $caller);  

        $func = new BeFunc();

        foreach ($params as $param) {
            $this->params[] = $this->reBuildUrl($param);
        }

        $this->engine = [
            'class'   => (count($this->params) > 1 ? $this->params[count($this->params) - 2 ] : $this->params[0]),
            'action'  => (count($this->params) > 1 ? $this->params[count($this->params) - 1]  : null)
        ];
            
        ## load and save files   
        if (count($params) > 1) {
            $branchFile =  $this->baseDir . $baseController . implode("/", array_slice($this->params, 0, count($this->params) - 1)) . "Controller.php";
        
            if (file_exists($branchFile)) {
                $this->loadFiles[] = $branchFile;
            }
        }  
    }

    public function getParams() {
        return $this->params;
    }

    public function getLoadfiles () {
        return $this->loadFiles;
    }

    public function load($loadFiles, $dependencies = []) {

        foreach($loadFiles as $file) {
         
            ## Require file
            require_once (file_exists($file) ? $file : null);

            ## a text string to get value
            $list     = preg_split("/[\/\\\]/", $file);
            $filename = explode(".", $list[count($list) - 1])[0];

            ## Init class an pass dependencies
            $className = ucwords($filename);
            $initClass = new $className($dependencies);
            
            if (isset($this->engine['action']) 
                && method_exists($initClass, $this->engine['action'])) {
                
                ## call function based on url   
                call_user_func_array([$initClass, $this->engine['action']], []);
            } elseif (method_exists($initClass, 'index')) {
                
                ## if action param not exists then call index
                $initClass->index();
            } elseif (method_exists($initClass, 'error')) {
                ## call errors
                $initClass->error();  
            }
        }

        ## Event Handler when url cannot get method
        if (empty($loadFiles) && !$this->env) {        
            
            $initClass = new DefaultRouter($dependencies);  
            $initClass->index();
        }
    }

    private function reBuildUrl(String $url) {
        $actions = explode("-", $url);
        $action  = '';

        foreach($actions as $key => $item) {
            if (!$key) {
                $action = $item;
                continue;
            } 

            $action .= ucwords($item);
        }

        return $action;
    }
 }