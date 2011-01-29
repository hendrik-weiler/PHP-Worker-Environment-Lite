<?php
/**
 * Translator Class
 *
 * Sets or gets translation by key
 *
 * @version 1.0
 * @category PWEL
 * @package Model
 */
class translator
{
    /**
     * Contains a eDB object
     * @var object
     */
    public $db;

    /**
     * Sets the edb path
     * 
     * @param string $db
     */
    public function __construct($db)
    {
        $this->db = new eDB($db);
        $this->db->create();
    }

    /**
     * Creates a translator table for a specific language
     *
     * @param string $lang
     */
    public function createTranslatorTable($lang)
    {
       $table = new eDB_Table("translation_$lang",array(
            'id','keyword','translation'
        ));
        $table->setPrimaryKey("translation_$lang",'id');
    }

    /**
     * Adds translation row to table
     *
     * @param string $lang
     * @param string $keyword
     * @param string $translation
     */
    public function addTranslations($lang,$keyword,$translation)
                    {
        new eDB_Insert("translation_$lang",array(
            'id' => '*','keyword' => $keyword, 'translation' => $translation
        ));
    }

    /**
     * Returns the translated text of the specific keyword
     *
     * @param string $keyword
     * @return string
     */
    public function translate($keyword)
    {
        $select = new eDB_Select(
            'translation_' . PWEL_COMPONENT_ROUTE::$variables["lang"], array(
            'keyword' => $keyword
        ));
        return $select->result[0]["translation"];
    }
}