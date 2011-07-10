<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of doc_create
 *
 * @author Hendrik
 */
class doc_create
{
    private $reflection;

    private $head;

    private $chapter = array();
    
    private $doc_functions = array();
    
    public function __construct($class)
    {
        $this->reflection  = new ReflectionClass($class);
        $this->doc_functions = $this->reflection->getMethods();
        $this->createLegend();
        $this->createChapters();
    }

    private function createLegend()
    {
          $this->head = '<div class="legend">
                           <div class="smalltitle">Legend</div>
                          <ul>
                          <li><a href="#descrip">Description</a></li>
                          <li><a href="#functions">Functions</a></li>';
          foreach($this->doc_functions as $function) {
              if($function->isPublic()) {
                  $this->functionList .= '<li><a href="#' . $function->name . '">' . $function->name . '()</a></li>';
              }
          }
          $this->head .= $this->functionList . '</ul>
                        </div>';
    }

    private function createChapters()
    {
        $this->chapter[0] = '<div class="chaptertitle"><a name="descrip">Description</a></div>
<p>
<span class="clickTextarea">[Click to fill in]</span>
</p>';
        $this->chapter[1] = '<div class="chaptertitle"><a name="functions">Functions</a></div>
            <ul>' . str_replace('()</a>', '</a> <span><span class="clickTextarea">[Click to fill in]</span></span>', $this->functionList) . '</ul>';
        foreach($this->doc_functions as $function) {
            if($function->isPublic()) {
                $chapterContent = '<div class="chaptertitle"><a name="' . $function->name . '">' . $function->name . '</a></div>';
                $chapterContent .= '<blockquote><em>Description:</em>
                                    ' . $this->generateDescription($function) . '
                                    <p>Example:
                                    <pre class="sh_php code"><span class="clickTextarea">[Click to fill in]</span></pre>
                                    </blockquote>
                                    </div>';
                $this->chapter[] = $chapterContent;
            }
        }
    }

    private function generateDescription(ReflectionMethod $function)
    {
        $comment = $function->getDocComment();
        $comment = str_replace(array(
            '/**', '*/', '*'
        ), array(
            '', '', '<br />'
        ),$comment);
        return $comment;
    }

    public function generateDoc()
    {
        return $this->head . implode('', $this->chapter);
    }
}