<?php

$f = new Functions();

if($this->getData('vagas')) {
    $vagas = $this->getData('vagas');
}

if(count($vagas) > 0){

echo 
"<a name=\"btn-imp-agendas\" id=\"btn-imp-agendas\" class=\"btn btn-primary mrg-bottom pull-right call-data newtab\" data-redirect=\"\" href=\"Reports/agendas\" data-params='{\"idUnidade\":\"slct-unidade\",\"idEspec\":\"slct-espec\",\"idProf\":\"slct-prof\",\"dtIni\":\"dt-init\",\"dtFin\":\"dt-fin\"}'>
    <span class=\"glyphicon glyphicon-print\"> Imprimir</span>
</a>
    
<table class=\"table table-hover mrg-bottom\">
    <thead class=\"bg-black\">
        <tr> 
            <th>#</th>
            <th>Unidade</th>                        
            <th>Dia</th>  
            <th>Data</th>
            <th>Hora</th>
            <th>Especialidade</th>                        
            <th>Profissional</th> 
            <th>Vagas</th> 
            <th>Obs</th> 

        </tr>

    </thead>              

    <tbody>";

    foreach ($vagas as $v) { 
        echo 
        "<tr> 
            <td>
                {$f->btnQuestion("btn-danger", "btn-delAgendaAME", "btn-delAgendaAME", "Agendasame/delete", "<span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span>", "\"id\":\"{$v['idAgenda']}\"", "Excluir Agenda", "Excluir Agenda?","reload","Agendasame/getAgendas","\"idUnidade\":\"slct-unidade\",\"idEspec\":\"slct-espec\",\"idProf\":\"slct-prof\",\"dtIni\":\"dt-init\",\"dtFin\":\"dt-fin\"","agendas-geradas")}
            </td>
            <td>{$v['unidade']}</td>
            <td>{$v['dia']}</td>
            <td>{$f->BRdateFormat($v['data'])}</td>
            <td>{$v['hora']}</td>
            <td>{$v['especialidade']}</td>
            <td>{$v['profissional']}</td>
            <td>{$v['vagas']}</td>
            <td>{$v['obs']}</td>
        </tr>"; 
    }
    
    echo "
     
    </tbody>

</table>";

} else {
    echo "<div class=\"alert alert-danger\"> Nenhum registro encontrado...</div>";
}
