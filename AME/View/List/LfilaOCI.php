<?php
$f = new Functions();

if($this->getData('fila')) {
    $fila= $this->getData('fila');
}

if(!empty($fila)){
echo   
"<fieldset class=\"for-panel\" style=\"margin-top: -10px;\">
        <legend class=\"text-primary\">Atendimentos OCI - {$f->BRdateFormat($fila[0]['data'])}</legend> 
            <table class=\"table-fila-oci\">
                <thead>
                    <tr>
                        <th>Nome</th>    
                        <th></th>
                    </tr>
                </thead>
                <tbody>";

    foreach ($fila as $i) {              
        echo            
                "<tr>
                    <td>
                        <div  style=\"margin-bottom:5px;\" class=\"panel-group\" id=\"accordion-{$i['idPac']}\">
                                 <div style=\"background-color:transparent;\" class=\"panel panel-fila-oci\">  
                                     <a data-toggle=\"collapse\" id=\"link-collapse\" data-parent=\"#accordion\" href=\"#proced-pac-{$i['idPac']}\">

                                     <button id=\"btn-show-itens\" name=\"btn-show-itens\" class=\"btn btn-sm btn-default glyphicon glyphicon-plus call-data collapse-toogle\" href=\"OCI/loadProcedSec\" data-params='{\"data\":\"dtp-data-oci\",\"idPac\":\"{$i['idPac']}\",\"medico\":\"inp-medico\",\"listMode\":\"1\",\"status\":\"{$i['status']}\"}' data-redirect=\"load\" data-redirect-target=\"proced-pac-{$i['idPac']}\">
                                     </button> 
                                     
                                 </a>

                                 <span> <B class=\"text-dark-orange\">{$i['nome']}</B> - {$f->BRdateFormat($i['dtnasc'])} - CID:{$i['cid_principal']} / CID2:{$i['cid_secundario']}</span>                                                       
                                 <div id=\"proced-pac-{$i['idPac']}\" name=\"div-collapse\" class=\"panel-collapse collapse\">
                                     <hr>
                                     <div class=\"panel-body\" name=\"proced-pac-{$i['idPac']}\"></div>
                                 </div>

                         </div>

                     </div>
                     </td>          
                     <td>";
                     if($i['status'] == 0){
                         echo "<button type=\"button\" class=\"btn btn-sm btn-danger call-modal\" id=\"btn-del-fila-oci\" name=\"btn-del-fila-oci\" data-modal-params='{\"id\":\"{$i['id']}\"}' data-modal-title=\"Atenção\" data-modal-confirm=\"true\" data-modal-question=\"Excluir paciente da OCI?\" data-modal-type=\"5\" data-modal-cls=\"advice-dialog\" data-modal-href=\"OCI/delFila\" data-redirect=\"reload\" data-modal-redirect-url=\"OCI/loadFila\" data-modal-redirect-params='{\"data\":\"dtp-data-oci\",\"medico\":\"inp-medico\",\"proced\":\"slct-oci-proced\"}' data-redirect-target=\"container-fila-oci\" data-modal-close=\"true\"><span style=\"font-size:10px;\" class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span></button>
                     </td>        
                 </tr>"; 
                     }      
    }    
    echo "     
                </tbody>
            </table> 
</fieldset>  ";
} else {
    
}