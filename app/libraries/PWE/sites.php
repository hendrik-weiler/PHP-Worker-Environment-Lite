<?php
/**
 * Controlls complete content display of the current site
 * --Controller Mode: oop
 *
 * @author Hendrik Weiler
 */
class sites extends cases {

    private $src;
    private $sublevel;
    /**
     * Sets the source for the sites
     * @param <string> $name
     * @param <string> $file
     * @return navi
     */
    
    public function src($file,$sublevel=1) {
        if(empty($file)) { 
               throw new unError(message::$parameter, "src",help::$load_view_file);
        }         
        $this->sublevel = $sublevel;
        $this->src = $file;
        if(!is_array($file))
        {
            $this->src = simplexml_load_file(RELATIVE."content/".$file);
        }
        return $this;
    }

    /**
     * Display the current content
     * @return <string>
     */
    function display()
    {
        foreach($this->src->link as $link) {
            if(strpos($link->url, $this->param($this->sublevel))) {
                if(file_exists(RELATIVE.$link->content)) {
                    if(!explode(".php", $link->content)) {
                        define(INCLUDE_AT, $link->content);
                    }
                    else {
                        define(INCLUDE_AT, false);
                        return file_get_contents(RELATIVE.$link->content);
                    }
                }
                else
                {
                    return '<font color="#ff3232"><strong>X</strong></font> '.$link->content;
                }
            }
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
