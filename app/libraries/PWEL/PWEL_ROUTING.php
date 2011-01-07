<?php
/**
 * PHP Worker Environment Lite - Routing Class
 * 
 * Managing the routing
 *
 * @author Hendrik Weiler
 * @package PWEL
 */
class PWEL_ROUTING extends PWEL_CONTROLLER {
    /**
     * The controller which will be load automaticly if no variables are given
     * @var string
     */
    static $start_controller;
    
    /**
     * The controller which will be load if the controller doenst exist
     * @var string
     */
    static $error_controller;
    
    /**
     * Set a namespace for getting controllers/views/models from right folder
     * Example:
     * PWEL_ROUTING::$namespace = "projectname";
     * It try to get all now all from app/models/projectname,app/views/projectname/app/controller/projectname
     * @var string
     */
    static $namespace;
    
    /**
     * Sets the range of the accessable namespaces by enabled controller search
     * @var array
     */
    static $namespaceRange = array();
 
    /**
     * If set to true the given controller will be searched in '$namespace / subfolders'
     * @var bool $autoSearch
     * @var string $searchResult
     */   
    static $autoSearch = false;
    static $searchResult;
    
    /**
     * Path to current directory
     * @var string
     */
    static $relative_path;
    
    /**
     * All url variables
     * @var string
     */
    private $url_variables;
    
    /**
     * Config informations are stored in it
     * @var array
     */
    static $config = array();
    
    
    /**
     * Path to controller (used in controllersearch method)
     * @var string
     */
    private $pathToController; 
    
    /**
     * Array of all components
     * @var array
     */
    private $components = array();
    
    /**
     * Contains the current state of the controller
     * @var bool
     */
    static $controllerNotFound = false;
    
    /**
     * Handles correct function calls at component execution
     * @var array
     */
    static $componentCalls = array(
        "route" => "routeCurrentDir",
        "display" => "displayController"
    );
    
    /**
     * Sets relative path and start routing
     */
    public function __construct() {
        $this->locateRelativePath();
        $this->getConfig();
        $this->setHeader();
        $this->initComponents(func_get_args());
        if(!$this->components["route"]) {
           $this->routeCurrentDir(); 
        }
    }

    /**
     * Initialize components at startup
     * @var array $arguments
     */
    private function initComponents($arguments) {
        if(!is_array($arguments)) { return false; }
        foreach($arguments as $arg) {
            if(is_object($arg)) {
                $this->components[$arg->_componentTarget][] = $arg;
            }
        }
        foreach(self::$componentCalls as $call => $x) {
            if($this->components[$call])
                $this->execComponents($call);
        }
    }
    
    /**
     * Prepare a component type for execution
     * @var string $componentTarget
     */
    private function prepareComponent($componentTarget) {
        if(empty($this->components)) {
            return false;
        }
        foreach($this->components[$componentTarget] as $component) {
            if(method_exists($component,"_initFunctions")) {
               if(isset($component->_executionPosition)) {
                   $component->_initFunctions();
                   $return[$component->_executionPosition][] = $component;
               }
            }
            else {
               $component->_initFunctions();
               $return['start'][] = $component;
            }
        }
        return $return;
    }

    /**
     * Locate the relative path to the current directory and save it
     */
    private function locateRelativePath() {  
        self::$relative_path = $_SERVER["DOCUMENT_ROOT"].$_SERVER['PHP_SELF'];
        self::$relative_path = str_replace("//", "/", self::$relative_path);
        self::$relative_path = str_replace("index.php", "", self::$relative_path);
        if(is_dir(self::$relative_path."app")) {
            return;
        }
        $path = explode("/",self::$relative_path);
        $count = count($path);
        for($i=$count;$i>=0;--$i) {
            unset($path[$i]);
            self::$relative_path = implode("/",$path)."/";
            if(is_dir(self::$relative_path."/app")) {
                break;
            }
        }
    }
 
    /**
     * Loads the config file
     * 
     */
    private function getConfig() {
        if(file_exists(self::$relative_path."app/config.ini"))
        self::$config = parse_ini_file(self::$relative_path."app/config.ini",true);
    }
 
    /**
     * Execute the components
     * @var string $typeOf
     */
    private function execComponents($typeOf) {
        $components = $this->prepareComponent($typeOf);
        //Execute components at start of function
        if($components['start']) {
            foreach($components['start'] as $component) {
                $component->_execute();
                if($component->_standAlone == false) {
                    $func = self::$componentCalls[$typeOf];
                    $this->$func();
                }
            }
        }
        /////////////////////////////////////////
        //Execute components at end of function
        if($components['end']) {
            foreach($components['end'] as $component) {
                if($component->_standAlone == false) {
                    $func = self::$componentCalls[$typeOf];
                    $this->$func();
                }                
                $component->_execute();
            }
        }
        ///////////////////////////////////////
    }
    
    /**
     * Checks if the controllers are avaible else send to error controller
     * @return null
     */
    private function routeCurrentDir() {
        $url = new PWEL_URL();
        $this->url_variables = $url->locateUrlVariables();
        if(empty($this->url_variables)) {
            $check = $this->checkIncludeControllerClass(self::$start_controller);
            if($check) {}
            else {
                $check = $this->checkIncludeControllerClass(self::$error_controller);
                if(!$check) { return; } 
            }
            $this->displayController(new $check(),"startController"); 
            self::$controllerNotFound = false;
        }
        else {
            self::$controllerNotFound = false;
            $check = $this->checkIncludeControllerClass($this->url_variables[0]);
            if($check) {}
            else {
                $check = $this->checkIncludeControllerClass(self::$error_controller);
                self::$controllerNotFound = true;
                if(!$check) { return; }                 
            }
            $this->displayController(new $check());
        }

    }

    /**
     * Loads the method else send to error controller
     * @param class $class
     * @param string $mode 
     */
    private function displayController($class,$mode="default") {
        if(method_exists($class, "startup")) {
            $class->startup();
        }        
        switch($mode) {
            case "startController":
                if(method_exists($class, "index")) {
                    $class->index();
                }
                else {
                    // Error Output: No index defined!
                }
                break; 
            case "default":
                if(isset($this->url_variables[1]) && method_exists($class, $this->url_variables[1])) {
                    $method = $this->url_variables[1];
                    $class->$method();
                }
                else {
                    if(method_exists($class, "index")) {
                        $class->index();
                    } 
                    else {
                        //Error Output: No index defined!
                    }
                }
                break;
        }
        
    }

    /**
     * Check if class exists include it and return the name or false
     * @param string $class
     * @return string/false 
     */
    private function checkIncludeControllerClass($class) {
        if(!empty(self::$namespace)) {
            self::correctNamespace();
        }
        if(self::$autoSearch == true) {
            self::autoSearch("app/controller/",$class.".php");
            self::$searchResult = str_replace("app/controller/","",self::$searchResult);
        }
        if(file_exists(self::$relative_path.'app/controller/'.self::$searchResult.$class.'.php')) {
            require_once self::$relative_path.'app/controller/'.self::$searchResult.$class.'.php';
            return $class;
        }     
        else {
            return false;
        }
    }
    
    /**
     * This function correct the namespace value 
     * Example: /html/ -> html/, projectname
     */
    static function correctNamespace() {
       $namespace = self::$namespace."/";
       $namespace = str_replace("//","/",$namespace);
       $namespace = str_replace("./","",$namespace);
       $namespace = str_replace(".","",$namespace);
       self::$namespace = $namespace;
    }

    /**
     * Sets a header
     */
    public function setHeader() {
        if(!self::$config["header"]["charset"])
            self::$config["header"]["charset"] = "UTF-8";
        
        if(!self::$config["header"]["contentType"])
            self::$config["header"]["contentType"] = "text/html";
        header('Content-Type: '.self::$config["header"]["contentType"].'; charset='.self::$config["header"]["charset"]);
    }

    /**
     * Browsing subfolders after a specific file
     * @var string $path
     * @var string $search
     * @return bool
     */
    static function autoSearch($path, $search) {
        $dir = self::$relative_path.$path;
        if(!is_dir($dir)) { return false; }
        $directoryContent = scandir($dir);
        if(in_array($search,$directoryContent)) {
            self::$searchResult = $path;
            return true;
        }
        else {
            $hasDirectory = false;
            foreach($directoryContent as $file) {
                if(is_dir($dir.$file) && $file != "." && $file != "..") {
                    if(!in_array($file,self::$namespaceRange)) {
                        $dirs[] = $path.$file;
                        $hasDirectory = true;
                    }
                }
            }
            if($hasDirectory == true && !empty($dirs)) {
                foreach($dirs as $directory) {
                    if(self::autoSearch($directory."/", $search) == true) {
                        return true;
                    }
                }
                return false;
            }
            else {
                return false;
            }
        }
    }
}

    //PHP Worker Environment Lite - a easy to use PHP framework
    //Copyright (C) 2010  Hendrik Weiler
    //
    //This program is free software: you can redistribute it and/or modify
    //it under the terms of the GNU General Public License as published by
    //the Free Software Foundation, either version 3 of the License, or
    //(at your option) any later version.
    //
    //This program is distributed in the hope that it will be useful,
    //but WITHOUT ANY WARRANTY; without even the implied warranty of
    //MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    //GNU General Public License for more details.
    //
    //You should have received a copy of the GNU General Public License
    //along with this program.  If not, see <http://www.gnu.org/licenses/>.
?>
