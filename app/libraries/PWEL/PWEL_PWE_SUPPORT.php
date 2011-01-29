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
 * PHP Worker Environment Lite - PWE Support
 *
 * Make it possible to use PWE classes
 *
 * @author Hendrik Weiler
 * @package PWEL
 * @version 1.0
 * @category PWEL
 * @since Release since 1.01
 */
class PWEL_PWE_SUPPORT {
    public $classes;

    function __construct() {
        define(MODE_CONTROLLER, 'oop');
        define(SQL_ID,PWEL_ROUTING::$config['sql']['username']);
        define(SQL_ID,PWEL_ROUTING::$config['sql']['password']);
        define(SQL_ID,PWEL_ROUTING::$config['sql']['dbname']);
    }

    function getClasses() {
           $dir = opendir(PWEL_ROUTING::$relative_path . 'app/libraries/PWE');
           while($file = readdir($dir))
           {
               if($file != '..' && $file != '.' && $file != 'init.php'
                  && $file != 'cases.php' && $file != 'errors.php')
               {
                    $split = explode('.', $file);
                    if($file == 'sql.php')
                        $this->classes->sql = new sql(SQL_ID,SQL_PW,SQL_DB);
                    else
                        $this->classes->$split[0] = new $split[0]();
               }
           }
           return $this->classes;
    }
}