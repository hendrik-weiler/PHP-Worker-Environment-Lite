<?php
/**
 * Manages to create content for a custom list
 * 
 * @author Hendrik Weiler
 * @package Items_Table
 */
class Items_Table {
    /**
     * Format of row
     * @var string 
     */
    private $format;
    
    /**
     * Format of head
     * @var string 
     */
    private $formatHead;
    
    
    /**
     * An array of all replacements
     * @var array
     */
    private $replaceElements;
    
    /**
     * An array of all replacements from head
     * @var array
     */
    private $replaceElementsHead;
    
    /**
     * contain all rows
     * @var array
     */
    private $rows = array();
    
    /**
     * Set th row
     * @var array
     */
    private $headRow = array();
    /**
     * Sets the output format like
     * <td>&1</td><td>&2</td>
     * @param string $format 
     */
    
    /**
     * Controlls displaying of head
     * @var bool
     */
    private $headActive = false;
    public function setFormat($format) {
        $this->format = $format;
        preg_match_all("/&([0-9]{1})/i", $format, $matches);
        $this->replaceElements = $matches[1];
    }
    
    /**
     * Sets the head output format like
     * <th>&1</th><th>&2</th>
     * @param string $format 
     */
    public function setHeadFormat($format) {
        $this->headActive = true;
        $this->formatHead = $format;
        preg_match_all("/&([0-9]{1})/i", $format, $matches);
        $this->replaceElementsHead = $matches[1];
    }
    /**
     * Adds a row if instance is of Items_Table_Row
     * @param Items_Table_Row $row
     * @return bool/nothing
     */
    public function addRow(Items_Table_Row $row) {
        if(!$row instanceof Items_Table_Row) {
            return false;
        }
        $this->rows[] = $row->elements;
    }

    /**
     * Adds multiple rows by using array parameter
     * @param array $arrayOfRow
     * @return bool/nothing 
     */
    public function addRows($arrayOfRow) {
        foreach($arrayOfRow as $row) {
            if(!$row instanceof Items_Table_Row) {
                return false;
            }
            $this->rows[] = $row->elements;
        }
    }    

    /**
     * Generate a standard table format
     * @param string $typeof
     * @return string/bool 
     */
    private function generateStandardFormat($typeof) {
        if(is_array($this->rows[0])) {
            $this->formatElements = count($this->rows[0]);
        }
        else {
            return false;
        }
        switch($typeof) {
            case "elements":
                $tag = "td";
                break;
            case "head":
                $tag = "th";
                break;
        }
        $output = "<tr>";
        for($i=0;$i<$this->formatElements;++$i) {
            $output .= "<{$tag}>&".($i+1)."</{$tag}>";
        }
        $output .= "</tr>"; 
        return $output;
    }
    
    /**
     * Sets a header
     * @param Items_Table_Row $row
     * @return bool/nothing
     */
    public function setHead(Items_Table_Row $row) {
        if(!$row instanceof Items_Table_Row) {
            return false;
        }
        $this->headActive = true;
        $this->headRow = $row->elements;
    }
    /**
     * Return all rows together in the given format
     * @return bool/string
     */
    public function renderElements() {
        if(!isset($this->format)) {
            $this->format = $this->generateStandardFormat("elements");
            $this->setFormat($this->format);
        }     
		$output = '';   
        foreach($this->rows as $row) {
            $outputFormat = $this->format;
            foreach($this->replaceElements as $element) {
               $outputFormat = str_replace("&{$element}", $row[($element-1)], $outputFormat);
            }
            $output .= $outputFormat;
        }
        return $output;
    }

    /**
     * Return the head in the given format
     * @return string 
     */
    public function renderHead() {
        if($this->headActive == false) {
            return;
        }
        if(!isset($this->formatHead)) {
            $this->formatHead = $this->generateStandardFormat("head");
            $this->setHeadFormat($this->formatHead);
        } 
		$output = '';
        $outputFormat = $this->formatHead;
        foreach($this->replaceElementsHead as $element) {
           $outputFormat = str_replace("&{$element}", $this->headRow[($element-1)], $outputFormat);
        }
        $output .= $outputFormat;
        return $output;
    }

    /**
     * Returns the full table in the given format
     * default format is set for a simple table
     * @param string $format
     * @return string
     */
    public function render($format="<table>&content</table>") {
        $head = $this->renderHead();
        $elements = $this->renderElements();
        return str_replace("&content", $head.$elements, $format);
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
