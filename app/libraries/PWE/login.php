<?php
/**
 * Login class - controlls most login situations easily
 *
 * @author Hendrik Weiler
 */
class login extends sql {
    private $id;
    private $pw;
    public $session;
    public $login;
    public $logout;
    public $sites;

    /**
     * Sets login variables
     * @param <string> $id
     * @param <string> $pw
     */
    public function __construct($id=null, $pw=null) {
        parent::__construct(SQL_ID, SQL_PW, SQL_DB);
        $this->id = $id;
        $this->pw = md5($pw);
    }

    /**
     * Sets login variables manually
     * @param <string> $id
     * @param <string> $pw
     * @return login
     */
    function set($id,$pw)
    {
        if(empty($id)) { 
               throw new loginError(message::$parameter, "set");
        }         
        $this->id = $id;
        $this->pw = md5($pw);
        return $this;
    }

    /**
     * Login
     * returns true or false 
     * @return <bool>
     */
    public function login()
    {
        $search = array(
            LOGIN_DB_USER=>"$this->id",
            LOGIN_DB_PWD=>"$this->pw"
                        );
        $result = $this->get_row($search, SQL_LOGIN_DB);
        if($result != false)
        {
            $this->login = true;
            session_start();
            session_regenerate_id();
            $this->setSave(LOGIN_DB_SESSION, session_id());
            $this->session = session_id();
            $sql = $this->save();
            return true;
        }
        else
        {
            $this->login = false;
            return false;
        }
    }

    /**
     * Logout
     * returns true or false
     * @param <string> $session
     * @return <bool>
     */
    public function logout($session=null)
    {
        if(MODE_LOGIN == "cookie") { $session = $_COOKIE["PHPSESSID"]; }
        $search = array(LOGIN_DB_SESSION=>$session);
        $result = parent::get_row($search, SQL_LOGIN_DB);
        if($result != false)
        {
            $this->login = false;
            $this->logout = true;
            $this->setSave(LOGIN_DB_SESSION,"LogoutNr:".rand(1,99999999999999));
            $this->session = $this->getSave(LOGIN_DB_SESSION);
            $this->save();
            setcookie("PHPSESSID", $value, 0);
            return true;
        }
        else
        {
            $this->login = true;
            $this->logout = false;
            return false;
        }
    }
    /**
     * Checks if login true or false
     * If login mode is cookie you can leave $session empty
     * @param <string> $session
     * @return <bool>
     */
   public function check_login($session=null)
    {
       if(MODE_LOGIN == "cookie")
       {
           if(isset($_COOKIE["PHPSESSID"])) {
            $session = $_COOKIE["PHPSESSID"];
           }
           else {
               return false;
           }
       }
        $search = array(LOGIN_DB_SESSION=>$session);
        $result = parent::get_row($search, SQL_LOGIN_DB);
        if($result != false)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    //Outdated function similiar to load->view()
    //due to outdate theres no custom error handling
    function set_templates($templates,$replacement,$langfile=null)
    {
        if(is_string($templates))
        {
            $templates = array($templates);
        }
        if(is_array($langfile))
        {
            foreach($langfile as $file)
            {
                if(file_exists($file))
                {
                $lang[] = file($file);
                }
                else
                {
                    echo "The file under \"$file\" doenst exist.";
                }
            }
        }
        if(is_string($langfile))
        {
                if(file_exists($langfile))
                {
                $lang[0] = file($langfile);
                }
                else
                {
                    echo "The file under \"$file\" doenst exist.";
                }
        }
        foreach($templates as $site)
        {
            ///Getname
            $name = explode("/",$site);
            $name2 = explode(".",$name[(count($name)-1)]);
            $sitename = $name2[0];
            ///
            ///
            $this->sites->$sitename = file_get_contents($site);
            if(!file_exists($site))
            {
                $this->sites->$sitename = "File doenst exist in \"$site\"";
            }
            foreach($replacement as $key => $value)
            {
                $this->sites->$sitename = str_replace("[".$key."]", $value, $this->sites->$sitename);
            }
                if(is_array($langfile))
                {
                    $int = 0;
                    for($i=0;$i<count($lang[$int]);$i+=1)
                    {
                            $this->sites->$sitename = str_replace("[".$i."]", $lang[$int][$i], $this->sites->$sitename);
                            $int+=1;
                    }
                }
                if(is_string($langfile))
                {
                    for($i=0;$i<count($lang[0]);$i+=1)
                    {
                            $this->sites->$sitename = str_replace("[".$i."]", $lang[0][$i], $this->sites->$sitename);
                            $int+=1;
                    }
                }
        }
    }
}
    //PHP Worker Environment - a easy to use PHP framework
    //Copyright (C) 2010  Hendrik Weiler
    //
    //This program is free software: you can redistribute it and/or modify
    //it under the terms of the GNU General Public License as published by
    //the Free Software Foundation, either version 3 of the License, or
    //(at your option) any later version.
    //
    //This program is distributed in the hope that it will be useful,
    //but WITHOUT ANY WARRANTY; without even the implied warranty of
    //MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    //GNU General Public License for more details.
    //
    //You should have received a copy of the GNU General Public License
    //along with this program.  If not, see <http://www.gnu.org/licenses/>.
?>