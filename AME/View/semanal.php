<?php
$mc = new Maincontroller();
$f = new Functions();
$title = $this->getData('title');

echo 
"<div class=\"col-sm-12\">
    <div class=\"page-header\">
        <h2><small>Cadastros ></small> {$title}</h2>
    </div>
    
    <div class=\"col-sm-8 white\">
    
        <fieldset class=\"for-panel\">
            <legend class=\"text-primary\">Dados da grade</legend>        

            <div class=\"col-sm-12\">   
                                           
                <div class=\"col-sm-5\">                
                    <label>Profissionais</label>
                        {$f->select(Daoagendas::slctProf(), "select mrg-bottom", "slct-prof", "slct-prof", "\"idEspec\":\"slct-espec\",\"idProf\":\"slct-prof\"", "id", "nome", "", null, "", "grades-geradas","Agendasame/getGrade",true,"Selecione o Profissional")}            
                </div>
                
                 <div class=\"col-sm-1\">                
                    <label>Dia</label>
                        {$f->select(Daoagendas::slctDias(), "select mrg-bottom", "slct-dias", "slct-dias", "", "id", "dia", "", null, "", "", "",true,"Selecione o Dia")}
                </div>
                
                <div class=\"col-sm-2\">
                    <label>Período</label>
                        {$f->select(Daoagendas::slctPeriodo(), "select mrg-bottom", "slct-periodos", "slct-periodos", "", "id", "descricao", "", null, "", "","",true,"Selecione  Período")}  
                </div>
                                           
                <div class=\"col-sm-1\">
                    <label>Sala</label>
                        {$f->select(Daoagendas::slctSalas(), "select mrg-bottom", "slct-salas", "slct-salas", "", "id", "numero", "", null, "", "","",true,"Selecione a Sala")}  
                </div>
                                              
                <div class=\"col-sm-3\">                            
                    <label>Responsável agenda</label> 
                        {$f->select(Daoagendas::slctUser(), "select mrg-bottom", "slct-usuario", "slct-usuario", "", "id", "nome", "", null, "", "","",true,"Selecione o Usuário")} 
                </div>                                                 
                                                                    
            </div>                       
            
            <div class=\"col-sm-12 mrg-bottom\"> 

                <button id=\"btn-salvardias\" name=\"btn-salvardias\" class=\"btn btn-success mrg-top call-data\" type=\"button\" href=\"Agendasame/savesemanal\" data-params='{\"idprof\":\"slct-prof\",\"iddias\":\"slct-dias\",\"idperiodo\":\"slct-periodos\",\"idsala\":\"slct-salas\",\"iduser\":\"slct-usuario\"}' data-redirect=\"load\" data-redirect-target=\"grades-semanal\">
                    Salvar
                </button>               

            </div>
                                   
        </fieldset> 
        
        <div class=\"col-sm-12\" id=\"grades-semanal\" name=\"grades-semanal\">
        </div> 
                        
    </div>   

</div>";