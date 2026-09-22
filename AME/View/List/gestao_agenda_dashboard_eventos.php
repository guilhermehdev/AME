<?php
$eventos = $this->getData('eventos');
$tipoLabel = [
    'AUSENCIA' => ['Ausência', 'danger', 'remove'],
    'FOLGA' => ['Folga', 'warning', 'calendar'],
    'REAGENDAMENTO' => ['Reagendamento', 'info', 'calendar'],
    'FERIAS' => ['Férias', 'primary', 'plane'],
    'LICENCA' => ['Licença', 'purple', 'briefcase'],
    'OUTRO' => ['Outro', 'default', 'info-sign']
];

if (empty($eventos)) {
    echo '<p class="text-muted" style="padding:15px;">Nenhum evento encontrado para este período.</p>';
    return;
}

echo '<div class="row dashboard-eventos-cards" style="margin-right:5px; margin-left:-5px;">';
$cardsDashboard = array();
// Agrupa primeiro por profissional + especialidade e, dentro do card,
// organiza os eventos por data.
$grupos = array();
foreach ($eventos as $ev) {
    $idServidor = isset($ev['id_servidor']) ? (int)$ev['id_servidor'] : 0;
    $idEspec = isset($ev['id_espec']) ? (int)$ev['id_espec'] : 0;
    $chave = $idServidor . '|' . $idEspec;
    if (!isset($grupos[$chave])) {
        $grupos[$chave] = array(
            'id_servidor' => $idServidor,
            'id_espec' => $idEspec,
            'nome_servidor' => $ev['nome_servidor'],
            'especialidade' => $ev['especialidade'],
            'datas' => array()
        );
    }
    if (!isset($grupos[$chave]['datas'][$ev['data_evento']])) {
        $grupos[$chave]['datas'][$ev['data_evento']] = array();
    }
    $grupos[$chave]['datas'][$ev['data_evento']][] = $ev;
}

foreach ($grupos as $grupo) {
    $prof = $grupo['nome_servidor'] ?: 'Sem profissional fixo';
    $titulo = htmlspecialchars($prof . ' - ' . $grupo['especialidade'], ENT_QUOTES, 'UTF-8');
    $conteudo = '';
    $primeiraData = key($grupo['datas']);
    $primeirosEventos = reset($grupo['datas']);
    $primeiroEvento = reset($primeirosEventos);
    $tipoPreview = isset($tipoLabel[$primeiroEvento['tipo']])
        ? $tipoLabel[$primeiroEvento['tipo']]
        : array($primeiroEvento['tipo'], 'default', 'info-sign');
    $totalEventosGrupo = 0;
    foreach ($grupo['datas'] as $eventosDaDataGrupo) {
        $totalEventosGrupo += count($eventosDaDataGrupo);
    }
    $eventosAdicionais = max(0, $totalEventosGrupo - 1);
    $preview = '<div class="dashboard-evento-card-preview">'
        . '<strong>' . Functions::BRdateFormat($primeiraData) . '</strong> '
        . '<span class="label label-' . $tipoPreview[1] . '">'
        . htmlspecialchars($tipoPreview[0], ENT_QUOTES, 'UTF-8') . '</span> '
        . '<span>' . htmlspecialchars($primeiroEvento['descricao'], ENT_QUOTES, 'UTF-8') . '</span>'
        . '</div>';
    $badgeAdicionais = $eventosAdicionais > 0
        ? '<span class="badge dashboard-evento-card-mais" title="' . $eventosAdicionais . ' ocorrências além da prévia">+' . $eventosAdicionais . '</span>'
        : '';

    foreach ($grupo['datas'] as $dataEvento => $eventosDaData) {
        $data = Functions::BRdateFormat($dataEvento);
        $itens = '';

        foreach ($eventosDaData as $ev) {
            $tipo = isset($tipoLabel[$ev['tipo']])
                ? $tipoLabel[$ev['tipo']]
                : array($ev['tipo'], 'default', 'info-sign');
            $criadoEm = !empty($ev['criado_em'])
                ? Functions::BRfullDateTime($ev['criado_em'])
                : 'Data não registrada';
            $reagendado = $ev['dt_reagend']
                ? '<div class="small dashboard-evento-reagendamento"><span class="glyphicon glyphicon-calendar"></span> Reagendado para ' . Functions::BRdateFormat($ev['dt_reagend']) . '</div>'
                : '';

            $itens .= '<li style="margin-bottom:8px;">
                    <div class="dashboard-evento-descricao"><span class="label label-' . $tipo[1] . '"><span class="glyphicon glyphicon-' . $tipo[2] . '"></span> ' . $tipo[0] . '</span> &gt; ' . htmlspecialchars($ev['descricao']) . '</div>
                    ' . $reagendado . '
                    <div class="small text-muted">Criado em: ' . $criadoEm . '</div>
                </li>';
        }

        $conteudo .= '<div class="dashboard-evento-data-grupo" style="margin-bottom:10px;">
                <div style="font-weight:bold; margin-bottom:5px;"><span class="glyphicon glyphicon-calendar"></span> ' . $data . '</div>
                <ul style="padding-left:18px; margin:0;">' . $itens . '</ul>
            </div>';
    }

    $cardsDashboard[] = '<div class="col-sm-6 col-md-4">
        <div class="panel panel-default dashboard-evento-card" data-profissional-especialidade="' . $titulo . '" data-id-servidor="' . $grupo['id_servidor'] . '" data-id-espec="' . $grupo['id_espec'] . '" data-total-ocorrencias="' . $totalEventosGrupo . '" style="border:1px solid #ccc;">
            <div class="panel-heading" style="padding:8px 12px; color:#337ab7; font-weight:bold;">
                <span style="font-size:11px;">' . $titulo . '</span>
                ' . $preview . '
                ' . $badgeAdicionais . '
                <span class="glyphicon glyphicon-chevron-down dashboard-evento-expandir-icon"
                      aria-hidden="true" title="Passe o mouse para expandir"></span>
            </div>
            <div class="panel-body" style="padding:12px; line-height:1.25; max-height:420px; overflow-y:auto;">
                ' . $conteudo . '
            </div>
        </div>
    </div>';
}

// Mantém uma estrutura de colunas desde o HTML inicial. Assim, a expansão de
// um card não altera a posição dos cards que estão na coluna ao lado.
$quantidadeColunasDashboard = 3;
for ($coluna = 0; $coluna < $quantidadeColunasDashboard; $coluna++) {
    echo '<div class="dashboard-eventos-coluna">';
    for ($indiceCard = $coluna; $indiceCard < count($cardsDashboard); $indiceCard += $quantidadeColunasDashboard) {
        echo $cardsDashboard[$indiceCard];
    }
    echo '</div>';
}
echo '</div>';
