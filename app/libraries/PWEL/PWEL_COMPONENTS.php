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
 * PHP Worker Environment Lite - Component Class
 *
 * Managing components
 *
 * @author Hendrik Weiler
 * @package PWEL
 * @category PWEL
 * @version 1.0
 * @since Release since 1.03
 */
class PWEL_COMPONENTS
{
    /**
     * Array of all components
     * 
     * @var array
     */
    public static $components = array();

    /**
     * Handles correct function calls at component execution
     *
     * @var array
     */
    static $componentCalls = array(
        'route' => 'routeCurrentDir',
        'display' => 'displayController'
    );

    /**
     * Initalize all components
     *
     * @param object $components
     */
    public function __construct($components)
    {
        $routing = new PWEL_ROUTING();
        $routing->setHeader();
        
        $this->initComponents($components);
    }
    
    /**
     * Initialize components at startup
     * 
     * @var array $arguments
     */
    private function initComponents($arguments) {
        if(!is_array($arguments))
            return false;
        
        foreach($arguments as $arg) {
            if(is_object($arg)) {
                $r = new ReflectionClass($arg);
                if(in_array('PWEL_COMPONENT_INTERFACE', $r->getInterfaceNames()))
                    $this->components[$arg->_componentTarget][] = $arg;
            }
        }
        self::$components = $this->components;
        foreach(self::$componentCalls as $call => $x) {
            if($this->components[$call])
                $this->execComponents($call);
        }
    }

    /**
     * Prepare a component type for execution
     * 
     * @var string $componentTarget
     */
    private function prepareComponent($componentTarget)
    {
        if(empty($this->components))
            return false;
        
        foreach($this->components[$componentTarget] as $component) {
            if(method_exists($component, '_initFunctions')) {
               if(isset($component->_executionPosition)) {
                   $component->_initFunctions();
                   $return[$component->_executionPosition][] = $component;
               }
            }
            else {
               $component->_initFunctions();
               $return['start'][] = $component;
            }
        }
        return $return;
    }

    /**
     * Execute the components
     * 
     * @var string $typeOf
     */
    private function execComponents($typeOf) {
        $routing = new PWEL_ROUTING();
        $components = $this->prepareComponent($typeOf);
        //Execute components at start of function
        if($components['start']) {
            foreach($components['start'] as $component) {
                $component->_execute();
                if($component->_standAlone == false) {
                    $func = self::$componentCalls[$typeOf];
                    $routing->$func();
                } else {
                    if(PWEL_ROUTING::$routed == false)
                        $routing->routeCurrentDir();
                }
            }
        }
        /////////////////////////////////////////
        //Execute components at end of function
        if($components['end']) {
            foreach($components['end'] as $component) {
                if($component->_standAlone == false) {
                    $func = self::$componentCalls[$typeOf];
                    $routing->$func();
                } else {
                    if(PWEL_ROUTING::$routed == false)
                        $routing->routeCurrentDir ();
                }
                $component->_execute();
            }
        }
        ///////////////////////////////////////
    }
}