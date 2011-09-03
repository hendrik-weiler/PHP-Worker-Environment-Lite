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
 * Edits a row in a table
 *
 * @author Hendrik Weiler
 * @package eDB
 * @category Collection
 * @version 1.0
 */
class eDB_Update
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
     * Sets variables and peform an update
     * @param string $tableName
     * @param array $search
     * @param string $values
     */
    public function __construct($tableName=null, $search=null, $values=null)
    {
        if(eDB::$dbPath && eDB::$dbName) {
            $this->dbName = eDB::$dbName;
            $this->dbPath = eDB::$dbPath;
            
            if($tableName != null && $search != null && $values != null)
                $this->update($values, $search, $tableName);
        }
    }

    /**
     * Peform an update
     * @param array $values
     * @param array $search
     * @param string $tableName
     */
    private function update($values, $search, $tableName)
    {
        $select = new eDB_Select();
        $rowID = $select->getRowIdBySearch($search, $tableName);
        $ChangedRow = $select->getRowById($rowID, $tableName);
        foreach($values as $col => $value) {
            $ChangedRow[0][$col] = $value;
        }
        $cols = $select->getColumns($tableName,true);
        $content = $select->search($tableName, '*');
        $completeContent[] = $cols;
        foreach($content as $row) {
            $completeContent[] = $row;
        }
        $completeContent[$rowID] = $ChangedRow[0];
        $this->updateTable($completeContent, $tableName);
    }

    /**
     * Update the given table with the given rows
     * @param array $rows
     * @param string $tableName
     */
    public function updateTable($rows,$tableName)
    {
        $ifTable = new eDB_Table();
		$inFile = null;
        if($ifTable->exists($tableName)) {
            $table = fopen(str_replace('//', '/', $this->dbPath . "/"
                                        . $tableName . '.edb'), 'w');
            $count = 0;
            foreach($rows as $row) {
                if($count == 0)
                    $break = '';
                else
                    $break = "\r\n";
                
                $inFile .= $break.implode('|', $row);
                ++$count;
            }
            fputs($table,$inFile);
        }
    }
}