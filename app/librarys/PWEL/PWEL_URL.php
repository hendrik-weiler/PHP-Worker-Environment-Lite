<?php
/**
 * PHP Worker Environment Lite - URL Class
 * 
 * Url informations
 *
 * @author Hendrik Weiler
 * @package PWEL
 */
class PWEL_URL {
    
    /**
     * Returns an array of all variables given in the url
     * @return array
     */
    public function locateUrlVariables() {
        $realUriPath = $_SERVER['REQUEST_URI'];
        $realPhpPath = $_SERVER['PHP_SELF'];
        
        $realPhpPathSearch = str_replace("index.php", "", $realPhpPath);
        
        $realUriVar = str_replace($realPhpPathSearch, "", $realUriPath);
        
        $realUriVarArray = explode("/", $realUriVar);
        for($i=0;$i<count($realUriVarArray);++$i) {
            if(empty($realUriVarArray[$i])) 
                unset($realUriVarArray[$i]);
        }
        return $realUriVarArray;
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
