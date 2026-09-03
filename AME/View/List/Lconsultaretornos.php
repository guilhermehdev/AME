<?php
$f = new Functions();
$msg = "<b class=\"text-default mrg-left\">Nenhum registro encontrado!</b>";

if($this->getData('retornos')) {
    $retorno = $this->getData('retornos');
    $pac = $this->getData('paciente');
    $exc = AppController::checkSession()['exc_retorno'];            
}

    foreach ($retorno as $r) { 
        
        if(count($r) > 0){
            echo   
            "
            <h3><span class=\"text-orange\">{$r[0]['paciente']}</span> - <span class=\"\">{$f->BRdateFormat($r[0]["nascimento"])}</span> | <span class=\"text-skyblue\">{$r[0]['tel']}</span></h3><hr><br>   
            <table class=\"table table-hover mrg-bottom\">
                <thead class=\"\">

                <tr> 
                        <th>Ações</th>           
                        <th>Data da consulta</th>
                        <th>Médico</th>                        
                        <th>Responsável</th>  
                        <th>Desfecho</th>            
                        <th>Tempo de retorno</th> 
                        <th>Data do retorno</th> 
                        <th>USAFA</th> 
                </tr>

            </thead>              

            <tbody>";
        } else {
           
        }
        
        foreach ($r as $v) {   
            if(count($v) > 0){
                                    
                $dsf = $v['desfecho'] == 0 ? "ALTA PARA UBS" : "RETORNO";
                $dr = $v['data_retorno'] == null ? "null" : $f->BRdateFormat($v['data_retorno']);
                $dtrt = $dr == "null"?"":$f->BRdateFormat($v['data_retorno']);
                echo 
                "<tr style='border-bottom:1px solid #ccc'>  
                    <td>
                        <button type=\"button\" id=\"btn-edit-retorno\" name=\"btn-edit-retorno\" class=\"btn btn-warning call-modal\" data-modal-title=\"Editar: {$pac[0]['nome']}\" data-modal-type=\"4\" data-modal-size=\"1\" data-modal-cls=\"md-dialog\" data-modal-href=\"Retornos/formedit\" data-modal-params={\"id\":\"{$v['id']}\",\"dtconsulta\":\"{$v['data_consulta']}\",\"medico\":\"{$v['id_servidor']}\",\"desfecho\":\"{$v['desfecho']}\",\"tempo\":\"".urlencode($v['tempo_retorno'])."\",\"dtretorno\":\"{$f->ENdateFormat($dr)}\",\"idpac\":\"{$v['idpac']}\",\"unidade\":\"{$v['idUnidade']}\"}> 
                            <span class=\"glyphicon glyphicon-edit\" aria-hidden=\"true\"></span>
                        </button> "; 
                        if($exc == 1) {       
                        echo $f->btnQuestion("btn-danger", "btn-del-retorno", "btn-del-retorno", "Retornos/del", "<span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span>", "\"id\":\"{$v['id']}\"", "Atenção", "Excluir Retorno?", "reload", "Retornos/getconsulta", "\"id\":\"{$v['idpac']}\",\"nasc\":\"null\"", "container-consultas");
                        } 
              echo "</td> 
                    <td>{$f->BRdateFormat($v['data_consulta'])}</td>
                    <td>{$v['medico']}</td>            
                    <td>{$v['responsavel']}</td>
                    <td>{$dsf}</td>
                    <td>{$v['tempo_retorno']}</td>
                    <td>{$dtrt}</td>
                     <td>{$v['USAFA']}</td>
                </tr>"; 
            }
        }
    
    echo "     
        </tbody>

    </table><br>";
    }