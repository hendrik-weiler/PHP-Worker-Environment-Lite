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
 * PHP Worker Environment Lite Components - Interface

 * @author Hendrik Weiler
 * @package PWEL_COMPONENT
 * @category PWEL
 * @version 1.0
 * @since Release since version 1.05
 */
interface PWEL_COMPONENT_INTERFACE
{
    /**
     * Identifies the class
     */
    const type = "component";
    
    /**
     * Function which will be automaticly loaded(used for load multiple functions or other stuff)
     */
    public function _initFunctions();

    /**
     * Function which will be automaticly loaded
     * Execute the component
     */
    public function _execute();
}