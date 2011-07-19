<?php
class XmlConstruct extends XMLWriter 
{ 

    /** 
     * Constructor. 
     * @param string $prm_rootElementName A root element's name of a current xml document 
     * @param string $prm_xsltFilePath Path of a XSLT file. 
     * @access public 
     * @param null 
     */
    private $LastParent = null; // last parent element in XML. reassigned after each successful element
    private $cur_index = null;

    public function __construct($prm_rootElementName, $prm_xsltFilePath=''){ 
        $this->openMemory(); 
        $this->setIndent(true); 
        $this->setIndentString(' '); 
        $this->startDocument('1.0', 'UTF-8'); 

        if($prm_xsltFilePath){ 
            $this->writePi('xml-stylesheet', 'type="text/xsl" href="'.$prm_xsltFilePath.'"'); 
        } 

        $this->startElement($prm_rootElementName); 
    } 

    /** 
     * Set an element with a text to a current xml document. 
     * @access public 
     * @param string $prm_elementName An element's name 
     * @param string $prm_ElementText An element's text 
     * @return null 
     */ 
    public function setElement($prm_elementName, $prm_ElementText){ 
        $this->startElement($prm_elementName); 
        $this->text($prm_ElementText); 
        $this->endElement(); 
    } 

    /** 
     * Construct elements and texts from an array and an object.
     * The array/object should contain an attribute's name in index part
     * and a attribute's text in value part. 
     * @access public 
     * @param array/object $prm_array/$prm_object Contains attributes and texts
     * @return null 
     */ 
    public function fromObject($prm_object){ 
      if(is_object($prm_object)){ 
	  $prm_array = get_object_vars($prm_object);
        $this->fromArray($prm_array);
      } 
    }

    public function fromArray($prm_array){
      if(is_array($prm_array)){
        foreach ($prm_array as $index => $element){
            $my_parent = $this->LastParent; // saving parent of this element
            $cur_parent = $this->setLastParentByIndex($index); // setting parent element for children

          if(is_object($element)){
            $this->startElement($this->cur_index);
            $this->fromObject($element);
            $this->endElement();
          } elseif (is_array($element)) {
            $this->startElement($this->cur_index);
            $this->fromArray($element);
            $this->endElement();
          }
          else {
              $this->setElement($this->cur_index, $element);
          }

          $this->setLastParentByName($my_parent); // setting LastParent back after closing this element to parent of this element
        }
      }
    }

    private function setLastParentByIndex($index){
        // small tweak to avoid XMLWriter startElement writing numeric XML tags - which rises exception
        if (!is_numeric($index)){
                $this->LastParent = $index;
                $this->cur_index = $index;
        } else {
                $this->cur_index = $this->LastParent . '-child';/*.$index*/;
                $this->LastParent = $this->cur_index;
        }
        return $this->LastParent;
    }

    private function setLastParentByName($name){
        $this->LastParent = $name;
    }

    /** 
     * Return the content of a current xml document. 
     * @access public 
     * @param null 
     * @return string Xml document 
     */ 
    public function getDocument(){ 
        $this->endElement(); 
        $this->endDocument(); 
        return $this->outputMemory(); 
    } 

    /** 
     * Output the content of a current xml document. 
     * @access public 
     * @param null 
     */ 
    public function output(){ 
        header('Content-type: text/xml'); 
        echo $this->getDocument();
    } 
   

} 

?>