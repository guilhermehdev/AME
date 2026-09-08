<?php

/**
 * Description of Login
 *
 * @author Guilherme
 */
class Loginadm {

    /**
     * @throws Exception
     */
    public function login(): void
    {
        session_start();
        session_destroy();
        $v = new TGui('loginadm');
        try {
            $v->renderize(GUI_PATH, true);
        } catch (Exception $e) {

        }
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
    
    public function recuperar($param){                     
        $id = $param[2];        
        $userData = Daouser::get(null,null,$id);        
        $provisoria = $userData[0]['senha_provisoria'];    
        
        $v = new TGui('recuperar_senha');    
        $v->addData("senhaProvisoria", $provisoria);
        $v->addData("userData", $userData);
        $v->renderize(APP_VIEW);                    
    }

    public function atualizarSenha() {
        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        $id = $post['inp-id-user'];
        $senhaProvisoria = $post['inp-senha-provisoria'];
        $novaSenha = $post['inp-senha-nova'];
        $confirmaSenha = $post['inp-senha-nova-confirma'];

        if ($novaSenha !== $confirmaSenha) {
            Functions::messages("msg", "As senhas informadas não coincidem.", "danger");
            return;
        }

        $res = Daouser::recoverPass($id, $senhaProvisoria, $novaSenha);

        if ($res) {
            Functions::messages("header", URL . "Loginadm/login");
        } else {
            Functions::messages("msg", "Senha provisória inválida.", "danger");
        }
    }

    public function solicitarLink() {
        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $cpf = Functions::cleanString($post['inp-cpf-user']);
        $email = $post['inp-email-user'];

        $res = Daouser::gerarTokenRecuperacao($cpf, $email);

        if (!$res) {
            Functions::messages("msg", "CPF ou e-mail não encontrados.", "danger");
            return;
        }

        $link = URL . "Loginadm/redefinir/" . $res['token'];
        $corpo = "<p>Você solicitou a redefinição de senha no sistema AME - Peruíbe.</p>"
               . "<p><a href=\"{$link}\">Clique aqui para definir uma nova senha</a></p>"
               . "<p>Este link expira em 2 horas. Se você não fez essa solicitação, ignore este e-mail.</p>";

        $enviado = Mailer::send($res['email'], "Recuperação de senha - AME", $corpo);

        if ($enviado) {
            Functions::messages("msg", "Enviamos um link de recuperação para o seu e-mail.", "success");
        } else {
            Functions::messages("msg", "Não foi possível enviar o e-mail. Tente novamente mais tarde.", "danger");
        }
    }

    public function redefinir($param) {
        $token = $param[2];
        $id = Daouser::validarToken($token);

        if (!$id) {
            header("location: " . URL . "Loginadm/login");
            return;
        }

        $v = new TGui('redefinir_senha');
        $v->addData("token", $token);
        $v->renderize(APP_VIEW);
    }

    public function atualizarSenhaToken() {
        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $token = $post['inp-token'];
        $novaSenha = $post['inp-senha-nova'];
        $confirmaSenha = $post['inp-senha-nova-confirma'];

        if ($novaSenha !== $confirmaSenha) {
            Functions::messages("msg", "As senhas informadas não coincidem.", "danger");
            return;
        }

        $res = Daouser::redefinirSenhaPorToken($token, $novaSenha);

        if ($res) {
            Functions::messages("header", URL . "Loginadm/login");
        } else {
            Functions::messages("msg", "Link inválido ou expirado.", "danger");
        }
    }
}