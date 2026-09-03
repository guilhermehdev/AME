<?php
/**
 * Description of Pacientes
 *
 * @author Guilherme
 */
class Servidores {
    
    public function index() {
        
        if(AppController::checkSession()){
            $view = new TGui("cadastro_servidores");           
            $view->renderize(APP_VIEW_MODAL,true);
        } else {
            header("location: ".URL."Loginadm/login");
        }  
        
    }
    
    public function cad() {
        session_start();
        if(isset($_SESSION['adm'])){
            $view = new TGui("servidores"); 
            $view->addData("title", "Profissionais");
            $view->renderize('./View');
        } else {
            header("location: ".URL."Loginadm/login");
        }  
        
    }
    
    public function cadUsuarios() {
        session_start();
        if(isset($_SESSION['adm'])){
            $view = new TGui("usuarios"); 
            $view->addData("title", "Usuários");
            $view->renderize('./View');
        } else {
            header("location: ".URL."Loginadm/login");
        }  
        
    }
    
    public function get($param) {        
        $x = $param[2];                
                
        $view = new TGui("selecionar_servidores"); 
        
        if(is_numeric(Functions::cleanString($x))){
            $pacs = Daoservidores::get(Functions::cleanString($x), 'null');
            $view->addData("buscado", $x);
        }else{        
            $pacs = Daoservidores::get('null', Functions::removeQuotes($x));
            $view->addData("buscado", Functions::removeQuotes($x));
        }
        $view->addData("pacs", $pacs);
        $view->renderize(APP_VIEW_MODAL,true);
        
    }
    
    public function getUsuarios($param) {        
        $x = $param[2];                
                
        $view = new TGui("selecionar_usuarios"); 
        
        if(is_numeric(Functions::cleanString($x))){
            $users = Daouser::get(Functions::cleanString($x), 'null');
            $view->addData("buscado", $x);
        }else{        
            $users = Daouser::get('null', Functions::removeQuotes($x));
            $view->addData("buscado", Functions::removeQuotes($x));
        }
        $view->addData("users", $users);
        $view->renderize(APP_VIEW_MODAL,true);
        
    }

    public function delete($params){
        $id = $params[2];
        $res = Daoservidores::delete($id);

        if($res != false){
          echo "<script>location.reload();</script>";
        }
    }
    
    public function deleteUsuarios($params){
        $id = $params[2];
        $res = Daoservidores::delete($id);

        if($res != false){
          echo "<script>location.reload();</script>";
        }
    }
    
}
