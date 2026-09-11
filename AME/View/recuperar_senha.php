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

if (!empty($provisoria)){      
 
    echo "<form class=\"form-horizontal\" method=\"POST\" action=\"" . URL . "Loginadm/atualizarSenha\" id=\"frm-atualizar-senha\" name=\"frm-atualizar-senha\">
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
    
  } elseif (empty($user[0]['email']) && !empty($user[0]['CPF'])) {
      
      echo  "<form class=\"form-horizontal\" method=\"POST\" action=\"" . URL . "Loginadm/solicitarLink\" id=\"frm-solicitar-link\" name=\"frm-solicitar-link\">
        <div class=\"col-sm-12\">

                    <input type=\"hidden\" name=\"inp-id-user\" id=\"inp-id-user\" value=\"{$user[0]['id']}\">

                    <div class=\"col-sm-12\" style=\"margin-bottom:10px;\">
                        <p class=\"text-muted\">Este é o primeiro cadastro de e-mail para o seu usuário. Confirme seu CPF e data de nascimento para continuar.</p>
                    </div>";
      
            echo  "<div class=\"col-sm-3\">
                            <label>CPF</label><br>                   
                                {$f->input("text", "form-control cpf-mask", "inp-cpf-user", "inp-cpf-user", "", "Somente números", "1", "Informe o CPF")}  
                        </div>";                                   

            echo  "<div class=\"col-sm-3\">
                            <label>Data de nascimento</label><br>                   
                                {$f->input("text", "form-control calendar data-br", "inp-dtnasc-user", "inp-dtnasc-user", "", "", "1", "Informe a data de nascimento")}  
                        </div>";
                             
                       echo "<div class=\"col-sm-4\">
                            <label>Email</label><br>                   
                                        {$f->input("email", "form-control", "inp-email-user", "inp-email-user", "", "Digite o Email", "1", "Informe o e-mail")} 
                        </div>
                        
                    <div class=\"col-sm-12\" style=\"margin-top:15px;\">
                        <button type=\"button\" class=\"btn btn-primary submit\">Enviar link</button>
                    </div>
                        
            </div>
        </form>";
  } else {
        $emailMascarado = Functions::maskEmail($user[0]['email']);
        echo  "<form class=\"form-horizontal\" method=\"POST\" action=\"" . URL . "Loginadm/solicitarLink\" id=\"frm-solicitar-link\" name=\"frm-solicitar-link\">
        <div class=\"col-sm-12\">   

                    <input type=\"hidden\" name=\"inp-id-user\" id=\"inp-id-user\" value=\"{$user[0]['id']}\">    
                    <div class=\"col-sm-12 mrg-bottom\">
                    
                        Enviaremos um link de redefinição para <b>{$emailMascarado}</b>. Confirme seu e-mail completo abaixo
                            
                    </div>";
          if (empty($user[0]['CPF'])){              
            echo  "<div class=\"col-sm-3\">
                            <label>CPF</label><br>                   
                                {$f->input("text", "form-control cpf", "inp-cpf-user", "inp-cpf-user", "", "Digite o CPF", "1", "Informe o CPF")}  
                        </div>";  
          } else {
              echo " <input type=\"hidden\" name=\"inp-cpf-user\" id=\"inp-cpf-user\" value=\"{$user[0]['CPF']}\">";
          }
                     
            echo "<div class=\"col-sm-4\">
                            <label>Email</label><br>                   
                                {$f->input("email", "form-control", "inp-email-user", "inp-email-user", "", "Digite o Email", "1", "Informe o e-mail")} 
                        </div>
                        
                    <div class=\"col-sm-12\" style=\"margin-top:15px;\">
                        <button type=\"button\" class=\"btn btn-primary submit\">Enviar link</button>
                    </div>
                        
            </div>
        </form>";
  }
}