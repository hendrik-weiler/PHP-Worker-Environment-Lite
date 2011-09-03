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
 * PHP Worker Environment Lite
 *
 * Initializing Framework
 *
 * @author Hendrik Weiler
 * @package PWEL
 * @category PWEL
 * @version 1.0
 * @since Release since 1.05
 */
class PWEL
{
    /**
     * Contains the current versionsnumber
     * @var int
     */
    static $version = 1.05;

    /**
     * Config informations are stored in it
     * @var array
     */
    static $config = array();

    /**
     * Contains all registered objects
     * @var object 
     */
    static $registeredObjects;

    /**
     * Contains the value if its configured
     * @var bool 
     */
    static $configured = false;

    /**
     * Registers objects to $registeredObjects
     * @param object $content
     */
    static function register($content)
    {
        self::$registeredObjects[strtolower(get_class($content))] = $content;
    }

    /**
     * Configure the routing in a function
     * @param array $config
     */
    public function configRouting(array $config)
    {
        if(!empty($config['error']) || !empty($config['start']))
        {
            PWEL_ROUTING::$error_controller = $config['error'];
            PWEL_ROUTING::$start_controller = $config['start'];
        } else {
            $this->autoConfigRouting();
        }

        if(!empty($config['autosearch']))
        {
            PWEL_ROUTING::$autoSearch = $config['autosearch'];
        }

        if(!empty($config['namespace']))
        {
            PWEL_ROUTING::$namespace = $config['namespace'];
        }

        if(!empty($config['namespacerange']))
        {
            PWEL_ROUTING::$namespaceRange = $config['namespacerange'];
        }
        self::$configured = true;
    }

    /**
     * Automaticly sets the needed configuration
     */
    public function autoConfigRouting()
    {
        PWEL_ROUTING::$error_controller = 'errorController';
        PWEL_ROUTING::$start_controller = 'startController';
        self::$configured = true;
    }

    /**
     * Loads the config file
     *
     */
    static function getConfig()
    {
        if(file_exists(PWEL_ROUTING::$relative_path . 'app/config.ini'))
            self::$config = parse_ini_file(PWEL_ROUTING::$relative_path . 'app/config.ini',true);
    }

    /**
     * Inizialize the cool framework
     */
    public function initialize()
    {
        PWEL_ROUTING::locateRelativePath();
        $this->getConfig();
        try {
            if(self::$configured == false) {
                $this->autoConfigRouting();
            }
            $routing = new PWEL_ROUTING();
            $routing->loadAutoInject();
            $components = new PWEL_COMPONENTS(func_get_args());
            $routing->start();
            $this->disablePlugins();
        }
        catch(Exception $e) {
           if(PWEL::$config['pwel']['status'] == strtolower('development')) {
               print '<div style="padding:5px;">';
               print '<strong>Message:</strong> ' . $e->getMessage() . '<br />';
               print '<strong>File:</strong> ' . $e->getFile() . '<br />';
               print '<strong>Line:</strong> ' . $e->getLine() . '<br />';
               print '<strong>Trace:</strong> <pre><code>' . $e->getTraceAsString() . '</code></pre><br />';
               print '</div>';
           } else {
               $routing = new PWEL_ROUTING();
               $routing->displayError();
           }
        }
    }

    /**
     * Registeres all plugins
     * 
     * @param object $plugin
     */
    public function plugin($plugin=null)
    {
        if(is_array($plugin)) {
            foreach($plugin as $plug) {
                if(is_object($plug)) {
                    self::register($plug);
                    $plug->enable();
                }
            }
        }
        if(is_object($plugin)) {
            self::register($plugin);
            $plugin->enable();
        }
        return $this;
    }

    /**
     * Disable all plugins at once
     */
    private function disablePlugins()
    {
        $c = new PWEL_CONTROLLER();
        if(isset($c->plugin)) {
            foreach($c->plugin as $plug) {
                $plug->disable();
            }
        }
    }
}
?>
