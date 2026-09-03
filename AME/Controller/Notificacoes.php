<?php

/**
 * Description of Notificacoes
 *
 * @author Guilherme
 */
class Notificacoes implements IPrivateTO {
    
    public function whatsapp($param){
        $cel = Functions::cleanString($param[2]); 
        $nome = Functions::cleanString($param[3]); 
        $nasc = Functions::cleanString($param[4]); 
        $view = new TGui("whatsapp_message");         
        $view->addData("num", Functions::removeParentheses($cel));
        $view->addData("nome",$nome);
        $view->addData("nasc",$nasc);
        $view->renderize(APP_VIEW_MODAL,true);
    }
    
      public function confirmar(){
        $view = new TGui("confirma");
        $view->addData("title", "Confirmar consulta");
        $view->renderize(APP_VIEW);    
      }
    
      public function agenda($param) {
        $data = $param[2];    
        $view = new TGui("agenda");
        $view->addData("agendas", $agendas);
        $view->renderize(APP_VIEW);        
    }
    
    public function agendas($param) {
        $data = $param[2];
        
        $agendas = Daonotificacoes::getAgendas($data);
               
        $view = new TGui("agendasdodia");
        $view->addData("agendas", $agendas);
        $view->renderize(APP_VIEW_LIST,true);
        
    }
    
    public function painel() {
        session_start();
        if(isset($_SESSION['adm'])){
            $view = new TGui("cadpainelavisos");
            $view->renderize(APP_VIEW);
        } else {
            header("location: ".URL."Loginadm/login");
        }
    }
        
    public function saveaviso($param) {
        $iduser = $param[2];
        $msg = $param[3];
       
        Daonotificacoes::saveaviso($iduser, $msg);       
    }
    
    public function updatests($param) {
        $id = $param[2];
        $sts = $param[3];
        
        Daonotificacoes::updatests($id, $sts);
        
    }
    
    public function delaviso($param) {
        $id = $param[2];
        
        Daonotificacoes::delavisos($id);        
    }
    
    public function confirmnewmessage($param) {
        $idmsg = $param[2];
        $iduser = $param[3];
        
        Daonotificacoes::confirmnewmessage($idmsg, $iduser);        
    }  
    
}
