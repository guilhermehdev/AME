<?php

$mc = new Maincontroller();
$f = new Functions();
$o = new OCI();
$title = $this->getData('title');
$idUser = $_SESSION['adm']['id'] ;
$cboSUS = Daooci::getCBO($_SESSION['adm']['id']);
$cbo = $cboSUS[0]['cbo'];
$medico = $cboSUS[0]['SUS'];
echo
"<div class=\"col-sm-12\">
    <div class=\"page-header\">
        <h3>Cadastro {$title}</h3>
    </div>
    
    <input type=\"hidden\" name=\"inp-id\" id=\"inp-id\">
    <input type=\"hidden\" name=\"inp-medico\" id=\"inp-medico\" value=\"{$medico}\">
      
    <div class=\"col-sm-7\">

        <fieldset class=\"for-panel\">
            <legend class=\"text-primary\">Paciente</legend> 

             <div class=\"col-sm-12\"> 
             
                 <div class=\"col-sm-4\">
                        <label>Nascimento</label><br>
                        <div class=\"input-group\">
                            {$f->input("text", "form-control data-br", "inp-cad-dtnasc", "inp-cad-dtnasc", "", "Nascimento", "", "Digite a Data de nascimento","",10)}
                            <span class=\"input-group-btn\">
                            <button id=\"btn-fnd-pac-data\" name=\"btn-fnd-pac-data\" class=\"btn btn-primary call-modal\" data-modal-title=\"Selecione o Paciente\" data-modal-type=\"2\" data-modal-size=\"3\" data-modal-cls=\"sv-dialog\" data-modal-href=\"Pacientes/get\" data-modal-params='{\"data\":\"inp-cad-dtnasc\"}' data-check-input=\"inp-cad-dtnasc\" type=\"button\"><span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\">
                            </span> </button>
                            </span>
                        </div>
                </div>
		
                <div class=\"col-sm-8\">
                    <label>Nome</label><br>
                    <div class=\"input-group\">
                        {$f->input("text", "form-control", "inp-nome", "inp-nome", null, "Nome", true, "Digite o Nome",null,4)}  
                        <span class=\"input-group-btn\">
                        <button id=\"btn-fnd-pac-nome\" name=\"btn-fnd-pac-nome\" class=\"btn btn-primary call-modal\" data-modal-title=\"Selecione o Paciente\" data-modal-type=\"2\" data-modal-size=\"3\" data-modal-cls=\"sv-dialog\" data-modal-href=\"Pacientes/get\" data-modal-params='{\"name\":\"inp-nome\"}' data-check-input=\"inp-nome\" type=\"button\"><span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\">
                        </span> </button>
                        </span>
                    </div>
                </div>     
                
            </div>           
            
            <div class=\"col-sm-12\">
            
                <div class=\"col-sm-12 mrg-top\"> 
                     <fieldset class=\"for-panel\">
                        <legend class=\"text-primary\">OCI</legend>
                                           
                <div class=\"col-sm-9\">
                    <label>Procedimento <span class=\"text-danger\">*</span></label>
                           {$f->select(Daooci::getTipoOCI($idUser), "select mrg-bottom", "slct-oci-proced", "slct-oci-proced", "", "id", "abrev", "", null, "", "", "OCI/getCID", true, "Selecione o procedimento")}
                </div>
                
                  <div class=\"col-sm-3\">
                    <label>Data <span class=\"text-danger\">*</span></label>
                        <input type=\"text\" class=\"form-control date calendar mrg-bottom\" id=\"dtp-data-oci\" name=\"dtp-data-oci\">
                </div>
                
                <div class=\"col-sm-12\">
                    <label>CID <span class=\"text-danger\">*</span></label>
                        <select class=\"select mrg-bottom\" name=\"slct-oci-cid\" id=\"slct-oci-cid\">
                            <option>--------------</option>
                        </select>     
                </div>                
                              
                <div class=\"col-sm-12\">
                    <label>CID secundário</label>
                        <select class=\"select mrg-bottom\" name=\"slct-oci-cid-sec\" id=\"slct-oci-cid-sec\">
                            <option>--------------</option>
                        </select>     
                </div>
                
                <div class=\"col-sm-9\">
                    <label>Procedimento secundário</label>                      
                        <select class=\"select mrg-bottom\" name=\"slct-oci-proced-sec\" id=\"slct-oci-proced-sec\" disabled>
                            <option>--------------</option>
                        </select>                           
                </div>
                
                 <div class=\"col-sm-2\">
                    <label>Qtd</label>                      
                          <input type=\"number\" class=\"form-control\" id=\"inp-qtd-proced-sec\"  name=\"inp-qtd-proced-sec\" min=\"1\" max=\"100\" step=\"1\" value=\"1\" disabled>
                </div>
                
                <div class=\"col-sm-1\">
                    <label>&nbsp;</label><br>   
                        <button id=\"btn-add-proced-sec\"  name=\"btn-add-proced-sec\" class=\"btn btn-success call-data\" type=\"button\" href=\"Daooci/saveProcedSec\" data-params='{\"idPac\":\"inp-id\",\"procedSec\":\"slct-oci-proced-sec\",\"qtd\":\"inp-qtd-proced-sec\",\"cbo\":\"{$cbo}\",\"sus\":\"{$medico}\",\"data\":\"dtp-data-oci\"}' disabled>+</button>
                </div>
                
                <div class=\"mrg-top\" id=\"proceds-secs\" name=\"proceds-secs\">                   
                </div>
                
                <div class=\"col-sm-12\">
                     <span class=\"text-danger\"><i>* campos obrigatórios<i/></span>
                </div>      

        </fieldset>  
        
                <div class=\"col-sm-12\">               
                    <button id=\"btn-add-oci\" name=\"btn-add-oci\" class=\"btn btn-success call-data\" type=\"button\" href=\"OCI/addToList\" data-params='{\"medico\":\"inp-medico\",\"idPac\":\"inp-id\",\"data\":\"dtp-data-oci\",\"procedPrincipal\":\"slct-oci-proced\",\"cidPrincipal\":\"slct-oci-cid\",\"cidSecundario\":\"slct-oci-cid-sec\"}'> 
                        Adicionar à OCI
                    </button>
                </div> 
                   
            </div> 

        </div>
    </fieldset>         
   
</div>   

    <div class=\"col-sm-5 mrg-top\" id=\"container-fila-oci\" name=\"container-fila-oci\">    
    </div>  

</div>";