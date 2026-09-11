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
            <div id=\"card-eventos-hoje\">
                <legend class=\"text-primary\">
                    <span class=\"glyphicon glyphicon-star-empty\"></span> Avisos
                </legend>
                <div id=\"container-eventos-hoje\" name=\"container-eventos-hoje\">
                    <p class=\"text-muted\" style=\"padding:5px;\">Carregando...</p>
                </div>
            </div>
            <div id=\"card-eventos-proxima-semana\">
                <hr>
                <legend class=\"text-primary\">
                    <span class=\"glyphicon glyphicon-calendar\"></span> Eventos de <span id=\"periodo-proximos-dias\"></span>
                </legend>
                <div id=\"container-eventos-proxima-semana\" name=\"container-eventos-proxima-semana\">
                    <p class=\"text-muted\" style=\"padding:5px;\">Carregando...</p>
                </div>
            </div>
            <hr>
            <button type=\"button\" id=\"btn-toggle-filtro-dashboard\" class=\"btn btn-default btn-md\">
                <span class=\"glyphicon glyphicon-search\"></span>
            </button>
            
            <div id=\"filtro-dashboard\" class=\"row\" style=\"padding:15px; display:none;\">
                <div class=\"col-sm-3\">
                    <label>Período</label>
                    <select id=\"slct-periodo-dashboard\" class=\"form-control\">
                        <option value=\"data\">Data específica</option>
                        <option value=\"mes\">Mês</option>
                    </select>
                </div>
                
                <div class=\"col-sm-3\">
                    <label>Data de referência</label>
                    <input type=\"text\" id=\"inp-data-dashboard\" class=\"form-control calendar\" value=\"" . date('d/m/Y') . "\" autocomplete=\"off\">
                    <select id=\"slct-mes-dashboard\" class=\"form-control\" style=\"display:none;\"></select>
                </div>
                
                <div class=\"col-sm-2\">
                    <label>&nbsp;</label><br>
                    <button type=\"button\" id=\"btn-buscar-dashboard\" class=\"btn btn-primary\">Consultar</button>
                    <button type=\"button\" id=\"btn-limpar-dashboard\" class=\"btn btn-default\">Limpar</button>
                </div>
            </div>
            <div id=\"container-eventos-periodo\" name=\"container-eventos-periodo\" class=\"mrg-top\">
                <p class=\"text-muted\" style=\"padding:15px;\"></p>
            </div>
        </fieldset>
    </div>

</div>";      
} else {
    header("location: ".URL."Loginadm/login");
}
