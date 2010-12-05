<?php
/**
 * Handles all sql delete behaviours
 * 
 * @author Hendrik Weiler
 * @package SQL
 */
class Sql_Delete {
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
     * Sets an instance of Sql_Select as base of connection/call query/generate where string
     * @var Sql_Select $selectClass
     */    
    public function __construct(Sql_Select $selectClass) {
        $this->selectClass = $selectClass;
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
     * Alias of table
     * @var string $tablename
     * @return $this
     */
    public function from($tablename) {
        $this->table($tablename);
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
     * Generate the delete query
     * @return string
     */
    private function generateQuery() {
        if(empty($this->db)) {
            $this->db = $this->selectClass->con->dbname;
        }
        if(empty($this->table)) {
            $this->table =  $this->selectClass->from;
        }
        $query = "DELETE FROM `".$this->db."`.`{$this->table}` ";
        if($this->whereString) {
            $query .= " ".$this->whereString;
        }
        else {
            if(is_array($this->selectClass->result) || is_object($this->selectClass->result)) {
                foreach($this->selectClass->result as $col => $val) {
                    $query .= " WHERE `{$this->table}`.`{$col}` ={$val}";
                    break;
                }
            }
        }
        return $query;
    }
    
    /**
     * delete line from database
     * @return bool
     */
    public function delete() {
        if(!preg_match("/(.*)WHERE(.*)/i",$this->generateQuery())) {
            return false;
        }
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