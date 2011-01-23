<?php
/**
 * PHP Worker Environment Lite - Controller Class
 * 
 * Managing all controller behaviours
 *
 * @author Hendrik Weiler
 * @package PWEL
 */
class PWEL_CONTROLLER {
    /**
     * Contains data about all displayed files
     * @var array
     */
    static $displayedFile;

    /**
     * Loads register/components into class
     */
    public function __construct() {
        if(!class_exists("PWEL"))
            return;
        
        if(isset(PWEL::$registeredObjects)) {
            foreach(PWEL::$registeredObjects as $obj) {
                if(preg_match("/plugin/i",strtolower(get_class($obj)))) {
                    $pluginname = strtolower(str_replace("PWEL_PLUGIN_", "", get_class($obj)));
                    $this->plugin->$pluginname = $obj;
                }
            }
        }
        if(isset(PWEL_COMPONENTS::$components)) {
            foreach(PWEL_COMPONENTS::$components as $route) {
                foreach($route as $obj) {
                    if(preg_match("/component/i",strtolower(get_class($obj)))) {
                        $componentname = strtolower(str_replace("PWEL_COMPONENT_", "", get_class($obj)));
                        $this->component->$componentname = $obj;
                    }
                }
            }
        }
    }

    /**
    * Display a file from view folder
    * Variables will be stored in class itself and will be accessable as normal variables
    * in controller class
    * Example:
    * function index() {
    *   $this->testVariable = "hello!";
    *   $this->display("outputfile");
    * }
    * 
    * Outputfile:
    * <?php print $testVariable.' im a test!';
    *
    * @param mixed $vars
    * @param string $filename
    */
    public function display($filename,$vars=null) {
        if(preg_match_all("/(.*)\.(php|html|phtml)/i",$filename,$result)) {
            if(isset($result[2])) {
                $filename = $result[1][0];
                $extension = ".".$result[2][0];
            }
            else {
                $extension = ".php";
            }        
        }
        else {
            $filename .= ".php";
        }
        PWEL_ROUTING::correctNamespace();
        if(PWEL_ROUTING::$autoSearch == true) {
            if(isset($extension))
                $searchname = $filename.$extension;
            else
                $searchname = $filename;
            
            PWEL_ROUTING::autoSearch("app/views/",$searchname);
            PWEL_ROUTING::$searchResult = str_replace("app/views/","",PWEL_ROUTING::$searchResult);
            $namespace = null;
        }
        else {
            $namespace = PWEL_ROUTING::$namespace;
        }
        //Set & Correct path 
        $path = PWEL_ROUTING::$relative_path."app/views/".PWEL_ROUTING::$searchResult.$namespace."{$filename}{$extension}";
        $path = str_replace("//","/",$path);
        /////////////////////      
        if(file_exists($path)) {
            self::$displayedFile[] = array(
                "path" => $path
            );
            extract(get_object_vars($this));
            if($vars!=null)
                extract($vars);
            
            require $path;
        }
        else {
            //Error Output: file doenst exist
            throw new Exception("File to display couldnt be found in $path.");
        }
    }
    
    /**
     * Returns a validated css link tag
     * @var string
     * @return string
     */
    public function validateCss($file) {
        $path = $this->validateLink($file);
        return '<link rel="stylesheet" href="'.$path.'">'."\n";
    }

    /**
     * Returns a validated script link tag
     * @var string
     * @return string
     */    
    public function validateJS($file) {
        $path = $this->validateLink($file);
        return '<script type="text/javascript" src="'.$path.'"></script>'."\n";
    }
 
    /**
     * Returns a validated link
     * from PWEL_URL
     * 
     * @var string
     * @return string
    */    
    public function validateLink($file) {
        $uri = new PWEL_URL();
        return $uri->validateLink($file);
    }

    /**
     * Returns a registered object
     *
     * * in name is a wildcard to spare the time writing full class names
     * Example:
     * PWEL_PLUGIN_HTML_HELPER => *_HTML_HELPER, *_HTML, *_HELPER
     *
     * @param string $name
     * @return object
     */
    public function getRegister($name) {
        if(preg_match("#\*#i", $name)) {
            foreach (PWEL::$registeredObjects as $class) {
                if(preg_match("#".str_replace("*","",$name)."#i",get_class($class)))
                   $name = get_class($class);
            }   
        }
        return PWEL::$registeredObjects[strtolower($name)];
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