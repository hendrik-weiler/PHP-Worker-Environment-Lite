<?php
/**
 * Managing adding and deletion of images in sql database
 *
 * @author Hendrik Weiler
 * @package Image_Embed
 */
class Image_Collector_SQL {
    /**
     * contains host for sql connection
     * @var string
     */
    public $host;
    
    /**
     * contains id for sql connection
     * @var string
     */    
    public $id;
    
    /**
     * contains pw for sql connection
     * @var string
     */    
    public $pw;
    
    /**
     * contains db for sql connection
     * @var string
     */    
    public $db;

    /**
     * Connect to database at startup
     * @param string $host
     * @param string $id
     * @param string $pw
     * @param string $db 
     */
    public function __construct($host,$id,$pw,$db) {
        $this->host = $host;
        $this->id = $id;
        $this->pw = $pw;
        $this->db = $db;
        $sql = mysql_connect($host,$id,$pw);
        mysql_select_db($db,$sql);
    }

    /**
     * Adds a picture to database
     * @param Image_Embed_Embed $picture 
     */
    public function addPicture(Image_Collector_File $picture) {
        $src = $picture->imageSrc;
        $name = $picture->imageName;
        $extension = $picture->imageExtension;
        $extConv = array(
            'jpg' , 'jpeg'
        );
        $extension = preg_replace("/$extConv[0]/", $extConv[1], $extension);
        $query = "INSERT INTO  `{$this->db}`.`Images` (
        `ID` ,
        `Name` ,
        `Type`,
        `Source`
        )
        VALUES (
        NULL ,  '{$name}', '{$extension}',  '{$src}'
        );";
        if(mysql_query($query) == false) {
            print mysql_error();
        }
        print '<li>Picture '.$name.' was added successfully.</li>';
    }

    /**
     * Deletes a picture from database
     * @param string $name 
     */
    public function removePicture($name) {
        $row = $this->searchName($name);
        $query = "DELETE FROM `{$this->db}`.`Images` WHERE `Images`.`ID` = {$row->ID}";
        if(mysql_query($query) == false) {
            print mysql_error();
        }
        print '<li>Picture '.$name.' was deleted successfully.</li>';
    }

    /**
     * Search for imagename in the table
     * @param string $name
     * @return object
     */
    public function searchName($name) {
        $querySearch = "SELECT * FROM `Images` WHERE `Name` LIKE '".mysql_real_escape_string($name)."'";
        $resultSearch =  mysql_query($querySearch);
        if($resultSearch == false) {
            mysql_error();
            return false;
        }
        return mysql_fetch_object($resultSearch);
    }
}
    //Hendrik's Class Collection
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
