<?php
/**
 * file class - receive of data in different ways
 * 
 *
 * @author Hendrik Weiler
 */
class file {
   
        /**
    	 * Receive the content of a File
    	 *
    	 * @param string $name
    	 * @return string
    	 */
        function get_content($name) {
        if(empty($name)) { 
              throw new unError(message::$parameter, "get_content");
        }             
            if(true === file_exists($name)) {
            $inhalt = file_get_contents($name);
            }
            return $inhalt;
        }
        /**
         * Get all files of a Directory as value in a array
         *
         * @param string $ordner
         * @return array
         */
       public function readdir($ordner) {
        if(empty($ordner)) { 
              throw new unError(message::$parameter, "readdir");
        }             
            if($ordner == "this")
       		{
       			$ordner = ".";
       		}
            if(!is_dir($ordner))
            {
                return false;
            }
            $dir = opendir($ordner);
            while($file = readdir($dir)) {
            if($file != ".." && $file != "." && $file) {
                $array[] = $file;
            }
        }
        return $array;
    }
    /**
     * Returns a array list of the directory
     * $sort parameter = "files","a-z or 1-9","z-a or 9-1"
     * (Should work)
     * @param string $ordner
     * @param string $sort
     * @return array
     */
           public function readdir_sort($ordner,$sort) {
            if(empty($ordner) || empty($sort)) { 
               throw new unError(message::$parameter, "readdir_sort");
            }  
            if($ordner == "this")
       		{
       			$ordner = ".";
       		}
            $dir = opendir($ordner);
            if(!is_dir($ordner))
            {
                throw new unError(message::$file_readdir_sort, "readdir_sort");
            }
            while($file = readdir($dir)) {
            $zz = 0;
            if($file != ".." && $file != ".") {
            	if($sort == "files")
            	{
                if(true === is_dir($ordner."/".$file))
                {
                	$return["dir"][] = $file;
                }
                if(true === is_file($ordner."/".$file))
                {
                	$return["file"][] = $file;
                }
            	}
            	else
            	{
            		$return[] = $file;
            	}
                $zz+=1;
            }
        if(is_null($return))
        {
            return false;
        }
        if($sort == "a-z" || $sort == "1-9")
        {
        	asort($return);
        }
        if($sort == "z-a" || $sort == "9-1")
        {
        	arsort($return);
        }
        $return["length"] = $zz;
            }
        return $return;
    }
            /**
         * Get all files of a Directory which have a defined ending
         * (Unsure if its work properly)
         * @param string $ordner
         * @return array
         */
       public function readdir_typeof($ordner,$typeof) {
            if(empty($ordner) || empty($typeof)) { 
               throw new unError(message::$parameter, "readdir_typeof");
            }  
       		if($ordner == "this")
       		{
       			$ordner = ".";
       		}
            if(is_dir($ordner)) {
            $dir = opendir($ordner);
            while($file = readdir($dir)) {
           	$ending = explode(".",$file);
			if(array_search("$typeof",$ending) !== false)
			{
                $array[] = $file;
            }
        }
            }
        if(isset($array))
        {
        return $array;
        }
        else
        {
        	return false;
        }
    }
}
    //PHP Worker Environment - a easy to use PHP framework
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