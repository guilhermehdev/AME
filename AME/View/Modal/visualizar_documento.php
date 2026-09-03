<?php
$f = new Functions();

if($this->getData('data')) {
    $data = $this->getData('data'); 
    $tipo = $docs[0]['tipo'];
}

switch ($tipo) {
    case 0:
        $nametipo = "Memorando";
        break;
     case 1:
        $nametipo = "Ofício";
        break;
     case 2:
        $nametipo = "Circular";
        break;       
}

foreach ($data as $v) {
    echo 
    "<div class=\"col-sm-12\">        
        
        <fieldset class=\"for-panel\">
            <legend class=\"text-primary\">{$nametipo}</legend>
                <span>Nº: <b>{$v['ordem']}</b> / <b>{$v['ano']}</b></span><br> 
                <span>Origem: <b>{$v['origem']}</b> <br> Destino: <b>{$v['destino']}</b></span><br><br>
                <span>Assunto: <b>{$v['assunto']}</b></span><br><br>

                <span>".urldecode($v['conteudo'])."</span>

        </fieldset>
        
        <div class=\"col-sm-12\">  
        
            <a target=\"_blank\" name=\"btn-imp-documento\" id=\"btn-imp-documento\" class=\"btn btn-primary mrg-bottom\" href=\"".URL."Html2pdf/documentos/{$v['id']}\">               
                <span>Imprimir</span>
            </a>
          
        </div>

    </div>";
}