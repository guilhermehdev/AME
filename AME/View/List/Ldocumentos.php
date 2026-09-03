<?php
$f = new Functions();

if($this->getData('docs')) {
    $docs = $this->getData('docs');
    $tipo = $docs[0]['tipo'];
}

if(count($docs) > 0){
    switch ($tipo) {
        case 0:
            $nametipo = "Memorandos";
            break;
         case 1:
            $nametipo = "Ofícios";
            break;
         case 2:
            $nametipo = "Circulares";
            break;       
    }
    
echo "<h3 class=\"text-primary\">".$nametipo."<hr></h3>";

    foreach ($docs as $d) {        
                
        echo 
        "<div class=\"col-sm-2\">
            Nº: <span class=\"text-success\"><b>{$d['ordem']}</b>/<b>{$d['ano']}</b></span> - <span>{$f->BRdateFormat($d['data'])}</span><br>  
         
            <a class=\"mrg-right call-modal\" data-modal-title=\"Visualizar documento\" data-modal-type=\"0\" data-modal-size=\"0\" data-modal-cls=\"lg-dialog\" data-modal-href=\"Documentos/viewdoc\" data-modal-params='{\"id\":\"{$d['id']}\"}' data-check-input=\"\" title=\"Clique para detalhes\"><img src=\"".APP_IMG."docthumb.png\" width=\"150px\" height=\"200px\"></a><br>
             
            <span>Assunto: <b>{$d['assunto']}</b></span>
        </div>"; 
    } 
    
    echo "
     
    </tbody>

</table>";
} else {
    echo "<b class=\"text-default mrg-left\">Nenhum registro encontrado!</b>";
}