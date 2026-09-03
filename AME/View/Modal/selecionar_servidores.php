<?php
$f = new Functions(); 
if($this->getData('pacs')){    
    $users = $this->getData('pacs'); 
    $buscado = $this->getData('buscado');   

echo
"Exibindo resultados da busca para:<span class=\"\"> <b><i>{$f->uppercase($buscado)}</i></b></span>
    <br><br>
<table class=\"table table-active table-condensed\">
    <thead class=\"\">

    </thead>
    <tbody>";

foreach ($users as $p) {
echo    
        "<tr class=\"bg-success\">"
            . "<th></th>"            
            . "<th>Nome</th>"                    
            . "<th>CPF</th>"   
        . "</tr>
        <tr>"        
            . "<td class=\"text-nowrap\">
                    <button class=\"btn btn-primary\" name=\"btn-select-servidor\" id=\"btn-select-servidor-{$p['id']}\" data-id=\"{$p['id']}\" data-params='{\"nome\":\"{$p['nome']}\",\"cpf\":\"{$p['CPF']}\"}' data-modal-close=\"true\">
                <span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>
                    </button>
               </td>"
            . "<td class=\"text-nowrap\">{$p['nome']}</td>"
            . "<td>{$p['CPF']}</td>" 
           
    . "</tr><tr><td colspan=5><hr></td></tr>";
    }
    
echo 
    "</tbody>        
</table>";

} else {
    echo "Nenhum registro encontrado!";
}