<?php
$f = new Functions();

if($this->getData('encaixes')) {
    $encaixes = $this->getData('encaixes');
}

if(!empty($encaixes)){
echo   
"<br><h5><span class=\"text-primary\">Resultados encontrados:</span></h5>
    
<table class=\"table table-hover mrg-bottom\">
    <thead class=\"\">    
        <tr>   
            <th></th>
            <th>Data</th>
            <th>Profissional</th>                        
            <th>Especialidade</th>  
            <th>Encaixes</th> 
        </tr>

    </thead>              

    <tbody>";

    foreach ($encaixes as $e) { 
        
        echo 
        "<tr>"
         . "<td>";
             echo $f->btnQuestion("btn-danger", "btn-del-encaixe", "btn-del-encaixe", "Agendasame/delEncaixe", "<span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span>", "\"id\":\"{$e['id']}\"", "Atenção", "Excluir Encaixe?", "reload", "Agendasame/getEncaixes", "\"idEspec\":\"{$e['id_espec']}\",\"idProf\":\"{$e['id_prof']}\",\"dataIni\":\"inp-dataini-encaixe\",\"dataFin\":\"inp-datafin-encaixe\"", "container-encaixes");
        echo
          "</td>"
         . "<td>{$f->BRdateFormat($e['data'])}</td>
            <td>{$e['prof']}</td>            
            <td>{$e['espec']}</td>
            <td>{$e['encaixes']}</td>        
        </tr>"; 
    }
    
    echo "
     
    </tbody>

</table>";
} else {
    echo "<b class=\"text-default mrg-left\">Nenhum registro encontrado!</b>";
}