<?php
class doc extends PWEL_CONTROLLER {
    function startup() {
        print '   <html><head> <link rel="stylesheet" href="'.$this->validateLink('../docs/css/tuto.css').'" />
    <link type="text/css" rel="stylesheet" href="'.$this->validateLink('../docs/css/sh_rand01.min.css').'">
    <script type="text/javascript" src="'.$this->validateLink('../docs/js/sh_main.min.js').'"></script>
    <script type="text/javascript" src="'.$this->validateLink('../docs/lang/sh_html.min.js').'"></script>
    <script type="text/javascript" src="'.$this->validateLink('../docs/lang/sh_php.min.js').'"></script>
    <script type="text/javascript" src="'.$this->validateLink('../docs/js/jquery.js').'"></script>
    <script type="text/javascript" src="'.$this->validateLink('../docs/js/jquery.copy.min.js').'"></script>
    <script type="text/javascript" src="'.$this->validateLink('../docs/js/tuto.js').'"></script>
    <script type="text/javascript" src="'.$this->validateLink('/docCreater.js').'"></script></head><body>
    <input style="margin:8px; height:25px; width:150px;" value="To HTML" type="button" class="copy" />';
    }

    function index() {
        if(empty(PWEL_COMPONENT_ROUTE::$variables['param']))
            return;
        
        $doc = new doc_create(PWEL_COMPONENT_ROUTE::$variables['param']);
        print $doc->generateDoc();
    }

    public function __destruct() {
        print'</body></html>';
    }
}
?>
