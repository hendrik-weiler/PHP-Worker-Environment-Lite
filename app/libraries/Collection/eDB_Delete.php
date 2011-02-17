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
class eDB_Delete
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
     * Sets variables
     * @param string $tableName
     * @param array $search
     */
    public function __construct($tableName, $search)
    {
        if(eDB_Path && eDB_Name) {
            $this->dbName = eDB::$dbName;
            $this->dbPath = eDB::$dbPath;
            $this->delete($tableName, $search);
        }
    }

    /**
     * Deletes a row from a table
     * @param string $tableName
     * @param array $search
     */
    private function delete($tableName, $search)
    {
        $select = new eDB_Select();
        $rowID = $select->getRowIdBySearch($search, $tableName);
        $content = $select->search($tableName, '*');
        unset($content[$rowID - 1]);
        $cols = $select->getColumns($tableName);
        $completeContent[] = $cols;
        foreach($content as $row) {
            $completeContent[] = $row;
        }
        $update = new eDB_Update();
        $update->updateTable($completeContent, $tableName);
    }
}