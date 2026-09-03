<?php
    
echo "<div class=\"col-sm-12\">";    
  
    $arrSalas = $this->getData("data"); 
    $idUnidade = $this->getData("idUnidade"); 
       
if(count($arrSalas) > 0) {
    
    echo "
        
    <fieldset class=\"for-panel\">
            <legend class=\"text-primary\">Inventário</legend>
            
            <div style=\"margin-bottom:50px;\">
                <a name=\"btn-imp-inventario\" id=\"btn-imp-inventario\" class=\"btn btn-primary mrg-bottom pull-right newtab\" data-redirect=\"\" href=\"#\" data-params='{\"idUnidade\":\"{$idUnidade}\"}' data-url=\"Reports/inventarioUnidade\" style=\"margin-right:10px;\">
    <span class=\"glyphicon glyphicon-print\"> Imprimir</span>
                </a>
            </div>";

        foreach ($arrSalas as $s) {            

        echo "      
            <div class=\"panel-group\" id=\"accordion\">

                <div class=\"panel panel-patrimonio\">  

                    <a data-toggle=\"collapse\" id=\"link-collapse\" data-parent=\"#accordion\" href=\"#sala-{$s['id']}\">
                        <button id=\"btn-show-itens\" name=\"btn-show-itens\" class=\"btn btn-default glyphicon glyphicon-plus call-data collapse-toogle\" href=\"Patrimonio/getItens\" data-params='{\"idUnidade\":\"{$idUnidade}\",\"idSala\":\"{$s['id']}\"}' data-redirect=\"load\" data-redirect-target=\"sala-{$s['id']}\">
                        </button> 
                    </a>
                    
                    <span class=\"text-success mrg-left font-md w100\">                    
                       
                        <span class=\"text-success\">Sala: <B>{$s['descricao']}</B></span>
                      
                    </span>
                    
                    <div id=\"sala-{$s['id']}\" name=\"div-collapse\" class=\"panel-collapse collapse\">
                        <hr>
                        <div class=\"panel-body\" name=\"sala-{$s['id']}\"></div>
                    </div>

                </div>

            </div>
                ";

            }

        echo "</fieldset>

        </div>";
        
} else {
  echo "<fieldset class=\"for-panel\">
            <legend class=\"text-primary\">Resultados</legend>
                <b class=\"text-danger mrg-left\">Nenhum registro encontrado!</b>
        </fieldset>";
}