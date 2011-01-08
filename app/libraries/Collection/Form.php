<?php
/**
 * Contains all important form tags
 *
 * @author Hendrik Weiler
 * @package Form
 */
class Form {
    /**
     * Contains the 'form' tag
     * @var string
     */
    private $form_open;
    
    /**
     * Contains all textfield and areas
     * @var string
     */
    private $text;
    
    /**
     * Contains all buttons
     * @var string
     */
    private $button;
    
    /**
     * Contain the whole form
     * @var string
     */
    private $close;

    /**
     * Opens a form tag
     * @param <string> $action
     * @param <string> $type
     * @param <array> $attr
     * @return form
     */
    function open($action,$type="get",$attr=null)
    {
        if(is_array($attr))
        {
            foreach($attr as $key => $val)
            {
                $all_attr .= "$key=\"$val\" ";
            }
        }
        $this->form_open = "\r<form $all_attr action=\"$action\" accept-charset=\"UTF-8\" method=\"$type\">\r";
        return $this;
    }

    /**
     * Reset the variables in class
     * @return this 
     */
    function renew()
    {
        unset($this->form_open);
        unset($this->text);
        unset($this->button);
        unset($this->close);
        return new $this;
    }
    /**
     * Add html as string or html as a file
     * @param <string> $file
     * @return form
     */
    function addhtml($file)
    {       
        if(file_exists($file))
        {
            $this->text[] = file_get_contents($file);
        }
        else
        {
            $this->text[] = $file;
        }
        return $this;
    }
    /**
     * Creates textfield
     * for passwort
     * attr have to be array("type"=>"password")
     * @param <string> $name
     * @param <string> $value
     * @param <array> $attr
     * @return form 
     */
    function tfield($name,$value=null,$attr=null)
    {      
        if(is_array($attr))
        {
            if(in_array("password", $attr))
            { $type = "password"; }
            else
            { $type = "text"; }
            foreach($attr as $key => $val)
            {
                if($val != "password" && $val != "type")
                {
                    $all_attr .= "$key=\"$val\" ";
                }
            }
        }
        $this->text[] = "<input $all_attr type=\"$type\" name=\"$name\" value=\"$value\" />\r";
        return $this;
    }

    /**
     * Adds a <br />
     * @return form 
     */
    function br()
    {
        $this->text[] = "<br />";
        return $this;
    }

    /**
     * Adds a "label"
     * 
     *
     * @param <string> $value
     * @param <string> $mode
     * @return form
     */
    function label($value=null)
    {
        $this->text[] = '<span class="label_">'.$value.'</span>';
        return $this;
    }

    /**
     * Creates a listbox with given elements
     * Syntax = array("KEY"=>"VALUE")
     * If your gonna use sql results
     * you have to give as third argument
     * which col is value and which the key
     * Syntax = array("key"=>"name","value"=>"id")
     *
     * @param <string> $name
     * @param <array> $array
     * @param <array> $attr
     * @return form 
     */
    function listbox($name,$array,$attr=null)
    {
        if(is_array($attr) && !array_key_exists("key", $attr))
        {
            foreach($attr as $key => $val)
            {
                $all_attr .= "$key=\"$val\" ";
            }
        }
        $this->text[] = "<select $all_attr name=\"$name\">";
        if(is_array($array))
        {
            if(is_object($array[0]))
            {
                foreach($array as $key)
                {
                    if(array_key_exists("key", $attr) && array_key_exists("value", $attr))
                    {
                        $this->text[] = "<option value=\"".$key->$attr["value"]."\">".$key->$attr["key"]."</option>";
                    }
                }
            }
            else
            {
                foreach($array as $key => $val)
                {
                    if(is_int($object))
                    {
                         $key = $val;
                    }
                    $this->text[] = "<option value=\"$val\">$key</option>";
                }
            }
        }
        $this->text[] = "</select>";
        return $this;
    }

    /**
     * Adds a hidden field
     * @param <string> $name
     * @param <string> $value
     * @param <array> $attr
     * @return form
     */
    function hidden($name,$value=null,$attr=null)
    {     
        if(is_array($attr))
        {
            foreach($attr as $key => $val)
            {
                $all_attr .= "$key=\"$val\" ";
            }
        }
        $this->text[] = "<input $all_attr type=\"hidden\" name=\"$name\" value=\"$value\">";
        return $this;
    }

    /**
     * Adds a checkbox
     * @param <string> $name
     * @param <string> $value
     * @param <array> $attr
     * @return form 
     */
    function checkbox($name,$value,$attr=null)
    {
        if(is_array($attr))
        {
            foreach($attr as $key => $val)
            {
                $all_attr .= "$key=\"$val\" ";
            }
        }
        $this->text[] = "<input $all_attr type=\"checkbox\" name=\"$name\" value=\"$value\">";
        return $this;
    }

    /**
     * Adds a textarea
     * @param <string> $name
     * @param <string> $value
     * @param <array> $attr
     * @param <int> $height
     * @param <int> $cols
     * @return form
     */
    function tarea($name,$value=null,$attr=null,$height=35,$cols=4)
    {   
        if(is_array($attr))
        {
            foreach($attr as $key => $val)
            {
                $all_attr .= "$key=\"$val\" ";
            }
        }
        $this->text[] = "\r<textarea $all_attr cols=\"$height\" rows=\"$cols\" name=\"$name\">\r$value</textarea>\r";
        return $this;
    }

    /**
     * Adds a submit button
     * @param <string> $value
     * @param <array> $attr
     * @return form
     */
    function button($value,$attr=null)
    {
        if(is_array($attr))
        {
            foreach($attr as $key => $val)
            {
                $all_attr .= "$key=\"$val\" ";
            }
        }
        $this->button[] = "<input $all_attr type=\"submit\" value=\"$value\" />\r";
        return $this;
    }

    /**
     * Closes the form tag and merge all together
     * @return <string>
     */
    function close()
    {
        $this->close .= $this->form_open;
        if(count($this->text) > 0)
        {
        foreach($this->text as $val)
        {
            $this->close .= $val;
        }
        }
        foreach($this->button as $val) {
            $buttons .= $val;
        }
        $this->close .= $buttons;
        $return = $this->close;
        $this->renew();
        return $return."\r</form>";
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