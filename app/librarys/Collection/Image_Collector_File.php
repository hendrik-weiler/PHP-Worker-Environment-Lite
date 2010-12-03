<?php
/**
 * Contains all important image data
 *
 * @author Hendrik Weiler
 * @package Image_Embed
 */
class Image_Collector_File {
    /**
     * contains the name of the image
     * @var string
     */
    public $imageName;

    /**
     * contains the data of the image
     * @var string
     */    
    public $imageSrc;
    
    /**
     * contains the extension of the image
     * @var string
     */    
    public $imageExtension;
    
    /**
     * Read the image and set all variables
     * @param string $image 
     */
    public function __construct($image) {
        $file = fopen($image,"r");
        $content = base64_encode(fread($file,filesize($image)));
        $name = explode(".",$image);
        
        $this->imageSrc = $content;
        $this->imageName = $name[0];
        $this->imageExtension = $name[1];
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
