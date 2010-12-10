<?php
/**
 * Second Importants class
 * get or view mvc elements
 *
 * @author Hendrik
 */
class load extends cases {
    private $view;
    private $includeJS;
    private $includeCSS;

    /**
     * Views a given element
     * @param <string> $file
     * @param <string> $var
     * @param <bool> $returns
     * @return <maybe a string>
     */
    public function view($file,$var=null,$returns=false) {
        if(!isset($file)) {
            throw new unError(message::$parameter,"view",help::$load_view_file);
        }
        $path = "view";
        //////////////////////////////////////////////
        $ssplit = explode("::",$file);
        if(count($ssplit) > 1) {
                if(empty($ssplit[0])) {
                    throw new unError("Empty path linking!","view",help::$load_view_01);
                }
                $path = $ssplit[0];
                $check = explode("/",$ssplit[0]);
                if(count($check) >= 2) {
                    $backwards = true;
                    $path = $check[1];
                }                
                $file = $ssplit[1];
        }
        $ext = explode(".",$file);
        if(count($ext)>=2) {
            $extension = $ext[count($ext)-1];
        }
        else {
            $extension = AUTO_EXTENSION;
        }
        //////////////////////////////////////////////
            if($extension == "php")
            {
                if(is_array($var)) {
                    foreach ($var as $key => $value)
                    {
                        if(ERROR == 1 && OOP_ERROR == 1) {
                            $var = OOP_ERROR_ON;
                            if(file_exists(RELATIVE."content/".OOP_ERROR_DOC)) {
                                $$var = file_get_contents(RELATIVE."content/".OOP_ERROR_DOC);
                            }
                            else {
                                $$var = file_get_contents(RELATIVE."content/error/".ERROR404);
                            }
                        }
                        $$key = $value;
                    }
                }
                if($backwards == true) {
                    include RELATIVE."{$path}/{$ext[0]}.{$extension}";
                    return;
                }
                include RELATIVE."content/{$path}/{$ext[0]}.{$extension}";
                return;
            }

            if(in_array($extension,array("html","xhtml"))) {
            $return = file_get_contents(RELATIVE."content/{$path}/{$ext[0]}.{$extension}");
            if($backwards == true) {
                $return = file_get_contents(RELATIVE."{$path}/{$ext[0]}.{$extension}");
            }
            ///////////////////
            if(isset($var))
            {
                if(is_array($var)) {
                    foreach($var as $value => $replace)
                    {
                        if(DEFAULT_VIEW_VAR != false) {
                            $return = str_replace(DEFAULT_VIEW_VAR.$value, $replace, $return);
                        }
                        else {
                            $return = str_replace($value, $replace, $return);
                        }
                    }
                }
            }
            if(is_array($this->includeJS)) {
                foreach($this->includeJS as $value)
                {
                    if($this->vali_url($value) == TRUE)
                    { $html = "<script src=\"$value\"></script>\r</head>\r"; }
                    else
                    { $html = "<script src=\"".HOST."content/js/$value.js\"></script>\r</head>\r"; }
                    $return = str_replace("</head>",$html, $return);
                }
            }
            if(is_array($this->includeCSS)) {
                foreach($this->includeCSS as $value)
                {
                    if($this->vali_url($value) == true)
                    { $html = "<link type=\"text/css\" rel=\"stylesheet\" href=\"$value\" />\r</head>\r"; }
                    else
                    { $html = "<link type=\"text/css\" rel=\"stylesheet\" href=\"".HOST."content/css/$value.css\" />\r</head>\r"; }
                    $return = str_replace("</head>",$html, $return);
                }
            }
            if(is_array($file)) { $filename = $file[0]; } else { $filename = $file; }
            $this->view->$filename = $return;
            if($returns == true) { return $this->view->$filename; }
            else { echo $this->view->$filename; }
            }

            if(!in_array($extension,array("php","html","xhtml"))) {
                include RELATIVE."content/view/{$file}";
            }
            
    }

    /**
     * laods a given model into this class
     * @param <string> $file
     * @return load
     */
    public function model($file)
    {
        if(empty($file)) {
            throw new unError(message::$parameter, "model", help::$load_model_file);
        }
        $split = explode("/",$file);
        require_once RELATIVE."content/model/$file.php";
        if(is_array($split))
        { $this->$split[(count($split)-1)] = new $split[(count($split)-1)](); }
        else
        { $this->$file = new $file(); }
        return $this;
    }

    /**
     * Include Javasript file into the view file
     * Working only by using default view mode
     * @param <string> $file
     * @return load 
     */
    public function includeJS($file)
    {
        if(empty($file)) {
            throw new unError(message::$parameter, "includeJS", help::$load_model_file);
        }
        $this->includeJS[] = $file;
        return $this;
    }

    /**
     * Include CSS file into the view file
     * Working only by using default view mode
     * @param <string> $file
     * @return load
     */
    public function includeCSS($file)
    {
        if(empty($file)) {
            throw new unError(message::$parameter, "includeCSS", help::$load_model_file);
        }
        $this->includeCSS[] = $file;
        return $this;
    }

    /**
     * Dont know really anymore why this function exists
     * but recommended using with default controller and view mode
     * @param <string> $url
     * @return <bool>
     */
    private function vali_url($url)
    {
        if(empty($url)) {
            throw new unError(message::$parameter, "model", help::$load_vali_url);
        }
        if(count(explode("http://",$url)) >= 2)
        {
            return true;
        }
        else
        {
            return false;
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