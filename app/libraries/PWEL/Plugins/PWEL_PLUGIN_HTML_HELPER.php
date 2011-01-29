<?php
/*
 * PHP Worker Environment Lite - a easy to use PHP framework
 * Copyright (C) 2010  Hendrik Weiler
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
/**
 * PHP Worker Environment Lite Plugins - HTML HELPER
 *
 * Before any content will be printed
 * all parts of the content can be manipulated
 *
 * @todo find a way to make it work
 * @author Hendrik Weiler
 * @package PWEL_COMPONENT
 * @version 0.5
 * @category PWEL
 * @since Release since version 1.05
 */
class PWEL_PLUGIN_HTML_HELPER implements PWEL_PLUGIN_INTERFACE
{
    
    /**
     * Contains the class as variable
     */
    const name = 'PWEL_PLUGIN_HTML_HELPER';

    /**
     * List of all data collected by enabling
     *
     * @var array
     */
    static $data;

    /**
     * Contains all method calls by the time
     * the plugin was enabled
     *
     * @var array
     */
    static $methodCalls;

    /**
     * Enabling the plugin
     */
    public function enable()
    {
        ob_start();
    }

    /**
     * Disable the plugin
     */
    public function disable()
    {
        $this->containData();
        $this->makeChanges();
        ob_end_clean();
        $this->outputData(); 
    }

    /**
     * Contain all data and store it
     */
    public function containData()
    {
        $x = ob_get_contents();
        preg_match_all('#(.*)?(<(.*)>)?(.*)?#i', $x, $matches);
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
            foreach(self::$methodCalls as $method) {
                $methodName = $method['method'];
                switch(count($method['args'])) {
                    case 0:
                        $this->$methodName();
                        break;
                    case 1:
                        $this->$methodName($method['args'][0]);
                        break;
                    case 2:
                        $this->$methodName($method['args'][0],$method['args'][1]);
                        break;
                }
            }
        }
    }

    /**
     * Output all data
     */
    public function outputData() {
        if(!self::$data)
             throw new Exception ('Missing data - failed to call ' . self::name . '::containData() ?');

        print implode("\n",self::$data);
    }

    /**
     * Replaces content from collected data
     *
     * @param string/array $search
     * @param string/array $replace
     */
    public function replaceContent($search,$replace) {
        self::$methodCalls[] = array(
            'method' => 'replaceContent',
            'args' => array(
                $search,$replace
            )
        );
        if(isset(self::$data)) {
            foreach(self::$data as $key => $value) {
                self::$data[$key] = str_replace($search, $replace, $value);
            }
        }
    }

    /**
     * Delete the css tags
     */
    public function disableCss() {
        self::$methodCalls[] = array(
            'method' => 'disableCss',
            'args' => array(
                
            )
        );
        $this->unsetTags($this->searchElements('style'));
    }

    /**
     * Delete the js tags
     */
    public function disableJs() {
        self::$methodCalls[] = array(
            'method' => 'disableJs',
            'args' => array(

            )
        );
        $this->unsetTags($this->searchElements('script'));
    }

    /**
     * Delete a specific tag
     *
     * @param string $tag
     */
    public function deleteTag($tag)
    {
        self::$methodCalls[] = array(
            'method' => 'deleteTag',
            'args' => array(
               $tag
            )
        );        
        $this->unsetTags($this->searchElements($tag));
    }

    /**
     * Delete tags by class name
     *
     * @param string $classname
     */
    public function deleteByClass($classname)
    {
    }

    /**
     * Delete tags by id
     *
     * @param string $idname
     */
    public function deleteById($idname)
    {
        
    }

    /**
     * Adds url tags
     *
     * @param string $url
     */
    public function addJs($url)
    {
        self::$methodCalls[] = array(
            'method' => 'addJs',
            'args' => array(
                $url
            )
        );
        $c = new PWEL_CONTROLLER();
        $head = $this->searchElements('head');
        if($head) {
            foreach($head as $pos) {
                self::$data[$pos] = str_replace('</head>', $c->validateCss($url)."\r\n" . '</head>', $this->data[$pos]);
            }
        }
    }

    /**
     * Adds css tag
     *
     * @param string $url
     */
    public function addCss($url)
    {
        self::$methodCalls[] = array(
            'method' => 'addCss',
            'args' => array(
                $url
            )
        );
        $c = new PWEL_CONTROLLER();
        $head = $this->searchElements('head');
        if($head) {
            foreach($head as $pos) {
                self::$data[$pos] = str_replace('</head>', $c->validateCss($url)."\r\n" . '</head>', $this->data[$pos]);
            }
        }
    }

    /**
     * Browse the current data after tags
     * and return an array of found matches
     *
     * @param string $name
     * @param string $mode
     * @return array
     */
    private function searchElements($name,$mode='tag') {
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
            } else {
                if(preg_match($searchPatternEnd, $searchData)) {
                    $dataPos[] = $i;
                    $tillEnd = false;
                } else {
                    $dataPos[] = $i;
                }
            }
            ++$i;
        }
        return $dataPos;
    }

    /**
     * Deletes all found tags
     * 
     * @param array $dataPos
     */
    private function unsetTags($dataPos)
    {
        if(is_array($dataPos)) {
            foreach($dataPos as $num) {
                unset(self::$data[$num]);
            }
        }
    }

    /**
     * Should sort the data
     */
    private function alphaData()
    {
        $alpha = 'a';
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
    public function haveContent()
    {
        return (ob_get_length() > 0) ? true : false;
    }
}