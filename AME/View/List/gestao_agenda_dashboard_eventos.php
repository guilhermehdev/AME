<?php
$eventos = $this->getData('eventos');
$tipoLabel = [
    'AUSENCIA' => ['Ausência', 'danger', 'remove'],
    'FOLGA' => ['Folga', 'warning', 'calendar'],
    'REAGENDAMENTO' => ['Reagendamento', 'info', 'calendar'],
    'FERIAS' => ['Férias', 'success', 'plane'],
    'LICENCA' => ['Licença', 'purple', 'briefcase'],
    'OUTRO' => ['Outro', 'black', 'info-sign']
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
    $mesesPreview = array(
        1 => 'Jan', 2 => 'Fev', 3 => 'Mar', 4 => 'Abr',
        5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
        9 => 'Set', 10 => 'Out', 11 => 'Nov', 12 => 'Dez'
    );
    $tagsPorMes = array();
    $ordemMesesPreview = array();
    $totalEventosGrupo = 0;
    foreach ($grupo['datas'] as $eventosDaDataGrupo) {
        $totalEventosGrupo += count($eventosDaDataGrupo);
        foreach ($eventosDaDataGrupo as $eventoGrupo) {
            $chaveTipo = (string)$eventoGrupo['tipo'];
            $tipoPreview = isset($tipoLabel[$chaveTipo])
                ? $tipoLabel[$chaveTipo]
                : array($chaveTipo, 'default', 'info-sign');
            $mesNumero = (int)date('n', strtotime($eventoGrupo['data_evento']));
            $mesNome = isset($mesesPreview[$mesNumero])
                ? $mesesPreview[$mesNumero]
                : 'Mês não informado';

            if (!isset($tagsPorMes[$mesNumero])) {
                $tagsPorMes[$mesNumero] = array(
                    'nome' => $mesNome,
                    'tipos' => array()
                );
                $ordemMesesPreview[] = $mesNumero;
            }

            if (!isset($tagsPorMes[$mesNumero]['tipos'][$chaveTipo])) {
                $tagsPorMes[$mesNumero]['tipos'][$chaveTipo] = array(
                    'classe' => $tipoPreview[1],
                    'nome' => $tipoPreview[0]
                );
            }
        }
    }

    $tagsPreview = '';
    foreach ($ordemMesesPreview as $mesNumero) {
        $mesGrupo = $tagsPorMes[$mesNumero];
        $tagsPreview .= '<div class="dashboard-evento-card-preview-linha">'
            . '<span class="label label-mes-dashboard">'
            . htmlspecialchars($mesGrupo['nome'], ENT_QUOTES, 'UTF-8') . '</span> ';

        foreach ($mesGrupo['tipos'] as $tipoGrupo) {
            $tagsPreview .= '<span class="label label-' . $tipoGrupo['classe'] . '">'
                . htmlspecialchars($tipoGrupo['nome'], ENT_QUOTES, 'UTF-8') . '</span> ';
        }

        $tagsPreview .= '</div>';
    }

    $eventosAdicionais = max(0, $totalEventosGrupo - 1);
    $preview = '<div class="dashboard-evento-card-preview">'
        . $tagsPreview
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
                <div style="font-weight:bold; margin-bottom:5px; color:#5b2c6f;"><span class="glyphicon glyphicon-calendar"></span> ' . $data . '</div>
                <ul style="padding-left:18px; margin:0;">' . $itens . '</ul>
            </div>';
    }

    $cardsDashboard[] = '<div class="col-sm-6 col-md-3">
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
$quantidadeColunasDashboard = 4;
for ($coluna = 0; $coluna < $quantidadeColunasDashboard; $coluna++) {
    echo '<div class="dashboard-eventos-coluna">';
    for ($indiceCard = $coluna; $indiceCard < count($cardsDashboard); $indiceCard += $quantidadeColunasDashboard) {
        echo $cardsDashboard[$indiceCard];
    }
    echo '</div>';
}
echo '</div>';
