<?php
/**
 * View a image from src : sql or file
 *
 * @author Hendrik Weiler
 * @package Image_Embed
 */
class Image_Collector_Viewer {
    /**
     * contains Image_Embed_SQL object
     * @var string
     */
    private $sql;
    
    /**
     * contains the host for sql connection
     * @var string 
     */
    private $host;
    
     /**
     * contains the id for sql connection
     * @var string 
     */
    private $id;
    
    /**
     * contains the pw for sql connection
     * @var string 
    */
    private $pw;
    
    /**
     * contains the db for sql connection
     * @var string 
     */    
    private $db;

    /**
     * contains the filepath to the php file
     * @var string 
     */
    private $imageFile;

    /**
     * Sets class propertys for sql
     * @param Image_Embed_SQL $sql 
     */
    public function __construct(Image_Collector_SQL $sql=null) {
        if($sql instanceof Image_Collector_SQL) {
            $this->host = $sql->host;
            $this->db = $sql->db;
            $this->id = $sql->id;
            $this->pw = $sql->pw;
            $this->sql = $sql;
        }
    }

    /**
     * Sets the imagepath
     * @param string $path 
     */
    public function setImageFile($path) {
        $this->imageFile = $path;
    }

    /**
     * Show a image stored in php file
     * @param string $name 
     */
    public function view($name) {
        require_once $this->imageFile;
        @ob_end_flush();
        @ob_implicit_flush(true);
        
        header("Content-Type: image/".$image[$name]["Type"]);
	print base64_decode($image[$name]["Data"]);
    }

    /**
     * Show a image stored in sql database
     * @param string $name 
     */
    public function viewSQL($name) {
        @ob_end_flush();
        @ob_implicit_flush(true);
        //connect to db
        $sql = mysql_connect($this->host,$this->id,$this->pw);
        mysql_select_db($this->db,$sql);
        $row = $this->sql->searchName($name);
        
        header("Content-Type: image/".$row->Type);
	print base64_decode($row->Source);
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
