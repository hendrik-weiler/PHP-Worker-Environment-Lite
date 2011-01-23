<?php
//////////////////////////////////
///Autoloading of the libraries///
//////////////////////////////////
require_once '../app/libraries/Class_Auto_Load.php';
//Loading of PWEL classes
new Class_Auto_Load('../app/libraries/PWEL');
new Class_Auto_Load('../app/libraries/PWEL/Interfaces');
new Class_Auto_Load('../app/libraries/PWEL/Components');
new Class_Auto_Load('../app/libraries/PWEL/Plugins');
//Loading of other Classes(optional)
new Class_Auto_Load('../app/models/html');
new Class_Auto_Load('../app/libraries/Collection');
//////////////////////////////////
///    Initializing Framewok   ///
//////////////////////////////////
$pwel = new PWEL();
$pwel->configRouting(array(
    "start" => "loader",
    "error" => "error",
    "autosearch" => true,
    "namespace" => "tutorials",
    "namespacerange" => array(
        "tutorials","layout"
    )
));
$pwel->initialize(
             new PWEL_COMPONENT_ROUTE("param:guide")
             );
//////////////////////////////////
///      DEBUG-Mode/Helper     ///
//////////////////////////////////
//PWEL_ANALYZER::viewInfo();
?>