<?php
class loader extends PWEL_CONTROLLER {
    function startup() {
        $this->param = PWEL_COMPONENT_ROUTE::$variables['param'];
        $this->display("guide");
    }

    function index() {
        
    }
}
?>
