<?php
/**
 * Handles all sql selection behaviours
 * 
 * @author Hendrik Weiler
 * @package SQL
 */
class Sql_Select {
    /**
     * Contains the connectorClass to sql
     * @var class $con
     */
    public $con;

    /**
     * Contains all search conditions
     * @var array $where
     */    
    private $where = array();
    
    /**
     * Contains the selection of the current database
     * @var string $select
     */
    private $select = "SELECT * ";

    /**
     * Contains the current table
     * @var string $from
     */    
    public $from;
 
    /**
     * Result of the query
     * @var object $result
     */   
    public $result;

    /**
     * Contains column and order way
     * @var string $orderID
     * @var string $orderWAY
     */    
    private $orderID;
    private $orderWAY;
 
    /**
     * Sets the connectorclass
     * [Class should have a propery for mysql link(called "sqlLink") connect]
     * @var class $connectcorClass
     */   
    public function __construct($connectorClass) {
       $this->con = $connectorClass; 
    }

    /**
     * Sets the current table
     * @var string $table
     * @return this
     */
    public function from($table) {
        $this->from = $table;
        return $this;
    }
 
    /**
     * Sets the current db
     * @var string $db
     * @return this
     */   
    public function select($db) {
        $this->select = $db;
        return $this;
    }
 
    /**
     * Sets the search conditions
     * @var array $cond
     * @return this
     */   
    public function where($cond) {
        foreach($cond as $column => $value) {
            $this->where[$column] = $value;
        }
        return $this;
    }
 
    /**
     * Sets the order 
     * Syntax:
     * column->ASC/DESC
     * 
     * @var string $order
     * @return this
     */   
    public function order($order) {
        if(preg_match_all("/(.*)->(.*)/i",$order,$result)) {
            $this->orderID = $result[1][0];
            $this->orderWAY = $result[2][0];
        }
        else {
            return false;
        }
        return $this;
    }
 
    /**
     * Generate the query string
     * @return string
     */   
    private function generateQuery() {
        $this->select = mysql_real_escape_string($this->select);
        $this->from = mysql_real_escape_string($this->from);
        
        $query = $this->select." FROM ".$this->from;
        $query .= $this->generateWhere($this->where);
        if($this->orderID) {
            $query .= " ORDER BY `".$this->orderID."`";           
        }
        if($this->orderWAY) {
            $query .= " ".$this->orderWAY;
        }
        return $query;
    }

    /**
     * Generate the string-part of "where"
     * @var array $where
     * @return string
     */    
    public function generateWhere($where) {
        if(!empty($where)) {
            $query .= " WHERE ";
            $CondCount = count($where);
            if(is_array($where) || is_object($where)) {
                foreach($where as $column => $value) {
                    $value = mysql_real_escape_string($value);
                    if(preg_match("/[0-9]/",$value)) {
                        $query .= " $column = '$value' ";
                    }
                    else {
                       $query .= " $column LIKE '$value' "; 
                    }
                    if($CondCount > 1) {
                    $query .= " AND ";
                    }
                    $CondCount--;
                }
            }
        }
        return $query;        
    }
 
 
    /**
     * Converts result into 100% loopable format
     * @return this
     */
    public function toForeach() {
        if(count($this->result) == 1) {
            $cache = $this->result;
            unset($this->result);
            $this->result[0] = $cache;
        }
        return $this;
    }
 
    /**
     * Execute the query and returns a single or multi line 
     * @return array/object
     */   
    public function query() { 
        $query = mysql_query($this->generateQuery(),$this->con->sqlLink);
        if(!$query) {
            return false;
        }
        if(mysql_num_rows($query) == 1) {
            $this->result = mysql_fetch_object($query);
        }
        else {
            if(mysql_num_rows($query) != 0) {
                while($row = mysql_fetch_object($query)) {
                    $this->result[] = $row;
                }
            }
        }
        return $this->result;
    }

    /**
     * queryfunction for external use (like other same-typ-sql classes)
     * @var string $query
     * @return bool
     */    
    public function queryString($query) {
        return mysql_query($query,$this->con->sqlLink);
    }
 
    /**
     * Retrieve all column names of the current table
     * @return array
     */   
    public function getColumns() {
        $colNames = ($this->queryString("SHOW FIELDS FROM {$this->from} FROM `{$this->con->dbname}`"));
        while($colFields = mysql_fetch_object($colNames)) {
            $return[] = $colFields->Field;
        }
        return $return;
    }
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
?>