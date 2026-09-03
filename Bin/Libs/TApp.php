<?php

/**
 * Description of TApp
 *
 * @author Guilherme
 */

error_reporting(0);
ini_set('display_errors', 0 );

class TApp {
    
    const SLUG_CLASS = 'class';
    const SLUG_METHOD = 'method';
   
    private $to;
    private $method;
    private $params;
    
    public function __construct() {
        $url = isset($_REQUEST['url']) ? rtrim($_REQUEST['url'], "/") : false;
        if ($url) {
            
			$arr = explode("/", $url);
			if (isset($arr[0])) {
				$this->to = $this->getNameFromSlug($arr[0]);
			}
			if (isset($arr[1])) {
				$this->method = $this->getNameFromSlug($arr[1], self::SLUG_METHOD);
			}

			unset($arr[0]);
			unset($arr[1]);
			$this->params = $arr;
                                    
        } else {
            $this->to = DEFAULT_CONTROLLER;
            $this->method = DEFAULT_METHOD;
            $this->params = null;
        }
		
		//var_dump($this->to,$this->method);
    }
    
     private function getNameFromSlug($slug, $tipo = self::SLUG_CLASS) {
        $tmp = str_replace(" ", "", ucwords(implode(" ", explode("-", strtolower($slug)))));
        if ($tipo == self::SLUG_CLASS) {
            return $tmp;
        }
        return lcfirst($tmp);
    }
    
    public function execute($accessControl = true) {         
        $f = new Functions();
		
                       
            if(class_exists($this->to)){
                try {
                    $c = new $this->to();
                    if ($c instanceof IPrivateTO) {                    

                        if($accessControl) {
                            session_start();

                            if ($_SESSION['user'] != "authok") { 
								
                                echo "<meta HTTP-EQUIV='Refresh' CONTENT='0;URL=" . URL . "Controllogin/login'>";                           
                                session_destroy();
								
                            }

                            $f->sessionTime();

                        } 
                    }
                    if(method_exists($c, $this->method)){
                        if($this->params !== null){
                            $c->{$this->method}($this->params);
                        } else {
                            $c->{$this->method}();
                        }
                    } else {
                         throw new Exception(("Metodo {$this->method} inexistente para {$this->to}!"));
                    }
					
                } 
                catch (Exception $exc) {
                    throw new Exception($exc->getMessage());
                }                    
            } else {
                throw new Exception ("Classe {$this->to} inexistente!",404);
            }         
    }  
    
}