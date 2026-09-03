<?php
$f = new Functions(); 
if($this->getData('dados')){    
    $results = $this->getData('dados'); 

foreach ($results as $p) {
       
echo    
   "<table class=\"table table-active table-condensed\">
        <thead class=\"\">"
            . "<th>Patrimônio</th>"            
            . "<th>Unidade</th>"                    
            . "<th>Sala</th>" 
            . "<th>Descrição</th>" 
        ."</thead>
        
        <tbody>
            <tr class=\"bg-warning\">"
                . "<th>{$p['patrimonio']}</th>"            
                . "<th>{$p['unidade']}</th>"                    
                . "<th>{$p['sala']}</th>" 
                . "<th>{$p['item']}</th>" 
            . "</tr>
            <tr>";
    }
    
echo 
        "</tbody>        
    </table>";

} else {
    echo "Nenhum registro encontrado!";
}