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
 * PHP Worker Environment Lite - SQL selector class
 * 
 * Managing the login into sql database
 * 
 * @author Hendrik Weiler
 * @package PWEL
 * @category PWEL
 * @version 1.0
 * @since File available since release 1.01
 */
class PWEL_SQL
{

    /**
     * Contains the host for mysql-login
     * @var string
     */
    public $host;

    /**
     * Contains the id for mysql-login
     * @var string
     */
    public $id;

    /**
     * Contains the pw for mysql-login
     * @var string
     */
    public $pw = null;

    /**
     * Contains the dbname for mysql-login
     * @var string
     */
    public $dbname;
    
    /**
     * Contains the mysql_connect resource
     * @var resource $sqlLink
     */
    public $sqlLink;
    
    /**
     * Receive all sql configurations and connect 
     */
    public function __construct()
    {
        if(isset(PWEL::$config['sql'])) {
            $this->host = PWEL::$config['sql']['server'];
            $this->id = PWEL::$config['sql']['username'];
            $this->pw = PWEL::$config['sql']['password'];
            $this->dbname = PWEL::$config['sql']['dbname'];

            $this->connect();
        }
    }
 
    /**
     * Connects to mysql
     * 
     */   
    public function connect()
    {
        $this->sqlLink = mysql_connect($this->localhost, $this->id, $this->pw);
        mysql_select_db($this->dbname, $this->sqlLink);
    }

}