<?php

$f = new Functions();

if($this->getData('data')) {
    $itens = $this->getData('data');
}

if(count($itens) > 0){
    
echo 
"<a name=\"btn-imp-inventario\" id=\"btn-imp-inventario\" class=\"btn btn-primary mrg-bottom pull-right newtab\" data-redirect=\"\" href=\"#\" data-params='{\"idUnidade\":\"slct-unidade-patrimonio\",\"sala\":\"{$itens[0]['id_sala']}\"}' data-url=\"Reports/inventario\" style=\"margin-right:10px;\">
    <span class=\"glyphicon glyphicon-print\"></span>
</a>
    
<table class=\"table table-hover mrg-bottom\">
    <thead class=\"\">
    
        <tr>
            <th></th>
            <th>Descrição</th>
            <th>Patrimônio</th>                        
            <th>Estado</th>  
            <th>Qtd</th>            

        </tr>

    </thead>              

    <tbody>";

    foreach ($itens as $i) { 
        $i['patrimonio'] == 0 ? $patrimonio = "SP" : $patrimonio = $i['patrimonio']; 
        echo 
        "<tr> 
            <td>
                {$f->btnQuestion("btn-danger", "btn-del-item", "btn-del-item", "Patrimonio/delItem", "<span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span>", "\"id\":\"{$i['id']}\"", "Atenção", "Excluir Item?","reload","Patrimonio/getItens","\"idUnidade\":\"{$i['id_unidade']}\",\"idSala\":\"{$i['id_sala']}\"","sala-{$i['id_sala']}")}
            </td>
            <td>{$i['item']}</td>
            <td>{$patrimonio}</td>
            <td>{$i['estado']}</td>
            <td>{$i['quantidade']}</td>
           
        </tr>"; 
    }
    
    echo "
     
    </tbody>

</table>";

} else {
    echo "<b class=\"text-danger mrg-left\">Nenhum registro encontrado!</b>";
}