<?php
require_once '../app/libraries/Class_Auto_Load.php';
new Class_Auto_Load('../app/models/html');
new Class_Auto_Load('../app/libraries/PWEL');
new Class_Auto_Load('../app/libraries/Collection');

PWEL_ROUTING::$error_controller = "error";
PWEL_ROUTING::$start_controller = "test";
PWEL_ROUTING::$namespace = "html";
new PWEL_ROUTING();
?>
