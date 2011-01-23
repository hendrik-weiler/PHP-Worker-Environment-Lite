<?php
class translator {
    public $db;
    
    public function __construct($db) {
        $this->db = new eDB($db);
        $this->db->create();
    }

    public function createTranslatorTable($lang) {
       $table = new eDB_Table("translation_$lang",array(
            "id","keyword","translation"
        ));
        $table->setPrimaryKey("translation_$lang","id");
    }

    public function addTranslations($lang,$keyword,$translation) {
        new eDB_Insert("translation_$lang",array(
            "id" => "*","keyword" => $keyword, "translation" => $translation
        ));
    }

    public function translate($keyword) {   
        $select = new eDB_Select("translation_".PWEL_COMPONENT_ROUTE::$variables["lang"],array(
            "keyword" => $keyword
        ));
        return $select->result[0]["translation"];
    }
}
?>
