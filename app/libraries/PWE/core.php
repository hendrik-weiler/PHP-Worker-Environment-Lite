<?php
/**
 * Core component which handle the viewing of the controller modes
 *
 * @author Hendrik Weiler
 */
class core {
    public static $pwe_sub_domain = false;
    public static $pwe_start_controller;
    
    public function pwe_default() {
        if(MODE_CONTROLLER == "default")
        {
            if(isset ($_GET["doc"]))
            {
                if(file_exists("content/controller/".$_GET["doc"].".php"))
                {
                    include_once("content/controller/".$_GET["doc"].".php");
                }
                else
                {
                    include_once("content/error/".ERROR404);
                }
            }
            else
            {
                if(file_exists("content/controller/".self::$pwe_start_controller.".php"))
                {
                    include_once("content/controller/".self::$pwe_start_controller.".php");
                }
                else
                {
                    include_once("content/error/".ERROR404);
                }
            }
        }        
    }
    
    public function pwe_routing() {
        $result = cases::parameter();
        $result["class"] = $result[1];
        $result["method"] = $result[2]; 
        if(MODE_CONTROLLER == "oop") 
        {            
             if(empty($result["class"])) { $result["class"] = self::$pwe_start_controller; }
             if(file_exists(RELATIVE."content/controller/".$result["class"].".php"))
             {
                 define(in_controller,false);
                 include_once RELATIVE."content/controller/".$result["class"].".php";
                 $myclass = new $result["class"]();
                 if(method_exists($myclass, $result["method"]))
                 {
                       echo $myclass->$result["method"]();
                       define(ERROR, false);
                 }
                 else
                 {
                     if($result["method"] == null && method_exists($myclass, "index"))
                     {
                         echo $myclass->index();
                         define(ERROR, false);
                     }
                     else
                     {
                         define(ERROR, true);
                         if(OOP_ERROR != true) {
                            include_once(RELATIVE."content/error/".ERROR404);
                         }
                     }
                 }
             }
             else
             {
                 define(in_controller,true);
                 if(file_exists(RELATIVE."content/controller/".self::$pwe_start_controller.".php")) {
                     include_once(RELATIVE."content/controller/".self::$pwe_start_controller.".php");
                     $index_class = START_CONTROLLER;
                     $myclass = new $index_class();
                     if(method_exists($myclass, $result["class"])) {
                         echo $myclass->$result["class"]();
                         define(ERROR, false);
                     }
                     else {
                         define(ERROR, true);
                         if(OOP_ERROR != true) {
                             include_once(RELATIVE."content/error/".ERROR404);
                         }
                     }
                 }
                 else {
                     define(ERROR, true);
                     if(OOP_ERROR != true) {
                        include_once(RELATIVE."content/error/".ERROR404);
                     }
                 }
             }
        }        
    }   
}

?>
