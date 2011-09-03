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
 * Inserts a row in to the given table
 *
 * @author Hendrik Weiler
 * @package eDB
 * @category Collection
 * @version 1.0
 */
class eDB_Insert
{
    
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
     * Sets variables & insert a new row
     * @param string $table
     * @param array $content
     */
    public function __construct($table, array $content)
    {
        if(eDB::$dbPath && eDB::$dbName) {
            $this->dbName = eDB::$dbName;
            $this->dbPath = eDB::$dbPath;
            if($table)
                $this->insert($table, $content);
        }
    }

    /**
     * Insert a new row into a table
     * 
     * @param string $tableName
     * @param array $content
     * @return bool
     */
    private function insert($tableName, $content)
    {
        $ic = $this->generateIncrement($tableName);
        if(!empty($ic['key']) && !empty($ic['value'])) {
            $content[$ic['key']] = $ic['value'];
        }
        $table = new eDB_Table();
        if($table->exists($tableName)) {
            $select = new eDB_Select();
            if(count($content) <= $select->getColumnNumbers($tableName)) {
               $tableSource = fopen(str_replace('//', '/', $this->dbPath . '/' . $tableName . '.edb'), 'a+');
               fputs($tableSource, "\r\n".implode('|', $content));
               return true;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    /**
     * Generate array which contains primarykey and next number
     * @param string $tableName
     * @return array
     */
    private function generateIncrement($tableName)
    {
        $select = new eDB_Select();
        $cols = $select->getColumns($tableName,true);
        $keyNumber = 0;
        foreach($cols as $col) {
            if(preg_match('#[\w]*:key;[0-9]#i', $col)) {
                break;
            }
            $keyNumber++;
        }
        $cols[$keyNumber] = preg_replace('#(.*):key;[0-9]#i', "$1", $cols[$keyNumber]);
        $result = $select->search($tableName, '*');
        $result[count($result)-1][$cols[$keyNumber]]++;

        return array(
            'key' => $cols[$keyNumber],
            'value' => $result[count($result)-1][$cols[$keyNumber]]
        );
    }
}