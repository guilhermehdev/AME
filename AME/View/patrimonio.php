<?php
$mc = new Maincontroller();
$f = new Functions();
$title = $this->getData('title');

echo 
"<div class=\"col-sm-12\">
    <div class=\"page-header\">
        <h2><small>Cadastros ></small> {$title}</h2>
    </div>
       
    <div class=\"col-sm-4\">
    
        <fieldset class=\"for-panel\">
            <legend class=\"text-primary\">Buscar patrimônio</legend>  
                <div class=\"input-group\">
                    {$f->input("number", "form-control", "inp-busca-patrimonio", "inp-busca-patrimonio", null, "Patrimônio", true, "Digite o número",null,3)}  
                    <span class=\"input-group-btn\">
                    <button id=\"btn-busca-patrimonio\" name=\"btn-busca-patrimonio\" class=\"btn btn-primary call-modal\" data-modal-title=\"Dados encontrados do patrimônio\" data-modal-type=\"2\" data-modal-size=\"3\" data-modal-cls=\"lg-dialog\" data-modal-href=\"Patrimonio/getItemByPatrimonio\" data-modal-params='{\"patrimonio\":\"inp-busca-patrimonio\"}' data-check-input=\"inp-busca-patrimonio\" type=\"button\"><span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\">
                    </span> </button>
                    </span>
                </div>

        </fieldset> 
    
        <fieldset class=\"for-panel\">
            <legend class=\"text-primary\">Selecione os dados</legend>  
            
            <form class=\"form-horizontal\" method=\"POST\" action=\"" . URL . "Patrimonio/save\" id=\"frm-patrimonio\" name=\"frm-patrimonio\">

                <div class=\"col-sm-12\">   

                    <div class=\"col-sm-12\">                
                         <label>Unidades</label>
                            {$f->select(Daoagendas::slctUnidades(), "select mrg-bottom call-data", "slct-unidade-patrimonio", "slct-unidade-patrimonio", "\"id\":\"slct-unidade-patrimonio\"", "id", "descricao", "load", null, null, null, "Patrimonio/getSalas","","","salas")}                                  

                    </div>

                    <div class=\"col-sm-12\">                
                        <label>Salas</label>
                           <select class=\"select mrg-bottom\" id=\"slct-salas\"  name=\"slct-salas\" data-rule-required=1 data-msg-required='Selecione a Sala'>
                           </select>
                    </div>

                    <div class=\"col-sm-12\">
                        <label>Itens</label>
                            {$f->select(Daopatrimonio ::slctItens(), "select mrg-bottom", "slct-itens", "slct-itens", "", "id", "descricao", "", null, "", "", "",true,"Selecione o Item")} 

                    </div>

                    <div class=\"col-sm-12\">                            
                        <label>Patrimônio</label> 
                        {$f->input("text", "form-control mrg-bottom", "inp-pat", "inp-pat", "", "Patrimônio")}
                    </div>

                    <div class=\"col-sm-12\">                            
                        <label>Estado</label> 
                        <select class=\"select mrg-bottom\" id=\"slct-estado\" name=\"slct-estado\">
                            <option value=\"1\">ÓTIMO NOVO</option>
                            <option value=\"2\" selected>BOM</option>
                            <option value=\"3\">REGULAR</option>                        
                            <option value=\"4\">RUIM</option>                        
                            <option value=\"5\">PÉSSIMO</option>                        
                        </select>
                    </div>

                    <div class=\"col-sm-12\">                            
                        <label>Quantidade</label> 
                        {$f->input("number", "form-control mrg-bottom", "inp-qtd", "inp-qtd", "", "Qtd",false, "Digite a Quantidade",1,1)}
                    </div>

                    <div class=\"col-sm-12\"> 

                    <button id=\"btn-save-patrimonio\" name=\"btn-save-patrimonio\" class=\"btn btn-success call-data submit\" type=\"button\" href=\"Patrimonio/getSalas\" data-params='{\"idUnidade\":\"slct-unidade-patrimonio\"}' data-redirect=\"load\" data-redirect-url=\"\" data-redirect-params='{}' data-redirect-target=\"salas\">
                        Salvar
                    </button>               

                    </div>

                </div> 
            
            </form>
                                   
        </fieldset> 
        
    </div>
    
    <div class=\"col-sm-8\">

        <div id=\"salas\" name=\"salas\">
        
            <fieldset class=\"for-panel\">
                <legend class=\"text-primary\">Salas</legend>    
            
            </fieldset>
            
        </div>
        
    </div>

</div>"; 