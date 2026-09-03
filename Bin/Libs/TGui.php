<?php

/**
 * Description of TGui
 *
 * @author Guilherme
 */
class TGui {
    
    private $name;
    private $data;   
    
    function __construct($nome = null) {
        $this->name = $nome;       
        $this->data = array();
    }
    
    public function renderize($path = null,$onlyData = false,$header = false) {        
                
        if($onlyData == true) { 
            
            if (file_exists($path . "/" . $this->name . ".php")) {                
                if($header) {
                    header("Location: {$path}/{$this->name}.php");
                } else {
                    include_once $path . "/" . $this->name . ".php";  
                }
            } else {
                throw new Exception("View {$this->name} não encontrada!");
            }
            
        } else {
            
            include_once GUI_PATH.'header.php';            
           
            if (file_exists($path . "/" . $this->name . ".php")) {
                if($header) {
                    header("Location: {$path}/{$this->name}.php");
                } else {
                    include_once $path . "/" . $this->name . ".php";
                }                
            } else {
                throw new Exception("View {$this->name} não encontrada!");
            }            
            include_once GUI_PATH.'footer.php';
        }        
    }
    
    function getData($object = false) {
       if(!$object){
        return $this->data;
       } else {
           return isset($this->data[$object]) ? $this->data[$object] : false;
       }
    }

    function addData($name,$data) {
        $this->data[$name] = $data;
    }
}
