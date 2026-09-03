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
                <div class=\"col-sm-12\">                   
                    <input type=\"hidden\" name=\"inp-id\" id=\"inp-id\">
		
                        <div class=\"col-sm-8\">
                            <label>Nome</label><br>
                                <div class=\"input-group\">
                                    {$f->input("text", "form-control", "inp-nome-servidor", "inp-nome-servidor", null, "Nome", true, "Digite o Nome",null,4)}  
                                    <span class=\"input-group-btn\">
                                    <button id=\"btn-fnd-servidor-nome\" name=\"btn-fnd-servidor-nome\" class=\"btn btn-primary call-modal\" data-modal-title=\"Selecione o Servidor\" data-modal-type=\"2\" data-modal-size=\"3\" data-modal-cls=\"lg-dialog\" data-modal-href=\"Servidores/get\" data-modal-params='{\"name\":\"inp-nome-servidor\"}' data-check-input=\"inp-nome-servidor\" type=\"button\"><span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\">
                                    </span> </button>
                                    </span>
                                </div>
                        </div>
					
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
                  
                <div class=\"col-sm-12\">
                    <button type=\"button\" id=\"btn-submit-servidor\" class=\"btn btn-success mrg-top\" href=\"Daoservidores/save\" data-params='{\"nome\":\"inp-nome-servidor\",\"cpf\":\"inp-cpf-servidor\",\"setor\":\"3\",\"unidade\":\"1\"}'>Salvar </button>

                    <button type=\"button\" id=\"btn-update-servidor\" class=\"btn btn-warning mrg-top\" href=\"Daoservidores/update\" data-params='{\"id\":\"inp-id\",\"nome\":\"inp-nome-servidor\",\"cpf\":\"inp-cpf-servidor\",\"setor\":\"3\",\"unidade\":\"1\"}' style=\"display:none;\">Atualizar </button>

                    <button type=\"button\" class=\"btn btn-danger hidden call-modal mrg-top\" id=\"btn-delete-servidor\" name=\"btn-delete-servidor\" data-modal-params='{\"id\":\"inp-id\"}' data-modal-title=\"Atenção\" data-modal-confirm=\"true\" data-modal-question=\"Excluir Servidor?\"  data-modal-type=\"5\" data-modal-cls=\"advice-dialog\" data-modal-href=\"Servidores/delete\" data-redirect=\"\" data-modal-redirect-url=\"\" data-modal-redirect-params='{}' data-redirect-target=\"\" data-modal-close=\"true\">Excluir</button>

                    <button type=\"button\" id=\"btn-cancel-update-servidor\" class=\"btn btn-default mrg-top\" style=\"display:none;\">Cancelar </button>
                    
                </div>

            </div>";