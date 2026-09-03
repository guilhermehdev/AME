<?php

echo 
"<div class=\"col-sm-12\">
    <div class=\"page-header\">
            <h2>
                    <small>Retornos ></small> Consulta <button id=\"btn-retornos-pendentes\" name=\"btn-retornos-pendentes\" class=\"btn btn-warning call-data\" type=\"button\" href=\"Retornos/alerts\" data-redirect=\"header\">
                         Retornos pendentes >>
                     </button>
            </h2>            
    </div>       
    
    <fieldset class=\"for-panel\">
        <legend class=\"\">Pesquisar</legend>

        <div class=\"col-sm-2\"> 
            <label>Data de nascimento</label> 
            
            <div class=\"input-group\">                      
                <input type=\"text\" class=\"form-control date\" id=\"inp-consulta-nasc\" name=\"inp-consulta-nasc\" placeholder=\"Data\">
                <span class=\"input-group-btn\">
                    <button id=\"btn-consulta-nasc\" name=\"btn-consulta-nasc\" class=\"btn btn-primary call-data\" type=\"button\" href=\"Retornos/getconsulta\" data-params='{\"id\":\"null\",\"nasc\":\"inp-consulta-nasc\"}' data-redirect=\"load\" data-redirect-target=\"container-consultas\" data-check-input=\"inp-consulta-nasc\">
                        <span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\"></span>
                    </button>
                </span>
            </div>

        </div>

        <div class=\"col-sm-10\"> 
            <label>Paciente</label>   
            <input type=\"hidden\" name=\"id_consulta_pac\" id=\"id_consulta_pac\" value=\"\" placeholder=\"Paciente\">                             
            <input type=\"text\" class=\"form-control insert-eac\" id=\"inp-consulta-pac\" name=\"inp-consulta-pac\" placeholder=\"Nome\">

        </div>

    </fieldset>
    
    <div class=\"col-sm-12\" id=\"container-consultas\" name=\"container-consultas\"></div>
        
</div>";