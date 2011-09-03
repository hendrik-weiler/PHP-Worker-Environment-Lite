<?php
class myForm extends PWEL_CONTROLLER {
    function index() {
        $form = new Form();
        $this->variable = $form->open($this->validateLink("myForm/submit"),"post")
                 ->label("Name")->tfield("name")
                 ->label("Alter")->tfield("age")
                 ->button("Add")
                 ->close();

        print $this->variable;
    }

    function redirect() {
        $uri = new PWEL_URL();
        $uri->redirect("myForm/index");
    }
    
    public function __destruct() {
        PWEL_COMPONENT_LAYOUT::addVariables($this);
    }

}
?>