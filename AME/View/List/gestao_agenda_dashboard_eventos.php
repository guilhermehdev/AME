<?php
$eventos = $this->getData('eventos');
$tipoLabel = [
    'AUSENCIA' => ['Ausência', 'danger', 'remove'],
    'ATRASO' => ['Atraso', 'warning', 'time'],
    'REAGENDAMENTO' => ['Reagendamento', 'info', 'calendar'],
    'FERIAS' => ['Férias', 'primary', 'plane'],
    'LICENCA' => ['Licença', 'purple', 'briefcase'],
    'OUTRO' => ['Outro', 'default', 'info-sign']
];

if (empty($eventos)) {
    echo '<p class="text-muted" style="padding:15px;">Nenhum evento encontrado para este período.</p>';
    return;
}

echo '<div class="row dashboard-eventos-cards">';
foreach ($eventos as $ev) {
    $tipo = $tipoLabel[$ev['tipo']] ?? [$ev['tipo'], 'default', 'info-sign'];
    $data = Functions::BRdateFormat($ev['data_evento']);
    $prof = $ev['nome_servidor'] ?: 'Sem profissional fixo';
    $reagendado = $ev['dt_reagend']
        ? '<div class="small dashboard-evento-reagendamento"><span class="glyphicon glyphicon-calendar"></span> Reagendado para ' . Functions::BRdateFormat($ev['dt_reagend']) . '</div>'
        : '';

    echo '<div class="col-sm-6 col-md-4">
        <div class="panel panel-default dashboard-evento-card" style="border:1px solid #ccc;">
            <div class="panel-heading" style="padding:8px 12px;">
                <span class="dashboard-evento-data">' . $data . '</span>
                <span class="label label-' . $tipo[1] . ' pull-right">
                    <span class="glyphicon glyphicon-' . $tipo[2] . '"></span> ' . $tipo[0] . '
                </span>
            </div>
            <div class="panel-body" style="padding:12px; line-height:1.25;">
                <div class="dashboard-evento-especialidade" style="margin-bottom:2px;">' . htmlspecialchars($ev['especialidade']) . '</div>
                <div class="dashboard-evento-profissional" style="margin-top:0;">' . htmlspecialchars($prof) . '</div>
                <p class="dashboard-evento-descricao" style="margin:10px 0 6px;">' . htmlspecialchars($ev['descricao']) . '</p>
                ' . $reagendado . '
            </div>
        </div>
    </div>';
}
echo '</div>';
