<?php
    /**
     * PHP Worker Environment Lite Components - Route
     * 
     * Advanced component for routing
     * Making custom routes possible
     *
     * @author Hendrik Weiler
     * @package PWEL_COMPONENT
     */
    class PWEL_COMPONENT_ROUTE {
        /**
         * Action variables which use the routing class
         * @var string $_componentTarget
         * @var string $_executionPosition
         * @var bool $_standAlone
         */
        public $_componentTarget = "route";
        public $_executionPosition = "start";
        public $_standAlone = true;
        
        /**
         * Creates a array of default settings
         * @var array
         */
        private $setRoutes;
        
        /**
         * Contain all variables
         * @var array
         */
        static $variables;
        
        
        /**
         * Sets the default settings
         * String Syntax: variable:value/variable:value
         * @var string $newRoutes
         */
        public function __construct($newRoutes) {
            if(preg_match("#((.*):(.*)(/)?)+#i",$newRoutes)) {
                $splitDown_1 = explode("/",$newRoutes);
                foreach($splitDown_1 as $route) {
                    $splitDown_2 = explode(":",$route);
                    $this->setRoutes[$splitDown_2[0]] = $splitDown_2[1];
                }
                
            }
            else {
                //Error Output: Invalid syntax!
            }
        }
        
        /**
         * Prepare the variables for the final step
         */
        private function prepareVars() {
            $url = new PWEL_URL();
            $this->url_variables = $url->locateUrlVariables();
            $i = 0;
            foreach($this->setRoutes as $key => $value) {
                if(empty($this->url_variables[$i])) {
                    $this->url_variables[$key] = $value;
                    unset($this->url_variables[$i]);
                }
                else {
                    $this->url_variables[$key] = $this->url_variables[$i];   
                    unset($this->url_variables[$i]);                
                }
                ++$i;
            } 
            return $this->url_variables;
        }
        
        public function _initFunctions() {
            self::$variables = $this->prepareVars();
        }
        
        public function _execute() {
            if(empty(self::$variables) || empty(self::$variables['class'])) {
                $check = $this->checkIncludeControllerClass(PWEL_ROUTING::$start_controller);
                if($check) {}
                else {
                    $check = $this->checkIncludeControllerClass(PWEL_ROUTING::$error_controller);
                    if(!$check) { return; } 
                }
                $this->displayController(new $check(),"startController"); 
                PWEL_ROUTING::$controllerNotFound = false;
            }
            else {         
                PWEL_ROUTING::$controllerNotFound = false;
                $check = $this->checkIncludeControllerClass($this->url_variables['class']);
                if($check) {}
                else {
                    $check = $this->checkIncludeControllerClass(PWEL_ROUTING::$error_controller);
                    PWEL_ROUTING::$controllerNotFound = true;
                    if(!$check) { return; }                 
                }
                $this->displayController(new $check());
            }
            /**
             * Routing executed
             */
            PWEL_ROUTING::$routed = true;
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
                if(isset($this->url_variables['method']) && method_exists($class, self::$variables['method'])) {
                    $method = $this->url_variables['method'];
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
        if(!empty(PWEL_ROUTING::$namespace)) {
            PWEL_ROUTING::correctNamespace();
        }
        if(PWEL_ROUTING::$autoSearch == true) {
            PWEL_ROUTING::autoSearch("app/controller/",$class.".php");
            PWEL_ROUTING::$searchResult = str_replace("app/controller/","",PWEL_ROUTING::$searchResult);
        }
        if(file_exists(PWEL_ROUTING::$relative_path.'app/controller/'.PWEL_ROUTING::$searchResult.$class.'.php')) {
            require_once PWEL_ROUTING::$relative_path.'app/controller/'.PWEL_ROUTING::$searchResult.$class.'.php';
            return $class;
        }     
        else {
            return false;
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
}