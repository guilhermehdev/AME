<?php

$idUser = AppController::checkSession()['id'];

if($this->getData('avisos')) {
    $avisos = $this->getData('avisos');
}


echo 
"<div class=\"col-sm-12\">
    <div class=\"page-header\">
        <h2><small>Notificações > Painel ></small> Cadastrar nova mensagem</h2>
    </div>
    
    <div class=\"col-sm-12\">
    
        <fieldset class=\"for-panel\">
            <legend class=\"text-primary\">Digite a nova mensagem</legend> 
            
             <div class=\"col-sm-12\">
                
                <label>Mensagem</label>
                <textarea class=\"form-control\" rows=\"5\" id=\"area-painel-mensagem\" name=\"area-painel-mensagem\"></textarea>

            </div>

            <div class=\"col-sm-12 mrg-top\">
                {$f->button("button", "btn btn-success", "btn-save-aviso", "btn-save-aviso", "Notificacoes/saveaviso", "Salvar","\"iduser\":\"{$idUser}\",\"msg\":\"area-painel-mensagem\"")}
                <a class=\"btn btn-warning\" href=\"javascript:void(0)\" onclick='notify()' >Teste</a>
            </div>

        </fieldset>
 
    </div>
    
    <div class=\"col-sm-12\">
        
        <div id=\"avisos\" name=\"avisos\">";                
        Functions::incl(APP_VIEW_LIST, "Lavisos");                
  echo "</div>

    </div>

</div>";