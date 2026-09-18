<?php
$pdfs = $this->getData('pdfs') ?: array();
$assinados = $this->getData('assinados') ?: array();

if (!empty($pdfs)) {
    $grupos = array();

    foreach ($pdfs as $pdf) {
        $dataGrupo = trim(isset($pdf['data']) ? $pdf['data'] : '');
        $chaveGrupo = $dataGrupo !== '' ? $dataGrupo : 'sem-data';

        if (!isset($grupos[$chaveGrupo])) {
            $grupos[$chaveGrupo] = array(
                'titulo' => $dataGrupo !== '' ? $dataGrupo : 'Sem data',
                'pdfs' => array()
            );
        }

        $grupos[$chaveGrupo]['pdfs'][] = $pdf;
    }

    echo '<div class="alert dashboard-pdf-alert" style="margin:0 0 15px;border:1px solid #e0b4b4;border-radius:10px;background:#fff5f5;color:#7a2020;padding:14px 16px;">';
    echo '  <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">';
    echo '      <span class="glyphicon glyphicon-file" style="font-size:24px;color:#b22222;"></span>';
    echo '      <strong style="font-size:15px;">PDFs disponíveis para assinatura</strong>';
    echo '  </div>';
    echo '  <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">';
    echo '      <label style="margin:0;font-weight:normal;cursor:pointer;">';
    echo '          <input type="checkbox" class="check-selecionar-todos-pdfs"> Selecionar todos';
    echo '      </label>';
    echo '      <button type="button" class="btn btn-primary btn-sm btn-baixar-lote-serpro" style="font-size:13px;" disabled>';
    echo '          <span class="glyphicon glyphicon-download-alt"></span> Baixar selecionados';
    echo '      </button>';
    echo '      <button type="button" class="btn btn-success btn-sm btn-enviar-pdfs-assinados" style="font-size:13px;" disabled>';
    echo '          <span class="glyphicon glyphicon-upload"></span> Enviar assinados';
    echo '      </button>';
    echo '      <input type="file" class="input-pdfs-assinados" accept="application/pdf,.pdf" multiple style="display:none;">';
    echo '  </div>';
    echo '  <small style="display:block;color:#7a2020;line-height:1.5;">Selecione os pacientes, clique em <b>Baixar selecionados</b> e escolha uma pasta-base apenas na primeira vez. O sistema criará as subpastas do médico e da data. Depois, assine no SERPRO e clique em <b>Enviar assinados</b>. OCIs do executante seguem para o médico autorizador concluir a segunda assinatura.</small>';

    $indiceGrupo = 0;
    foreach ($grupos as $grupo) {
        $indiceGrupo++;
        $idGrupo = 'pdfs-oci-data-' . $indiceGrupo;
        $tituloGrupo = htmlspecialchars($grupo['titulo'], ENT_QUOTES, 'UTF-8');
        $quantidade = count($grupo['pdfs']);

        echo '<div class="pdfs-oci-grupo" style="border-top:1px solid #efd2d2;">';
        echo '  <button type="button" class="btn btn-link btn-toggle-pdfs-data" data-toggle="collapse" data-target="#' . $idGrupo . '" aria-expanded="false" style="display:flex;align-items:center;width:100%;padding:10px 0;text-align:left;text-decoration:none;color:#7a2020;">';
        echo '      <span class="glyphicon glyphicon-calendar" style="margin-right:8px;"></span>';
        echo '      <strong style="flex:1;">' . $tituloGrupo . '</strong>';
        echo '      <span class="badge" style="margin-right:8px;">' . $quantidade . '</span>';
        echo '      <span class="glyphicon glyphicon-chevron-down"></span>';
        echo '  </button>';
        echo '  <div id="' . $idGrupo . '" class="collapse">';

        foreach ($grupo['pdfs'] as $pdf) {
            $pdfUrl = htmlspecialchars($pdf['url'], ENT_QUOTES, 'UTF-8');
            $pdfNome = htmlspecialchars($pdf['nome'], ENT_QUOTES, 'UTF-8');
            $pdfPasta = htmlspecialchars(isset($pdf['pasta']) ? $pdf['pasta'] : '', ENT_QUOTES, 'UTF-8');
            $etapaAssinatura = isset($pdf['etapa']) && $pdf['etapa'] === 'autorizador' ? 'autorizador' : 'executante';
            $nomePaciente = pathinfo($pdf['nome'], PATHINFO_FILENAME);
            $separador = strpos($nomePaciente, '-');
            if ($separador !== false) {
                $nomePaciente = trim(substr($nomePaciente, $separador + 1));
            }
            $nomePaciente = htmlspecialchars($nomePaciente, ENT_QUOTES, 'UTF-8');

            echo '<div class="pdf-oci-item" style="display:flex;align-items:center;gap:10px;padding:8px 0 8px 24px;border-top:1px solid #f3dddd;">';
            echo '  <input type="checkbox" class="check-pdf-oci" aria-label="Selecionar ' . $nomePaciente . '">';
            echo '  <span class="glyphicon glyphicon-file"></span>';
            echo '  <div style="flex:1;min-width:0;">';
            echo '      <a href="' . $pdfUrl . '" target="_blank" rel="noopener" title="Abrir PDF" style="font-weight:bold;color:#7a2020;text-decoration:underline;">' . $nomePaciente . '</a>';
            echo '  </div>';
            echo '  <button type="button" class="btn btn-success btn-sm btn-assinar-serpro" style="font-size:13px;"';
            echo '          data-pdf-url="' . $pdfUrl . '"';
            echo '          data-pdf-name="' . $pdfNome . '"';
            echo '          data-pdf-original="' . $pdfNome . '"';
            echo '          data-pdf-pasta="' . $pdfPasta . '"';
            echo '          data-pdf-etapa="' . $etapaAssinatura . '"';
            echo '          data-salvar-url="OCI/salvarPdfAssinado">';
            echo '      <span class="glyphicon glyphicon-pencil"></span> ' . ($etapaAssinatura === 'autorizador' ? 'Assinar' : 'Assinar');
            echo '  </button>';
            if ($etapaAssinatura === 'executante') {
                echo '  <button type="button" class="btn btn-danger btn-sm btn-excluir-pdf-oci" style="font-size:13px;"';
                echo '          data-pdf-name="' . $pdfNome . '"';
                echo '          data-pdf-pasta="' . $pdfPasta . '"';
                echo '          title="Excluir PDF pendente">';
                echo '      <span class="glyphicon glyphicon-trash"></span> Excluir';
                echo '  </button>';
            }
            echo '</div>';
        }

        echo '  </div>';
        echo '</div>';
    }

    echo '<div class="alert alert-info serpro-status" style="display:none;margin:10px 0 0;color:#fff;"></div>';
    echo '<p class="serpro-autorizacao" style="display:none;margin:10px 0 0;"><a href="https://127.0.0.1:65156" target="_blank">Clique aqui para autorizar o certificado do Assinador SERPRO</a></p>';
    echo '</div>';
}

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
