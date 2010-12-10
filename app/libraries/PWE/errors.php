<?php
/**
 * Errorhandler for all classes
 *
 * @author Hendrik
 */
class sqlError extends Exception {
    public function __construct($message,$func,$info=null,$special=null) {
        parent::__construct($message);
        $this->special = $special;
        $this->sqlerror = mysql_error();
        $this->func = $func;
    }
}
class initError extends Exception {
    public function __construct($message,$func,$info=null) {
        parent::__construct($message);
        $this->func = $func;
        $this->info = $info;
    }
}
class loginError extends Exception {
    public function __construct($message,$func,$info=null) {
        parent::__construct($message);
        $this->func = $func;
        $this->info = $info;
    }
}
class unError extends Exception {
    public function __construct($message,$func,$info=null) {
        parent::__construct($message);
        $this->func = $func;
        $this->info = $info;
    }
}

class errors {
    static public $i;
    
    static function codeRange($file,$line) {
        $file = file($file);
        $return = null;
        for($i=($line-6);$i<($line-1);$i++) {
            if($i == ($line-2)) {
                $return .= "<font color='red'><strong>$i</strong>:  ".$file[$i]."</font>";
            }
            else {
                $return .= "<strong>$i</strong>:  ".$file[$i];
            }
        }
        return $return;
    }
    
    static function gen_error($errortype, $result) {
        if(APPLICATION_ENV == "production") {
            $trace = $result->getTraceAsString();
            $trace = explode("\n",$trace);
            foreach($trace as $i) {
                $return .= "{$i}\r\n";
            }   
            $fileContainer = "logs/".date("d\-m\-y").'.log';
            $filePointer = fopen($fileContainer,"a+");
            $logMsg = date("d\-m\-y H:i:s")."\r\n";
            $logMsg .= "---------------------\r\n";
            $logMsg .= "Error:".$errortype."\r\n";
            $logMsg .= "Message".$result->getMessage()."\r\n";
            $logMsg .= !empty($result->sqlerror) ? $result->sqlerror."\r\n" : "None\r\n";
            $logMsg .= "Used query:";
            $logMsg .= !empty($result->special) ? $result->special."\r\n" : "None\r\n";
            $logMsg .= "---------------------\r\n";
            $logMsg .= $return;
            $logMsg .= "---------------------\r\n";
            fputs($filePointer,$logMsg);
            fclose($filePointer);       
            return;
        }
        if(SHOW_ERROR == false) {
            return;
            //SHOW_ERROR = init->viewError()
        }
        $trace = $result->getTraceAsString();
        $return = null;
        $return .= "<u><h1>{$errortype}</h1></u>";
        $return .= "<h2>{$result->getMessage()}</h2>";
        $return .= "<h3><code>Function -> {$result->func}()</code></h3>";
        $trace = explode("\n",$trace);
        $return .= "<u>Tracing</u><ul>";
        foreach($trace as $i) {
            $return .= "<li>{$i}</li>";
        }
        $return .= "</ul>";
        $return .= "<li>File:<strong> {$result->getFile()}</strong></li>";
        $return .= "<li>Line:<strong> {$result->getLine()}</strong></li>";
        $return .= "<code><pre>".self::codeRange($result->getFile(), $result->getLine())."</pre></code></li>";
        if(isset($result->info)) {
            $return .= "<u>Information</u><br />{$result->info}";
        }
        if(isset($result->special)) {
            $return .= "<li>MySql Error Message:<strong> ".$result->sqlerror."</strong></li>";
            $return .= "<hr width='600px' align='left' /><li>Query Information:<br /><textarea cols='60' rows='8'>{$result->special}</textarea></li>";
        }
        $var["errormessage"] = $return;
        self::$i->load->view("error::errorHandler.html",$var);
    }
}
?>
