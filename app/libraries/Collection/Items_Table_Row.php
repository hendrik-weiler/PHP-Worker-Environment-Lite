<?php
/**
 * Creates a row object
 * 
 * @author Hendrik Weiler
 * @package Items_Table
 */
class Items_Table_Row {
    /**
     * Row Elements
     * @var array
     */
    public $elements = array();
    
    /**
     * Stores all elements into property
     * 
     * @param array/object $elements 
     */
    public function __construct($elements,$receive="value") {
        if(is_array($elements)) {
            $this->elements = $elements;
        }
        if(is_object($elements)) {
            foreach($elements as $column => $value) {
                if($receive == "value") {
                    $valueArray[] = $value;
                }
                if($receive == "key") {
                    $valueArray[] = $column;
                }
            }
            $this->elements = $valueArray;
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
