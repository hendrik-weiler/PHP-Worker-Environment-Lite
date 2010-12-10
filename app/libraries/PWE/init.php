<?php
/**
 * The Core of the framework
 * will be used in every oop class
 *
 * @author Hendrik Weiler
 */
class init extends cases {
   public $acc;
   public $plugins;
   public $css;
   public $js;


   public function __construct() {
            $args = func_get_args();
            $this->plugins = $args[0];
            $this->css = $args[1];
            $this->js = $args[2];
            //////////////////////////
            $this->initAll();
            $this->autoload();
            $this->defineHosts();
   }

   private function defineHosts() {
        $split = explode("/",$_SERVER["PHP_SELF"]);
        unset($split[count($split)-1]);
        foreach($split as $val)
        {
            $dir .= $val."/";
        }
       define(HOST,"http://".$_SERVER["HTTP_HOST"].$dir);
       define(RELATIVE,$_SERVER["DOCUMENT_ROOT"].$dir);   
   }
   /**
    * Initialize all classes into this class
    */
   function initAll()
   {
       require_once RELATIVE."content/classes/sql.php";


       $dir = opendir(RELATIVE."content/classes");
       while($file = readdir($dir))
       {
           if($file != ".." && $file != "." && $file != "init.php" && $file != "cases.php" && $file != "errors.php")
           {
                $split = explode(".", $file);
                require_once RELATIVE."content/classes/$file";
                if($file == "sql.php")
                { $this->sql = new sql(SQL_ID,SQL_PW,SQL_DB); }
                else
                { $this->$split[0] = new $split[0](); }
           }
       }
   }

   /**
    * Autoloads plugins,css,js files
    */
   function autoload()
   {
       if(is_array($this->plugins)) {
           foreach($this->plugins as $plugin)
           {
               require_once(RELATIVE."content/plugin/{$plugin[0]}/plugin_{$plugin[0]}_{$plugin[1]}.php");
           }
       }
       if(is_array($this->css)) {
           foreach($this->css as $css)
           {
               $this->load->includeJS($css);
           }
       }
       if(is_array($this->js)) {
           foreach($this->js as $js)
           {
               $this->load->includeJS($js);
           }
       }
   }

   /**
    * Class factory
    * - Receive a copy from a class -
    * @param <string> $class
    * @return sql 
    */
   function build($class)
   {
        if(!isset($class)) {
            throw new initError(message::$parameter,"build",help::$load_view_file);
        }       
       if($class == "sql")
       {
           return new sql(SQL_ID, SQL_PW, SQL_DB);;
       }
       return new $class();
   }
   
   /**
    * Control if errors will be displayed
    * @param <bool> $input 
    */
   public static function showError($input=true) {
       define(SHOW_ERROR,$input);
   }
   
   /**
    * Similiar to load->model()
    * but not recommended using this
    * @param <string> $name
    * @return name 
    */
   function getModel($name)
   {
        if(!isset($name)) {
            throw new initError(message::$parameter,"build",help::$load_view_file);
        }       
       include_once RELATIVE."content/model/$name.php";
       $split = explode("/",$name);
       if(is_array($split)) { $name = $split[count($split)-1]; }
       return new $name();
   }

   /**
    * Should check the language of the plugin which are could be add
    * (dont know if its needed or still working)
    * @param <string> $plugin
    * @param <string> $lang
    * @return <bool>
    */
   function plugin_lang_check($plugin,$lang)
   {
       $c = 0;
       foreach($this->plugins as $check)
       {
           $id = array_search($lang, $check[$c]);
           $c++;
       }
       echo $id;
       if(is_int($id))
       {
           if(isset($this->plugins[$id]))
           {
               return true;
           }
           else
           {
               return false;
           }
       }
       else
       {
           return false;
       }
   }
   
   /**
    * Easy var_dump
    * @param <anytypeof> $var 
    */
   public static function dump($var) {
       echo "<div style='border:1px solid black; background:#CCC; width:50%; padding:0.5em;'><h2><u>Result</u></h2><pre>";
       var_dump($var);
       echo "</pre></div>";
   }
}
    //PHP Worker Environment - a easy to use PHP framework
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