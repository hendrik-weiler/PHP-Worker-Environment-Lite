<?php
/*
 * Hendrik's Class Collection
 * Copyright (C) 2010-2011  Hendrik Weiler
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
 * along with this program.  If not, see <http: * www.gnu.org/licenses/>.
 */
/**
 * Creates a easy db table
 *
 * @author Hendrik Weiler
 * @package eDB
 * @category Collection
 * @version 1.0
 */
class eDB_Table
{
    /**
     * Contains the name of the table
     * @var string
     */
    public $tableName;

    /**
     * Contains the columns of the table
     * @var string
     */
    public $tableColums;

    /**
     * Sets the name of the db
     * @var string
     */
    public $dbName;

    /**
     * Sets the path of the db
     * @var string
     */
    public $dbPath;

    /**
     * Sets variables
     */
    public function __construct()
    {
        if(count(func_get_args())>0) {
            foreach(func_get_args() as $arg) {
                if(is_array($arg)) {
                    $columns = $arg;
                }
                else {
                    $name = $arg;
                }
            }
        }
        if(eDB_Path && eDB_Name) {
            $this->dbName = eDB::$dbName;
            $this->dbPath = eDB::$dbPath;
            $this->tableName = $name;
            $this->tableColums = $columns;
            if($name && $columns)
                $this->create();
        }
    }

    /**
     * Returns if the table exists
     * @param string $name
     * @return bool
     */
    public function exists($name)
    {
       return is_file(
            str_replace('//', '/', $this->dbPath . '/' . $name . '.edb')
       );
    }

    /**
     * Creates a new table
     */
    private function create()
    {
        if(!$this->exists('/'.$this->tableName)) {
           $table = fopen(
       str_replace('//', '/', $this->dbPath . '/' . $this->tableName . '.edb'),
       "w+"
           );
           fputs($table,implode('|',$this->tableColums));
        }
    }

    /**
     * Delete all table entries
     * @param string $name
     */
    public function clearTable($name)
    {
        $delete = new eDB_Delete($name, '*');
    }

    /**
     * Deletes a table
     * @param string $name
     */
    public function delete($name)
    {
        if($this->exists('/' . $name))
           unlink(str_replace('//', '/', $this->dbPath . '/' . $name . '.edb'));
    }

    /**
     * Deletes a column from table
     * 
     * @param string $name
     * @param string $table
     */
    public function deleteColumn($table,$name)
    {
        $select = new eDB_Select($table, '*');
        $count = -1;
        $cols = $select->getColumns($table);
        if(!in_array($name, $cols))
                return false;
        
       foreach($cols as $col) {
            if($table == $col)
                break;
            else
                ++$count;
       }
       $i = 0;
       foreach($select->result as $value) {
           unset($select->result[$i][$cols[$count]]);
           $i++;
       }
       unset($cols[$count]);
       $thecols[0] = $cols;
       $result = array_merge($thecols, $select->result);
       array_values($result);
       $update = new eDB_Update();
       $update->updateTable($result, $table);
    }

    /**
     * Adds a new column to the table
     *
     * @param string $table
     * @param string $name
     * @param int $pos
     */
    public function addColumn($table,$name,$pos=0)
    {
        $select = new eDB_Select($table, '*');
        $cols = $select->getColumns($table);
        
        //Columns

        foreach($cols as $key => $col) {
            if($key != $pos) {
                $colz[] = $col;
            }
            else {
                $colz[$pos] = $name;
                $colz[] = $col;
            }
        }
        if(count($cols) <= $pos) {
            $colz[] = $name;
        }

        //Tablecontent
        foreach($select->result as $key => $value) {
            $i = 0;
            foreach($value as $keyz => $rowValue) {
                if($i != $pos) {
                    $tableContent[$key][] = $rowValue;
                }
                else {
                    $tableContent[$key][$pos] = '';
                    $tableContent[$key][] = $rowValue;

                }
                ++$i;
            }
        }
        if(count($cols) <= $pos) {
            $i = 0;
            foreach($select->result as $key => $value) {
                    $tableContent[$i][] = '';
                    ++$i;
            }
        }
        
        //////////////

       $thecols[0] = $colz;
       $result = array_merge($thecols, $tableContent);
       array_values($result);
       
       $update = new eDB_Update();
       $update->updateTable($result, $table);
    }

    /**
     * Sets a primary key which contains auto increement
     *
     * @param string $table
     * @param string $name
     * @param int $startpos
     */
    public function setPrimaryKey($table,$name,$startpos=1)
    {
        $select = new eDB_Select($table, '*');
        $cols = $select->getColumns($table, true);
        if(!is_array($cols))
            return false;
        
        if(preg_match("#$name:key;[0-9]#i", implode('|',$cols))) {
            return false;
        }
        
        $i = 0;
        foreach($cols as $x) {
            if($x == $name)
                break;

            ++$i;
        }
        if($select->result) {
            foreach($select->result as $key => $x) {
                $select->result[$key][$cols[$i]] = $startpos;
                ++$startpos;
            }
        }
        else {
            $select->result = array();
        }
       $cols[$i] = $cols[$i].":key;$startpos";
       $thecols[0] = $cols;
       $result = array_merge($thecols, $select->result);
       array_values($result);
       
       $update = new eDB_Update();
       $update->updateTable($result, $table);
    }
}