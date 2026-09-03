<?php

/**
 * Description of Login
 *
 * @author Guilherme
 */
class Loginubs { 
    
    public function login() {
        $v = new TGui('login');       
        $v->renderize(GUI_PATH,true); 
    }
    
    public function logoutubs() {
        session_start();
        session_destroy();        
        header("location: ".URL."Loginubs/login"); 
    }
    
    public function ubs() {
        $view = new TGui("ubs");       
        $view->renderize(APP_VIEW,TRUE);
    }
       
    public function auth() { 
        
        $dataLog = filter_input_array(INPUT_POST,FILTER_DEFAULT);        
                
        $id = isset($dataLog['slct-user-login']) ? $dataLog['slct-user-login'] : FALSE;
        $pass = isset($dataLog['pass']) ? $dataLog['pass'] : FALSE;
        
        $user = Daouser::chkUser($id, $pass);
                       
        if($user) {
           
            session_start();

            $_SESSION['ubs'] = array('username'=>$user['nome'],'id'=>$user['id'],'cpf'=>$user['CPF'],'unidade'=>$user['id_unidade'],'nivel'=>$user['nivel']) ;   
                        
            if($user['id_unidade'] == null) {
                Functions::messages("msg",'Usuário sem unidade cadastrada!',"danger");
            } else {
                Functions::messages("header",URL."Loginubs/ubs");
            }
                  
        } else {
            
            session_start();            
            session_destroy();                 
            Functions::messages("msg",'Usuário não encontrado',"danger");
        } 
                       
    }
    
    public function logout($p = null) {
        if($p != null){
            $msgSts = $p[2];
        } else {
            $msgSts = null;
        }
                
        session_start();
        session_destroy();
        header("location: " . URL);
    }    

}
