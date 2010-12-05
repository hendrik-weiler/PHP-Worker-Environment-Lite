<?php
require_once 'app/libraries/Class_Auto_Load.php';
new Class_Auto_Load('app/models');
new Class_Auto_Load('app/libraries/PWEL');
new Class_Auto_Load('app/libraries/Collection');

PWEL_ROUTING::$error_controller = "error";
PWEL_ROUTING::$start_controller = "test";
$routing = new PWEL_ROUTING();
?>
