<?php

/**
 * Description of ErrorController
 *
 * @author Guilherme
 */
class ErrorController {   
    private $msg;
    private $trace;
    private $code;
    
    public function __construct(Exception $exc) {
        $f = new Functions();
        $l = new Loads();
        
        $this->msg = $exc->getMessage();
        $this->trace = $exc->getTraceAsString();
        $this->code = $exc->getCode(); 
              
       $l->css();       
       $l->js();
       
  echo "<script type=\"text/javascript\">		  	    
	    handleMSG('{$this->msg}','danger');
        </script>";         
    }  
} 