<?php
/**
 * Handles sql login
 *
 * @author Hendrik Weiler
 * @package SQL
 */
class Sql_Login {
    /**
     * Contains the class for sql connection
     * @var object
     */
    private  $connectorClass;

    /**
     * Contains the 'accounts' table name
     * @var string
     */
    private $table_name;

    /**
     * Contains the column for username
     * @var string
     */
    private $column_username;

    /**
     * Contains the column for password
     * @var string
     */
    private $column_password;

    /**
     * Contains the column for session
     * @var string
     */
    private $column_session;

    /**
     * Contains the current login status
     * @var string
     */
    public $loginStatus;

    /**
     * Sets the connector class
     * @param object $connectorClass
     */
    public function __construct($connectorClass=null) {
        if(is_object($connectorClass))
            $this->connectorClass = $connectorClass;
    }

    /**
     * Handles all configurations for the login
     * 
     * @param string $table
     * @param array $configArray
     * @return bool/void
     */
    public function configColumnTable($table,$configArray) {
        if(is_array($configArray) && is_string($table)) {
            $this->table_name = $table;
            if(isset($configArray["username"]))
                $this->column_username = $configArray["username"];

            if(isset($configArray["password"]))
                $this->column_password = $configArray["password"];
            
            if(isset($configArray["session"]))
                $this->column_session = $configArray["session"];
        }
        else {
            return false;
        }
    }

    /**
     * Attempts a login
     * 
     * @param string $id
     * @param string $pw
     * @return bool/string
     */
    public function LoginAttempt($id,$pw) {
        if($this->validateInput($id) == false
        || $this->validateInput($pw) == false) {
            return false;
        }

        session_start();
        $sqlS = new Sql_Select($this->connectorClass);
        $sql = new Sql_Update($sqlS);
        $curr_session = session_id();
        $sql->changeValues(array(
           $this->column_session => $curr_session
        ));
        $sql->table($this->table_name)
             ->where(array(
            $this->column_username => $id,
            $this->column_password => md5($pw)
        ));
        session_unset();
        session_destroy();
        return $sql->update() != null ? false : $curr_session;
    }

    /**
     * Attempts a logout
     * 
     * @param string $session
     * @return bool
     */
    public function LogoutAttempt($session) {
        $sqlS = new Sql_Select($this->connectorClass);
        $sql = new Sql_Update($sqlS);
        $sql->changeValues(array(
           $this->column_session => "logout".md5($session)
        ));
        $sql->table($this->table_name);
        $sql->where(array(
           "$this->column_session" => $session
        ));
        return $sql->update() != null ? false : true;
    }

    /**
     * Checks if session is valid
     * 
     * @param string $session
     * @return bool
     */
    public function CheckLogin($session) {
        $sql = new Sql_Select($this->connectorClass);
        $sql->from($this->table_name)
            ->where(array(
                $this->column_session => $session
            ));
       return $sql->query() ? true : false;
    }

    /**
     * Returns the account data from current user
     *
     * @param string $session
     * @return resource/bool
     */
    public function GetUserData($session) {
        $sql = new Sql_Select($this->connectorClass);
        $result = $sql->from($this->table_name)
            ->where(array(
                $this->column_session => $session
            ))->query();
        return $result;
    }

    /**
     * Checks if the string is valid
     *
     * @param string $input
     * @return bool
     */
    private function validateInput($input) {
        $test_1 = preg_match("#(union|select|from|;|drop|like|--|shutdown|truncate|delete|update|insert|%)#i", $input);
        $search = array(chr(0),chr(1),chr(2),
                        chr(3),chr(4),chr(5),
                        chr(6),chr(7),chr(8),
                        chr(11),chr(12),chr(14),
                        chr(15),chr(16),chr(17),
                        chr(18),chr(19));
        $test_2 = str_replace($search,null,$input);
        $test_3 = preg_match("#(?:%E(?:2|3)%8(?:0|1)%(?:A|8|9)\w)#i", $input);
        if($test_1
        || $test_2 != $input
        || $test_3) {
            return false;
        }
        else {
            return true;
        }
    }
}
?>
