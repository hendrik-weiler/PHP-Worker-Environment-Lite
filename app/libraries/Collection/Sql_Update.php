<?php
/**
 * Handles all sql change behaviours
 * 
 * @author Hendrik Weiler
 * @package SQL
 */
class Sql_Update {
    /**
     * Contains a Sql_Select object
     * @var Sql_Select $selectClass
     */
    private $selectClass;
    
    /**
     * Contains name of the mysql table 
     * @var string $table
     */
    private $table;
    
    /**
     * Contains name of current database
     * @var string $db
     */
    private $db;
    
    /**
     * Contains a generated mysql search string
     * @var string $whereString
     */
    private $whereString;
    
    /**
     * An array of all changes which should be made
     * Example:
     * array("ID"=>5,"username"=>"newuser")
     * @var array $newValues
     */
    private $newValues;

    /**
     * Sets an instance of Sql_Select as base of connection/call query/generate where string
     * @var Sql_Select $selectClass
     */    
    public function __construct(Sql_Select $selectClass) {
        $this->selectClass = $selectClass;
    }

    /**
     * Sets new values
     * @var array $values
     * @return $this
     */    
    public function changeValues($values) {
        $this->newValues = $values;
        return $this;
    }
    
    /**
     * Generates the whereString
     * @var array $cond
     * @return $this
     */
    public function where($cond) {
        $this->whereString = $this->selectClass->generateWhere($cond);
        return $this;
    }
    
    /**
     * Sets a new table
     * @var string $tablename
     * @return $this
     */
    public function table($tablename) {
        $this->table = $tablename;
        return $this;
    }
    
    /**
     * Sets a new database
     * @var string $db
     * @return $this
     */
    public function database($db) {
        $this->db = $db;
        return $this;        
    }
    
    /**
     * Generate the update query
     * @return string
     */
    private function generateQuery() {
        if(empty($this->db)) {
            $this->db = $this->selectClass->con->dbname;
        }
        $query = "UPDATE `".$this->db."`.`{$this->table}` SET ";
        $valueCount = count($this->newValues);
        if(is_object($this->newValues) || is_array($this->newValues)) {
            foreach($this->newValues as $column => $value) {
                $query .= "`{$column}` = '{$value}'";
                if($valueCount != 1) {
                    $query .= ",";
                }
                $valueCount--;
            }
        }
        if($this->whereString) {
            $query .= " ".$this->whereString;
        }
        else {
            foreach($this->selectClass->result as $col => $val) {
                $query .= " WHERE `{$this->table}`.`{$col}` ={$val}";
                break;
            }
            
        }
        return $query;
    }
    
    /**
     * Update the database
     * @return bool
     */
    public function update() {
        if($this->selectClass->queryString($this->generateQuery()) == false) {
            return false;
        }
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