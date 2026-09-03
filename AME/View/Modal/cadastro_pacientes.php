<?php
$f = new Functions();

if($this->getData('pacs')){
    
    $pac = $this->getData('pacs');
       
}

echo 
"<div class=\"col-sm-12\">
       
        <div class=\"row\">
        
            <div class=\"col-sm-12\">            

                <div class=\"col-sm-12\">   
                                        
                    <div class=\"col-sm-12\">
                        <label>Nome</label><br>
                        {$f->input("text", "form-control", "inp-nome", "inp-nome", "", "Nome", true, "Digite o Nome")}   
                    </div>

                    <div class=\"col-sm-12\">
                        <label>Data de Nascimento</label><br>
                        {$f->input("text", "form-control date", "inp-cad-dtnasc", "inp-cad-dtnasc", "", "Data", true, "Digite a Data")}
                    </div>

                    <div class=\"col-sm-12\">                    
                        <label>Prontuário</label><br>                               
                        {$f->input("text", "form-control", "inp-pront", "inp-pront", "", "Prontuário",false,"")}
                    </div>                            

                    <div class=\"col-sm-12\">
                        <label>Contato</label><br>                           
                        <input type=\"text\" class=\"form-control tel\" id=\"inp-tel\" name=\"inp-tel\" data-rule-required=\"false\" data-msg-required=\"Digite um contato\" value=\"\">
                        <br>
                    </div> 

                    <div class=\"col-sm-12\">                        
                        <button type=\"button\" id=\"btn-submit-paciente\" class=\"btn btn-success\" href=\"Daopacientes/save\" data-params='{\"nome\":\"inp-nome\",\"dtnasc\":\"inp-cad-dtnasc\",\"pront\":\"inp-pront\",\"contato\":\"inp-tel\"}'>Salvar </button>
                    </div>

                </div>              
            
            </div>                        
        
        </div>                
    
</div>";