<?php
    /**
     * PHP Worker Environment Lite Plugins - HTML HELPER
     *
     * Before any content will be printed
     * all parts of the content can be manipulated
     *
     * @todo find a way to make it work
     * @author Hendrik Weiler
     * @package PWEL_COMPONENT
     */
class PWEL_PLUGIN_HTML_HELPER implements PWEL_PLUGIN_INTERFACE {
    const name = "PWEL_PLUGIN_HTML_HELPER";
    static $data;
    static $methodCalls;
    
    public function enable() {
        ob_start();
    }

    public function disable() {
        $this->containData();
        $this->makeChanges();
        ob_end_clean();
        $this->outputData(); 
    }

    public function containData() {
        $x = ob_get_contents();
        preg_match_all("#(.*)?(<(.*)>)?(.*)?#i", $x, $matches);
        foreach($matches[0] as $match) {
            if(!empty($match))
                $result[] = $match;
        }
        self::$data = $result;
    }

    /**
     * Call all methods which was stored in self::$methodCalls
     */
    public function makeChanges() {
        if(is_array(self::$methodCalls)) {
            print 1;
            foreach(self::$methodCalls as $method) {
                $methodName = $method["method"];
                switch(count($method["args"])) {
                    case 0:
                        $this->$methodName();
                        break;
                    case 1:
                        $this->$methodName($method["args"][0]);
                        break;
                    case 2:
                        $this->$methodName($method["args"][0],$method["args"][1]);
                        break;
                }
            }
        }
    }

    public function outputData() {
        if(!self::$data)
             throw new Exception ("Missing data - failed to call ".self::name."::containData() ?");

        print implode("\n",self::$data);
    }

    public function replaceContent($search,$replace) {
        self::$methodCalls[] = array(
            "method" => "replaceContent",
            "args" => array(
                $search,$replace
            )
        );
        if(isset(self::$data)) {
            foreach(self::$data as $key => $value) {
                self::$data[$key] = str_replace($search, $replace, $value);
            }
        }
    }

    public function disableCss() {
        self::$methodCalls[] = array(
            "method" => "disableCss",
            "args" => array(
                
            )
        );
        $this->unsetTags($this->searchElements("style"));
    }

    public function disableJs() {
        self::$methodCalls[] = array(
            "method" => "disableJs",
            "args" => array(

            )
        );
        $this->unsetTags($this->searchElements("script"));
    }

    public function deleteTag($tag) {
        self::$methodCalls[] = array(
            "method" => "deleteTag",
            "args" => array(
               $tag
            )
        );        
        $this->unsetTags($this->searchElements($tag));
    }

    public function deleteByClass($classname) {
    }

    public function deleteById($idname) {
        
    }

    public function addJs($url) {
        self::$methodCalls[] = array(
            "method" => "addJs",
            "args" => array(
                $url
            )
        );
        $c = new PWEL_CONTROLLER();
        $head = $this->searchElements("head");
        if($head) {
            foreach($head as $pos) {
                self::$data[$pos] = str_replace("</head>", $c->validateJS($url)."\r\n</head>", $this->data[$pos]);
            }
        }
    }

    public function addCss($url) {
        self::$methodCalls[] = array(
            "method" => "addCss",
            "args" => array(
                $url
            )
        );
        $c = new PWEL_CONTROLLER();
        $head = $this->searchElements("head");
        if($head) {
            foreach($head as $pos) {
                self::$data[$pos] = str_replace("</head>", $c->validateCss($url)."\r\n</head>", $this->data[$pos]);
            }
        }
    }

    private function searchElements($name,$mode="tag") {
        if(!self::$data)
            return false;
        
        $i = 0;
        $tillEnd = false;
        foreach(self::$data as $searchData) {
            switch($mode) {
                case "tag":
                    $searchPatternStart = "#(.*)?<$name(.*)>(.*)?#i";
                    $searchPatternEnd = "#(.*)?</$name>(.*)?#i";
                break;
                case "class":
                    $searchPatternStart = '#class="'.$name.'"#i';
                    $searchPatternEnd = "#(.*)?</$name>(.*)?#i";
                break;
                case "id":
                break;
                    
            }
            if($tillEnd == false) {
                if(preg_match($searchPatternStart, $searchData)) {
                    $dataPos[] = $i;
                    $tillEnd = true;
                }
                if(preg_match($searchPatternEnd, $searchData)) {
                    $tillEnd = false;
                }
            }
            else {
                if(preg_match($searchPatternEnd, $searchData)) {
                    $dataPos[] = $i;
                    $tillEnd = false;
                }
                else {
                    $dataPos[] = $i;
                }
            }
            ++$i;
        }
        return $dataPos;
    }

    private function unsetTags($dataPos) {
        if(is_array($dataPos)) {
            foreach($dataPos as $num) {
                unset(self::$data[$num]);
            }
        }
    }

    private function alphaData() {
        $alpha = "a";
        if(isset(self::$data)) {
            foreach (self::$data as $key => $value) {
                self::$data[$alpha] = self::$data[$key];
                $alpha++;
                unset(self::$data[$key]);
            }
        }
    }

    /**
     * Returns if content is given or not
     * @return bool
     */
    public function haveContent() {
        return (ob_get_length() > 0) ? true : false;
    }
}

    //PHP Worker Environment Lite - a easy to use PHP framework
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
