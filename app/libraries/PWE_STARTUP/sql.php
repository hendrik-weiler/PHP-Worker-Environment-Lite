<?php
/**
 * Very Important Class
 * which controlls all database behaviors
 *
 * @author Hendrik Weiler
 */
class sql {
    //Logindata
    private $id;
    private $pw;
    private $dbname;
    ////////////
    public static $acc;
    public static $save;
    public $table;
    public $col_names;
    public $col_reduce;
    protected $current_table;


    /**
     * Sets variables at start
     * @param <string> $id
     * @param <string> $pw
     * @param <string> $dbname
     */
    function  __construct($id,$pw,$dbname) {
        $this->id = $id;
        $this->pw = $pw;
        $this->dbname = $dbname;
        $this->reset();
        if(MODE_CONTROLLER=="oop")
        {
            if(isset($_COOKIE["PHPSESSID"])) {
                $this->acc = $this->get_row(array("session"=>$_COOKIE["PHPSESSID"]), SQL_LOGIN_DB);
                self::$acc = $this->acc;
            }
        }        
        else {
            if(isset($_GET["session"])) {
                $this->acc = $this->get_row(array("session"=>$_GET["session"]), SQL_LOGIN_DB);
                self::$acc = $this->acc;
            }
        }
    }

    /**
     * Connects to database
     * @return <bool>
     */
    function connect()
    {
        $con = mysql_connect(SQL_HOST, $this->id, $this->pw);
        if($con == false)
        {
            throw new sqlError(message::$sql_connect,"connect",help::$sql_connect);
        }
        else
        {
            mysql_select_db($this->dbname);
            return true;
        }
    }

    /**
     * Checks if the rights are enough for allow action
     * @param <int> $rights 
     */
    public static function secure($rights) {
        $rightcol = LOGIN_DB_RIGHTS;
        if(self::$acc->$rightcol >= $rights) {}
        else {
            exit;
        }
    }
    
    /**
     * Convert json data
     * @param <array> $var
     * @return <array>
     */
    function json_post($var)
    {
        if(empty($var)) {
            throw new sqlError(message::$parameter, "json_post");
        }        
        return json_decode($_REQUEST[$var], true);
    }

    /**
     * Adds a row
     * @param <array> $values
     * @param <string> $table
     * @return <bool>
     */
    function add($values,$table)
    {
        if(empty($values) || empty($table)) {
            throw new sqlError(message::$parameter, "add");
        }        
        $query = "INSERT INTO `".SQL_DB."`.`$table` (";
            foreach($values as $keys => $vals)
            {
                $query .= "`".$keys."`,";
            }
            $query .= ") VALUES (";
            $query = str_replace(",)", ")", $query);
            foreach($values as $key => $val)
            {
                if(!isset($val))
                {
                    $query .= "NULL ,";
                }
                if(is_int($val))
                {
                    $query .= "$val ,";
                }
                if(is_string($val))
                {
                    $query .= "\"$val\" ,";
                }
            }
            $query .= ");";
            $query = str_replace(",);", ");", $query);
            if($this->query($query))
            {
                return true;
            }
            else {
                return false;                
            }
    }

    /**
     * Resets variables
     * @return sql
     */
    function reset()
    {
        unset($this->save);
        unset($this->table);
        return $this;
    }

    /**
     * Executes a query
     * @param <resource> $query
     * @return <bool/resource>
     */
    function query($query)
    {
        if(empty($query)) {
            throw new sqlError(message::$parameter, "query");
        }          
        $this->connect();
        $result = mysql_query($query);
        if($result == false)
        {
            throw new sqlError(message::$sql_query,"query",help::$sql_query,$query);
        }
        else
        {
            return $result;
        }
    }

    /**
     * Generates mysql code
     * @param <string> $where
     * @param <string> $table
     * @param <string> $col
     * @param <string> $typeof
     * @return <string>
     */
    function sort_by($where, $table, $col="ID",$typeof="ASC")
    {
        if(empty($where) || empty($table)) {
            throw new sqlError(message::$parameter, "add");
        }          
           if(is_array($where))
           {
               $where_option .= "WHERE";
               $z = 1;
               foreach($where as $name => $value)
               {
                   if(is_string($value))
                   {
                   $where_option .= " `$name` LIKE '$value'";
                   }
                   else
                   {
                   $where_option .= " `$name` =$value ";
                   }
                   if($z <= (count($where)-1))
                   {
                   $where_option .= " AND ";
                   }
                   $z+=1;
               }
           }
           $quar = "SELECT * FROM `$table` $where_option ORDER BY `$table`.`$col` $typeof";
           $this->sql2 = $quar;
           if($this->query($quar) == false)
           {
               return false;
           }
           return $quar;
       }
       /**
        * Returns a row from a table
        * @param <array> $search
        * @param <string> $table
        * @param <string> $col
        * @param <string> $typeof
        * @return <array>
        */
       function get_row($search, $table, $col="ID", $typeof="ASC")
       {
            if(empty($search) || empty($table)) {
                throw new sqlError(message::$parameter, "add");
            }  
           unset($this->save);
           $s_search = $search;
           $s_table = $table;
           if(is_string($s_search) && is_array($s_table))
           {
               $table = $s_search;
               $search = $s_table;
           }
           $this->current_table = $table;
           $this->get_col_names($table);
           $sql = $this->sort_by($search, $table, $col, $typeof);
           $result = $this->query($sql);
           if($result == false)
           {
               return false;
           }
           $output = mysql_fetch_array($result);
           if(!is_array($output))
           {
               return false;
           }
           foreach($output as $key => $value)
           {
               if(!is_int($key))
               {
                    $this->save->$key = $value;
               }
           }
           return $this->save;
       }

       /**
        * Save all changed data
        * @return <bool>
        */
       function save()
       {
           if(isset($this->save)) {
            foreach($this->save as $key => $value)
            {
                $query = "UPDATE `$this->dbname`.`".$this->current_table."` SET `$key` =  '$value' WHERE `".$this->save->table."`.`ID` =".$this->save->ID." LIMIT 1 ;";
                if($this->query($query) == false)
                {
                    throw new sqlError(message::$sql_save, "save", help::$sql_query, $query);
                }
                $this->query($query);
            }
            unset($this->save);
            return true;
           }
           else { 
               throw new sqlError(message::$sql_save_fail, "save", help::$sql_save_fail);
           }
       }

       /**
        * Returns all table entrys
        * @param <string> $table
        * @param <array> $search
        * @param <string> $col
        * @param <string> $typeof
        * @return <bool/array>
        */
       function get_table($table,$search=null, $col="ID", $typeof="ASC")
       {
            if(empty($table)) {
                throw new sqlError(message::$parameter, "get_table");
            }           
           $this->get_col_names($table);
           $this->current_table = $table;
           $result = $this->query("SELECT * FROM `$table` ORDER BY $col $typeof");
           if($search != null)
           {
               $query = $this->sort_by($search, $table,$col,$typeof);
               $result = $this->query($query);
           }
           if($result == false)
           {
               throw new sqlError(message::$sql_query, "get_table", help::$sql_query, $query);
           }
           $zz = 1;
           while($row = mysql_fetch_object($result))
           {
               foreach($row as $key => $value)
               {
               $this->table->$zz->$key = $value;
               }
               $zz+=1;
           }
           $this->reduce_cols();
           $this->table->count = ($zz);
           return $this->toForeach($this);
       }

       /**
        * Gives a column a new value
        * @param <string> $attr
        * @param <string> $value
        * @return sql
        */
       public function setSave($attr,$value)
       {
            if(empty($attr) || empty($value)) {
                throw new sqlError(message::$parameter, "setSave");
            }              
           $this->save->$attr = $value;
           return $this;
       }

       /**
        * Returns a column value
        * @param <string> $attr
        * @return <string>
        */
       public function getSave($attr)
       {
           if(empty($attr)) {
                throw new sqlError(message::$parameter, "get_table");
           }   
           return $this->save->$attr;
       }

       /**
        * Register the post array, for save changes
        * (could also be any other like array)
        * @param <array> $post
        * @return sql 
        */
       public function postSave($post)
       {
           if(!is_object($post) && !is_array($post)) {
               throw new sqlError(message::$sql_postsave,"postSave");
           }
           foreach($post as $key => $val)
           {
               if(isset($this->save->$key))
               {
                   $this->save->$key = $val;
               }
           }
           return $this;
       }

       /**
        * Returns new class
        * @return this
        */
       public function renew()
       {
           $this->reset();
           return new $this(SQL_ID,SQL_PW,SQL_DB);
       }

       /**
        * Delete a searched row
        * @param <array> $search
        * @param <string> $table
        * @param <string> $col
        * @param <string> $typeof
        * @return <nothing/false>
        */
       public function delete($search, $table, $col="ID", $typeof="ASC")
       {
            if(empty($search) || empty($table)) {
                throw new sqlError(message::$parameter, "delete");
            }             
           $s_search = $search;
           $s_table = $table;
           if(is_string($s_search) && is_array($s_table))
           {
               $table = $s_search;
               $search = $s_table;
           }
           $sql = $this->sort_by($search, $table, $col, $typeof);
           $result = $this->query($sql);
           if($result == false)
           {
               throw new sqlError(message::$sql_query, "delete", help::$sql_query, $sql);
           }
           $row = mysql_fetch_object($result);
           $query = "DELETE FROM `$this->dbname`.`$table` WHERE `$table`.`ID` = ".$row->ID;
           if($this->query($query) == false)
           {
               throw new sqlError(message::$sql_query, "get_table", help::$sql_query, $query);
           }
       }

       /**
        * Convert this table object to foreach usable array
        * @param <object> $tabledata
        * @return <array>
        */
       function toForeach($tabledata)
       {
            if(is_null($tabledata)) { 
                 throw new sqlError(message::$parameter, "toForeach");
           }           
           if(is_resource($tabledata)) {
               while($data = mysql_fetch_object($tabledata)) {
                   $return[] = $data;
               }
               return $return;
           }
            for($i=1;$i<$tabledata->table->count;$i+=1)
            {
                    $return[] = $tabledata->table->$i;
            }
            return $return;
       }

       /**
        * Returns all col names
        * @param <string> $table
        * @return <array>
        */
       function get_col_names($table)
       {
           if(empty($table)) { 
                 throw new sqlError(message::$parameter, "get_col_names");
           }  
           $query = $this->query("SHOW COLUMNS FROM $table");
           while($val = mysql_fetch_array($query))
           {
               $return[] = $val[0];
           }
           $this->col_names = $return;
           return $return;
       }

       /**
        * Reduce columns from this table object
        * (normal use by table class)
        */
       function reduce_cols()
       {
           if(isset($this->col_reduce)) {
               foreach($this->col_reduce as $key)
               {
                   $key = array_search($key, $this->col_names);
                   unset($this->col_names[$key]);
               }

               foreach ($this->col_reduce as $val)
               {
                   $count = 0;
                   foreach($this->table as $vals)
                   {
                       $count+=1;
                       if(isset($vals->$val))
                       {
                            unset($this->table->$count->$val);
                       }
                   }
               }
           }
       }

       /**
        * This delete a column from col_reduce variable
        * (used by table class)
        * @param <string> $column
        * @return sql
        */
       function del_col($column)
       {
           if(empty($column)) { 
                 throw new sqlError(message::$parameter, "del_col");
           }  
           if(is_string($column))
           {
               $this->col_reduce[] = $column;
           }
           if(is_array($column))
           {
               foreach ($column as $key)
               {
                   $this->col_reduce[] = $key;
               }
           }
           return $this;
       }
}
    //PHP Worker Environment - a easy to use PHP framework
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