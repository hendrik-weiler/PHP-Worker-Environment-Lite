<?php
require_once 'app/librarys/Class_Auto_Load.php';
new Class_Auto_Load('app/librarys/PWEL');
new Class_Auto_Load('app/librarys/Collection');

PWEL_ROUTING::$error_controller = "error";
PWEL_ROUTING::$start_controller = "test";
$routing = new PWEL_ROUTING();
?>
