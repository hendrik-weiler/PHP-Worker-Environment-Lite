<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Wapp_Window
 *
 * @author Hendrik
 */
class Wapp_Window
{

    protected $name = null;

    protected $x = 0;

    protected $y = 0;

    protected $width = 250;

    protected $height = 250;

    protected $content = null;

    /**
     * Set values to window object
     *
     * $options possible keys : x,y,width,height,content
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name, $options)
    {
        if(!is_string($name))
        {
            throw new Exception("First parameter must be a string.");
        }
        if(!is_array($options))
        {
            throw new Exception("Second parameter must be an array.");
        }

        $this->name = $name;

        foreach($options as $option => $value)
        {
            if(in_array($option, (array)$this))
            {
                $this->$option = $value;
            }
        }
    }
}
?>
