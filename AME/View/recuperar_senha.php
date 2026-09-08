<?php
$mc = new Maincontroller();
$f = new Functions();
$user = '';

if ($this->getData('userData')) {
    $provisoria = $this->getData('senhaProvisoria');
    $user = $this->getData('userData');    
    
      echo 
        "<div class=\"col-sm-12\">
            <div class=\"page-header\">
                <h2><small>Login ></small> Recuperar senha</h2>
         </div>";

  if ($provisoria != NULL){      
 
echo  "<form class=\"form-horizontal\" method=\"POST\" action=\"" . URL . "Loginadm/atualizarSenha\" id=\"frm-atualizar-senha\" name=\"frm-atualizar-senha\">
        <div class=\"col-sm-12\">   

                    <input type=\"hidden\" name=\"inp-id-user\" id=\"inp-id-user\" value=\"{$user[0]['id']}\">	

                        <div class=\"col-sm-3\">
                            <label>Senha provisória</label><br>                   
                                        {$f->input("text", "form-control", "inp-senha-provisoria", "inp-senha-provisoria", "", "Senha provisória", "1", "Informe a senha provisória","",6)}  
                        </div>   
                        
                          <div class=\"col-sm-3\">
                            <label>Nova senha</label><br>                   
                                        {$f->input("password", "form-control", "inp-senha-nova", "inp-senha-nova", "", "Nova senha", "1", "Informe a nova senha","",6)} 
                        </div>   
                        
                          <div class=\"col-sm-3\">
                            <label>Confirmar nova senha</label><br>                   
                                        {$f->input("password", "form-control", "inp-senha-nova-confirma", "inp-senha-nova-confirma", "", "Confirme a nova senha", "1", "Confirme a nova senha","",6)}   
                        </div>   
                        
                        <div class=\"col-sm-12\" style=\"margin-top:15px;\">
                            <button type=\"button\" class=\"btn btn-primary submit\">Salvar nova senha</button>
                        </div>
                        
            </div>
        </form>";
  } else {
      
      echo  "<form class=\"form-horizontal\" method=\"POST\" action=\"" . URL . "Loginadm/solicitarLink\" id=\"frm-solicitar-link\" name=\"frm-solicitar-link\">
        <div class=\"col-sm-12\">   

                    <input type=\"hidden\" name=\"inp-id-user\" id=\"inp-id-user\" value=\"{$user[0]['id']}\">	

                        <div class=\"col-sm-3\">
                            <label>CPF</label><br>                   
                                        {$f->input("text", "form-control cpf", "inp-cpf-user", "inp-cpf-user", "", "Digite o CPF", "1", "Informe o CPF")}  
                        </div>   
                        
                          <div class=\"col-sm-4\">
                            <label>Email</label><br>                   
                                        {$f->input("email", "form-control", "inp-email-user", "inp-email-user", "", "Digite o Email", "1", "Informe o e-mail")} 
                        </div>   
                        
                        <div class=\"col-sm-12\" style=\"margin-top:15px;\">
                            <button type=\"button\" class=\"btn btn-primary submit\">Enviar link por e-mail</button>
                        </div>
                        
            </div>
        </form>";
  }
}