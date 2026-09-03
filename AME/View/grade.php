<?php
$mc = new Maincontroller();
$f = new Functions();
$title = $this->getData('title');

echo 
"<div class=\"col-sm-12\">
    <div class=\"page-header\">
        <h2><small>Cadastros ></small> {$title}</h2>
    </div>
    
    <div class=\"col-sm-12 white\">
    
        <fieldset class=\"for-panel\">
            <legend class=\"text-primary\">Pesquisar Grade</legend>        

            <div class=\"col-sm-9\">   
            
                <div class=\"col-sm-4\">
                
                    <label>Especialidades <span class=\"text-danger\">*</span></label>
                        {$f->select(Daoagendas::slctEspecs(), "select mrg-bottom", "slct-espec", "slct-espec", "\"id\":\"slct-espec\"", "id", "especialidade", "", null, "", "", "",true,"Selecione a Especialidade")}                                    

                </div>
                
                <div class=\"col-sm-8\">
                
                    <label>Profissionais <span class=\"text-danger\">*</span></label>
                        {$f->select(Daoagendas::slctProf(), "select mrg-bottom", "slct-prof", "slct-prof", "\"idEspec\":\"slct-espec\",\"idProf\":\"slct-prof\"", "id", "nome", "reload", null, "", "grades-geradas","Agendasame/getGrade",true,"Selecione o Profissional")}
            
                </div>
                                           
                <div class=\"col-sm-4\">

                    <label>Mês</label>
                        <select class=\"select mrg-bottom call-data\" id=\"slct-mes-grd\" name=\"slct-mes-grd\" href=\"Agendasame/getGrade\" data-params='{\"idEspec\":\"slct-espec\",\"idProf\":\"slct-prof\",\"mes\":\"slct-mes-grd\",\"ano\":\"slct-ano-grd\"}' data-redirect=\"load\" data-redirect-target=\"grades-geradas\">
                        <option value=\"\"> MÊS </option>
                        <option value=\"0\"> JANEIRO </option>
                        <option value=\"1\"> FEVEREIRO </option>
                        <option value=\"2\"> MARÇO </option>
                        <option value=\"3\"> ABRIL </option>
                        <option value=\"4\"> MAIO </option>
                        <option value=\"5\"> JUNHO </option>
                        <option value=\"6\"> JULHO </option>
                        <option value=\"7\"> AGOSTO </option>
                        <option value=\"8\"> SETEMBRO </option>
                        <option value=\"9\"> OUTUBRO </option>
                        <option value=\"10\"> NOVEMBRO </option>
                        <option value=\"11\"> DEZEMBRO </option>
                    </select>
                            
                </div>
                
                <div class=\"col-sm-2\">";
                    Functions::selectYears('slct-ano-grd','slct-ano-grd');                            
          echo "</div>

                <div class=\"col-sm-3\">

                    <label>Dias</label>
                        {$f->select(Daoagendas::slctDias(), "select mrg-bottom", "slct-dias", "slct-dias", "\"id\":\"slct-unidade\"", "id", "dia", "", null, "", "", "",true,"Selecione a Unidade")} 
                            
                </div>
                
                <div class=\"col-sm-2\">
                            
                    <label>Limite diário</label> 
                        {$f->input("number", "form-control mrg-bottom", "inp-limite", "inp-limite", "", "Qtd",true, "Digite o nº de vagas diário", "", "", "", "Vagas")} 
                            
                </div>
                
                <div class=\"col-sm-1 mrg-bottom\"> 
                    <label class=\"\">&nbsp;</label><br>
                    <button id=\"btn-salvargrid\" name=\"btn-salvargrid\" class=\"btn btn-success call-data\" type=\"button\" href=\"Agendasame/savegrid\" data-params='{\"dias\":\"slct-dias\",\"idEspec\":\"slct-espec\",\"idProf\":\"slct-prof\",\"limite\":\"inp-limite\",\"mes\":\"slct-mes-grd\",\"ano\":\"slct-ano-grd\"}' data-redirect=\"exetoload\" data-redirect-url=\"Agendasame\getGrade\" data-redirect-params='{\"idEspec\":\"slct-espec\",\"idProf\":\"slct-prof\",\"mes\":\"slct-mes-grd\",\"ano\":\"slct-ano-grd\"}' data-redirect-target=\"grades-geradas\">
                        <span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span>
                    </button>               

                </div>
                                            
                <div class=\"col-sm-12\" id=\"grades-geradas\" name=\"grades-geradas\">
                </div> 
                                                    
            </div> 
            
            <div class=\"col-sm-3\">
                <label class=\"\">Dias selecionados</label><br>
                
                <div class=\"col-sm-12\" id=\"dias-selecionados\" name=\"dias-selecionados\" style=\"height:300px;overflow-y:scroll\"> 
                </div> 
                
                <button id=\"btn-restauradias\" name=\"btn-restauradias\" class=\"btn btn-primary mrg-top\" type=\"button\" style=\"display:none;\">
                    Restaurar dias
                </button> 
            </div>
            
            <div class=\"col-sm-12 mrg-bottom\"> 

                <button id=\"btn-salvardias\" name=\"btn-salvardias\" class=\"btn btn-success mrg-top call-data\" type=\"button\" href=\"Agendasame/inputVagas\" data-params='{\"idEspec\":\"slct-espec\",\"idProf\":\"slct-prof\",\"mes\":\"slct-mes-grd\",\"ano\":\"slct-ano-grd\"}' data-redirect=\"load\" data-redirect-target=\"cnt-distribuidor\">
                    Salvar e distribuir
                </button>               

            </div>
                                   
        </fieldset> 
                        
    </div>
    
    <div id=\"div-container-distribuidor-vagas\" class=\"col-sm-12 white\" style=\"display:none;\">

        <div id=\"distribuidor-vagas\" name=\"distribuidor-vagas\">
        
            <fieldset class=\"for-panel\">
                <legend class=\"text-primary\">Distribuidor de Vagas</legend>    

                <div class=\"col-sm-12 mrg-bottom\">
                    <h4><span class=\"text-danger\"><b id=\"nomeEspec\"></b> - <span id=\"nomeProf\" class=\"text-muted\"></span></h4>       
                </div>

            <div class=\"col-sm-12\" id=\"cnt-distribuidor\" name=\"cnt-distribuidor\">
            </div>

            </fieldset>
            
        </div>
        
    </div>

</div>"; 