<?php
    /**
     * PHP Worker Environment Lite - PWE Support
     * 
     * Make it possible to use PWE classes
     *
     * @author Hendrik Weiler
     * @package PWEL
     */
    class PWEL_PWE_SUPPORT {
        public $classes;
        
        function __construct() {
            define(MODE_CONTROLLER,"oop");
            define(SQL_ID,PWEL_ROUTING::$config["sql"]["username"]);
            define(SQL_ID,PWEL_ROUTING::$config["sql"]["password"]);
            define(SQL_ID,PWEL_ROUTING::$config["sql"]["dbname"]);
        }
        
        function getClasses() {
               $dir = opendir(PWEL_ROUTING::$relative_path."app/libraries/PWE");
               while($file = readdir($dir))
               {
                   if($file != ".." && $file != "." && $file != "init.php" && $file != "cases.php" && $file != "errors.php")
                   {
                        $split = explode(".", $file);
                        if($file == "sql.php")
                        { $this->classes->sql = new sql(SQL_ID,SQL_PW,SQL_DB); }
                        else
                        { $this->classes->$split[0] = new $split[0](); }
                   }
               }
               return $this->classes;
        }
    }
?>