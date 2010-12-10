<?php
/**
 * head class - generates a complete header
 * (supports method chaining)
 * 
 * Mostly outdated recommended
 * using the load class
 * includeJS() and includeCSS()
 * 
 * @author Hendrik Weiler
 * @deprecated
 */
    class head
    {
        private $head;
        private $css;
        private $js;
        public $send;

        /**
         * Call init()
         * @param <string> $mode
         */
        public function __construct($mode=null) {
            $this->init();
            if($mode == "send")
            {
                $this->send();
            }
        }

        function init()
        {
            $this->head->top = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\"\r\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">\r<html xmlns=\"http://www.w3.org/1999/xhtml\"\rxml:lang=\"de\"\rlang=\"de\"\rdir=\"ltr\">\r<head>\r<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r";
            $this->head->bottom = "\r</head>\r<body>\r";
            return $this;
        }
        function replace($string,$array)
        {
            foreach($array as $key => $value)
            {
                $string = str_replace($key,$value,$string);
            }
            return "\r<script type=\"text/javascript\">\r".$string."\r</script>\r";
        }
        function classCSS($class,$name,$attr=null)
        {
            if(is_string($attr))
            {  
                $this->css->class["$class"]["$name"] = $attr;
            }
            
            if(is_array($name))
            {
                foreach($name as $key => $value)
                {
                    $this->css->class["$class"]["$key"] = $value;
                }
            }
            return $this;
        }
        function idCSS($id,$name,$attr=null)
        {
            if(is_string($attr))
            {
                $this->css->id["$id"]["$name"] = $attr;
            }
            if(is_array($name))
            {
                foreach($name as $key => $value)
                {
                    $this->css->id["$id"]["$key"] = $value;
                }
            }
            return $this;
        }
        private function generate_css()
        {
            if(count((array)$this->css->class) > 0)
            {
            foreach($this->css->class as $name => $css)
            {
                $this->css->main->$css = ".$name {\r";
                foreach($css as $key => $value)
                {
                    $this->css->main->$css .= "$key:$value;\r";
                }
                $this->css->main->$css .= "}\r";
            }
            }
            if(count((array)$this->css->id) > 0)
            {
            foreach($this->css->id as $name => $css)
            {
                $this->css->main->$css = "#$name {\r";
                foreach($css as $key => $value)
                {
                    $this->css->main->$css .= "$key:$value;\r";
                }
                $this->css->main->$css .= "}\r";
            }
            }
            if(count((array)$this->css->main) > 0)
            {
            $this->css->head = "<style type=\"text/css\">\r";
            foreach($this->css->main as $key)
            {
                $this->css->middle .= $key;
            }
            $this->css->bottom = "</style>";
            $this->css->viewcss = $this->css->head.$this->css->middle.$this->css->bottom;
            }
        }
        function addJS($file,$array=null)
        {
            if(is_array($array))
            {
                $this->js->src[] = $this->replace(file_get_contents(RELATIVE.$file),$array);
            }
            else
            {
                $this->js->src[] = "\r<script type=\"text/javascript\" src=\"$file\"></script>";
            }
            return $this;
        }
        function addCSS($file)
        {
            $this->css->src[] = "\r<link rel=\"stylesheet\" type=\"text/css\" href=\"$file\">";
            return $this;
        }
        private function generate_src()
        {
            if(count((array)$this->js->src) > 0)
            {
            foreach($this->js->src as $src)
            {
                $this->js->viewjs .= $src;
            }
            }
            if(count((array)$this->css->src) > 0)
            {
            foreach($this->css->src as $src)
            {
                $this->css->viewcss .= $src;
            }
            }
        }
        function send($mode=null)
        {
            $this->generate_css();
            $this->generate_src();
            $return .= $this->head->top;
            $return .= $this->css->viewcss;
            $return .= $this->js->viewjs;
            $return .= $this->head->bottom;
            switch($mode)
            {
                case "1":
                    return $return;
                break;

                case "2":
                    return $this->css->viewcss.$this->js->viewjs;
                break;
            }
            
            echo $return;
            ///
            $this->send = true;
        }
        
        function create_meta($name,$description) {
            return "<meta name=\"{$name}\" content=\"{$description}\">";
        }
        
        function search_optimizing($description,$keywords) {
            return $this->create_meta("description",$description)."\r"
                   .$this->create_meta("keywords",$keywords);
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