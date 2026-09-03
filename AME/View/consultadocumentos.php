<?php
echo 
"<div class=\"col-sm-12\">
    <div class=\"page-header\">
        <h2><small>Documentos ></small> Consulta</h2>
    </div>
    
    <fieldset class=\"for-panel\">
        <legend class=\"text-primary\">Pesquisar</legend>

        <div class=\"col-sm-3\">
        
            <label>Tipo</label> 
            <select class=\"select\" id=\"slct-tipo-consulta-doc\" name=\"slct-tipo-consulta-doc\">               
                <option value=\"0\" selected>Memorando</option>
                <option value=\"1\">Ofício</option>
                <option value=\"2\">Circular</option>
            </select>
            
        </div>
        
        <div class=\"col-sm-2\">
        
            <label>Ordem</label>
            <input class=\"form-control\" type=\"number\" min=\"0\" id=\"inp-ordem-consulta-doc\" name=\"inp-ordem-consulta-doc\">
            
        </div>            
           
        <div class=\"col-sm-2\">
            
            <label>Data</label>
            <input type=\"text\" class=\"form-control calendar date mrg-bottom\" id=\"inp-data-consulta-doc\" name=\"inp-data-consulta-doc\">
            
        </div>
        
        <div class=\"col-sm-1\">";
            Functions::selectYears('slct-ano-consulta-doc','slct-ano-consulta-doc');            
  echo "</div>
        
        <div class=\"col-sm-4\">

            <label>Assunto</label>
            <input class=\"form-control\" type=\"text\" id=\"inp-assunto-consulta-doc\" name=\"inp-assunto-consulta-doc\">

        </div>
        
        <div class=\"col-sm-12\">                
          
            <button id=\"btn-consulta-doc\" name=\"btn-consulta-doc\" class=\"btn btn-primary mrg-top call-data\" href=\"Documentos/get\" data-redirect=\"load\" data-params='{\"tipo\":\"slct-tipo-consulta-doc\",\"ordem\":\"inp-ordem-consulta-doc\",\"data\":\"inp-data-consulta-doc\",\"ano\":\"slct-ano-consulta-doc\",\"assunto\":\"inp-assunto-consulta-doc\"}' data-redirect-target=\"container-consultas-doc\">
                Buscar
            </button> 

        </div>

    </fieldset>
    
    <div class=\"col-sm-12\" id=\"container-consultas-doc\" name=\"container-consultas-doc\"></div>
        
</div>";