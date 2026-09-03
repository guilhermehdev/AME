<?php
$f = new Functions();
$title = $this->getData('title');

echo 
"<div class=\"col-sm-12\">
    <div class=\"page-header\">
        <h2><small>Cadastros ></small> {$title}</h2>
    </div>
	
	<div class=\"col-sm-6\">       
        <div class=\"row\">        
            <div class=\"col-sm-12\"> 
                <div class=\"col-sm-6\">                   
                    <input type=\"hidden\" name=\"inp-id\" id=\"inp-id\">
		
                        <div class=\"col-sm-12\">
                            <label>Nome</label><br>
                                <div class=\"input-group\">
                                    {$f->input("text", "form-control", "inp-nome-usuario", "inp-nome-usuario", null, "Nome", true, "Digite o Nome",null,4)}  
                                    <span class=\"input-group-btn\">
                                    <button id=\"btn-fnd-usuario-nome\" name=\"btn-fnd-usuario-nome\" class=\"btn btn-primary call-modal\" data-modal-title=\"Selecione o Usuário\" data-modal-type=\"2\" data-modal-size=\"3\" data-modal-cls=\"lg-dialog\" data-modal-href=\"Servidores/getUsuarios\" data-modal-params='{\"name\":\"inp-nome-usuario\"}' data-check-input=\"inp-nome-usuario\" type=\"button\"><span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\">
                                    </span> </button>
                                    </span>
                                </div>
                        </div>
					
                <div class=\"col-sm-12\">
                    <label>CPF</label><br>
                    
                        <div class=\"input-group\">
                            {$f->input("text", "form-control cpf-mask", "inp-cpf-usuario", "inp-cpf-usuario", "", "CPF", "", "Digite o CPF","",14)}
                            <span class=\"input-group-btn\">
                            <button id=\"btn-fnd-usuario-cpf\" name=\"btn-fnd-usuario-cpf\" class=\"btn btn-primary call-modal\" data-modal-title=\"Selecione o Usuário\" data-modal-type=\"2\" data-modal-size=\"3\" data-modal-cls=\"lg-dialog\" data-modal-href=\"Servidores/getUsuarios\" data-modal-params='{\"cpf\":\"inp-cpf-usuario\"}' data-check-input=\"inp-cpf-usuario\" type=\"button\"><span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\">
                            </span> </button>
                            </span>
                        </div>
                </div>	
                
                 <div class=\"col-sm-12\">
                    <label>Acesso a cadastros</label><br>                      
                            {$f->input("checkbox", "", "inp-cadastro-usuario", "inp-cadastro-usuario", "", "", "", "Permitir acesso a cadastros")} 
                </div>
                
                
                
                    <div class=\"col-sm-6\">

                        <label>Senha</label><br> 
                            <div class=\"input-group\">
                                {$f->input("password", "form-control", "inp-pass-usuario", "inp-pass-usuario", "", "Senha", "", "Digite a Senha","",6)}
                                <span class=\"input-group-btn\">
                                    <button id=\"btn-show-usuario-pass\" name=\"btn-show-usuario-pass\" class=\"btn btn-default\" type=\"button\">
                                        <span id=\"span-pass\" class=\"glyphicon glyphicon-eye-open\" aria-hidden=\"true\"></span>                           
                                    </button>
                                </span>
                            </div>   

                    </div>	

                    <div class=\"col-sm-6\">
                        <label>Repetir senha</label><br>  
                            <div class=\"input-group\">
                                {$f->input("password", "form-control", "inp-pass2-usuario", "inp-pass2-usuario", "", "Senha", "", "Repita a Senha","",6)} 
                                <span class=\"input-group-btn\">
                                    <button id=\"btn-show-usuario-pass2\" name=\"btn-show-usuario-pass2\" class=\"btn btn-default\" type=\"button\">
                                        <span id=\"span-pass2\" class=\"glyphicon glyphicon-eye-open\" aria-hidden=\"true\"></span>                           
                                    </button>
                                </span>
                            </div>
                    </div>
                    
              
                  
                <div class=\"col-sm-12\">
                    <button type=\"button\" id=\"btn-submit-usuario\" class=\"btn btn-success mrg-top\" href=\"Daouser/save\" data-params='{\"nome\":\"inp-nome-usuario\",\"cpf\":\"inp-cpf-usuario\",\"pass\":\"inp-pass-usuario\",\"cad\":\"inp-cadastro-usuario\"}'>Salvar </button>

                    <button type=\"button\" id=\"btn-update-usuario\" class=\"btn btn-warning mrg-top\" href=\"Daouser/update\" data-params='{\"id\":\"inp-id\",\"nome\":\"inp-nome-usuario\",\"cpf\":\"inp-cpf-usuario\",\"pass\":\"inp-pass-usuario\",\"cad\":\"inp-cadastro-usuario\"}' style=\"display:none;\">Atualizar </button>

                    <button type=\"button\" class=\"btn btn-danger hidden call-modal mrg-top\" id=\"btn-delete-usuario\" name=\"btn-delete-usuario\" data-modal-params='{\"id\":\"inp-id\"}' data-modal-title=\"Atenção\" data-modal-confirm=\"true\" data-modal-question=\"Excluir Usuário?\"  data-modal-type=\"5\" data-modal-cls=\"advice-dialog\" data-modal-href=\"Servidores/delete\" data-redirect=\"\" data-modal-redirect-url=\"\" data-modal-redirect-params='{}' data-redirect-target=\"\" data-modal-close=\"true\">Excluir</button>

                    <button type=\"button\" id=\"btn-cancel-update-usuario\" class=\"btn btn-default mrg-top\" style=\"display:none;\">Cancelar </button>
                    
                </div>

            </div>";