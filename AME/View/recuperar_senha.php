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
 
echo  "<div class=\"col-sm-12\">   

                    <input type=\"hidden\" name=\"inp-id-user\" id=\"inp-id-user\">	

                        <div class=\"col-sm-3\">
                            <label>Senha provisória</label><br>                   
                                        {$f->input("text", "form-control", "inp-senha-provisoria", "inp-senha-provisoria", "", "Digite a Senha", "", "Digite a Senha","",6)}  
                        </div>   
                        
                          <div class=\"col-sm-3\">
                            <label>Nova senha</label><br>                   
                                        {$f->input("text", "form-control", "inp-senha-nova", "inp-senha-nova", "", "Digite a Senha", "", "Digite a Senha","",6)} 
                        </div>   
                        
                          <div class=\"col-sm-3\">
                            <label>Confirmar nova senha</label><br>                   
                                        {$f->input("text", "form-control", "inp-senha-nova-confirma", "inp-senha-nova-confirma", "", "Digite a Senha", "", "Digite a Senha","",6)}   
                        </div>   
                        
            </div>";
  } else {
      
      echo  "<div class=\"col-sm-12\">   

                    <input type=\"hidden\" name=\"inp-id-user\" id=\"inp-id-user\">	

                        <div class=\"col-sm-3\">
                            <label>CPF</label><br>                   
                                        {$f->input("text", "form-control cpf", "inp-cpf-user", "inp-cpf-user", "", "Digite o CPF", "", "Digite o CPF")}  
                        </div>   
                        
                          <div class=\"col-sm-4\">
                            <label>Email</label><br>                   
                                        {$f->input("email", "form-control", "inp-email-user", "inp-email-user", "", "Digite o Email")} 
                        </div>   
                        
                        
                        
            </div>";
  }
}