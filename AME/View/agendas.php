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
            <legend class=\"text-primary\">Gerar Vagas para impressão</legend>        

            <div class=\"col-sm-5\">                               

                    <label>Unidades <span class=\"text-danger\">*</span></label>
                        {$f->select(Daoagendas ::slctUnidades(), "select mrg-bottom", "slct-unidade", "slct-unidade", "\"id\":\"slct-unidade\"", "id", "descricao", "", null, "", "", "",true,"Selecione a Unidade")}

                    <label>Especialidades <span class=\"text-danger\">*</span></label>
                        {$f->select(Daoagendas ::slctEspecs(), "select mrg-bottom", "slct-espec", "slct-espec", "\"id\":\"slct-espec\"", "id", "especialidade", "", null, "", "", "",true,"Selecione a Especialidade")}

                    <label>Profissionais <span class=\"text-danger\">*</span></label>
                        {$f->select(Daoagendas ::slctProf(), "select mrg-bottom", "slct-prof", "slct-prof", "\"id\":\"slct-prof\"", "id", "nome", "", null, "", "","",true,"Selecione o Profissional")}
                            
                    <label class=\"\">Vagas</label> 
                        {$f->input("number", "form-control mrg-bottom", "inp-vagas", "inp-vagas", "", "Qtd",true, "Digite nº vagas", "", "", "", "Digite nº vagas")}

                <label class=\"\">Horário</label>
                {$f->input("text", "form-control hora mrg-bottom", "inp-horario", "inp-horario", "", "Hora",true, "Digite a Hora")} 

                <label>Obs</label>
                    {$f->textarea(4,"", "form-control mrg-bottom", "obs", "obs", "", "Observações", false, "",$obs)}
                    
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
                {$f->button("button", "btn btn-success", "btn-gerarAgenda", "btn-gerarAgenda", "Agendasame/save", "Salvar Agenda","\"idUnidade\":\"slct-unidade\",\"idEspec\":\"slct-espec\",\"idProf\":\"slct-prof\",\"vagas\":\"inp-vagas\",\"hora\":\"inp-horario\",\"obs\":\"obs\"","none")}
            
            </div>
            
        </fieldset>
  
    <fieldset class=\"for-panel mrg-top\">
        <legend class=\"text-primary\">Agendas cadastradas para Unidade selecionada</legend>

        <div class=\"col-sm-12\">
        
            <div class=\"col-sm-2\">";
                Functions::selectYears('slct-ano-agenda','slct-ano-agenda');                            
      echo "</div>    
    
            <div class=\"col-sm-3\">

                <label>Mês</label>
                    <select class=\"select mrg-bottom call-data\" id=\"slct-mes-agenda\" name=\"slct-mes-agenda\" href=\"Agendasame/getAgendas\" data-params='{\"idUnidade\":\"slct-unidade\",\"idEspec\":\"slct-espec\",\"idProf\":\"slct-prof\",\"mes\":\"slct-mes-agenda\",\"ano\":\"slct-ano-agenda\"}' data-redirect=\"load\" data-redirect-target=\"agendas-geradas\">
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

            <div class=\"col-sm-5 mrg-bottom\">
        
                <label>Período</label>
                <div class=\"input-group input-daterange\">

                    <input type=\"text\" id=\"dt-init\" name=\"dt-init\" class=\"form-control calendar date\">
                    <div class=\"input-group-addon\">a</div>
                    
                        <input type=\"text\" id=\"dt-fin\" name=\"dt-fin\" class=\"form-control  calendar date\">
                        <span class=\"input-group-btn\">
                            <button id=\"btn-searchAgendasGeradas\" name=\"btn-searchAgendasGeradas\" class=\"btn btn-default call-data\" type=\"button\" href=\"Agendasame/getAgendas\" data-redirect=\"load\" data-params='{\"idUnidade\":\"slct-unidade\",\"idEspec\":\"slct-espec\",\"idProf\":\"slct-prof\",\"dtIni\":\"dt-init\",\"dtFin\":\"dt-fin\"}' data-redirect-target=\"agendas-geradas\">
                            <span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\"></span>
                            </button>
                        </span>
                </div>
                                
            </div> 
            
            <div class=\"col-sm-2 mrg-bottom\">
            
                <label></label><br>
                <span class=\"text-danger\">* Selecione para refinar a busca</span> 
            
            </div>
            
        </div>
        
        <div class=\"col-sm-12\">
        
            <div id=\"agendas-geradas\" name=\"agendas-geradas\">
            </div>
            
        </div>

    </fieldset>
 
    </div>

</div>"; 