<?php
/**
 * Description of Pacientes
 *
 * @author Guilherme
 */
class Pacientes {
    
    public function index() {        
        if(AppController::checkSession()){
            $view = new TGui("pacientes");           
            $view->renderize(APP_VIEW,true);
        } else {
            header("location: ".URL."Loginadm/login");
        }          
    }            
    public function cad($param=0) {             
        if ($param[2] == 1) {
            $showBtn = true;
        } else {
            $showBtn = false;
        }
        session_start();
        if(isset($_SESSION['adm'])){
            $view = new TGui("pacientes"); 
            $view->addData("title", "Pacientes");
            $view->addData("show", $showBtn);
            $view->renderize('./View');
        } else {
            header("location: ".URL."Loginadm/login");
        }  
        
    }
    
    public function get($param) {        
        $x = $param[2]; 
        $view = new TGui("selecionar_pacientes"); 
               
        if(Functions::isDate($x)){              
            $pacs = Daopacientes::get('null', Functions::removeQuotes($x), 'null','null');
            $view->addData("buscado", Functions::BRdateFormat($x));
        }else if(Functions::validarCPF($x)){  
            $pacs = Daopacientes::get('null', 'null', 'null', Functions::cleanString($x));     
             echo json_encode($pacs, JSON_UNESCAPED_UNICODE);
             return;
        }else{
            $pacs = Daopacientes::get('null', 'null', Functions::removeQuotes($x),'null');
            $view->addData("buscado", Functions::removeQuotes($x));
        }
        $view->addData("pacs", $pacs);
        $view->renderize(APP_VIEW_MODAL,true);        
    }

    public function delete($params){
        $id = $params[2];
        $res = Daopacientes::delete($id);

        if($res != false){
          echo "<script>location.reload();</script>";
        }
    }
    
    public function logradouro($params){
        $logra = $params[2];
        $res =  Daopacientes::getAddress(null,$logra);  
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
    
    public function bairro($params){
        $bairro = $params[2];
        $res =  Daopacientes::getAddress(null,null,$bairro);  
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
    
    public function cep($params){
        $cep = $params[2];
        $res =  Daopacientes::getAddress($cep);  
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }  
    
     public function cadsus($params){
        $cpf = $params[2];
        $res =  CADSUS::consultaCADSUS($cpf);   
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($res);
    }   
}