<?php
$mc = new Maincontroller();
$f = new Functions();

if($this->getData('data')) {
    $data = $this->getData('data');
    $title = "Editar";
    $header = "";
    $col = 12;
    $s = 0;
        
    $id = $data[2];    
    $dtconsulta = $data[3] == NULL ? "" : $f->BRdateFormat($data[3]); 
    $medico = $data[4];
    $desfecho = $data[5];
    $tempo = $data[6];   
    $dtretorno = $data[7] == "null" ?  "" : $f->BRdateFormat($data[7]); 
    $idpac = $data[8];   
    $unidade = $data[9];   
    
    
} else {
    $title = $this->getData('title');
    $header = "<div class=\"page-header\">
                            <h2><small>Retornos ></small> {$title}</h2>                            
                        </div>";
    $col = 5;
    $s = 1;
}

echo 
"<div class=\"col-sm-12\">
    {$header}
    
    <div class=\"col-sm-{$col}\">    
       
                <form class=\"form-horizontal\" method=\"POST\" action=\"" . URL . "Retornos/save\" id=\"frm-retorno\" name=\"frm-retorno\">";
                if($s == 1){            
                    echo    
                    "<input type=\"hidden\" name=\"id_pac\" id=\"id_pac\" value=\"\" placeholder=\"Paciente\">

                        <div class=\"col-sm-4\">
                            <label>Nascimento</label> 
                            <div class=\"input-group\">
                                {$f->input("text", "form-control data-br", "search-nasc-pac", "search-nasc-pac", null, "Nascimento")}  
                                <span class=\"input-group-btn\">
                                
                                <button id=\"btn-fnd-pac-nasc\" name=\"btn-fnd-pac-nasc\" class=\"btn btn-primary call-modal\" data-modal-title=\"Selecione o Paciente\" data-modal-type=\"2\" data-modal-size=\"3\" data-modal-cls=\"lg-dialog\" data-modal-href=\"Agendasame/pacientesByDate\" data-modal-params='{\"dob\":\"search-nasc-pac\"}' data-check-input=\"search-nasc-pac\" type=\"button\">
                                
                                    <span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\">
                                    </span> 

                                </button>
                                </span>
                            </div>                            
                        </div>

                        <div class=\"col-sm-8\">
                            <label>Paciente</label> <span style=\"color:#888;\"><i>(digite para pesquisar)</i></span>
                                <div class=\"input-group\">                      
                                    <input type=\"text\" class=\"form-control\"  id=\"inp-pac\" name=\"inp-pac\" placeholder=\"Paciente\">
                                    <span class=\"input-group-btn\">
                                        <button id=\"btn-novo-pac\" name=\"btn-novo-pac\" class=\"btn btn-success call-data\" data-redirect=\"header\" type=\"button\" data-modal-title=\"Cadastrar novo  Paciente\" href=\"Pacientes/cad/1\" >
                                            <span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>";                    
                }
                echo "
                    <div class=\"col-sm-12\">
                                  
                         <label>Unidades</label>
                            {$f->select(Daoagendas::slctUnidadesAB(), "select mrg-bottom", "slct-unidade-retorno", "slct-unidade-retorno","", "id", "descricao", "", null, null, null, "","","","",$unidade)}                                  

                  
                    </div>
                    
                    <div class=\"col-sm-12\">

                        <label>Data da consulta</label><input type=\"text\" class=\"form-control calendar date mrg-bottom\" id=\"inp-data-consulta\" name=\"inp-data-consulta\" data-rule-required=\"true\" data-msg-required=\"Selecione a Data\" placeholder=\"Selecione uma data\" value=\"{$dtconsulta}\">

                    </div>

                    <div class=\"col-sm-12\">

                        <label>Profissional</label>
                            {$f->select(Daoagendas::slctProf(), "select mrg-bottom", "slct-prof", "slct-prof", "\"idEspec\":\"slct-espec\",\"idProf\":\"slct-prof\"", "id", "nome", "reload", null, "", "grades-geradas","",false,"Selecione um Profissional",null,$medico)}

                    </div>";
                            
                if($s == 1){
                    $dsb = "disabled";
                    echo
                    "<div class=\"col-sm-12\">

                        <label>Desfecho</label>
                            <select class=\"select mrg-bottom\" id=\"slct-desfecho\" name=\"slct-desfecho\" href=\"\" data-params='{}' data-redirect=\"\" data-redirect-target=\"\">                           
                                <option value=\"0\"> ALTA PARA UBS </option>
                                <option value=\"1\"> RETORNO </option>
                         
                           </select>

                    </div>                 

                    <div class=\"col-sm-12\">

                        <label>Tempo para retorno</label> 
                        <select class=\"select mrg-bottom\" id=\"slct-tempo\" name=\"slct-tempo\" href=\"\" data-params='{}' data-redirect=\"\" data-redirect-target=\"\" disabled>                   
                           <option value=\"1 SEMANA\">1 SEMANA </option>
                            <option value=\"2 SEMANAS\">2 SEMANAS </option>
                            <option value=\"3 SEMANAS\">3 SEMANAS </option>
                            <option value=\"4 SEMANAS\">4 SEMANAS </option>
                            <option value=\"1 MÊS\"> 1 MÊS </option>
                            <option value=\"2 MESES\"> 2 MESES </option>
                            <option value=\"3 MESES\"> 3 MESES </option>
                            <option value=\"4 MESES\"> 4 MESES </option>
                            <option value=\"5 MESES\"> 5 MESES </option>
                            <option value=\"6 MESES\"> 6 MESES </option>
                            <option value=\"7 MESES\"> 7 MESES </option>
                            <option value=\"8 MESES\"> 8 MESES </option>
                            <option value=\"9 MESES\"> 9 MESES </option>
                            <option value=\"10 MESES\"> 10 MESES </option>
                            <option value=\"11 MESES\"> 11 MESES </option>
                            <option value=\"1 ANO\"> 1 ANO </option>
                            <option value=\"RETORNO COM EXAMES\"> RETORNO COM EXAMES </option>
                        </select>

                    </div>";
                } else {
                    echo
                    "<div class=\"col-sm-12\">

                        <label>Desfecho</label>
                            <select class=\"select mrg-bottom\" id=\"slct-desfecho\" name=\"slct-desfecho\" href=\"\" data-params='{}' data-redirect=\"\" data-redirect-target=\"\">";                           
                            if($desfecho == 0){
                               echo
                               "<option value=\"0\" selected> ALTA PARA UBS </option>
                                <option value=\"1\"> RETORNO </option>";
                            } else {
                                echo
                               "<option value=\"0\"> ALTA PARA UBS </option>
                                <option value=\"1\" selected> RETORNO </option>";
                            }
                            echo
                           "</select>

                    </div>                 

                    <div class=\"col-sm-12\">";
                    if($desfecho == 1){
                        $opt = "<option value=\"{$tempo}\" selected> {$tempo} </option>";
                        $dsb = "";
                    } else {
                        $dsb = "disabled";
                        $opt = "";
                    }
                        echo
                       "<label>Tempo para retorno</label> 
                        <select class=\"select mrg-bottom\" id=\"slct-tempo\" name=\"slct-tempo\" href=\"\" data-params='{}' data-redirect=\"\" data-redirect-target=\"\" {$dsb}>                             {$opt}            
                            <option value=\"1 SEMANA\">1 SEMANA </option>
                            <option value=\"2 SEMANAS\">2 SEMANAS </option>
                            <option value=\"3 SEMANAS\">3 SEMANAS </option>
                            <option value=\"4 SEMANAS\">4 SEMANAS </option>
                            <option value=\"1 MÊS\"> 1 MÊS </option>
                            <option value=\"2 MESES\"> 2 MESES </option>
                            <option value=\"3 MESES\"> 3 MESES </option>
                            <option value=\"4 MESES\"> 4 MESES </option>
                            <option value=\"5 MESES\"> 5 MESES </option>
                            <option value=\"6 MESES\"> 6 MESES </option>
                            <option value=\"7 MESES\"> 7 MESES </option>
                            <option value=\"8 MESES\"> 8 MESES </option>
                            <option value=\"9 MESES\"> 9 MESES </option>
                            <option value=\"10 MESES\"> 10 MESES </option>
                            <option value=\"11 MESES\"> 11 MESES </option>
                            <option value=\"1 ANO\"> 1 ANO </option>
                            <option value=\"RETORNO COM EXAMES\"> RETORNO COM EXAMES </option>
                        </select>

                    </div>";
                }              
                            
                if($s == 1){                     
                    echo
                   "<div class=\"col-sm-1 mrg-bottom\"> 

                        <button id=\"btn-save-retorno\" name=\"btn-save-retorno\" class=\"btn btn-success submit call-data\" type=\"button\" href=\"Retornos/get\" data-params='{\"paciente\":\"id_pac\"}' data-redirect=\"load\" data-redirect-target=\"retornos-cadastrados\">
                            Salvar
                        </button>               

                    </div>";
                } else {
                    echo
                    "<div class=\"col-sm-1 mrg-bottom\"> 
                        <button id=\"btn-update-retorno\" name=\"btn-update-retorno\" class=\"btn btn-warning call-data\" type=\"button\"  data-url=\"Retornos/edit\" data-edit-params='{\"id\":\"{$id}\",\"unidade\":\"slct-unidade-retorno\",\"dtconsulta\":\"inp-data-consulta\",\"medico\":\"slct-prof\",\"desfecho\":\"slct-desfecho\",\"tempo\":\"slct-tempo\"}' href=\"Retornos/getconsulta\" data-params='{\"id\":\"{$idpac}\",\"nasc\":\"null\"}' data-redirect=\"load\" data-redirect-target=\"container-consultas\">
                            Salvar
                        </button>               

                    </div>";
                }
               echo     
               "</form> 
                        
    </div>
     
    <div class=\"col-sm-7\" id=\"container-retornos\" style=\"display:none;\" >

        <fieldset class=\"for-panel\">
            <legend class=\"text-primary\">Histórico do Paciente</legend>    

            <div class=\"col-sm-12 mrg-bottom\" id=\"retornos-cadastrados\" name=\"retornos-cadastrados\">
            </div>

        </fieldset>

    </div>   

</div>"; 