<?php
    class start extends PWEL_CONTROLLER {
        function index() {
            $this->display("pwel");
        }
        
        function autosearch() {
            print "\$this->display(\"autosearchtest\"); <br />He found the file in html/somesubfolder/<p>";
            $this->display("autosearchtest");
        }
    }
?>