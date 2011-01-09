<?php
/**
 * PHP Worker Environment Lite - Automatic Injection
 * 
 * All methods will be auto loaded at every site call
 *
 * @author Hendrik Weiler
 * @package PWEL
 */
class AutoInject extends PWEL_CONTROLLER {
    function configLogin() {
         $login = new Sql_Login(new PWEL_SQL());
         $login->configColumnTable("accounts",array(
                "username" => "username",
                "password" => "password",
                "session" => "session"
         ));
         PWEL_ROUTING::register($login);
    }

    function checkLogin() {
        $login = $this->getRegister("sql_login");

        $session = new PWEL_COOKIE("session");
        $vars = $session->getCookieVariables();

        if($login->CheckLogin($vars["SID"])) {
            $login->loginStatus = true;
        }
        else {
            $login->loginStatus = false;
        }
        PWEL_ROUTING::register($login);
    }
}
?>
