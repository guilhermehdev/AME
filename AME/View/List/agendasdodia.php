<?php
$f = new Functions();
$agendas = $this->getData('agendas');

if(count($agendas)) {
    
    foreach ($agendas as $a) {
        
        $d = Daonotificacoes::gradeSemanal($a['id_servidor'], $a['id_dia'], $a['id_espec']); 
              
        echo "<div class=\"col-sm-6\" style=\"font-size:13px;\">
                <fieldset class=\"for-panel\" style=\"height:160px;\">
                <legend class=\"text-primary\" style=\"font-size:13px;\"><span class=\"\"><b><u>{$a['especialidade']}</u></b></span></legend>
                    <i><span class=\"text-muted\">{$d[0]['periodo']} - <b>Sala {$d[0]['sala']}</b></span></i><br>"
                    . "<i><span class=\"text-muted\">Profissional</span></i> <br> <span class=\"text-primary\">{$a['profissional']}</span><br>"
                    . "<i><span class=\"text-muted\">Responsável</span></i> <br> <span class=\"text-orange\">{$d[0]['responsavel']}</span><br>"               
             . "</fieldset>"
           ."</div>";        
    }        
}else{
    
}
