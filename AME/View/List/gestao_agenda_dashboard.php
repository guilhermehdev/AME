<?php
$dados = $this->getData('dados');
$mes   = $this->getData('mes');
$ano   = $this->getData('ano');

$meses = [
    1=>'Jan',2=>'Fev',3=>'Mar',4=>'Abr',
    5=>'Mai',6=>'Jun',7=>'Jul',8=>'Ago',
    9=>'Set',10=>'Out',11=>'Nov',12=>'Dez'
];

if (empty($dados)) {
    echo "<p class=\"text-muted\" style=\"padding:5px;\">
            Nenhum dado de agenda cadastrado para este mês.
            <a href=\"" . URL . "GestaoAgenda/index\">Cadastrar agora</a>
          </p>";
    return;
}

foreach ($dados as $espec => $profissionais) {
    echo "<div class=\"col-sm-12\" style=\"margin-bottom:8px;\">
            <b class=\"text-primary\">{$espec}</b>
          </div>";

    foreach ($profissionais as $p) {
        $pct      = $p['vagas_ofertadas'] > 0
                        ? round($p['presentes'] / $p['vagas_ofertadas'] * 100)
                        : 0;
        $pctClass = $pct >= 80 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
        $prof     = $p['nome_servidor'] ?: 'Sem profissional fixo';
        $barWidth = min(100, $pct);

        // Badge de eventos
        $evBadge = '';
        if ($p['total_eventos'] > 0) {
            $evBadge = " <span class=\"badge\" style=\"background:#d9534f;\" title=\"{$p['total_eventos']} evento(s) registrado(s)\">{$p['total_eventos']}</span>";
        }

        // Observação
        $obs = '';
        if (!empty($p['show_dashboard']) && $p['observacao']) {
            $obs = " <small class=\"text-muted\">— " . htmlspecialchars($p['observacao']) . "</small>";
        }

        echo "<div class=\"col-sm-12\" style=\"margin-bottom:6px; padding-left:20px;\">
                <div class=\"col-sm-3\" style=\"padding:0;\">
                    <small>{$prof}{$evBadge}</small>{$obs}
                </div>
                <div class=\"col-sm-6\" style=\"padding-top:3px;\">
                    <div class=\"progress\" style=\"margin:0; height:14px;\">
                        <div class=\"progress-bar progress-bar-{$pctClass}\"
                             style=\"width:{$barWidth}%; min-width:20px; font-size:11px; line-height:14px;\">
                            {$pct}%
                        </div>
                    </div>
                </div>
                <div class=\"col-sm-3\">
                    <small class=\"text-muted\">
                        {$p['presentes']} / {$p['vagas_ofertadas']} vagas
                        " . ($p['faltas'] > 0 ? "| <span class=\"text-danger\">{$p['faltas']} faltas</span>" : "") . "
                    </small>
                </div>
              </div>";
    }

    echo "<div class=\"col-sm-12\"><hr style=\"margin:6px 0;\"></div>";
}
