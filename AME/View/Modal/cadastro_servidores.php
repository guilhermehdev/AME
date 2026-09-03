<?php
$f = new Functions();

echo 
"<div class=\"col-sm-12\">
       
        <div class=\"row\">
        
            <div class=\"col-sm-12\">            

                <div class=\"col-sm-12\">   
                                        
                    <div class=\"col-sm-12\">
                        <label>Nome</label><br>
                        {$f->input("text", "form-control", "inp-nome-servidor", "inp-nome-servidor", "", "Nome", true, "Digite o Nome")}   
                    </div> 
                    
                    <div class=\"col-sm-12\">
                        <label>CPF</label><br>
                        {$f->input("text", "form-control cpf", "inp-cpf-servidor", "inp-cpf-servidor", "", "CPF", true, "Digite o CPF")}   
                    </div>  

                    <div class=\"col-sm-12\">                        
                        <button type=\"button\" id=\"btn-submit-servidor\" class=\"btn btn-success\" href=\"Daopacientes/save\" data-params='{\"nome\":\"inp-nome-servidor\",\"cpf\":\"inp-cpf-servidor\",\"setor\":\"3\",\"unidade\":\"1\"}'>Salvar </button>
                    </div>

                </div>              
            
            </div>                        
        
        </div>                
    
</div>";