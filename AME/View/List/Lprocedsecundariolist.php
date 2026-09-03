<?php
$f = new Functions();

if($this->getData('proceds')) {
    $proceds = $this->getData('proceds');
    $idPac = $this->getData('idPac');
    $status = $this->getData('sts');
}

if(!empty($proceds)){
echo   
"<table style=\"font-size:11px;\">
    <thead style=\"border:none;\">
        <tr>
            <th style=\"width:80px;background:transparent;color:#222;font-size:12px;border:none;\">Procedimento</th>          
             <th style=\"width:20px;background:transparent;color:#222;font-size:12px;border:none;\">Qtd</th>
            <th style=\"width:400px;background:transparent;color:#222;font-size:12px;border:none;\">Descrição</th>   
            <th style=\"background:transparent;\"></th>
        </tr>
    </thead>
    <tbody>";

    foreach ($proceds as $p) {              
        echo            
           "<td style=\"border:none;padding-left: 5px;\">{$p['cod']}</td>
            <td style=\"border:none;padding-left: 5px;\">{$p['qtd']}</td>
            <td style=\"border:none;padding-left: 5px;\">{$p['descricao']}</td>
            <td style=\"border:none;padding-left: 5px;\">";
        
         if($status == 0){
             echo   "<button type=\"button\" class=\"btn btn-sm btn-danger call-modal\" id=\"btn-del-proced-sec\" name=\"btn-del-proced-sec\" data-modal-params='{\"id\":\"{$p['id']}\"}' data-modal-title=\"Atenção\" data-modal-confirm=\"true\" data-modal-question=\"Excluir procedimento?\" data-modal-type=\"5\" data-modal-cls=\"advice-dialog\" data-modal-href=\"OCI/delProced\" data-redirect=\"reload\" data-modal-redirect-url=\"OCI/loadProcedSec\" data-modal-redirect-params='{\"data\":\"dtp-data-oci\",\"idPac\":\"inp-id\",\"medico\":\"inp-medico\",\"listMode\":\"1\"}' data-redirect-target=\"proced-pac-{$idPac}\" data-modal-close=\"true\"><span style=\"font-size:10px;\" class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span></button>";          
         }  
          echo 
           "</td>           
        </tr>"; 
    }    
    echo 
   "</tbody>
</table>";
} 