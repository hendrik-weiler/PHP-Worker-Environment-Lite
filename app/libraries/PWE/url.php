<?php
/**
 * Manipulates the URL
 *    add, remove get elements with keys
 *
 * @author Hendrik Weiler
 */
class url extends cases {
    public  $goto;
    private $url;
    private $elements;
    public  $get;
    public  $vali;

    /**
     * In some way you can add and remove
     * data within the construct of this class
     * but i dont know anymore how
     * @param <string/array> $string
     * @param <string/array> $key
     * @param <string/array> $value
     */
    public function __construct($string=null,$key=null,$value=null) {
        $this->receive_elements($string);
        if(is_array($key) && $string == 1)
        {
            $this->multi_add($key);
        }
        if(is_array($key) && $string == 2)
        {
            $this->multi_remove($key);
        }
        if(isset($key) && isset($value) && $string == 1)
        {
            $this->add($key, $value);
        }
        if(isset($key) && isset($value) && $string == 2)
        {
            $this->remove($key);
        }
    }

    /**
     * Elements represents get arrays values
     * This function sets a object and
     * if you have entered a url string
     * it will split it down and add it
     * aswell
     * @param <string> $string
     */
    private function receive_elements($string=null)
    {
        foreach($_GET as $key => $value)
        {
            $this->get->$key = $value;
        }
        if(is_string($string))
        {
            unset($this->get);
            $part = explode("?",$string);
            $each = explode("&",$part[1]);
            $this->url = $part[0];
            if(isset($part[1]) && !empty($part[1]))
            {
            foreach($each as $value)
            {
                $getkeyvalue = explode("=",$value);
                $key = $getkeyvalue[0];
                $value = $getkeyvalue[1];
                $this->get->$key = $value;
            }
            }
        }
    }

    /**
     * Manage to create the url
     * @return <nothing>
     */
    private function compress_elements()
    {
        if(count((array)$this->get) == 0)
        {
            $this->goto = "?";
            return;
        }
        $this->elements = "?";
        foreach($this->get as $key => $value)
        {
            $this->elements .= "&$key=$value";
        }
        $this->elements = str_replace("?&","?",$this->elements);
        $this->goto = $this->url.$this->elements;
    }

    /**
     * Adds a new element
     * @param <string> $key
     * @param <string> $value
     * @return url
     */
    function add($key,$value=null)
    {
        if(!isset($key)) {
            throw new unError(message::$parameter,"add");
        }
        if(is_array($key))
        {
            $this->multi_add($key);
        }
        else
        {
            $this->get->$key = $value;
            $this->compress_elements();           
        }
        return $this;
    }

    /**
     * Removes a key
     * @param <string> $key
     * @return url 
     */
    function remove($key)
    {
        if(!isset($key)) {
            throw new unError(message::$parameter,"remove");
        }        
        unset($this->get->$key);
        $this->compress_elements();
        return $this;
    }

    /**
     * Validates links to css/js/images
     * @param <string> $path 
     */
    static public function validate($path,$bool=false) {
        if(preg_match("#http://#i",$path) == true) {
            return $path;
        }
        $get = self::parameter();
        unset($get[0]);
        for($i=0;$i<count($get);$i++) {
            if($i>=1) {
                $return .= "../";
            }
        }
        if($bool == true) {
            return str_replace("//","/",($return.$path));
        }
        echo str_replace("//","/",($return.$path));
    }
    
    /**
     * Returns/Echo mulitple validated JS tags 
     * @param <string/array> $path
     * @param <bool> $bool
     * @return string 
     */
    static public function valiJS($path,$bool=false) {
        if(is_array($path)) {
            foreach($path as $url) {
                $validated = self::validate("content/js/".$url,true);
                $validated = str_replace("//","/",$validated);
                $return .= "\r".'<script type="text/javascript" src="'.$validated.'"></script>';
            }
        }
        else {
                $validated = self::validate("content/js/".$path,true);
                $validated = str_replace("//","/",$validated);
                $return .= "\r".'<script type="text/javascript" src="'.$validated.'"></script>';
        }
        if($bool == true) {
            return $return;
        }
        echo $return;
    }
    /**
     * Returns/Echo mulitple validated CSS tags 
     * @param <string/array> $path
     * @param <bool> $bool
     * @return string 
     */
    static public function valiCSS($path,$bool=false) {
        if(is_array($path)) {
            foreach($path as $url) {
                $validated = self::validate("content/css/".$url,true);
                $validated = str_replace("//","/",$validated);
                $return .= "\r".'<link rel="stylesheet" href="'.$validated.'"></link>';
            }
        }
        else {
                $validated = self::validate("content/css/".$path,true);
                $validated = str_replace("//","/",$validated);
                $return .= "\r".'<link rel="stylesheet" href="'.$validated.'"></link>';
        }
        if($bool == true) {
            return $return;
        }
        echo $return;
    }    
    /**
     * Adds multiple elements
     * @param <array> $array
     * @return url
     */
    function multi_add($array)
    {
        if(!isset($array)) {
            throw new unError(message::$parameter,"multi_add");
        }        
        foreach($array as $key => $value)
        {
            $this->get->$key = $value;
        }
        $this->compress_elements();
        return $this;
    }

    function multi_remove($array)
    {
        foreach($array as $key)
        {
            unset($this->get->$key);
        }
        $this->compress_elements();
        return $this;
    }

    /**
     * Refresh to a new content
     * Updated 13.10.2010
     * @param <string> $url
     */
    function refresh($url=null)
    {
        if(MODE_CONTROLLER == "oop")
        {
            $param = $this->param();
            if(isset($param[1])) {
                $class = $param[1]."/";
            }
            $url = "../".$class.$url;
            $url = str_replace("//","/",$url);
        }
        if(empty($url))
        {
            echo "<meta http-equiv=\"refresh\" content=\"0; URL=".$this->goto."\">";
        }
        else
        {
            echo "<meta http-equiv=\"refresh\" content=\"0; URL=".$url."\">";
        }
    }

    /**
     * Adds session and document
     * (for controller mode: default)
     * @param <string> $site
     * @param <string> $session
     * @return url 
     */
    function logvars($site,$session)
    {
        if(!isset($site) || !isset($session)) {
            throw new unError(message::$parameter,"logvars");
        }
        unset($this->get);
        $this->get->doc = $site;
        $this->get->session = $session;
        $this->compress_elements();
        return $this;
    }

    /**
     * Move to absolute index aka startcontroller
     * @return <nothing>
     */
    function toIndex()
    {
        if(MODE_CONTROLLER == "oop")
        {
            $url = "../";
            header("Location: $url");
            return;
        }
        unset($this->get);
        $this->compress_elements();
        $this->refresh();
    }

    /**
     * Move to current page/controller(oop mode)
     * @return <nothing>
     */
    function toThisIndex()
    {
        if(MODE_CONTROLLER == "oop")
        {
            $param = $this->param();
            if(isset($param[1])) {
                $class = $param[1]."/";
            }
            $url = "../".$class.$url;
            $url = str_replace("//","/",$url);
        }
        $doc = $this->get->doc;
        unset($this->get);
        if($_GET["doc"] != "") {
        $this->get->doc = $doc;
        }
        $this->compress_elements();
        $this->refresh();
    }

    /**
     * Returns the url string
     * @return <string>
     */
    function view($name=null)
    {
        if(isset($name)) {
            return $this->vali[$name];
        }
        return $this->goto;
    }

    /**
     * Adds a document and allow multiple adds together
     * (Controller mode: default)
     * @param <string> $page
     * @param <array> $add
     * @return url
     */
    function toPage($page,$add=null)
    {
        if(!isset($page)) {
            throw new unError(message::$parameter,"toPage");
        }        
        $this->get->doc = $page;
        if(is_array($add))
        {
            $this->multi_add($add);
        }
        $this->compress_elements();
        return $this;
    }

   /**
    * Creates valid url's
    * In oop mode you can validate your links, images and 
    * stuff to permanently display them through your project
    * @param <string> $url
    * @return <this>
    */
   function vali_url($name,$url) {
        if(!isset($name) || !isset($url)) {
            throw new unError(message::$parameter,"vali_url");
        }       
       $this->vali[$name] = HOST."content/".$url;
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