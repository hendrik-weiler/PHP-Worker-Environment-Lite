<?php
class test {
    private $user;
    function startup() {
        $this->user = array(
            "Frank" => array(
                "Frank","Walter","30"
            ),
            "Tim" => array(
                "Tim","Walter","25"
            )
        );
    }
    
    function index() {
        print 'hello';
    }
    
    function Frank() {
        $table = new Items_Table();
        $table->setHead(new Items_Table_Row(array("Name","Nachname","Alter")));
        $table->addRow(new Items_Table_Row($this->user["Frank"]));
        print $table->render();
    }
    
    function Tim() {
        $table = new Items_Table();
        $table->setHead(new Items_Table_Row(array("Name","Nachname","Alter")));
        $table->addRow(new Items_Table_Row($this->user["Tim"]));
        print $table->render();
    }
}

?>
