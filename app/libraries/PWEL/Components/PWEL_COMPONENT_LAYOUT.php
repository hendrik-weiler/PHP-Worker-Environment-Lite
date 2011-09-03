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
 * PHP Worker Environment Lite Components - Layout
 *
 * Making possible set a layout
 *
 * @author Hendrik Weiler
 * @package PWEL_COMPONENT
 * @category PWEL
 * @version 1.0
 * @since Release since version 1.03
 */
class PWEL_COMPONENT_LAYOUT 
    extends PWEL_CONTROLLER
    implements PWEL_COMPONENT_INTERFACE
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
    public $_executionPosition = 'end';

    /**
     * Controls if the routing target will be called
     *
     * @var bool $_standAlone
     */
    public $_standAlone = true;

    /**
     * Contains the layoutfile
     * @var string
     */
    static $file;

    /**
     * Contains the visibility of the layout
     * @var bool
     */
    static $visible = true;

    /**
     * Contains all variables which will be used in layout
     * @var array/object
     */
    static $variables;

    /**
     * Sets the layout file
     * @var string $file
     */
    public function __construct($file)
    {
        self::$file = $file;
    }

    /**
     * Function which will be automaticly loaded
     * (used for load multiple functions or other stuff)
     */
    public function _initFunctions()
    {

    }

    /**
     * Adds all variables which will be used in the layout
     * @var object/array $variables
     */
    static function addVariables($variables)
    {
        if(is_array($variables) || is_object($variables)) {
            self::$variables = $variables;
        }
    }

    /**
     * Function which will be automaticly loaded
     * Execute the component
     */
    public function _execute() {
        $routing = new PWEL_ROUTING();
        $relativePath = $routing->requestRelativePath();
        if(self::$visible == true && PWEL_ROUTING::$controllerNotFound == false)  {
            PWEL_ROUTING::autoSearch('app/views/',self::$file);
            #var_dump(PWEL_ROUTING::$searchResult);
            $path = $relativePath.PWEL_ROUTING::$searchResult.self::$file;
            if(file_exists($path))
                $this->display (self::$file,(array)self::$variables);
            else
                throw new Exception
                ("File: <i>$path</i> doenst exist.<br /> <strong>Using '"
                 . "PWEL_ROUTING::\$namespaceRange' and missing adding "
                 . "directory to file?</strong>");
        }
    }

    /**
     * Disables the function on controller method
     */
    static function disableLayout()
    {
        self::$visible = false;
    }

    /**
     * Enables the function on controller method
     */
    static function enableLayout()
    {
        self::$visible = true;
    }
}