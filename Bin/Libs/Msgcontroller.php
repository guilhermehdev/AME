<?php
/**
 * Description of Msgcontroller
 *
 * @author Guilherme
 */
class Msgcontroller {
    private $msg;
    private $type;
     
    public function alert($msg,$type) {       
        $l = new Loads();
        
        $this->msg = $msg;
        $this->$type = $type;
              
       
       $l->css();       
       $l->js();
       
  echo "<script type=\"text/javascript\">	
	  	    
	    handleMSG('{$this->msg}',{$this->type});

        </script>";         
    }
}
