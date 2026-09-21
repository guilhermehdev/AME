<?php
$observacoes = $this->getData('observacoes');

if (empty($observacoes)) {
    return;
}

$meses = [
    1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',
    5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',
    9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'
];

foreach ($observacoes as $item) {
    $especialidadeOriginal = $item['especialidade'];
    $profissionalOriginal = $item['nome_servidor'] ? $item['nome_servidor'] : 'Sem profissional fixo';
    $especialidade = htmlspecialchars($especialidadeOriginal, ENT_QUOTES, 'UTF-8');
    $profissional = htmlspecialchars($profissionalOriginal, ENT_QUOTES, 'UTF-8');
    $tituloAviso = htmlspecialchars($profissionalOriginal . ' - ' . $especialidadeOriginal, ENT_QUOTES, 'UTF-8');
    $mesAno = (isset($meses[(int)$item['mes']]) ? $meses[(int)$item['mes']] : $item['mes']) . ' ' . $item['ano'];
    $texto = nl2br(htmlspecialchars($item['observacao'], ENT_QUOTES, 'UTF-8'));
    $criadoEm = !empty($item['criado_em']) ? Functions::BRfullDateTime($item['criado_em']) : 'Data não registrada';

    echo "<div class=\"dashboard-observacao-agenda alert alert-warning\" data-profissional-especialidade=\"{$tituloAviso}\" style=\"margin-bottom:8px;\">
            <small class=\"text-muted\">(Ref. Agenda {$mesAno})</small>
            <div style=\"color:#5b2a86; font-weight:600;\">{$texto}</div>
            <small class=\"text-muted\">Criado em: {$criadoEm}</small>
          </div>";
}
