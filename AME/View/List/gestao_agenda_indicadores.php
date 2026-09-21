<?php
$f           = new Functions();
$indicadores = $this->getData('indicadores');
$mes         = $this->getData('mes');
$ano         = $this->getData('ano');

$meses = [
    1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',
    5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',
    9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'
];

if (empty($indicadores)) {
    echo "<p class=\"text-muted\" style=\"padding:15px;\">Nenhum dado encontrado para o período selecionado.</p>";
    return;
}

// Totais gerais
$totalOfertadas = 0;
$totalPresentes = 0;
$totalFaltas    = 0;
$totalEventos   = 0;

foreach ($indicadores as $espec => $dados) {
    $totalOfertadas += $dados['total_ofertadas'];
    $totalPresentes += $dados['total_presentes'];
    $totalFaltas    += $dados['total_faltas'];
    foreach ($dados['profissionais'] as $p) {
        $totalEventos += $p['total_eventos'];
    }
}

$pctGeral      = $totalOfertadas > 0 ? round($totalPresentes / $totalOfertadas * 100) : 0;
$pctGeralClass = $pctGeral >= 80 ? 'success' : ($pctGeral >= 50 ? 'warning' : 'danger');
$mesNome       = (isset($meses[(int)$mes]) ? $meses[(int)$mes] : $mes) . '/' . $ano;

// Cards de resumo
echo "<div class=\"col-sm-12 mrg-bottom\">
    <h4 class=\"text-muted\">Resumo de {$mesNome}</h4>
</div>

<div class=\"col-sm-3\">
    <div class=\"panel panel-default text-center\">
        <div class=\"panel-body\">
            <h2>{$totalOfertadas}</h2>
            <p class=\"text-muted\">Vagas ofertadas</p>
        </div>
    </div>
</div>

<div class=\"col-sm-3\">
    <div class=\"panel panel-success text-center\">
        <div class=\"panel-body\">
            <h2 class=\"text-success\">{$totalPresentes}</h2>
            <p class=\"text-muted\">Presentes</p>
        </div>
    </div>
</div>

<div class=\"col-sm-3\">
    <div class=\"panel panel-danger text-center\">
        <div class=\"panel-body\">
            <h2 class=\"text-danger\">{$totalFaltas}</h2>
            <p class=\"text-muted\">Faltas</p>
        </div>
    </div>
</div>

<div class=\"col-sm-3\">
    <div class=\"panel panel-{$pctGeralClass} text-center\">
        <div class=\"panel-body\">
            <h2 class=\"text-{$pctGeralClass}\">{$pctGeral}%</h2>
            <p class=\"text-muted\">Aproveitamento geral</p>
        </div>
    </div>
</div>";

// Tabela agrupada por especialidade
echo "<div class=\"col-sm-12\">
    <table class=\"table table-condensed table-bordered table-hover\">
        <thead>
            <tr class=\"active\">
                <th>Especialidade / Profissional</th>
                <th class=\"text-center\">AME</th>
                <th class=\"text-center\">REG</th>
                <th class=\"text-center\">Ofertadas</th>
                <th class=\"text-center\">Presentes</th>
                <th class=\"text-center\">Faltas</th>
                <th class=\"text-center\">Aproveit.</th>
                <th class=\"text-center\">Eventos</th>
                <th>Observação</th>
            </tr>
        </thead>
        <tbody>";

foreach ($indicadores as $espec => $dados) {
    $subTotal      = $dados['total_ofertadas'];
    $subPresentes  = $dados['total_presentes'];
    $subFaltas     = $dados['total_faltas'];
    $pctSub        = $subTotal > 0 ? round($subPresentes / $subTotal * 100) : 0;
    $pctSubClass   = $pctSub >= 80 ? 'success' : ($pctSub >= 50 ? 'warning' : 'danger');
    $numProfs      = count($dados['profissionais']);

    // Linha de cabeçalho da especialidade
    echo "<tr class=\"info\">
            <td colspan=\"9\">
                <b>{$espec}</b>
                <span class=\"pull-right text-muted\">
                    Total: {$subTotal} ofertadas &bull;
                    {$subPresentes} presentes &bull;
                    {$subFaltas} faltas &bull;
                    <span class=\"label label-{$pctSubClass}\">{$pctSub}%</span>
                </span>
            </td>
          </tr>";

    // Linhas dos profissionais
    foreach ($dados['profissionais'] as $p) {
        $pct      = $p['vagas_ofertadas'] > 0
                        ? round($p['presentes'] / $p['vagas_ofertadas'] * 100)
                        : 0;
        $pctClass = $pct >= 80 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
        $prof     = $p['nome_servidor'] ?: '<em class="text-muted">Sem profissional fixo</em>';
        $obs      = $p['observacao'] ? htmlspecialchars($p['observacao']) : '—';
        $evBadge  = $p['total_eventos'] > 0
                        ? '<span class="badge" style="background:#d9534f;">' . $p['total_eventos'] . '</span>'
                        : '0';

        echo "<tr>
                <td style=\"padding-left:20px;\">{$prof}</td>
                <td class=\"text-center\">{$p['vagas_ame']}</td>
                <td class=\"text-center\">{$p['vagas_reg']}</td>
                <td class=\"text-center\"><b>{$p['vagas_ofertadas']}</b></td>
                <td class=\"text-center text-success\">{$p['presentes']}</td>
                <td class=\"text-center text-danger\">{$p['faltas']}</td>
                <td class=\"text-center\">
                    <span class=\"label label-{$pctClass}\">{$pct}%</span>
                </td>
                <td class=\"text-center\">{$evBadge}</td>
                <td><small>{$obs}</small></td>
              </tr>";
    }
}

echo "      </tbody>
        </table>
    </div>";
