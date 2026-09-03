<?php

$f = new Functions();

if($this->getData('perdas')) {
    $perdas = $this->getData('perdas');
}

if(count($perdas) > 0){

echo 
"
<table class=\"table table-hover mrg-bottom\">
    <thead class=\"bg-black\">
        <tr> 
            <th>#</th>
            <th>Unidade</th>
            <th>Data</th>                        
            <th>Profissional</th>  
            <th>Especialidade</th>

        </tr>

    </thead>              

    <tbody>";

    foreach ($perdas as $p) { 
        echo 
        "<tr> 
            <td>
                {$f->btnQuestion("btn-danger", "btn-delvagaPerda", "btn-delvagaPerda", "Agendasame/delperdas", "<span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span>", "\"idPerdas\":\"{$p['id']}\"", "Excluir", "Excluir registro?","reload","Agendasame/getPerdas","\"idUnidade\":\"slct-unidade\",\"idEspec\":\"slct-espec\",\"idProf\":\"slct-prof\",\"mes\":\"slct-mes-perdas\",\"ano\":\"slct-ano-perdas\"","perdas")}
            </td>
            
            <td>{$p['unidade']}</td>
            <td>{$f->BRdateFormat($p['data'])}</td>
            <td>{$p['especialidade']}</td>
            <td>{$p['profissional']}</td> 
                
        </tr>"; 
    }
    
    echo "
     
    </tbody>

</table>";

} else {
    echo "<div class=\"alert alert-danger\"> Nenhum registro encontrado...</div>";
}