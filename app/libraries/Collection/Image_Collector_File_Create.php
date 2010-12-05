<?php
/**
 * Creates a php file with the added pictures
 *
 * @author Hendrik Weiler
 * @package Image_Embed
 */
class Image_Collector_File_Create {
    /**
     * Contains all added images
     * @var array
     */
    private $embedImages = array();
    
    /**
     * Path where the file will be saved
     * @var string
     */
    private $outputPath = "./";
    
    /**
     * Filename
     * @var string
     */
    private $outputName = "pictures.php";
    
    /**
     * Adds a picture to class
     * @param Image_Embed_Embed $image
     * @return bool/nothing
     */
    public function addPicture(Image_Collector_File $image) {
        if($image instanceof Image_Collector_File)
            $this->embedImages[] = $image;
        else
            return false;
    }
    
    /**
     * Adds multiple pictures to class
     * @param array $imageArray
     * @return bool/nothing
     */
    public function addPictures($imageArray) {
        if(!is_array($imageArray)) {
            return false;
        }
        foreach($imageArray as $image) {
            $this->addPicture($image);
        }
    }
    
    /**
     * Sets a custom output path
     * @param string $path 
     */
    public function setOutputPath($path) {
        $this->outputPath = $path;
    }
    
    /**
     * Sets a custom output filename
     * @param string $name 
     */
    public function setOutputName($name) {
        $this->outputName = $name;
    }

    /**
     * Create the outputfile
     */
    public function create() {
        $content = "<?php\r";
        foreach($this->embedImages as $image) {
           $content .= "//Image: {$image->imageName}//\r";
           $content .= "\$image[\"{$image->imageName}\"]['Type'] = \"{$image->imageExtension}\";\r";
           $content .= "\$image[\"{$image->imageName}\"]['Data'] = \"{$image->imageSrc}\";\r";
        }
        $content .= "\r?>";
        //Creates the file
        $path = $this->outputPath."/".$this->outputName;
        $path = str_replace("//","/",$path);
        $file = fopen($path,"w+");
        fputs($file, $content);
        fclose($file);
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
