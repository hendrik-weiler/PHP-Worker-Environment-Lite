<?php
/**
 * Cases class for controll of controller settings
 * OOP or Url - based
 *
 * @author Hendrik Weiler
 */
class cases {
    private $mode;
    private $val;

    /**
     * Manuel set of var $mode
     * @param <string> $mode
     * @return cases
     */
    public function mode($mode)
    {
        if(empty($mode)) { 
               throw new unError(message::$parameter, "mode");
        }  
        $this->mode = $mode;
        return $this;
    }
    /**
     * Manuel set of var $val
     * @param <string> $val
     * @return cases 
     */
    public function val($val)
    {
        if(empty($val)) { 
               throw new unError(message::$parameter, "val");
        }         
        $this->val = $val;
        return $this;
    }

    /**
     * Main function by controller setting: default
     * Check if condition is true via using $_GET variables/or oop setting
     * @param <string> $mode
     * @param <string> $val
     * @return <bool>
     */
    public function check($mode=null,$val=null)
    {
        if(MODE_CONTROLLER == "oop") {
            $param = $this->param();
            if(count($param) >=2) { $i = 2; } else { $i = 1; }
            if(in_controller == true) { $i = 1; }
            if(empty($param[$i])) { $param[$i] = "index"; }
            if($param[$i] == $mode) {
                return true;
            }
            else {
                return false;
            }                
        }
        if($mode != "index" && $val == null)
        {
            if(isset($_GET["$mode"]))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        if($mode == "index" && count($_GET) == 1)
        {
                if(isset($_GET["doc"]))
                {
                    return true;
                }
                if(empty($_GET))
                {
                    return true;
                }
                else
                {
                    return false;
                }
        }
        else
        {
            if($mode=="index" && isset($_GET["doc"]))
            {
                return false;
            }
        }
        if(is_array($mode))
        {
            foreach($mode as $key => $val)
            {
                if($_GET["$key"] == $val)
                {
                    $proof[] = true;
                }
            }
            if(count($proof) == count($mode))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        if($mode != null && $val != null)
        {
            $this->mode = $mode;
            $this->val = $val;
        }
        if($_GET[$this->mode] == $this->val)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
   /**
    * Receive one or more url parameter
    * @param <int> $number
    * @return <string/array>
    */
   static public function parameter($number=null)
   {
       $url = $_SERVER["REQUEST_URI"];
       $split = explode("/",$url);
       if($_SERVER["SCRIPT_NAME"] != "/index.php") {
           unset($split[0]);         
       }
       $params = array_values($split); 
       if(empty($params[1])) { return null; }
       if(is_int($number))
       {
           return $params[$number];
       }
       else
       {
           return $params;
       }
   }
   /**
    * Alias of paramater()
    * @param <int> $number
    * @return <string/array>
    */
   function param($number=null)
   {
       return $this->parameter($number);
   }

   /**
    * Receive the control elements
    * @return <array/bool>
    */
   public function controller_oop()
   {
       $get_vars = $this->parameter();
       if(count($get_vars) != 0)
       {
            $result["class"] = $get_vars[1];
            $result["method"] = $get_vars[2];
            return $result;
       } else { return false; }
   }

   /**
    * Handles all links/refreshes in controller setting: oop
    * (old version)
    * @param <string> $url
    * @return <string>
    */
   function get_urls($url)
   {
        if(empty($url)) { 
               throw new unError(message::$parameter, "get_urls");
        }        
          $split = explode(".php",$_SERVER["PHP_SELF"]);
          $params = $this->param();
          $urls = $split[0].".php";
          for($i=0;$i<=count($params);$i++)
          {
              if($params[$i] == "")
              {
                  unset($params[$i]);
              }
          }
            if(count($params) >= 2) {
              unset($params[count($params)]);
            }
              $url = str_replace("../","",$url,$int);
              if($int != 0) {
                  for($i=1;$i<=$int;$i++)
                  {
                      unset($params[$i]);
                  }
              }
              foreach($params as $val)
              {
                      $urls .= "/$val";
              }
              var_dump($urls);
                $urls .= $url;
          $urls = str_replace("//","/",$urls);
          return $urls;
   }

   /**
    * Handles all links/refreshes in controller setting: oop
    * @param <string> $url
    * @deprecated
    * @return <string>
   */
   function get_url($url)
   {
        if(empty($url)) { 
               throw new unError(message::$parameter, "mode");
        }        
          $split = explode(".php",$_SERVER["PHP_SELF"]);
          $urls = $split[0].".php";
          $urls .= $url;
          $urls = str_replace("//", "/", $urls);
          return $urls;
   }

   /**
    * Sets a custom error <--
    * @param <boolean> $bool
    * @param <string> $errordoc
    */
   function setError($bool, $errordoc=null) {
        if(empty($bool)) { 
               throw new unError(message::$parameter, "setError");
        }        
       define(OOP_ERROR, $bool);
       if($errordoc != null) {
           define(OOP_ERROR_DOC, $errordoc);
       }
       return $this;
   }
   /**
    * --> On a error document(error folder) e.g index.html 
    * @param <string> $variable 
    */
   function on($variable) {
        if(empty($variable)) { 
               throw new unError(message::$parameter, "on");
        }        
       define(OOP_ERROR_ON, $variable);
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