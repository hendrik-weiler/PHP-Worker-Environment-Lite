<?php
/**
 * Create and configure an easy db
 *
 * @author Hendrik Weiler
 * @package eDB
 */
class eDB {
    /**
     * Sets the path of the db
     * @var string
     */
    public static $dbPath;

    /**
     * Sets the name of the db
     * @var string
     */
    public static $dbName;

    /**
     * Sets configuration
     * @param string $path
     */
    public function __construct($path=null) {
            $this->dbPath = str_replace("//", "/", $path);
            $this->dbName = $this->retrieveDbName($path);
            self::$dbPath = $this->dbPath;
            self::$dbName = $this->dbName;
    }

    /**
     * Receive the name of the db
     * @param string $path
     * @return string
     */
    private function retrieveDbName($path) {
        $path = explode("/",$path);
        return $path[count($path)-1];
    }

    /**
     * Creates a new db
     * @return bool
     */
    public function create() {
        if(!is_dir($this->dbPath))
        if(!mkdir($this->dbPath,777, true)) {
            return false;
        }
    }

    /**
     * Deletes the current db
     */
    public function delete() {
        function rrmdir($dir) {
           if (is_dir($dir)) {
             $objects = scandir($dir);
             foreach ($objects as $object) {
               if ($object != "." && $object != "..") {
                 if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
               }
             }
             reset($objects);
             rmdir($dir);
           }
        }
        rrmdir(str_replace($this->dbName,$this->dbPath,$this->dbPath));
    }
        //Hendrik's Class Collection
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
?>
