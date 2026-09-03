<?php
$f = new Functions();

if($this->getData('proceds')) {
    $proceds = $this->getData('proceds');
}

if(!empty($proceds)){
echo   
"<table class=\"table-procedimentos-secundarios\">
    <thead>
        <tr>
            <th style=\"width:80px;\">Procedimento</th>          
             <th style=\"width:20px;\">Qtd</th>
            <th style=\"width:370px;\">Descrição</th>           
            <th style=\"\">CBO</th>
            <th style=\"\"></th>
        </tr>
    </thead>
    <tbody>";

    foreach ($proceds as $p) {              
        echo            
           "<td style=\"padding:7px;\">{$p['cod']}</td>
            <td style=\"padding:7px;\">{$p['qtd']}</td>
            <td style=\"padding:7px;\">{$p['descricao']}</td>           
            <td style=\"padding:7px;\">{$p['cbo']}</td>
            <td style=\"padding:7px;\">
                <button type=\"button\" class=\"btn btn-sm btn-danger call-modal\" id=\"btn-del-proced-sec\" name=\"btn-del-proced-sec\" data-modal-params='{\"id\":\"{$p['id']}\"}' data-modal-title=\"Atenção\" data-modal-confirm=\"true\" data-modal-question=\"Excluir procedimento?\" data-modal-type=\"5\" data-modal-cls=\"advice-dialog\" data-modal-href=\"OCI/delProced\" data-redirect=\"reload\" data-modal-redirect-url=\"OCI/loadProcedSec\" data-modal-redirect-params='{\"data\":\"dtp-data-oci\",\"idPac\":\"inp-id\",\"medico\":\"inp-medico\"}' data-redirect-target=\"proceds-secs\" data-modal-close=\"true\"><span style=\"font-size:10px;\" class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span></button>
            </td>        
        </tr>"; 
    }    
    echo "     
    </tbody>
</table>";
} 