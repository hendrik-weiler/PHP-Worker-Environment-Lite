<?php
/**
 * PHP Worker Environment Lite - Analyzer Class
 *
 * Contains lots of data for debugging
 *
 * @author Hendrik Weiler
 * @package PWEL
 */
class PWEL_ANALYZER {
    /**
     * Contains infos
     *
     * @var string $currentController
     * @var array $currentDisplayedFile
     * @var array $currentVariables
     * @var array $currentComponents
     * @var array $currentRegisteredObjects
     * @var array $currentConfig
     */
    static $currentController;
    static $currentDisplayedFile;
    static $currentVariables;
    static $currentComponents;
    static $currentRegisteredObjects;
    static $currentConfig;

    /**
     * Get all informations and save them in class
     */
    public function getinfo() {
        if(empty(self::$currentVariables)) {
            $url = new PWEL_URL();
            self::$currentVariables = $url->locateUrlVariables();
        }
        self::$currentController = PWEL_ROUTING::$ControllerInfo;
        self::$currentDisplayedFile = PWEL_CONTROLLER::$displayedFile;
        self::$currentComponents = PWEL_COMPONENTS::$components;
        self::$currentRegisteredObjects = PWEL::$registeredObjects;
        self::$currentConfig = PWEL::$config;
    }

    /**
     * Output all infos about pwel
     */
    static function viewInfo() {       
        self::getinfo();

        print "\r\n".'<!-- Begin PWEL Analyzer -->'."\r\n";
        print '<div id="pwel_analyzer" style="background:white; z-index:100; border:1px solid black; width:400px; position:absolute; top:5px; left:5px;">';
        print '<span style="color:black; font-size:25px;"><a style="border-bottom:1px solid black; border-right:1px solid black; background:#CCC; text-decoration:none;" href="#" onclick="javascript:document.getElementById(\'pwel_analyzer\').style.display=\'none\';return false;">Close</a><span style="padding-left:70px; text-decoration:underline;">PWEL Analyzer</span></span><br />';
        print '<strong>Controller Info: </strong><br />';
        if(!empty(self::$currentController)) {
            print "Name => ".self::$currentController["name"];
            print "<br />";
            print "Path => ".self::$currentController["path"];
            if(self::$currentController["name"] == PWEL_ROUTING::$start_controller)
                print "<br /><i>[Start controller]</i>";
        }
        else {
            print "Undefined";
        }
        print "<br />";
        
        print '<strong>Display Info: </strong><br />';
        if(!empty(self::$currentDisplayedFile)) {
            foreach (self::$currentDisplayedFile as $key => $v) {
                print "Path => ".self::$currentDisplayedFile[$key]["path"];
                print "<br />";
            }
        }
        else {
            print "Undefined";
        }
        print "<br />";

        print '<strong>Variable Infos: </strong><br />';
        if(!empty(self::$currentVariables))
            foreach(self::$currentVariables as $key => $var) {
                if(empty($var))
                    $var = "<i>Empty</i>";
                print $key." => ".$var."<br />";
            }
        else
            print "Startcontroller: <code>".PWEL_ROUTING::$start_controller.">>index()</code>";
        print "<br />";
        
        print '<strong>Component Infos: </strong><br />';
        if(!empty(self::$currentComponents))
            foreach(self::$currentComponents as $route) {
                var_dump($route);
            }
        else
            print "No components initiated";
        print "<br />";
        
        print '<strong>Registered Objects: </strong><br />';
        if(!empty(self::$currentRegisteredObjects))
            foreach(self::$currentRegisteredObjects as $object) {
                var_dump($object);
            }
        else
            print "No registered objects in memory";

        print '</div>'."\r\n";
        print '<!-- End PWEL Analyzer ->';

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
