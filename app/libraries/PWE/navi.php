<?php
/**
 * Creates and controlls a navigation
 *
 * @author Hendrik Weiler
 */
class navi extends cases {
    private $links;
    public $indexname;

    /**
     * Sets the source for the navi
     * @param <string> $name
     * @param <string> $file
     * @return navi
     */
    public function src($name,$file) {
        if(empty($name)) { 
               throw new unError(message::$parameter, "src");
        }         
        $src = $file;
        if(!is_array($file))
        {
            $src = simplexml_load_file(RELATIVE."content/".$file);
        }
        $this->generate_navi($src,$name);
        return $this;
    }

    /**
     * Generate the complete navi
     * @param <object/array> $src
     * @param <string> $name
     */
    private function generate_navi($src,$name)
    {      
        $check = true;
        if(!is_object($src)) {
            foreach($src as $linkname => $href) {
            if($check == true) { $this->indexname[$name] = $linkname; $check = false; }
            $this->links[$name] .= "<ul>";
                if(is_array($href)) {
                    $this->links[$name] .= '<li><a class="navi_links" href="'.$href["this"].'">'.$linkname.'</a></li>';
                    $this->links[$name] .= "<ul>";
                    foreach($href[0] as $linkname => $href) {
                        $this->links[$name] .= '<li><a class="navi_links" href="'.$href.'">'.$linkname.'</a></li>';
                    }
                    $this->links[$name] .= "</ul>";
                }
                else {
                    $this->links[$name] .= '<li><a class="navi_links" href="'.$href.'">'.$linkname.'</a></li>';
                }
            $this->links[$name] .= "</ul>";
            }
        }
        else {
            $this->links[$name] .= "<ul>\r";
            for($i=0;$i<count($src);$i++) {
                if($check == true) { $this->indexname[$name] = $src->link[$i]->name; $check = false; }  
                $this->links[$name] .= '<li>'."\r".'<a class="navi_links" href="'.url::validate($src->link[$i]->url,true).'">'.$src->link[$i]->name.'</a>'."\r".'</li>';
                if(isset($src->link[$i]->sub)) {
                    $this->links[$name] .= "<ul>\r";
                    foreach($src->link[$i]->sub->sublink as $link) {
                        $this->links[$name] .= '<li>'."\r".'<a class="navi_links" href="'.url::validate($link->url,true).'">'.$link->name.'</a>'."\r".'</li>'."\r";
                    }
                    $this->links[$name] .= "</ul>\r";
                }
            }
            $this->links[$name] .= "</ul>";
        }
    }

    /**
     * View the navi
     * @param <string> $name
     * @return <string>
     */
    public function view($name) {
        if(empty($name)) { 
               throw new unError(message::$parameter, "view");
        }         
        return $this->links[$name];
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