<?php
    class start extends PWEL_CONTROLLER {
        function index() {
            PWEL_COMPONENT_LAYOUT::disableLayout();
            $this->display("pwel");
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