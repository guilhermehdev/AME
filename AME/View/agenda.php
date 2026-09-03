<?php

$f = new Functions();

echo
"<div class=\"col-sm-12\">
    <div class=\"page-header\">
            <h2>
                    <small>Notificações ></small> Agendas 
            </h2>            
    </div>
    
    <fieldset class=\"for-panel\">
        <legend class=\"text-primary\">Pesquisar</legend>
                        
        <div class=\"col-sm-7\"> 
                <label>Profissional</label>
                {$f->select(Daoagendas ::slctProf(), "select mrg-bottom", "slct-prof-not-agenda", "slct-prof-not-agenda", "", "id", "nome", "", null, "", "","",false,"")}
        </div>
.
        <div class=\"col-sm-3\"> 
            <label>Mês</label>             
                             
                  <select class=\"select mrg-bottom\" id=\"slct-mes-not-agenda\" name=\"slct-mes-not-agenda\" data-params='{\"mes\":\"slct-mes-pendente\",\"ano\":\"slct-ano-pendente\",\"idservidor\":\"slct-prof-pendente\"}' >
                        <option value=\"\"> MÊS </option>
                        <option value=\"1\"> JANEIRO </option>
                        <option value=\"2\"> FEVEREIRO </option>
                        <option value=\"3\"> MARÇO </option>
                        <option value=\"4\"> ABRIL </option>
                        <option value=\"5\"> MAIO </option>
                        <option value=\"6\"> JUNHO </option>
                        <option value=\"7\"> JULHO </option>
                        <option value=\"8\"> AGOSTO </option>
                        <option value=\"9\"> SETEMBRO </option>
                        <option value=\"10\"> OUTUBRO </option>
                        <option value=\"11\"> NOVEMBRO </option>
                        <option value=\"12\"> DEZEMBRO </option>
                  </select>
          
        </div>
      
        <div class=\"col-sm-2\">";
            Functions::selectYears('slct-ano-not-agenda','slct-ano-not-agenda');
echo "</div>

    </fieldset>
    
</div>    
    <div class=\"col-sm-12\" id=\"container-not-agenda\" name=\"container-not-agenda\">
    </div>        
</div>";