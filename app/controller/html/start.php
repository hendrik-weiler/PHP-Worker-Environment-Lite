<?php
    class start extends PWEL_CONTROLLER {
        function index() {
            PWEL_COMPONENT_LAYOUT::disableLayout();
            switch(PWEL_COMPONENT_ROUTE::$variables["lang"]) {
                case "de":
                    $file = "pwel_de";
                break;
                case "eng":
                    $file = "pwel_eng";
                break;
                default:
                    $file = "pwel_eng";
                break;
            }
            $this->display($file);
        }
        
        function autosearch() {
            PWEL_COMPONENT_LAYOUT::disableLayout();
            print "\$this->display(\"autosearchtest\"); <br />He found the file in html/somesubfolder/<p>";
            $this->display("autosearchtest");
        }
        
        function layout() {
            $this->variable = "Hello, im a layout!";
            
        }
        
        public function __destruct() {
            PWEL_COMPONENT_LAYOUT::addVariables($this);
        }

    }
?>