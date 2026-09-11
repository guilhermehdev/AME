<?php
$f = new Functions();
$title = $this->getData('title');
$avisos = Daonotificacoes::getAvisos('');
$hoje = Functions::dateFull();
$idUser = AppController::checkSession()['id'];


if(AppController::checkSession()){    

echo 
"<div class=\"col-sm-12\">
    <div class=\"page-header\" style=\"padding-bottom:0;\">
        <h1>{$title} - <small>{$hoje}</small></h1>
    </div>          
  
    <!-- Card: Gestão de Agendas -->
    <div class=\"col-sm-12 mrg-top\">
        <fieldset class=\"for-panel\">
            <legend class=\"text-primary\">
                <span class=\"glyphicon glyphicon-star-empty\"></span> Eventos — " . date('m/Y') . "
              
            </legend>
            <div id=\"container-dashboard-agendas\" name=\"container-dashboard-agendas\">
                <p class=\"text-muted\" style=\"padding:5px;\">Carregando...</p>
            </div>
        </fieldset>
    </div>

</div>";      
} else {
    header("location: ".URL."Loginadm/login");
}