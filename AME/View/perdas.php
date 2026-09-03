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
            <legend class=\"text-primary\">Lançar Vagas</legend>        

            <div class=\"col-sm-5\">                               

                    <label>Unidades <span class=\"text-danger\">*</span></label>
                        {$f->select(Daoagendas ::slctUnidades(), "select mrg-bottom", "slct-unidade-perdas", "slct-unidade-perdas", "\"id\":\"slct-unidade\"", "id", "descricao", "", null, "", "", "",true,"Selecione a Unidade")}

                    <label>Especialidades <span class=\"text-danger\">*</span></label>
                        {$f->select(Daoagendas ::slctEspecs(), "select mrg-bottom", "slct-espec-perdas", "slct-espec-perdas", "\"id\":\"slct-espec\"", "id", "especialidade", "", null, "", "", "",true,"Selecione a Especialidade")}

                    <label>Profissionais <span class=\"text-danger\">*</span></label>
                        {$f->select(Daoagendas ::slctProf(), "select mrg-bottom", "slct-prof-perdas", "slct-prof-perdas", "\"id\":\"slct-prof\"", "id", "nome", "", null, "", "","",true,"Selecione o Profissional")}
                            
                    <label class=\"\">Vagas perdidas</label> 
                        {$f->input("number", "form-control mrg-bottom", "inp-vagas", "inp-vagas", "", "Qtd",true, "Digite nº vagas", "", "", "", "Digite nº vagas")}               
                    
            </div> 

            <div class=\"col-sm-6\"> 
            
                <div class=\"col-sm-7\"> 
                    <label>Selecione as Datas</label>
                        <div id=\"dtp-adddatas\" name=\"dtp-adddatas\" class=\"calendar dinamicrows call-data\" href=\"\" data-redirect-target=\"tbl-datas\">                                    
                        </div>
                </div>
                    
                <div class=\"col-sm-5\">
                    <label>Datas selecionadas</label>
                    <div class=\"panel\">

                        <table class=\"table table-hover\">
                            
                            <tbody id=\"tbl-datas\" name=\"tbl-datas\">                                        
                            </tbody>

                        </table>
                    </div>
                </div>
                
            </div>
            
            <div class=\"col-sm-12\">
                {$f->button("button", "btn btn-success", "btn-salvarPerda", "btn-salvarPerda", "Agendasame/saveperdas", "Lançar","\"idUnidade\":\"slct-unidade-perdas\",\"idEspec\":\"slct-espec-perdas\",\"idProf\":\"slct-prof-perdas\",\"vagas\":\"inp-vagas\"","none")}
            
            </div>
            
        </fieldset>
  
    <fieldset class=\"for-panel mrg-top\">
        <legend class=\"text-primary\">Vagas perdidas</legend>

        <div class=\"col-sm-12\">
        
            <div class=\"col-sm-2\">";
                Functions::selectYears('slct-ano-perdas','slct-ano-perdas');                            
      echo "</div>    
    
            <div class=\"col-sm-3\">

                <label>Mês</label>
                    <select class=\"select mrg-bottom call-data\" id=\"slct-mes-perdas\" name=\"slct-mes-perdas\" href=\"Agendasame/getPerdas\" data-params='{\"idUnidade\":\"slct-unidade-perdas\",\"idEspec\":\"slct-espec-perdas\",\"idProf\":\"slct-prof-perdas\",\"mes\":\"slct-mes-perdas\",\"ano\":\"slct-ano-perdas\"}' data-redirect=\"load\" data-redirect-target=\"perdas\">
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
            
            <div class=\"col-sm-2 mrg-bottom\">
                               
               
            </div>
            
        </div>
        
        <div class=\"col-sm-12\">
            <div id=\"perdas\" name=\"perdas\"></div>
        </div>
                
    </fieldset>
    
    
 
    </div>

</div>";