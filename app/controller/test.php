<?php
class test extends PWEL_CONTROLLER {
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
        $userDB = new userDB();
        $userDB->getDB();
        $this->weitererTest = "Testmännchen";
        $this->display("irgendwas");
    }
    
    function sql() {
        $sql = new Sql_Select(new PWEL_SQL());
        $sql->order("id->DESC")
            ->from("ee_category")
            ->query();
        foreach($sql->toForeach()->result as $object) {
            $item[] = new Items_Table_Row($object);
        }
        $table = new Items_Table();
        $table->setHead(new Items_Table_Row($sql->result[0],"key"));
        $table->setHeadFormat("<tr><th>&1</th><th>&2</th></tr>");
        $table->setFormat("<tr><td>&1</td><td>&2</td></tr>");
        $table->addRows($item);
        print $table->render();
    }
    
    function update() {
        $sql = new Sql_Select(new PWEL_SQL());
        $sql->order("id->DESC")
            ->from("accounts")
            ->where(array("ID"=>1))
            ->query();
        $update = new Sql_Update($sql);
        $update->table("accounts")
                     ->changeValues(array(
                     "ID"=>10,"username"=>"mumuside"
                     ))
                     ->where(array("username"=>"dadaside"))
                ->update();
    }
 
 
    function delete() {
       $url = new PWEL_URL();
       $vars = $url->locateUrlVariables(); 
       $select = new Sql_Select(new PWEL_SQL());
       $select->from("rpgmarket_coins")->where(array("ID"=>(int)$vars[2]))->query();
       $delete = new Sql_Delete($select);
       $delete->delete();
    }   
    
    function add() {
       $select = new Sql_Select(new PWEL_SQL());
       $select->from("accounts");
       $add = new Sql_Add($select);
       $add->addValues(array("ID"=>2,"username"=>"hallo","passwort"=>"abc123"))
            ->add();
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
