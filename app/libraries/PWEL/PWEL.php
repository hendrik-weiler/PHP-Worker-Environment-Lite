<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PWEL
 *
 * @author Hendrik
 */
class PWEL {
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
     * Registers objects to $registeredObjects
     * @param object $content
     */
    static function register($content) {
        self::$registeredObjects[strtolower(get_class($content))] = $content;
    }

    /**
     * Configure the routing in a function
     * @param array $config
     */
    public function configRouting(array $config) {
        PWEL_ROUTING::$error_controller = $config["error"];
        PWEL_ROUTING::$start_controller = $config["start"];
        PWEL_ROUTING::$autoSearch = $config["autosearch"];
        PWEL_ROUTING::$namespace = $config["namespace"];
        PWEL_ROUTING::$namespaceRange = $config["namespacerange"];
    }

    /**
     * Loads the config file
     *
     */
    static function getConfig() {
        if(file_exists(PWEL_ROUTING::$relative_path."app/config.ini"))
        self::$config = parse_ini_file(PWEL_ROUTING::$relative_path."app/config.ini",true);
    }

    /**
     * Inizialize the cool framework
     */
    public function initialize() {
        PWEL_ROUTING::locateRelativePath();
        $this->getConfig();
        try {
            $routing = new PWEL_ROUTING();
            $routing->loadAutoInject();
            $components = new PWEL_COMPONENTS(func_get_args());
            $routing->start();
            $this->disablePlugins();
        }
        catch(Exception $e) {
           if(PWEL::$config["pwel"]["status"] == strtolower("development")) {
               print '<div style="padding:5px;">';
               print "<strong>Message:</strong> ".$e->getMessage()."<br />";
               print "<strong>File:</strong> ".$e->getFile()."<br />";
               print "<strong>Line:</strong> ".$e->getLine()."<br />";
               print "<strong>Trace:</strong> <pre><code>".$e->getTraceAsString()."</code></pre><br />";
               print '</div>';
           }
           else {
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
    public function plugin($plugin=null) {
        if(is_array($plugin)) {
            foreach($plugin as $plug) {
                if(is_object($plug))
                    self::register($plug);
                    $plug->enable();
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
    private function disablePlugins() {
        $c = new PWEL_CONTROLLER();
        if(isset($c->plugin)) {
            foreach($c->plugin as $plug) {
                $plug->disable();
            }
        }
    }
}
?>
