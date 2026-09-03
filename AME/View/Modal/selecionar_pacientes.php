<?php
$f = new Functions(); 
 $formCad = "<div class=\"col-sm-12 mrg-top\" style=\"text-align:left;\" >
     
                    <input type=\"hidden\" name=\"inp-id-logra-modal\" id=\"inp-id-logra-modal\">
                    <input type=\"hidden\" name=\"inp-pront-modal\" id=\"inp-pront-modal\">
                    <input type=\"hidden\" name=\"inp-complemento-modal\" id=\"inp-complemento-modal\">
        
                   <div class=\"col-sm-12 mrg-bottom\">
                        <span class=\"text-danger\">Para cadastrar um paciente preencha os campos abaixo.</span>
                   </div>

                    <div class=\"col-sm-2\">
                        <label>Nascimento <span class=\"text-danger\">*</span></label>                                  
                              {$f->input("text", "form-control data-br", "inp-cad-dtnasc-modal", "inp-cad-dtnasc-modal", "", "Data de nascimento", "", "Digite a Data","",10)}    
                    </div>
		
                    <div class=\"col-sm-5\">
                        <label>Nome <span class=\"text-danger\">*</span></label>
                            {$f->input("text", "form-control", "inp-nome-modal", "inp-nome-modal", null, "Nome", true, "Digite o Nome",null,4)}                           
                    </div>	
                            
                    <div class=\"col-sm-3\">
                      <label>CPF</label><br>
                        {$f->input("text", "form-control cpf-mask", "inp-cpf-modal", "inp-cpf-modal", "", "000.000.000-00", true,"","",14)}
                          <div id=\"popup-cpf\" class=\"popup-msg\">CPF inválido</div>
                  </div>
                  
                   <div class=\"col-sm-2\">                    
                        <label>Sexo <span class=\"text-danger\">*</span></label><br>
                        <select class=\"form-control\"  id=\"inp-sexo-modal\" name=\"inp-sexo-modal\">
                               <option value=\"\" selected disabled>----------</option>
                               <option value=\"M\">M</option>
                               <option value=\"F\">F</option>                                          
                       </select>     
                  </div>   

                   <div class=\"col-sm-5\">                    
                      <label>Nome da mãe</label><br>                               
                        {$f->input("text", "form-control", "inp-mae-modal", "inp-mae-modal", "", "")}
                  </div>   

                  <div class=\"col-sm-3\">
                      <label>Contato</label>                       
                        {$f->input("text", "form-control tel-mask", "inp-tel-modal", "inp-tel-modal", "", "(00)00000-0000", true,"Digite um contato")}  
                  </div>   
                  
                  <div class=\"col-sm-2\">
                      <label>CEP</label>    
                         <input type=\"text\" class=\"form-control\" id=\"inp-cep-modal\" name=\"inp-cep-modal\" data-rule-required=\"false\" data-params='{\"cep\":\"inp-cep-modal\"}' placeholder=\"00000-000\">                       
                  </div>   
                  
                   <div class=\"col-sm-2\">                    
                        <label>Número</label><br>
                         <input type=\"text\" class=\"form-control numeric-mask\" id=\"inp-numero-modal\" name=\"inp-numero-modal\" data-rule-required=\"false\" placeholder=\"\">                   </div>   
                  
                  <div class=\"col-sm-4 mrg-top\">    
                        <button type=\"button\" id=\"btn-submit-paciente-modal\" name=\"btn-submit-paciente-modal\" class=\"btn btn-success\" href=\"Daopacientes/save\" data-params='{\"nome\":\"inp-nome-modal\",\"dtnasc\":\"inp-cad-dtnasc-modal\",\"cpf\":\"inp-cpf-modal\",\"pront\":\"inp-pront-modal\",\"contato\":\"inp-tel-modal\",\"mae\":\"inp-mae-modal\",\"idLogra\":\"inp-id-logra-modal\",\"numero\":\"inp-numero-modal\",\"complemento\":\"inp-complemento-modal\",\"sexo\":\"inp-sexo-modal\"}'>Salvar </button>
                   </div>  
                  
                   <div class=\"col-sm-12 mrg-top\">
                        <span class=\"text-danger\"><i>* campos obrigatórios<i/></span>
                    </div>                         
               </div>";

if($this->getData('pacs')){    
    $users = $this->getData('pacs'); 
    $buscado = $this->getData('buscado');  
    
echo
"Exibindo resultados da busca para:<span class=\"\"> <b><i>{$f->uppercase($buscado)}</i></b></span>
    <br><br>
    <table class=\"table table-active table-condensed\">
        <thead class=\"\">
        </thead>
    <tbody>
        <tr class=\"bg-dark\">"
            . "<th></th>"            
            . "<th>Nome</th>"                    
            . "<th>Nascimento</th>" 
            . "<th>CPF</th>"
            . "<th>Nome da mãe</th>"                    
            . "<th>Prontuário</th>" 
            . "<th>Contato</th>"                       
        . "</tr>";

foreach ($users as $p) {    
      
echo "<td class=\"text-nowrap\">
                    <button class=\"btn btn-primary\" name=\"btn-select-paciente\" id=\"btn-select-paciente-{$p['id']}\" data-id=\"{$p['id']}\" data-params='{\"nome\":\"{$p['nome']}\",\"dtnasc\":\"{$p['dtnasc']}\",\"pront\":\"{$p['pront']}\",\"contato\":\"{$p['tel']}\",\"cpf\":\"{$p['cpf']}\",\"mae\":\"{$p['mae']}\",\"cep\":\"{$p['CEP']}\",\"tipo\":\"{$p['tipo']}\",\"logradouro\":\"{$p['logradouro']}\",\"bairro\":\"{$p['bairro']}\",\"numero\":\"{$p['numero']}\",\"complemento\":\"{$p['complemento']}\",\"idLogra\":\"{$p['id_logradouro']}\",\"sexo\":\"{$p['sexo']}\"}' data-modal-close=\"true\">
                <span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>
                    </button>
               </td>"
            . "<td class=\"text-nowrap\">{$p['nome']}</td>"
            . "<td>{$f->BRdateFormat($p['dtnasc'])}</td>"                 
            . "<td>{$p['cpf']}</td>"
            . "<td>{$p['mae']}</td>"
            . "<td>{$p['pront']}</td>"
            . "<td>{$p['tel']}</td>"              
           
    . "</tr><tr><td colspan=7><hr>        
        </td></tr>";
    }
    
echo 
    "</tbody>        
</table>
        <div class=\"col-sm-12 mrg-top\" style=\"text-align:left;\">
            <button type=\"button\" id=\"btn-show-paciente-form\" name=\"btn-show-paciente-form\" class=\"btn btn-success\" href=\"\" data-params='{}'>Novo cadastro </button>
        </div>
        <div  id=\"hidden-cad-pac\" name=\"hidden-cad-pac\" style=\"display:none;\">
            {$formCad}
        </div>
";

} else {
    echo $formCad;
}