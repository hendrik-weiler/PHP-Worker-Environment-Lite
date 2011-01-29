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
 * PHP Worker Environment Lite - Session Cookie Class
 *
 * Managing the sessions in cookie form
 *
 * @author Hendrik Weiler
 * @package PWEL
 * @category PWEL
 * @version 1.0
 * @since Release since 1.04
 */
class PWEL_COOKIE
{
    
    /**
     * Contains the name of the cookie
     * @var string
     */
    private $cookieName;

    /**
     * Contains all variables
     * @var array
     */
    private $cookieVars;
    
    /**
     * Contains the expire time
     * @var int 
     */
    private $cookieTime;

    /**
     * Sets the name of the cookie
     * @param string $name
     */
    public function __construct($name, $time=3600) {
        $this->cookieName = $name;
        $this->cookieTime = $time;
    }

    /**
     * Adds a variable to the cookie
     * @param string $key
     * @param string $value
     */
    public function addVar($key, $value) {
        $this->cookieVars[$key] = $value;
        
        setcookie ($this->cookieName . "[$key]", $value,time() + $this->cookieTime);
        return $this;
    }

    /**
     * Removes the variable from the cookie
     * @param string $key
     */
    public function removeVar($key) {
        unset($this->cookieVars[$key]);

        setcookie ($this->cookieName . "[$key]", '');
        unset($_COOKIE[$cookie][$key]);
        return $this;
    }

    /**
     * Returns the cookie variables
     * @return array
     */
    public function getCookieVariables()
    {
        if(!empty($_COOKIE[$this->cookieName]))
            $vars = $_COOKIE[$this->cookieName];
        else
            $vars = $this->cookieVars;
        
        return $vars;
    }

    /**
     * Deletes the cookie session
     */
    public function deleteCookieSession()
    {
        $cookie = $this->cookieName;
        if(is_array($this->getCookieVariables())) {
            foreach($this->getCookieVariables() as $key => $value) {
                setcookie ($cookie."[$key]", '');
            }
            unset($this->cookieVars);
            unset($_COOKIE[$cookie]);
        }
    }
}