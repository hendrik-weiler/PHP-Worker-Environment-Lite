<?php
/*
 * PHP Worker Environment Lite - a easy to use PHP framework
 * Copyright (C) 2010  Hendrik Weiler
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
/**
 * PHP Worker Environment Lite - Routing Class
 * 
 * Managing the routing
 *
 * @author Hendrik Weiler
 * @package PWEL
 * @category PWEL
 * @version 1.0
 * @since File release since version 1.0
 */
class PWEL_ROUTING extends PWEL_CONTROLLER
{

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
     * @var bool
     */   
    static $autoSearch = false;
    
    /**
     * Result of autosearch
     * @var string
     */
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
     * Informations about current controller (used in controllersearch method)
     * 
     * @var string
     */
    static $ControllerInfo;
    
    /**
     * Contains the current state of the controller
     * @var bool
     */
    static $controllerNotFound = false;
    
    /**
     * Checks if the final route function was executed
     * @var bool 
     */
    static $routed = false;

    /**
     * Tells whether its linux or windows
     * 
     * @var string 
     */
    static $platform = "linux";

    /**
     * Sets relative path and start routing
     */
    public function start()
    {
        $this->locateRelativePath();
        if(empty(PWEL_COMPONENTS::$components['route'])) {
           $this->routeCurrentDir(); 
        }
    }

    public function loadAutoInject()
    {
        include_once self::$relative_path . 'app/AutoInject.php';
        $injection = new AutoInject();
        $methods = get_class_methods('AutoInject');
        if(is_array($methods)) {
            foreach($methods as $function) {
                if(!method_exists('PWEL_CONTROLLER', $function))
                    $injection->$function();
            }
        }
    }



    /**
     * Locate the relative path to the current directory and save it
     */
    static function locateRelativePath()
    {
		 $dir = './';
		 while(!is_dir($dir . '/app'))
		 	$dir .= '../';
		 
		 self::$relative_path = realpath($dir) . '/';
    }

    /**
     * Returns the relative path
     * @return string
     */
    public function requestRelativePath()
    {
        $this->locateRelativePath();
        return self::$relative_path;
    }
    
    /**
     * Checks if the controllers are avaible else send to error controller
     * @return null
     */
    public function routeCurrentDir()
    {
        if(PWEL_ROUTING::$routed == true)
            return true;
        
        $url = new PWEL_URL();
        $this->url_variables = $url->locateUrlVariables();
        if(empty($this->url_variables)) {
            $check = $this->checkIncludeControllerClass(self::$start_controller);
            if($check)
            {

            } else {
                $check = $this->checkIncludeControllerClass(self::$error_controller);
                if(!$check)
                { 
                    return;
                }
            }
            $this->displayController(new $check(), "startController");
            self::$controllerNotFound = false;
        }
        else {
            self::$controllerNotFound = false;
            $check = $this->checkIncludeControllerClass($this->url_variables[0]);
            if($check) 
            {

            } else {
                $check = $this->checkIncludeControllerClass(self::$error_controller);
                self::$controllerNotFound = true;
                if(!$check)
                {
                    return;
                }
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
     * $mode got default and startController as possible values
     * @param class $class
     * @param string $mode 
     */
    public function displayController($class,$mode="default")
    {
        self::$ControllerInfo["name"] = get_class($class);
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
                    throw new Exception("Method: Index must be defined in " . get_class($class));
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
                        throw new Exception("Method: Index must be defined in " . get_class($class));
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
    public function checkIncludeControllerClass($class)
    {
        if(!empty(self::$namespace)) {
            self::correctNamespace();
            $namespace = self::$namespace;
        }
        if(self::$autoSearch == true) {
            self::autoSearch("app/controller/", $class . ".php");
            self::$searchResult = str_replace("app/controller/", "", self::$searchResult);
            $namespace = null;
        }
        $path = self::$relative_path.'app/controller/' . self::$searchResult . $namespace . $class . '.php';

        if(file_exists($path)) {
            require_once $path;
            self::$ControllerInfo["path"] = $path;
            return $class;
        }     
        else {
            throw new Exception("Controller: <strong>$class</strong> not found in <em>" . $path . '</em>');
        }
    }
    
    /**
     * This function correct the namespace value 
     * Example: /html/ -> html/, projectname
     */
    static function correctNamespace()
    {
       $namespace = self::$namespace."/";
       $namespace = str_replace("//", "/", $namespace);
       $namespace = str_replace("./", "", $namespace);
       $namespace = str_replace(".", "", $namespace);
       self::$namespace = $namespace;
    }

    /**
     * Display the error document
     */
    public function displayError()
    {
        $class = PWEL_ROUTING::$error_controller;
        self::autoSearch("app/controller/", $class . ".php");
        require_once str_replace("//", "/", self::$relative_path.self::$searchResult . "/$class.php");
        $this->displayController(new $class());
    }

    /**
     * Sets a header
     */
    public function setHeader()
    {
        if(!PWEL::$config["header"]["charset"])
            PWEL::$config["header"]["charset"] = "UTF-8";
        
        if(!PWEL::$config["header"]["contentType"])
            PWEL::$config["header"]["contentType"] = "text/html";
        header('Content-Type: ' . PWEL::$config["header"]["contentType"] . '; charset=' . PWEL::$config["header"]["charset"]);
    }

    /**
     * Browsing subfolders after a specific file
     * @var string $path
     * @var string $search
     * @return bool
     */
    static function autoSearch($path, $search)
    {
        if(self::$platform == "windows") {
            $seperator = "\\";
            $path = str_replace("/", $seperator, $path);
        } else {
            $seperator = "/";
            $path = str_replace("\\", $seperator, $path);
        }
        
        $dir = self::$relative_path . $path;

        if(!is_dir($dir))
        {
            return false;
        }
        $directoryContent = scandir($dir);
        if(in_array($search, $directoryContent)) {
            self::$searchResult = $path;
            return true;
        }
        else {
            $hasDirectory = false;
            foreach($directoryContent as $file) {
                if(is_dir($dir . $file) && $file != "." && $file != "..") {
                    if(!empty(self::$namespaceRange)) {
                        if(in_array($file, self::$namespaceRange)) {
                            $dirs[] = $path . $file;
                            $hasDirectory = true;
                        }
                    } else {
                           $dirs[] = $path . $file;
                           $hasDirectory = true;
                    }
                }
            }
            if($hasDirectory == true && !empty($dirs)) {
                foreach($dirs as $directory) {
                    if(self::autoSearch($directory . $seperator, $search) == true) {
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