<?php
$mc = new Maincontroller();
$f = new Functions();
$title = $this->getData('title');

echo 
"<div class=\"col-sm-12\">
    <div class=\"page-header\">
        <h2><small>Relatórios ></small> {$title}</h2>
    </div>
    
    <div class=\"col-sm-6 white\">
    
        <fieldset class=\"for-panel\">
            <legend class=\"text-primary\">Selecione</legend>        

            <div class=\"col-sm-12\">   
            
                <div class=\"col-sm-6\">
                
                    <label>Unidades <span class=\"text-danger\">*</span></label>
                        {$f->select(Daoagendas ::slctUnidades(), "select mrg-bottom", "slct-unidade", "slct-unidade", "\"id\":\"slct-unidade\"", "id", "descricao", "", null, "", "", "",true,"Selecione a Unidade")}
                            
                </div>
                            
                <div class=\"col-sm-6\">
                
                    <label>Especialidades <span class=\"text-danger\">*</span></label>
                        {$f->select(Daoagendas::slctEspecs(), "select mrg-bottom", "slct-espec", "slct-espec", "\"id\":\"slct-espec\"", "id", "especialidade", "", null, "", "", "",true,"Selecione a Especialidade")}                                    

                </div>
                
                <div class=\"col-sm-6\">
                
                    <label>Profissionais <span class=\"text-danger\">*</span></label>
                        {$f->select(Daoagendas::slctProf(), "select mrg-bottom", "slct-prof", "slct-prof", "\"idEspec\":\"slct-espec\",\"idProf\":\"slct-prof\"", "id", "nome", "reload", null, "", "grades-geradas","Agendasame/getGrade",true,"Selecione o Profissional")}
            
                </div>
                                           
                <div class=\"col-sm-4\">

                    <label>Mês</label>
                        <select class=\"select mrg-bottom\" id=\"slct-mes-grd\" name=\"slct-mes-grd\">
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
                    Functions::selectYears('slct-ano-grd','slct-ano-grd');                            
          echo "</div>  
              
            </div>
                               
            <div class=\"col-sm-12\"> 

                <a name=\"btn-imp-grade\" id=\"btn-imp-grade\" class=\"btn btn-primary newtab\" data-redirect=\"\" href=\"#\" data-url=\"Reports/gradeUBS\" data-params='{\"idUnidade\":\"slct-unidade\",\"idEspec\":\"slct-espec\",\"idProf\":\"slct-prof\",\"mes\":\"slct-mes-grd\",\"ano\":\"slct-ano-grd\"}' style=\"margin-top:25px;\">
                    <span class=\"glyphicon glyphicon-print\"> Imprimir</span>
                </a>

            </div>
                                  
        </fieldset>         
        
    </div>

</div>"; 