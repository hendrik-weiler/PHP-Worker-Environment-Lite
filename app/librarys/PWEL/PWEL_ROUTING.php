<?php
/**
 * PHP Worker Environment Lite - Routing Class
 * 
 * Managing the routing
 *
 * @author Hendrik Weiler
 * @package PWEL
 */
class PWEL_ROUTING {
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
     * Path to current directory
     * @var string
     */
    private $relative_path;
    
    /**
     * All url variables
     * @var string
     */
    private $url_variables;
    
    /**
     * Sets relative path and start routing
     */
    public function __construct() {
        $this->locateRelativePath();
        $this->routeCurrentDir();
    }

    /**
     * Locate the relative path to the current directory and save it
     */
    private function locateRelativePath() {  
        $this->relative_path = $_SERVER["DOCUMENT_ROOT"].$_SERVER['PHP_SELF'];
        $this->relative_path = str_replace("//", "/", $this->relative_path);
        $this->relative_path = str_replace("index.php", "", $this->relative_path);
        if(is_dir($this->relative_path."app")) {
            return;
        }
        $path = explode("/",$this->relative_path);
        $count = count($path);
        for($i=$count;$i>=0;--$i) {
            unset($path[$i]);
            $this->relative_path = implode("/",$path)."/";
            if(is_dir($this->relative_path."/app")) {
                break;
            }
        }
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
        }
        else {
            $check = $this->checkIncludeControllerClass($this->url_variables[0]);
            if($check) {}
            else {
                $check = $this->checkIncludeControllerClass(self::$error_controller);
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
        if(file_exists($this->relative_path.'app/controller/'.$class.'.php')) {
            require_once $this->relative_path.'app/controller/'.$class.'.php';
            return $class;
        }     
        else {
            return false;
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
