<?php
/**
*
* Made for object exchange between classes
*
*/
class store {
	public $attr;
	
	public function __construct($key=null,$value=null) {
		if(is_array($key)) {
			$this->multi_set($key);
		}
		else {
                    if(!is_null($key)) {
			$this->set($key,$value);
                    }
		}
	}
	
	public function set($key,$value) {
		$this->$key = $value;
		return $this;
	}
	
	public function multi_set($array) {
		foreach($array as $key => $value) {
			$this->$key = $value;	
		}
                return $this;
	}
        
        public function get($name) {
            return $this->$name;
        }
        
        public function each() {
            $return = $this;
            unset($return->attr);
            return $return;
        }
}
?>