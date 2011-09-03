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
 * PHP Worker Environment Lite Components - Route
 *
 * Advanced component for routing
 * Making custom routes possible
 *
 * @author Hendrik Weiler
 * @package PWEL_COMPONENT
 * @category PWEL
 * @version 1.0
 * @since Release since version 1.04
 */
class PWEL_COMPONENT_ROUTE implements PWEL_COMPONENT_INTERFACE
{
    
    /**
     * Target where the injection will be set
     * @var string $_componentTarget
     */
    public $_componentTarget = 'route';

    /**
     * Position where the call
     * will be executed
     * (before or after 'route' target)
     *
     * @var string $_executionPosition
     */
    public $_executionPosition = 'start';

    /**
     * Controls if the routing target will be called
     *
     * @var bool $_standAlone
     */
    public $_standAlone = true;

    /**
     * Creates a array of default settings
     * @var array
     */
    private $setRoutes;

    /**
     * Contain all variables
     * @var array
     */
    static $variables;

    /**
     * Contains a array of accepting redirections
     * Example:
     * lang default = eng
     * The lang definitions are "de" and "eng"
     * If someone try to look for "ita" he would be redirected to "eng"
     * but with "de" and "eng" aswell
     *
     * With the range which can be set, the component accepting
     * "de" or "eng"
     * Scheme:
     * array( $namespace = array($param,$param,$param,$param))
     *
     * @var array
     */
    static $acceptRange = array();

    /**
     * Sets the default settings
     * String Syntax: variable:value/variable:value
     * @var string $newRoutes
     */
    public function __construct($newRoutes)
    {
        if(preg_match('#((.*):(.*)(/)?)+#i', $newRoutes)) {
            $splitDown_1 = explode('/', $newRoutes);
            foreach($splitDown_1 as $route) {
                $splitDown_2 = explode(':',$route);
                $this->setRoutes[$splitDown_2[0]] = $splitDown_2[1];
            }

        } else {
            //Error Output: Invalid syntax!
        }
    }

    /**
     * Prepare the variables for the final step
     */
    private function prepareVars()
    {
        $url = new PWEL_URL();
        $this->url_variables = $url->locateUrlVariables();
        $i = 0;
        foreach($this->setRoutes as $key => $value) {
            if(empty($this->url_variables[$i])) {
                $this->url_variables[$key] = $value;
                unset($this->url_variables[$i]);
            }
            else {
                $this->url_variables[$key] = $this->url_variables[$i];
                unset($this->url_variables[$i]);
            }
            ++$i;
        }
        if(class_exists('PWEL_ANALYZER'))
            PWEL_ANALYZER::$currentVariables = $this->url_variables;

        return $this->url_variables;
    }

    public function _initFunctions()
    {
        self::$variables = $this->prepareVars();
    }

    /**
     * Checks if the custom params can be accessed
     */
    private function checkValues()
    {
        $red = new PWEL_URL();
        $vars = self::$variables;
		$link = '';
		
        if(empty($this->setRoutes['class']))
            unset($vars['class']);

        if(empty($this->setRoutes['method']))
            unset($vars['method']);

        if(empty($this->setRoutes['param']))
            unset($vars['param']);

        if(is_array($vars)) {
            foreach($vars as $key => $value) {
                if($vars[$key] != $this->setRoutes[$key]) {
                    foreach(self::$variables as $innerkey => $var) {
                        if(!empty($var)) {
                            if($key == $innerkey) {
                               $link .= "{$this->setRoutes[$key]}/";
                            }
                            else {
                               $link .= "$var/";
                            }
                        }
                    }

                    if(isset(self::$acceptRange[PWEL_ROUTING::$namespace]) 
                    	&& !is_array(self::$acceptRange[PWEL_ROUTING::$namespace])) {
                        return;
                    }

                    if(isset(self::$acceptRange[PWEL_ROUTING::$namespace])
                    	&& !in_array($vars[$key], self::$acceptRange[PWEL_ROUTING::$namespace]))
                       $red->redirect($link);
                }
            }
        }
    }


    public function _execute()
    {
        $this->checkValues();
        $routing = new PWEL_ROUTING();
        if(!isset($this->setRoutes['class'])) {
            $check = $routing->checkIncludeControllerClass(PWEL_ROUTING::$start_controller);
            $this->displayController(new $check());
            PWEL_ROUTING::$controllerNotFound = false;
            PWEL_ROUTING::$routed = true;
            return true;
        }

        if(empty(self::$variables) || empty(self::$variables['class'])) {
            $check = $routing->checkIncludeControllerClass(PWEL_ROUTING::$start_controller);
            if($check) {

            } else {
                $check = $routing->checkIncludeControllerClass(PWEL_ROUTING::$error_controller);
                if(!$check) { return; }
            }
            $this->displayController(new $check(), 'startController');
            PWEL_ROUTING::$controllerNotFound = false;
        }
        else {
            PWEL_ROUTING::$controllerNotFound = false;
            $check = $routing->checkIncludeControllerClass(self::$variables['class']);
            if($check) {

            } else {
                $check = $routing->checkIncludeControllerClass(PWEL_ROUTING::$error_controller);
                PWEL_ROUTING::$controllerNotFound = true;
                if(!$check) { return; }
            }
            $this->displayController(new $check());
        }

        // Routing executed
        PWEL_ROUTING::$routed = true;
    }

    /**
     * Loads the method else send to error controller
     * @param class $class
     * @param string $mode
     */
    private function displayController($class,$mode='default')
                    {
        PWEL_ROUTING::$ControllerInfo['name'] = get_class($class);
        if(method_exists($class, 'startup')) {
            $class->startup();
        }
        switch($mode) {
            case 'startController':
                if(method_exists($class, 'index')) {
                    $class->index();
                } else {
                    // Error Output: No index defined!
                    throw new Exception('Method: Index must be defined in ' . get_class($class));
                }
                break;
            case 'default':
                if(isset($this->url_variables['method']) && method_exists($class, self::$variables['method'])) {
                    $method = $this->url_variables['method'];
                    $class->$method();
                } else {
                    if(method_exists($class, 'index')) {
                        $class->index();
                    }
                    else {
                        //Error Output: No index defined!
                        throw new Exception('Method: Index must be defined in ' . get_class($class));
                    }
                }
                break;
        }

    }
}