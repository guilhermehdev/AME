<?php
$assinados = $this->getData('assinados') ?: array();

if (!empty($assinados)) {
    $gruposAssinados = array();

    foreach ($assinados as $pdfAssinado) {
        $dataGrupo = trim(isset($pdfAssinado['data']) ? $pdfAssinado['data'] : '');
        $chaveGrupo = $dataGrupo !== '' ? $dataGrupo : 'sem-data';

        if (!isset($gruposAssinados[$chaveGrupo])) {
            $gruposAssinados[$chaveGrupo] = array(
                'titulo' => $dataGrupo !== '' ? $dataGrupo : 'Sem data',
                'pdfs' => array()
            );
        }

        $gruposAssinados[$chaveGrupo]['pdfs'][] = $pdfAssinado;
    }

    echo '<div class="alert" style="margin:0 0 15px;border:1px solid #b8d8c0;border-radius:10px;background:#f1fbf3;color:#245b2f;padding:14px 16px;">';
    echo '  <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">';
    echo '      <span class="glyphicon glyphicon-ok-sign" style="font-size:24px;color:#2e8b57;"></span>';
    echo '      <strong style="font-size:15px;">Histórico de PDFs assinados</strong>';
    echo '  </div>';

    $indiceHistorico = 0;
    foreach ($gruposAssinados as $grupoAssinado) {
        $indiceHistorico++;
        $idHistorico = 'historico-pdfs-oci-data-' . $indiceHistorico;
        $tituloGrupo = htmlspecialchars($grupoAssinado['titulo'], ENT_QUOTES, 'UTF-8');
        $quantidade = count($grupoAssinado['pdfs']);

        echo '<div style="border-top:1px solid #d7eadb;">';
        echo '  <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#' . $idHistorico . '" aria-expanded="false" style="display:flex;align-items:center;width:100%;padding:10px 0;text-align:left;text-decoration:none;color:#245b2f;">';
        echo '      <span class="glyphicon glyphicon-calendar" style="margin-right:8px;"></span>';
        echo '      <strong style="flex:1;">' . $tituloGrupo . '</strong>';
        echo '      <span class="badge" style="margin-right:8px;">' . $quantidade . '</span>';
        echo '      <span class="glyphicon glyphicon-chevron-down"></span>';
        echo '  </button>';
        echo '  <div id="' . $idHistorico . '" class="collapse">';

        foreach ($grupoAssinado['pdfs'] as $pdfAssinado) {
            $pdfUrl = htmlspecialchars($pdfAssinado['url'], ENT_QUOTES, 'UTF-8');
            $nomeArquivo = pathinfo($pdfAssinado['nome'], PATHINFO_FILENAME);
            $nomeArquivo = preg_replace('/_assinado.*$/iu', '', $nomeArquivo);
            $separador = strpos($nomeArquivo, '-');
            if ($separador !== false) {
                $nomeArquivo = trim(substr($nomeArquivo, $separador + 1));
            }
            $nomeArquivo = htmlspecialchars(str_replace('_', ' ', $nomeArquivo), ENT_QUOTES, 'UTF-8');

            echo '<div style="display:flex;align-items:center;gap:10px;padding:8px 0 8px 24px;border-top:1px solid #e1f0e4;">';
            echo '  <span class="glyphicon glyphicon-ok" style="color:#2e8b57;"></span>';
            echo '  <strong style="flex:1;min-width:0;">' . $nomeArquivo . '</strong>';
            echo '  <a class="btn btn-default btn-sm" style="font-size:13px;" href="' . $pdfUrl . '" target="_blank" rel="noopener">';
            echo '      <span class="glyphicon glyphicon-eye-open"></span> Visualizar PDF';
            echo '  </a>';
            echo '</div>';
        }

        echo '  </div>';
        echo '</div>';
    }

    echo '</div>';
}
?>
