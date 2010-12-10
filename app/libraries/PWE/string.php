<?php
/**
 * String class - manipulate and create strings
 *
 * @author Hendrik Weiler
 */
class string extends cases {
    public $string;
	public $left;
	public $right;

    /**
     * Checks if value entered correct
     * @param <string> $string
     * @return <error>
     */
    public function __construct($string=null) {
        $this->string = $string;
    }

    /**
     * Convert serialized data from jQuery into an array
     * @param <string> $data
     * @return <array> 
     */
    static public function convertSerializedData($data) {
        $step1 = explode("&",$data);
        $return = null;
        foreach($step1 as $value) {
            $step2 = explode("=",$value);
            $return[$step2[0]] = urldecode($step2[1]);
        }
        return $return;
    }
    
    static public function convert_url_str($str,$mode="normal") {
        $search = array(
            //ö
            "%C3%B6",
            //ü
            "%C3%BC",
            //ä
            "%C3%A4",
            //space
            "%20"
        );
        $search_2 = array(
            //ö
            "Ã¶",
            //ü
            "Ã¼",
            //ä
            "Ã¤"            
        );
        $replace = array(
            "ö","ü","ä"," "
        );        
        switch($mode) {
            case "normal":
                $return = str_replace($search, $replace, $str);
            break;
            case "reverse":
                $return = str_replace($search_2, $replace, $str);
            break;
        }         
        return $return;
    }
    /**
     * alias of convertSerializedData
     * @param <string> $data
     * @return <array> 
     */
    static public function cSData($data) {
        return self::convertSerializedData($data);
    }
    /**
     * Cut a string at one point with custom end like "..."
     * @param <string> $number
     * @param <bool> $error
     * @return <string>
     */
    function cut($number,$string,$end="\r")
    {
        if(empty($number)) { 
               throw new unError(message::$parameter, "cut");
        }         
        $cut = wordwrap($string, $number,$end);
        $length = strlen($string);
        if($number > $length)
        {
            $return[0] = $string;
            return $return;
        }
        $split = explode($end,$cut);
        return $split;
    }

    /**
     * Checks if entered value is correct
     * @param <string> $item
     * @return <bool>
     */
    public function check($item)
    {
        if(is_array($item) || is_object($item) || is_bool($item))
        {
           return true;
        }
        else
        {
           return false;
        }
    }

    /**
     * Adds new string to the string;
     * @param <string> $add
     */
    function add($add)
    {
        $this->string = $this->string.$add;
    }

    /**
     * reset the string
     */
    function clear()
    {
        unset($this->string);
    }

    /**
     * Sets string new
     * @param <string> $string
     */
    function set($string)
    {
        $this->string = $string;
		return $this;
    }

    /**
     * View the string
     */
    function view()
    {
        echo $this->left.$this->string.$this->right;
    }

    /**
     * Creates a link
     * 
     * @param <string> $name
     * @param <string> $url
     * @param <array> $attr
     * @return <string>
     */
    function link($name,$url,$attr=null)
    {
        if(empty($name)) { 
               throw new unError(message::$parameter, "anchor");
        }  
        if(MODE_CONTROLLER == "oop")
        {
          $toLink = url::validate($url,true);
        }
        if(isset($attr)) {
            foreach($attr as $key => $val)
            {
                $attributes .= $key."=".$val." ";
            }
        }       
        return "<a href=\"$toLink\" $attributes>$name</a>";
    }
	/**
	* Quote a text
	* @param <string> $text
	*/
	function quote($mode="normal") {
		$quotation = '<span style="font-size:150%;">"</span>';
		switch($mode) {
			case "big":
				$return = "<h1>$quotation".utf8_encode($this->string)."$quotation</h1>";
			break;
			case "normal":
				$return = "$quotation".utf8_encode($this->string)."$quotation";
			break;	
			case "strong":
				$return = "<strong>$quotation".utf8_encode($this->string)."$quotation</strong>";
			break;	
			default:
				$return = '<span class="'.$mode.'">'."$quotation".utf8_encode($this->string)."$quotation</span>";
			break;	
		}
			if(!empty($this->left)) {
				return $this->left.$return.$this->right;
			}
			else {
				return $return;
			}
	}
	
	/**
	* Creates a simple list using html
	* @param <array/object> $list
	*/
	function make_list($list) {
        if(!is_array($list) && !is_object($list)) { 
               throw new unError(message::$badparam, "make_list");
        } 		
		$return .= "\r<ul>";
		foreach($list as $item) {
			$return .= "\r<li>$item</li>";
		}
		$return .= "\r</ul>";
		return $this->left.$return.$this->right;
	}
	
	/**
	* Various formating functions
	*/
	function b() {
		$this->left .= "<strong>";
		$this->right .= "</strong>";
		return $this;
	}
	function i() {
		$this->left .= "<i>";
		$this->right .= "</i>";
		return $this;
	}
	function u() {
		$this->left .= "<u>";
		$this->right .= "</u>";
		return $this;
	}
	function color($color="black") {
		$this->left .= "<span style=\"color:$color;\">";
		$this->right .= "</span>";
		return $this;
	}	
	/**
	* Adds tabs to a text
	* @param <string> $innerValue
	* @param <int> $count
	*/
	function tab($count=1) {		
		for($i=1;$i<=$count;$i++) {
			$this->left .= "<blockquote>";
			$this->right .= "</blockquote>";
		}	
		return $this;
	}
    /**
     * Alias of link()
     * for those who know CI
     * @param <string> $name
     * @param <string> $url
     * @param <array> $attr
     * @return <string>
     */
    function anchor($name,$url,$attr=null)
    {
        if(empty($name)) { 
               throw new unError(message::$parameter, "anchor");
        }         
        return $this->link($name, $url, $attr);
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