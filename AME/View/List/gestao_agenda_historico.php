<?php
$f        = new Functions();
$historico = $this->getData('historico');

if (empty($historico)) {
    echo "<p class=\"text-muted\" style=\"padding:15px;\">Nenhum registro encontrado para os filtros selecionados.</p>";
    return;
}

echo "<table class=\"table table-condensed table-hover table-bordered mrg-top\">
    <thead>
        <tr class=\"active\">
            <th>Especialidade</th>
            <th>Profissional</th>
            <th>Mês/Ano</th>
            <th class=\"text-center\">Ofertadas</th>
            <th class=\"text-center\">Presentes</th>
            <th class=\"text-center\">Faltas</th>
            <th class=\"text-center\">Aproveit.</th>
            <th class=\"text-center\">Eventos</th>
            <th>Observação</th>
        </tr>
    </thead>
    <tbody>";

$meses = [
    1=>'Jan',2=>'Fev',3=>'Mar',4=>'Abr',
    5=>'Mai',6=>'Jun',7=>'Jul',8=>'Ago',
    9=>'Set',10=>'Out',11=>'Nov',12=>'Dez'
];

foreach ($historico as $h) {
    $pct      = $h['vagas_ofertadas'] > 0
                    ? round($h['presentes'] / $h['vagas_ofertadas'] * 100)
                    : 0;
    $pctClass = $pct >= 80 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
    $mesAno   = ($meses[(int)$h['mes']] ?? $h['mes']) . '/' . $h['ano'];
    $prof     = $h['nome_servidor'] ?: '<span class="text-muted">—</span>';
    $obs      = $h['observacao']
                    ? '<span class="label label-default">' . htmlspecialchars($h['observacao']) . '</span>'
                    : '—';
    $evBadge  = $h['total_eventos'] > 0
                    ? '<span class="badge" style="background:#d9534f;">' . $h['total_eventos'] . '</span>'
                    : '<span class="text-muted">0</span>';

    echo "<tr>
            <td><b>{$h['especialidade']}</b></td>
            <td>{$prof}</td>
            <td>{$mesAno}</td>
            <td class=\"text-center\">{$h['vagas_ofertadas']}</td>
            <td class=\"text-center text-success\"><b>{$h['presentes']}</b></td>
            <td class=\"text-center text-danger\"><b>{$h['faltas']}</b></td>
            <td class=\"text-center\">
                <span class=\"label label-{$pctClass}\">{$pct}%</span>
            </td>
            <td class=\"text-center\">{$evBadge}</td>
            <td>{$obs}</td>
          </tr>";
}

echo "  </tbody>
      </table>";