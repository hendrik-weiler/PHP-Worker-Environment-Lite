<?php
/**
 * Search rows and returns them
 *
 * @author Hendrik Weiler
 * @package eDB
 */
class eDB_Select {
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
     * Contains the table name
     * @var string
     */
    public $tableName;

    /**
     * Contains the search pattern
     * @var array
     */
    public $search;

    /**
     * Contains a searchresult
     * @var array
     */
    public $result;

    /**
     * Call a search and put it in $this->result
     * @param array $table
     * @param string $search
     */
    public function __construct($table=null, $search=null) {
        if(eDB_Path && eDB_Name) {
            $this->dbName = eDB::$dbName;
            $this->dbPath = eDB::$dbPath;
            $this->search = $search;
            $this->tableName = $table;
            if($table != null && $search != null)
                $this->result = $this->search ($table, $search);
        }
    }

    /**
     * Returns the column names as array
     * @param string $tableName
     * @return array
     */
    public function getColumns($tableName=null,$primary=false) {
        $table = new eDB_Table();

        if($table != null) {
            if($table->exists($tableName)) {
                $tableContent = $this->getTableContent($tableName);
            }
        }
        else {
            if($table->exists($this->tableName)) {
                $tableContent = $this->getTableContent($this->tableName);
            }
        }
        if($primary==false)
            $tableContent[0] = preg_replace("#:key;[0-9]#i", "", $tableContent[0]);

        
        return explode("|",$tableContent[0]);
    }

    /**
     * Returns the number of the columns
     * @param string $tableName
     * @return int
     */
    public function getColumnNumbers($tableName=null) {
        return count($this->getColumns($tableName));
    }

    /**
     * Returns all content of the given table as array
     * @param string $tablename
     * @return array
     */
    private function getTableContent($tablename) {
        $content = file_get_contents(str_replace("//","/",$this->dbPath."/".$tablename.".edb"));
        $contentSplit = explode("\r",$content);
        if(is_string($contentSplit))
            $contentSplit = explode("\n",$content);
        return $contentSplit;
    }

    /**
     * Returns a search result as array
     *
     * Search Patterns:
     * array($columName => $searchValue)
     *
     * Advanced:
     * $columname => "<=1000"
     * $columname => ">=1000"
     * $columname => "<1000"
     * $columname => ">1000"
     * $columname => "!=1000"
     *
     * @param string $tableName
     * @param array $search
     * @param string $column
     * @param string $sortWay
     * @return array
     */
    public function search($tableName, $search,$column=null,$sortWay="ASC") {
        $col = $this->getColumns($tableName);
        if($search == "*") {
            $tableContent = $this->getTableContent($tableName);
            unset($tableContent[0]);
            $result = $this->toResult($tableContent,$tableName);
            if($result == false)
                return false;
            else
                return $this->sortBy ($col[0], $sortWay, $result);
        }
        else {
            if(is_array($search) || is_object($search)) {
                $result = $this->searchForElements($search,$tableName);
                if(!is_array($result))
                    return false;
                else
                    $result = $this->toResult ($result, $tableName);
                    return $this->sortBy ($col[0], $sortWay, $result);
            }
        }
    }

    /**
     * Returns a sorted row array back
     * @param string $column
     * @param string $sortWay
     * @param array $result
     * @return array
     */
    private function sortBy($column,$sortWay,$result) {
        foreach($result as $values) {
            $sortStep1[] = $values[$column];
        }
        if(strtolower($sortWay) == "asc") {
            sort($sortStep1);
        }
        if(strtolower($sortWay) == "desc") {
            rsort($sortStep1);
        }
        foreach($sortStep1 as $values) {
            foreach($result as $row) {
                if($row[$column] == $values)
                    $sortResult[] = $row;
            }
        }

        return $sortResult;
    }

    /**
     * Converts a given raw search array to a valid usable array
     * @param array $ResultArray
     * @param string $tableName
     * @return mixed
     */
    private function toResult($ResultArray,$tableName) {
        if(is_array($ResultArray) || is_object($ResultArray)) {
            foreach($ResultArray as $row) {
                $cols = $this->getColumns($tableName);
                $i = 0;
                foreach(explode("|",$row) as $value) {
                    $data[$cols[$i]] = trim($value);
                    ++$i;
                }
                $result[] = $data;
            }
            return $result;
        }
        return false;
    }

    /**
     * Big search construct for returning bigger searches
     * @param array $search
     * @param string $tableName
     * @return array
     */
    private function searchForElements($search,$tableName) {
          $tableContent = $this->getTableContent($tableName);
          unset($tableContent[0]);
          $success_Max = count($search);
          foreach($tableContent as $row) {
              $success = 0;
              foreach($search as $searchColumn => $searchValue) {
                  $convertRow[0] = $row;
                  $rowResult = $this->toResult($convertRow, $tableName);
                  $integerValue = str_replace(array(">=","<=","!=","<",">"), "", $searchValue);
                  if(preg_match("#>[0-9]#i",$searchValue)) {
                      if($rowResult[0][$searchColumn] > $integerValue) {
                          ++$success;
                      }
                  }
                  if(preg_match("#<[0-9]#i",$searchValue)) {
                      if($rowResult[0][$searchColumn] < $integerValue) {
                          ++$success;
                      }
                  }
                  if(preg_match("#>=[0-9]#i",$searchValue)) {
                      if($rowResult[0][$searchColumn] >= $integerValue) {
                          ++$success;
                      }
                  }
                  if(preg_match("#<=[0-9]#i",$searchValue)) {
                      if($rowResult[0][$searchColumn] <= $integerValue) {
                          ++$success;
                      }
                  }
                  if(preg_match("#$searchValue#i", $row)) {
                      ++$success;
                  }
                  if(preg_match("#!=[a-zA-Z0-9]#i",$searchValue)) {
                      if(trim($rowResult[0][$searchColumn]) != trim($integerValue)) {
                          ++$success;
                      }
                  }
                  if($success == $success_Max) {
                      $result[] = $row;
                  }
              }
          }
          return $result;
    }

    /**
     * Returns the ID of the searched row
     * @param array $search
     * @param string $tablename
     * @return int 
     */
    public function getRowIdBySearch($search,$tablename) {
        $row = $this->search($tablename, $search);
        if(is_array($row))
            $row = implode("|",$row[0]);

        $count = 0;
        foreach($this->getTableContent($tablename) as $singleRow) {
            if(trim($singleRow) == trim($row)) {
                return $count;
            }
            ++$count;
        }
        return false;
    }

    /**
     * Returns a specific row from the table
     * @param int $id
     * @param string $tablename
     * @return array
     */
    public function getRowById($id,$tablename) {
        $count = 0;
        $content = $this->getTableContent($tablename);
        if(!$content[$id])
            return false;
        $xd[] = $content[$id];
        return $this->toResult($xd, $tablename);
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
