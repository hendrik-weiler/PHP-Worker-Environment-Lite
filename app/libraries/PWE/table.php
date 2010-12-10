<?php
/**
 * Table class - creates tables with arrays or sql data
 *
 * @author Hendrik Weiler
 */
class table extends sql {
    public $table;
    public $content;
    public $vars;

    private $datatable = false;
    private $datatable_action;
    private $datatable_lang = "Save";


    public function __construct() {
        parent::__construct(SQL_ID,SQL_PW,SQL_DB);
    }
    /**
     * Activate the datagrid mode
     * (Buggy) : Problem with different tables and column reduces
     * @param <boolean> $bool
     * @param <string> $table
     * @param <string> $action
     * @param <string> $lang
     * @return string
     */
    function dataTable($bool,$table,$action=null,$lang=null)
    {
        if(empty($bool)) { 
               throw new unError(message::$parameter, "dataTable");
        }         
        $this->datatable = $bool;
        $this->current_table = $table;
        $this->datatable_action = $action;
        if(isset($lang))
        {
            $this->datatable_lang = $lang;
        }
        return $this;
    }

    /**
     * Setup a complete Table
     * @param <array> $classes
     * @param <array> $variables
     * @param <array> $cols
     * @param <array> $rows
     * @return table 
     */
    function setup_table($classes,$variables,$cols=null,$rows=null)
    {
        if(empty($classes)) { 
               throw new unError(message::$parameter, "setup_table");
        }         
        $cols = array_values($cols);
        $this->col_names = $cols;
        $this->vars = $variables;
        if(is_array($classes))
        {
            $col = " class=\"{$classes[0]}\" ";
            $row = " class=\"{$classes[2]}\" ";
            if(!is_array($classes[1]))
            { $rest = " class=\"{$classes[1]}\" "; }
        }
        if(is_string($classes))
        {
            $col = " class=\"{$classes}\" ";
            $row = " class=\"{$classes}\" ";
            $rest = " class=\"{$classes}\" ";
        }
        if($this->datatable == true)
        {
            $this->content->rows[-5] = "<form name=\"tabledata[]\" method=\"post\" action=\"".$this->datatable_action."\">\r";
            $this->content->rows[-4] = "<input type=\"submit\" value=\"".$this->datatable_lang."\">";
        }
        $this->content->rows[-3] = "<table cellspacing=\"0\" cellpadding=\"0\">\r";
        if(is_array($cols) || is_object($cols))
        {
            $this->content->rows[-2] = "<tr{$col}>\r";
            foreach($cols as $var)
            {
                if(empty($var)) { $var = "&nbsp;"; }
                $this->content->rows[-2] .= "<td>$var</td>\r";
            }
            $this->content->rows[-2] .= "</tr>\r";
        }
        if(is_array($variables) || is_object($variables))
        {
            for($i=0;$i<count($variables);$i+=1)
            {
                if(is_array($classes[1]))
                {
                    if($asd==null)
                    {
                        $rest = " class=\"{$classes[1][0]}\" ";
                        $asd = 1;
                    }
                    else
                    {
                        $rest = " class=\"{$classes[1][1]}\" ";
                        $asd = null;
                    }
                }
                $this->content->rows[$i] .= "<tr{$rest}>\r";
                $l = 0;
                foreach($variables[$i] as $var)
                {
                    if(empty($var) && $this->datatable == false) { $var = "&nbsp;"; }
                    if($this->datatable == true)
                    {
                        if($l >= count($cols)) { $l = 0; }
                        $this->content->rows[$i] .= "<td><input name=\"".$cols[$l]."[]\" type=\"text\" value=\"$var\"></td>\r";
                        $l+=1;
                    }
                    else
                    {
                        $this->content->rows[$i] .= "<td>$var</td>\r";
                    }
                }
                $this->content->rows[$i] .= "</tr>\r";
            }
        if($this->datatable == true)
        {
            $this->content->rows[] = "<tr>";
            $l = 0;
            foreach($this->col_names as $val)
            {
                $this->content->rows[] = "<td><input name=\"new:".$cols[$l]."\" type=\"text\" value=\"New\"></td>\r";
                $l+=1;
            }
            $this->content->rows[] = "</tr>";
        }
        }
        if(is_array($rows) || is_object($rows))
        {
            $i = 0;
            foreach($rows as $var)
            {
                if(empty($var)) { $var = "&nbsp;"; }
                $this->content->rows[$i] = str_replace(" >", " >\r<td{$row}>$var</td>", $this->content->rows[$i]);
                $i += 1;
            }
        }
        $this->content->rows[] = "</table>";
        if($this->datatable == true)
        {
            $this->content->rows[] = "<input type=\"hidden\" name=\"table\" value=\"{$this->current_table}\"></form>";
        }
        return $this;
    }

    /**
     * Create and return the table
     * @return <string>
     */
    function create_table()
    {
        foreach($this->content->rows as $value)
        {
            $this->table .= "$value";
        }
        return $this->table;
    }

    /**
     * Would save all changes which was made in the datagrid
     * (Should work)
     */
    function accept_changes()
    {
        $rows = $this->get_table($_POST["table"]);
            for($i=0;$i<=count($rows);$i+=1)
            {
                $this->get_row(array("ID" => $rows[$i]->ID), $_POST["table"]);
                foreach($this->col_names as $key)
                {
                    if(!empty($_POST[$key][$i])) {
                        $this->save->$key = $_POST[$key][$i];
                    }
                }
                $this->save();
            }
            foreach($this->col_names as $key)
            {
                $keys = "new:".$key;
                        $array[$key] = $_POST[$keys];
            }
            $proof = array_search("New", $array);
            $array = str_replace("NULL", "",$array);
            if(!is_string($proof)) {
             $this->add($array, $_POST["table"]);
            }
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