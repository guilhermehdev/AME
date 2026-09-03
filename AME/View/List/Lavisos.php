<?php
$f = new Functions();
$idUser = AppController::checkSession()['id'];        
$avisos = Daonotificacoes::getAvisos($idUser);

if(count($avisos) > 0){

echo 
"   
<fieldset class=\"for-panel\">
    <legend class=\"text-primary\">Suas mensagens cadastradas</legend> 

        <table class=\"table table-hover mrg-bottom\">
            <thead class=\"\">
                <tr> 
                    <th style=\"width:60px;\">#</th>
                    <th style=\"width:120px;\">Status</th>
                    <th>Mensagem</th> 
                    <th style=\"width:160px;\">Postado</th>  
                </tr>

            </thead>              

            <tbody>";

            foreach ($avisos as $a) { 
                if($a['status'] == 1){
                    $sts = "<select class=\"select call-data\" href=\"Notificacoes/updatests\" data-params='{\"id\":\"{$a['id']}\",\"sts\":\"0\"}' data-redirect=\"\" id=\"slct-aviso-sts\" name=\"slct-aviso-sts\">
                                <option value=\"1\" selected> Ativo </option>
                                <option value=\"0\"> Inativo </option>
                            </select>";                        
                }else{
                    $sts = "<select class=\"select call-data\" href=\"Notificacoes/updatests\" data-params='{\"id\":\"{$a['id']}\",\"sts\":\"1\"}' data-redirect=\"\" id=\"slct-aviso-sts\" name=\"slct-aviso-sts\">
                                <option value=\"1\"> Ativo </option>
                                <option value=\"0\" selected> Inativo </option>
                            </select>"; 
                }
                echo 
                "<tr>                     
                    <td>
                        {$f->btnQuestion("btn-danger", "btn-del-aviso", "btn-del-aviso", "Notificacoes/delaviso", "<span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span>", "\"id\":\"{$a['id']}\"", "Atenção", "Excluir esta mensagem?","refresh","Notificacoes/painel","","avisos")}
                    </td>
                    <td>{$sts}</td>                    
                    <td>".preg_replace("/{bar}/",'/',$a['texto'])."</td>
                    <td>{$f->BRfullDateTime($a['data'])}</td>                    
                </tr>"; 
            }

            echo "

            </tbody>

        </table>

</fieldset>";

} else {
    echo "<b class=\"text-default mrg-left\">Nenhuma mensagem cadastrada!</b>";
}