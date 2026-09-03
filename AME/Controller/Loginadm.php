<?php

/**
 * Description of Login
 *
 * @author Guilherme
 */
class Loginadm { 
    
    public function login() {
        session_start();
        session_destroy();
        $v = new TGui('loginadm');       
        $v->renderize(GUI_PATH,true); 
    }
    
    public function logoutadm($param) {               
        $id = $param[2];
        Maincontroller::doQuery("UPDATE usuarios SET islogged=:BOOL WHERE id=:ID",['BOOL'=>0,'ID'=>$id]);
        session_start();
        session_destroy();        
        header("location: ".URL."Loginadm/login"); 
    }
    
    public function adm() {
        
        if(AppController::checkSession()){
            $view = new TGui("dashboard");
            $view->addData("title", 'Dashboard');
            $view->renderize(APP_VIEW);
        }else{
            header("location: ".URL."Loginadm/login");
        }        
    }
       
    public function auth() {         
        $dataLog = filter_input_array(INPUT_POST,FILTER_DEFAULT);        
        header("Access-Control-Allow-Credentials: true");
        $id = isset($dataLog['slct-user-login']) ? $dataLog['slct-user-login'] : FALSE;
        $pass = isset($dataLog['pass']) ? $dataLog['pass'] : FALSE;
        
        $user = Daouser::chkUser($id, $pass);
        
        Maincontroller::doQuery("UPDATE usuarios SET islogged=:BOOL WHERE id=:ID",['BOOL'=>1,'ID'=>$id]);
                       
        if($user) {                      
            session_start();
            $_SESSION['adm'] = array('username'=>$user['nome'],'id'=>$user['id'],'cadastros'=>$user['cadastros'],'cadpac'=>$user['cadpac'],'retornos'=>$user['retornos'],'exc_retorno'=>$user['exc_retorno'],'oci'=>$user['oci'],'notificacao'=>$user['notificacao'],'impressos'=>$user['impressos']) ;         
            Functions::messages("header",URL."Loginadm/adm");
                                         
        } else {  
            Maincontroller::doQuery("UPDATE usuarios SET islogged=:BOOL WHERE id=:ID",['BOOL'=>0,'ID'=>$id]);
            session_start();            
            session_destroy();                 
            Functions::messages("msg",'Usuário não encontrado',"danger");
        } 
                       
    }   
}