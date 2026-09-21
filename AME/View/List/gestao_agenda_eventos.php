<?php
$f       = new Functions();
$eventos = $this->getData('eventos');

$tiposEvento = [
    'AUSENCIA'      => 'Ausência',
    'FOLGA'         => 'Folga',
    'REAGENDAMENTO' => 'Reagendamento',
    'FERIAS'        => 'Férias',
    'LICENCA'       => 'Licença',
    'OUTRO'         => 'Outro',
];

$tipoLabel = [
    'AUSENCIA'      => 'danger',
    'FOLGA'         => 'warning',
    'REAGENDAMENTO' => 'info',
    'FERIAS'        => 'primary',
    'LICENCA'       => 'default',
    'OUTRO'         => 'default',
];

if (empty($eventos)) {
    echo "<p class=\"text-muted\" style=\"padding:15px;\">Nenhum evento encontrado para os filtros selecionados.</p>";
    return;
}

echo "<table class=\"table table-condensed table-hover table-bordered mrg-top\">
    <thead>
        <tr class=\"active\">
            <th>Data</th>
            <th>Especialidade</th>
            <th>Profissional</th>
            <th>Tipo</th>
            <th>Descrição</th>
            <th>Criado em</th>
            <th>Reagendado para</th>
            <th>Dashboard</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>";

foreach ($eventos as $ev) {
    $tipo      = $ev['tipo'];
    $labelCls  = isset($tipoLabel[$tipo]) ? $tipoLabel[$tipo] : 'default';
    $tipoNome  = isset($tiposEvento[$tipo]) ? $tiposEvento[$tipo] : $tipo;
    $dataEv    = Functions::BRdateFormat($ev['data_evento']);
    $criadoEm  = !empty($ev['criado_em']) ? Functions::BRfullDateTime($ev['criado_em']) : '—';
    $dtReagend = $ev['dt_reagend'] ? Functions::BRdateFormat($ev['dt_reagend']) : '—';
    $prof      = $ev['nome_servidor'] ?: '<span class="text-muted">—</span>';

    echo "<tr>
            <td><b>{$dataEv}</b></td>
            <td>{$ev['especialidade']}</td>
            <td>{$prof}</td>
            <td><span class=\"label label-{$labelCls}\">{$tipoNome}</span></td>
            <td>" . htmlspecialchars($ev['descricao']) . "</td>
            <td><small>{$criadoEm}</small></td>
            <td>{$dtReagend}</td>
            <td class=\"text-center\">
                <input type=\"checkbox\" class=\"toggle-dashboard-evento\"
                       data-id=\"{$ev['id']}\"
                       " . (!empty($ev['show_dashboard']) ? 'checked' : '') . ">
            </td>
            <td>
                <button type=\"button\" class=\"btn btn-danger btn-xs btn-excluir-evento\"
                        data-id=\"{$ev['id']}\" data-origem=\"historico\" title=\"Excluir evento\">
                    <span class=\"glyphicon glyphicon-trash\"></span>
                </button>
            </td>
          </tr>";
}

echo "  </tbody>
      </table>";
