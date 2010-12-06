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
    * @param string $filename
    */
    public function display($filename) {
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
            PWEL_ROUTING::autoSearch("app/views/",$filename);
            PWEL_ROUTING::$namespace = PWEL_ROUTING::$searchResult;
            PWEL_ROUTING::$namespace = str_replace("app/views/","",PWEL_ROUTING::$namespace);
        }  
        //Set & Correct path 
        $path = PWEL_ROUTING::$relative_path."app/views/".PWEL_ROUTING::$namespace."{$filename}{$extension}";
        $path = str_replace("//","/",$path);
        /////////////////////      
        if(file_exists($path)) {
            extract(get_object_vars($this));
            require $path;
        }
        else {
            //Error Output: file doenst exist
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