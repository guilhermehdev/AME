<?php
$f = new Functions();
$title = $this->getData('title');
$show = $this->getData('show');
$btn = "";

if ($show == true){
    $btn = "<button id=\"btn-back-retornos\" name=\"btn-back-retornos\" class=\"btn btn-warning call-data\" href=\"Retornos/index\" data-redirect=\"header\" >  << Voltar para Retornos</button>";
}

echo 
"<div class=\"col-sm-12\">
    <div class=\"page-header\">
        <h2><small>Cadastros ></small> {$title} {$btn}</h2>          
    </div>
	
    <div class=\"col-sm-8\">       
        <div class=\"row\">        
            <div class=\"col-sm-12\"> 
            
                <div class=\"col-sm-12\">                   
                    <input type=\"hidden\" name=\"inp-id\" id=\"inp-id\">
                    <input type=\"hidden\" name=\"inp-id-logra\" id=\"inp-id-logra\">
                    
                             <div class=\"col-sm-3\">
                                    <label>Nascimento</label><br>
                                    <div class=\"input-group\">
                                        {$f->input("text", "form-control data-br", "inp-cad-dtnasc", "inp-cad-dtnasc", "", "Data", "", "Digite a Data","",10)}
                                        <span class=\"input-group-btn\">
                                        <button id=\"btn-fnd-pac-data\" name=\"btn-fnd-pac-data\" class=\"btn btn-primary call-modal\" data-modal-title=\"Selecione o Paciente\" data-modal-type=\"2\" data-modal-size=\"3\" data-modal-cls=\"sv-dialog\" data-modal-href=\"Pacientes/get\" data-modal-params='{\"data\":\"inp-cad-dtnasc\"}' data-check-input=\"inp-cad-dtnasc\" type=\"button\"><span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\">
                                        </span> </button>
                                        </span>
                                    </div>
                            </div>
		
                            <div class=\"col-sm-6\">
                                        <label>Nome</label><br>
                                        <div class=\"input-group\">
                                            {$f->input("text", "form-control", "inp-nome", "inp-nome", null, "", true, "Digite o Nome",null,4)}  
                                            <span class=\"input-group-btn\">
                                            <button id=\"btn-fnd-pac-nome\" name=\"btn-fnd-pac-nome\" class=\"btn btn-primary call-modal\" data-modal-title=\"Selecione o Paciente\" data-modal-type=\"2\" data-modal-size=\"3\" data-modal-cls=\"sv-dialog\" data-modal-href=\"Pacientes/get\" data-modal-params='{\"name\":\"inp-nome\"}' data-check-input=\"inp-nome\" type=\"button\"><span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\">
                                            </span> </button>
                                            </span>
                                        </div>
                            </div>	
                            
                              <div class=\"col-sm-3\">
                                <label>CPF</label><br>
                                    <div class=\"input-group\">
                                        {$f->input("text", "form-control cpf-mask", "inp-cpf", "inp-cpf", "", "CPF", true,"","",14)}
                                        <div id=\"popup-cpf\" class=\"popup-msg\">CPF inválido</div>
                                            <span class=\"input-group-btn\">
                                                <button id=\"btn-cadsus\" name=\"btn-cadsus\" class=\"btn btn-default newtab\" type=\"button\" style=\"padding:0;\" onclick=\"CartaoSus()\"><img src=\"".APP_IMG."sus.png\" alt=\"Cartão SUS\" style=\"width:49px;\">
                                                </button>
                                            </span>
                                        </div>
                            </div>
                            
                             <div class=\"col-sm-4\">                    
                                <label>Nome da mãe</label><br>                               
                                {$f->input("text", "form-control", "inp-mae", "inp-mae", "", "")}
                            </div>    
                            
                             <div class=\"col-sm-2\">                    
                                     <label>Sexo</label><br>
                                     <select class=\"form-control\"  id=\"inp-sexo\" name=\"inp-sexo\" data-rule-required=\"true\">
                                            <option value=\"\" selected disabled>----------</option>
                                            <option value=\"M\">M</option>
                                            <option value=\"F\">F</option>                                          
                                    </select>     
                               </div>   
                            
                            <div class=\"col-sm-3\">
                                <label>Contato</label><br> 
                                    <div class=\"input-group\">
                                            {$f->input("text", "form-control tel-mask", "inp-tel", "inp-tel", "", "(00)00000-0000", true,"Digite um contato")}    
                                            <span class=\"input-group-btn\">
                                                <button id=\"btn-send-whatsapp\" name=\"btn-send-whatsapp\" class=\"btn btn-success call-modal\" data-modal-title=\"Confirmar consulta\" data-modal-type=\"2\" data-modal-size=\"3\" data-modal-cls=\"md-dialog\" data-modal-href=\"Notificacoes/whatsapp\" data-modal-params='{\"num\":\"inp-tel\",\"nome\":\"inp-nome\",\"nasc\":\"inp-cad-dtnasc\"}' data-check-input=\"inp-tel\" type=\"button\">
                                                       <i class=\"bi bi-whatsapp\"></i>
                                                </button>
                                            </span>
                                    </div>
                            </div>    
					
                            <div class=\"col-sm-3\">                    
                                <label>Prontuário</label><br>                               
                                {$f->input("text", "form-control", "inp-pront", "inp-pront", "", "000000")}
                            </div>      
                            
                              <div class=\"col-sm-2\">                    
                                     <label>CEP</label><br>
                                      <input type=\"text\" class=\"form-control cep\" id=\"inp-cep\" name=\"inp-cep\" data-rule-required=\"false\" data-params='{\"cep\":\"inp-cep\"}' placeholder=\"00000-000\"> 
                                       <div id=\"popup-cep\" class=\"popup-msg\">CEP inválido</div>
                              </div>   
                            
                              <div class=\"col-sm-3\">                    
                                     <label>Tipo</label><br>
                                     <select class=\"form-control\"  id=\"inp-tipo-log\" name=\"inp-tipo-log\">
                                            <option value=\"\" selected disabled>----------</option>
                                            <option value=\"081\">RUA</option>
                                            <option value=\"008\">AVENIDA</option>
                                            <option value=\"031\">ESTRADA</option>
                                            <option value=\"004\">ALAMEDA</option>
                                            <option value=\"065\">PRAÇA</option>
                                            <option value=\"105\">VIELA</option>
                                            <option value=\"095\">SETOR</option>
                                    </select>     
                               </div>   
                               
                                <div class=\"col-sm-7\" style=\"position: relative\";>                    
                                     <label>Logradouro</label> <span style=\"color:#888;\"><i>(digite para pesquisar)</i></span><br>
                                     <div style=\"position: relative;\">
                                            <input type=\"text\" class=\"form-control\" id=\"inp-logra\" name=\"inp-logra\" data-rule-required=\"false\" data-params='{\"logradouro\":\"inp-logra\"}' placeholder=\"\">  
                                            <div id=\"resultados\" ></div>
                                      </div>
                               </div>   
                               
                               <div class=\"col-sm-2\">                    
                                     <label>Número</label><br>
                                      <input type=\"text\" class=\"form-control\" id=\"inp-numero\" name=\"inp-numero\" data-rule-required=\"false\" placeholder=\"\">         
                               </div>   
                               
                                 <div class=\"col-sm-6\" style=\"position: relative\";>                    
                                     <label>Bairro</label> <span style=\"color:#888;\"><i>(digite para pesquisar)</i></span><br>
                                            <div style=\"position: relative;\">
                                                    <input type=\"text\" class=\"form-control\" id=\"inp-bairro\" name=\"inp-bairro\" data-rule-required=\"false\" data-params='{\"bairro\":\"inp-bairro\"}' placeholder=\"\">  
                                                    <div id=\"resultados-bairro\"></div>
                                            </div>
                               </div>   
                               
                                 <div class=\"col-sm-4\">                    
                                     <label>Complemento</label><br>
                                      <input type=\"text\" class=\"form-control\" id=\"inp-complemento\" name=\"inp-complemento\" data-rule-required=\"false\" placeholder=\"\">         
                               </div>   
					
                    <div class=\"col-sm-12 mrg-top\"> 
                    
                            <button type=\"button\" id=\"btn-submit-paciente\" class=\"btn btn-success\" href=\"Daopacientes/save\" data-params='{\"nome\":\"inp-nome\",\"dtnasc\":\"inp-cad-dtnasc\",\"cpf\":\"inp-cpf\",\"pront\":\"inp-pront\",\"contato\":\"inp-tel\",\"mae\":\"inp-mae\",\"idLogra\":\"inp-id-logra\",\"numero\":\"inp-numero\",\"complemento\":\"inp-complemento\",\"sexo\":\"inp-sexo\"}'>Salvar </button>

                            <button type=\"button\" id=\"btn-update-paciente\" class=\"btn btn-warning\" href=\"Daopacientes/update\" data-params='{\"id\":\"inp-id\",\"nome\":\"inp-nome\",\"dtnasc\":\"inp-cad-dtnasc\",\"cpf\":\"inp-cpf\",\"pront\":\"inp-pront\",\"contato\":\"inp-tel\",\"mae\":\"inp-mae\",\"idLogra\":\"inp-id-logra\",\"numero\":\"inp-numero\",\"complemento\":\"inp-complemento\",\"sexo\":\"inp-sexo\"}' style=\"display:none;\">Atualizar </button>						

                            <button type=\"button\" class=\"btn btn-danger hidden call-modal\" id=\"btn-delete-paciente\" name=\"btn-delete-paciente\" data-modal-params='{\"id\":\"inp-id\"}' data-modal-title=\"Atenção\" data-modal-confirm=\"true\" data-modal-question=\"Excluir Paciente?\"  data-modal-type=\"5\" data-modal-cls=\"advice-dialog\" data-modal-href=\"Pacientes/delete\" data-redirect=\"\" data-modal-redirect-url=\"\" data-modal-redirect-params='{}' data-redirect-target=\"\" data-modal-close=\"true\">Excluir</button>							

                            <button type=\"button\" id=\"btn-cancel-update\" class=\"btn btn-default\" style=\"display:none;\">Cancelar </button>

                    </div>";