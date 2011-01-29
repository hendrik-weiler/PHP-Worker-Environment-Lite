<?php
/*
 * Hendrik's Class Collection
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
 * Autoload Class for loading classes automaticly
 *
 * @author Hendrik Weiler
 * @todo Enabling multi-directory including
 * @package Class
 */
class Class_Auto_Load
{
    /**
     * path to class directory
     * @var string
     */
    private $loadPath;
    
    /**
     * Set a new include path and include all files for easy
     * object creating
     * 
     * @param string $loadPath
     * @param array $classes 
     */
    public function __construct($loadPath)
    {
        $this->loadPath = $loadPath;
        $this->include_classes($loadPath);
    }
    
    /**
     * Sets a new include path
     */
    private function set_new_include_path()
    {
        $includePath = implode(PATH_SEPARATOR, array(
            get_include_path(),
            realpath($this->loadPath)
        ));
        set_include_path($includePath);
    }

    /**
     * Include all classes by given directory
     * @param string $dir 
     */
    private function include_classes($dir)
    {
        $this->set_new_include_path();
        $searchDir = opendir($dir);
        while($file = readdir($searchDir)) {
            //Subfolder including if it would work
            //////////////////////////////////////
            //if(is_dir($this->loadPath."/".$file)) {
            //    $this->loadPath = $this->loadPath."/".$file;
            //   $this->include_classes($file);
            //}
            if(preg_match('/(.*).php/i',$file)) {
                require_once $file;
            }
        }
    }
}