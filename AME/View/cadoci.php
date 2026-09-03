<?php

$mc = new Maincontroller();
$f = new Functions();
$title = $this->getData('title');

echo
"<div class=\"col-sm-12\">
    <div class=\"page-header\">
        <h2><small>Cadastros ></small> {$title}</h2>
    </div>
    
    <div class=\"col-sm-4\">
    
        <fieldset class=\"for-panel\">
            <legend class=\"text-primary\">Lançar Encaixe</legend>        

            <div class=\"col-sm-12\">  

                <label>Especialidades <span class=\"text-danger\">*</span></label>
                    {$f->select(Daoagendas::slctEspecs(), "select mrg-bottom", "slct-espec-encaixe", "slct-espec-encaixe", "", "id", "especialidade", "", null, "", "", "Agendasame/getProfByEspec", true, "Selecione a Especialidade")}

                <label>Profissional <span class=\"text-danger\">*</span></label>
                    <select class=\"select mrg-bottom\" name=\"slct-prof-encaixe\" id=\"slct-prof-encaixe\">
                        <option>----------------------</option>
                    </select>                       

                <label>Data <span class=\"text-danger\">*</span></label>
                    <input type=\"text\" class=\"form-control date calendar\" id=\"dtp-data-encaixe\" name=\"dtp-data-encaixe\">
                    
                <div class=\"col-sm-3\">
                    <label class=\"\">Qtd</label> 
                        {$f->input("number", "form-control mrg-bottom", "qtd-encaixes", "qtd-encaixes", "", "Qtd", true, "Digite nº encaixes", 1, 1, "", "Digite nº encaixes")}  

                    <button id=\"btn-salvarEncaixe\" name=\"btn-salvarEncaixe\" class=\"btn btn-success call-data\" type=\"button\" href=\"Agendasame/saveEncaixes\" data-params='{\"idEspec\":\"slct-espec-encaixe\",\"idProf\":\"slct-prof-encaixe\",\"data\":\"dtp-data-encaixe\",\"qtd\":\"qtd-encaixes\"}'> 
                        Salvar 
                    </button>

                </div>  
                
            </div>            
            
        </fieldset>
        
    </div>  
    
    <div class=\"col-sm-8\">

        <fieldset class=\"for-panel\">
            <legend class=\"text-primary\">Busca</legend> 

            <div class=\"col-sm-5\">
                <label>Periodo</label>
                    <div class=\"input-group input-daterange\">
                        <input id=\"inp-dataini-encaixe\" name=\"inp-dataini-encaixe\" type=\"text\" class=\"form-control date calendar\" required>
                        <div class=\"input-group-addon\">a</div>
                        <input id=\"inp-datafin-encaixe\" name=\"inp-datafin-encaixe\" type=\"text\" class=\"form-control date calendar\" required>
                            <span class=\"input-group-btn\">
                            
                                <button id=\"btn-busca-encaixe\" name=\"btn-busca-encaixe\" class=\"btn btn-primary call-data\" type=\"button\" href=\"Agendasame/getEncaixes\" data-params='{\"idEspec\":\"slct-espec-encaixe\",\"idProf\":\"slct-prof-encaixe\",\"dataIni\":\"inp-dataini-encaixe\",\"dataFin\":\"inp-datafin-encaixe\"}' data-redirect=\"load\" data-redirect-target=\"container-encaixes\">
                                    <span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\"></span>
                                </button>
                                
                            </span>
                    </div>                
            </div> 
            
            <div class=\"col-sm-12\">
               
                <div class=\"mrg-top\" id=\"container-encaixes\" name=\"container-encaixes\"></div>
            </div> 

        </fieldset>       
        
    </div>
   
</div>";
