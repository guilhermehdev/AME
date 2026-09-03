<?php
$mc = new Maincontroller();
$f = new Functions();
$user = '';

if ($this->getData('userData')) {
    $user = $this->getData('userData');
     

echo 
"<div class=\"col-sm-12\">
    <div class=\"page-header\">
        <h2><small>Login ></small> Recuperar senha</h2>
 </div>
	
    <div class=\"col-sm-12\">   
    
            <input type=\"hidden\" name=\"inp-id\" id=\"inp-id\">	
            
                <div class=\"col-sm-4\">
                    <label>CPF</label><br>
                        <div class=\"input-group\">
                                {$f->input("text", "form-control cpf", "inp-cpf-servidor", "inp-cpf-servidor", "", "CPF", "", "Digite a CPF","",14)}
                                <span class=\"input-group-btn\">
                                <button id=\"btn-fnd-servidor-cpf\" name=\"btn-fnd-servidor-cpf\" class=\"btn btn-primary call-modal\" data-modal-title=\"Selecione o Servidor\" data-modal-type=\"2\" data-modal-size=\"3\" data-modal-cls=\"lg-dialog\" data-modal-href=\"Servidores/get\" data-modal-params='{\"cpf\":\"inp-cpf-servidor\"}' data-check-input=\"inp-cpf-servidor\" type=\"button\"><span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\">
                                </span> </button>
                                </span>
                        </div>
                </div>					
                  </div>   
    </div>";

}