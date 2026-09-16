<?php
/**
 * Description of Documentos
 *
 * @author Guilherme
 */
class OCI {
    
     public function home(){     
        $sessao = AppController::checkSession();
        if (!$sessao) {
            header("location: ".URL."Loginadm/login");
            return;
        }

        $view = new TGui("homeoci");
        $view->addData("title", " OCI / APAC");

        $idUsuario = isset($sessao['id']) ? $sessao['id'] : null;
        $cboSUS = $idUsuario ? Daooci::getCBO($idUsuario) : array();
        $medico = !empty($cboSUS) && !empty($cboSUS[0]['SUS']) ? $cboSUS[0]['SUS'] : '';
        $nomeUsuario = isset($sessao['username']) ? $sessao['username'] : '';
        $view->addData("pdfs", Daooci::getPdfsGerados($medico, $idUsuario, $nomeUsuario));
        $view->addData("assinados", Daooci::getPdfsAssinados($medico, $idUsuario, $nomeUsuario));

        $view->renderize(APP_VIEW);
    }
    
    public function impressos(){               
        $view = new TGui("printoci");
        $view->addData("title", "Imprimir guias APAC");
        $view->renderize(APP_VIEW);
    } 

    public function salvarPdfAssinado(){
        header('Content-Type: application/json; charset=utf-8');

        if (!AppController::checkSession()) {
            http_response_code(401);
            echo json_encode(array('erro' => true, 'mensagem' => 'Sessão expirada. Faça login novamente.'));
            return;
        }

        $base64 = isset($_POST['pdf']) ? $_POST['pdf'] : '';
        $nome = isset($_POST['nome']) ? $_POST['nome'] : 'documento_assinado.pdf';
        $nomeOriginalInformado = isset($_POST['original']) ? basename($_POST['original']) : basename($nome);
        $pastaRecebida = isset($_POST['pasta']) ? trim(str_replace('\\', '/', $_POST['pasta'])) : '';
        $base64 = preg_replace('/^data:application\/pdf;base64,/', '', $base64);
        $conteudo = base64_decode(str_replace(' ', '+', $base64), true);

        if ($conteudo === false || substr($conteudo, 0, 4) !== '%PDF') {
            http_response_code(422);
            echo json_encode(array('erro' => true, 'mensagem' => 'O conteúdo recebido não é um PDF válido.'));
            return;
        }

        $partesPasta = array();
        if ($pastaRecebida !== '') {
            foreach (explode('/', $pastaRecebida) as $partePasta) {
                $partePasta = trim($partePasta);
                if ($partePasta === '' || $partePasta === '.' || $partePasta === '..') {
                    continue;
                }

                if (preg_match('/^[\pL\pN _-]+$/u', $partePasta)) {
                    $partesPasta[] = $partePasta;
                }
            }
        }

        $subpasta = implode(DIRECTORY_SEPARATOR, $partesPasta);
        $diretorioBase = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Impressos' . DIRECTORY_SEPARATOR . 'OCI' . DIRECTORY_SEPARATOR . 'Assinados' . DIRECTORY_SEPARATOR;
        $diretorio = $diretorioBase . ($subpasta !== '' ? $subpasta . DIRECTORY_SEPARATOR : '');

        if (!is_dir($diretorio) && !mkdir($diretorio, 0755, true)) {
            http_response_code(500);
            echo json_encode(array('erro' => true, 'mensagem' => 'Não foi possível criar a pasta de PDFs assinados.'));
            return;
        }

        $nome = pathinfo(basename($nome), PATHINFO_FILENAME);
        $nome = preg_replace('/[^A-Za-z0-9_-]+/', '_', $nome);
        $nome = trim($nome, '_');
        if ($nome === '') {
            $nome = 'documento_assinado';
        }

        $arquivo = $nome . '_' . date('Ymd_His') . '_' . substr(sha1(uniqid('', true)), 0, 6) . '.pdf';
        $caminho = $diretorio . $arquivo;

        if (file_put_contents($caminho, $conteudo) === false) {
            http_response_code(500);
            echo json_encode(array('erro' => true, 'mensagem' => 'Não foi possível salvar o PDF assinado.'));
            return;
        }

        $ociAtualizada = Daooci::atualizarAssinadoPorArquivo($nomeOriginalInformado, 1);

        $urlArquivo = URL . 'Impressos/OCI/Assinados/';
        foreach ($partesPasta as $partePasta) {
            $urlArquivo .= rawurlencode($partePasta) . '/';
        }
        $urlArquivo .= rawurlencode($arquivo);

        echo json_encode(array(
            'erro' => false,
            'mensagem' => 'PDF assinado e salvo no servidor.',
            'arquivo' => $urlArquivo,
            'ociAtualizada' => $ociAtualizada
        ));
    }

    public function excluirPdfGerado(){
        header('Content-Type: application/json; charset=utf-8');

        $sessao = AppController::checkSession();
        if (!$sessao) {
            http_response_code(401);
            echo json_encode(array('erro' => true, 'mensagem' => 'Sessão expirada. Faça login novamente.'));
            return;
        }

        $idUsuario = isset($sessao['id']) ? $sessao['id'] : null;
        $nomeUsuario = isset($sessao['username']) ? $sessao['username'] : '';
        $cboSUS = $idUsuario ? Daooci::getCBO($idUsuario) : array();
        $medico = !empty($cboSUS) && !empty($cboSUS[0]['SUS']) ? $cboSUS[0]['SUS'] : '';

        $resultado = Daooci::excluirPdfGerado(
            isset($_POST['nome']) ? $_POST['nome'] : '',
            isset($_POST['pasta']) ? $_POST['pasta'] : '',
            $idUsuario,
            $nomeUsuario,
            $medico
        );

        if (!empty($resultado['erro'])) {
            http_response_code(422);
        }

        echo json_encode($resultado);
    }

    public function salvarPdfAssinadoLote(){
        header('Content-Type: application/json; charset=utf-8');

        if (!AppController::checkSession()) {
            http_response_code(401);
            echo json_encode(array('erro' => true, 'mensagem' => 'Sessão expirada. Faça login novamente.'));
            return;
        }

        $pastaRecebida = isset($_POST['pasta']) ? trim(str_replace('\\', '/', $_POST['pasta'])) : '';
        $partesPasta = array();

        foreach (explode('/', $pastaRecebida) as $partePasta) {
            $partePasta = trim($partePasta);
            if ($partePasta === '' || $partePasta === '.' || $partePasta === '..') {
                continue;
            }

            if (!preg_match('/^[\pL\pN _-]+$/u', $partePasta)) {
                http_response_code(422);
                echo json_encode(array('erro' => true, 'mensagem' => 'Pasta do lote inválida.'));
                return;
            }

            $partesPasta[] = $partePasta;
        }

        if (empty($partesPasta) || !isset($_FILES['arquivo'])) {
            http_response_code(422);
            echo json_encode(array('erro' => true, 'mensagem' => 'Selecione um PDF assinado e informe a data do lote.'));
            return;
        }

        $arquivoUpload = $_FILES['arquivo'];
        if (!isset($arquivoUpload['error']) || $arquivoUpload['error'] !== UPLOAD_ERR_OK) {
            http_response_code(422);
            echo json_encode(array('erro' => true, 'mensagem' => 'Não foi possível receber o PDF assinado.'));
            return;
        }

        $nomeOriginal = basename($arquivoUpload['name']);
        if (strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION)) !== 'pdf' ||
            !preg_match('/^[^\\\/]+\.pdf$/i', $nomeOriginal)) {
            http_response_code(422);
            echo json_encode(array('erro' => true, 'mensagem' => 'O arquivo enviado precisa ser um PDF válido.'));
            return;
        }

        $subpasta = implode(DIRECTORY_SEPARATOR, $partesPasta);
        $diretorioGerados = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Impressos' . DIRECTORY_SEPARATOR . 'OCI' . DIRECTORY_SEPARATOR . 'Gerados' . DIRECTORY_SEPARATOR . $subpasta . DIRECTORY_SEPARATOR;
        $diretorioAssinados = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Impressos' . DIRECTORY_SEPARATOR . 'OCI' . DIRECTORY_SEPARATOR . 'Assinados' . DIRECTORY_SEPARATOR . $subpasta . DIRECTORY_SEPARATOR;
        $arquivoOrigem = $diretorioGerados . $nomeOriginal;

        // Dependendo da configuração do SERPRO, o lote pode manter o nome
        // original ou criar uma cópia com sufixo "_assinado"/"-assinado".
        // Nesse segundo caso, localiza o PDF original pelo nome-base para que
        // o médico não precise renomear os arquivos antes de enviá-los.
        if (!is_file($arquivoOrigem) && is_dir($diretorioGerados)) {
            $nomeBaseUpload = pathinfo($nomeOriginal, PATHINFO_FILENAME);
            $nomeBaseSemAssinatura = preg_replace('/(?:[ _-]+)(?:assinado|signed)(?:[ _-].*)?$/iu', '', $nomeBaseUpload);
            $nomeBaseSemAssinatura = trim($nomeBaseSemAssinatura, " _-");

            foreach (scandir($diretorioGerados) as $arquivoGerado) {
                if ($arquivoGerado === '.' || $arquivoGerado === '..' ||
                    strtolower(pathinfo($arquivoGerado, PATHINFO_EXTENSION)) !== 'pdf') {
                    continue;
                }

                $nomeBaseGerado = pathinfo($arquivoGerado, PATHINFO_FILENAME);
                if (strcasecmp($nomeBaseGerado, $nomeBaseSemAssinatura) === 0) {
                    $nomeOriginal = $arquivoGerado;
                    $arquivoOrigem = $diretorioGerados . $arquivoGerado;
                    break;
                }
            }
        }

        if (!is_file($arquivoOrigem)) {
            http_response_code(422);
            echo json_encode(array('erro' => true, 'mensagem' => 'O PDF não pertence ao lote selecionado.'));
            return;
        }

        $conteudo = file_get_contents($arquivoUpload['tmp_name']);
        if ($conteudo === false || substr($conteudo, 0, 4) !== '%PDF') {
            http_response_code(422);
            echo json_encode(array('erro' => true, 'mensagem' => 'O arquivo enviado não é um PDF válido.'));
            return;
        }

        if (sha1_file($arquivoOrigem) === sha1_file($arquivoUpload['tmp_name'])) {
            http_response_code(422);
            echo json_encode(array('erro' => true, 'mensagem' => 'Este PDF ainda não parece ter sido assinado pelo SERPRO.'));
            return;
        }

        if (!is_dir($diretorioAssinados) && !mkdir($diretorioAssinados, 0755, true)) {
            http_response_code(500);
            echo json_encode(array('erro' => true, 'mensagem' => 'Não foi possível criar a pasta de PDFs assinados.'));
            return;
        }

        $nomeBase = pathinfo($nomeOriginal, PATHINFO_FILENAME);
        $nomeBase = preg_replace('/[^A-Za-z0-9_-]+/', '_', $nomeBase);
        $nomeBase = trim($nomeBase, '_');
        if ($nomeBase === '') {
            $nomeBase = 'documento_assinado';
        }

        $arquivoAssinado = $nomeBase . '_assinado_' . date('Ymd_His') . '_' . substr(sha1(uniqid('', true)), 0, 6) . '.pdf';
        $caminhoAssinado = $diretorioAssinados . $arquivoAssinado;

        if (file_put_contents($caminhoAssinado, $conteudo) === false) {
            http_response_code(500);
            echo json_encode(array('erro' => true, 'mensagem' => 'Não foi possível salvar o PDF assinado.'));
            return;
        }

        $ociAtualizada = Daooci::atualizarAssinadoPorArquivo($nomeOriginal, 1);

        echo json_encode(array(
            'erro' => false,
            'mensagem' => 'PDF assinado importado com sucesso.',
            'arquivos' => array($nomeOriginal),
            'arquivo' => $arquivoAssinado,
            'ociAtualizada' => $ociAtualizada
        ));
    }

    public function solicitarPdf(){
        header('Content-Type: application/json; charset=utf-8');

        if (!AppController::checkSession()) {
            http_response_code(401);
            echo json_encode(array('erro' => true, 'mensagem' => 'Sessão expirada. Faça login novamente.'));
            return;
        }

        $idsRecebidos = isset($_POST['idsOCI']) ? $_POST['idsOCI'] : array();
        if (!is_array($idsRecebidos)) {
            $idsRecebidos = json_decode($idsRecebidos, true);
        }

        if (!is_array($idsRecebidos)) {
            http_response_code(422);
            echo json_encode(array('erro' => true, 'mensagem' => 'Nenhuma OCI foi informada.'));
            return;
        }

        $ids = array_values(array_unique(array_filter(array_map('intval', $idsRecebidos), function ($id) {
            return $id > 0;
        })));

        if (empty($ids)) {
            http_response_code(422);
            echo json_encode(array('erro' => true, 'mensagem' => 'Nenhum ID de OCI válido foi informado.'));
            return;
        }

        $listaIds = implode(',', $ids);
        $stmt = Maincontroller::doQuery("SELECT id, num_apac FROM oci WHERE id IN ({$listaIds})");
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(array('erro' => true, 'mensagem' => 'Não foi possível consultar as OCIs.'));
            return;
        }

        $encontradas = array();
        while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $encontradas[] = (int)$linha['id'];
        }

        $faltantes = array_values(array_diff($ids, $encontradas));
        if (!empty($faltantes)) {
            http_response_code(422);
            echo json_encode(array(
                'erro' => true,
                'mensagem' => 'Uma ou mais OCIs não foram encontradas na tabela oci.',
                'ids_faltantes' => $faltantes
            ));
            return;
        }

        $entrada = OCI_FILA_ENTRADA;
        if (!is_dir($entrada) && !mkdir($entrada, 0755, true)) {
            http_response_code(500);
            echo json_encode(array('erro' => true, 'mensagem' => 'Não foi possível criar a pasta de entrada do serviço.'));
            return;
        }

        $jobId = 'oci_' . date('Ymd_His') . '_' . substr(sha1(uniqid('', true)), 0, 8);
        $solicitacao = array(
            'id' => $jobId,
            'idsOCI' => $ids,
            'pdfNome' => 'OCI_' . date('Ymd_His') . '.pdf'
        );

        $arquivo = $entrada . DIRECTORY_SEPARATOR . $jobId . '.json';
        $temporario = tempnam($entrada, 'oci_');
        $conteudo = json_encode($solicitacao, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($temporario === false || file_put_contents($temporario, $conteudo) === false || !rename($temporario, $arquivo)) {
            if ($temporario && file_exists($temporario)) {
                unlink($temporario);
            }
            http_response_code(500);
            echo json_encode(array('erro' => true, 'mensagem' => 'Não foi possível enviar a solicitação ao serviço.'));
            return;
        }

        echo json_encode(array(
            'erro' => false,
            'status' => 'aguardando',
            'jobId' => $jobId,
            'mensagem' => 'Solicitação enviada para geração do PDF.'
        ));
    }

    public function consultarPdf($params){
        header('Content-Type: application/json; charset=utf-8');

        if (!AppController::checkSession()) {
            http_response_code(401);
            echo json_encode(array('erro' => true, 'mensagem' => 'Sessão expirada. Faça login novamente.'));
            return;
        }

        $jobId = isset($params[2]) ? basename($params[2]) : '';
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $jobId)) {
            http_response_code(422);
            echo json_encode(array('erro' => true, 'mensagem' => 'Identificador de solicitação inválido.'));
            return;
        }

        $resultado = null;
        $status = 'aguardando';
        $arquivoResultado = OCI_FILA_CONCLUIDOS . DIRECTORY_SEPARATOR . $jobId . '.json';
        $arquivoErro = OCI_FILA_ERROS . DIRECTORY_SEPARATOR . $jobId . '.json';

        if (file_exists($arquivoResultado)) {
            $resultado = json_decode(file_get_contents($arquivoResultado), true);
        } elseif (file_exists($arquivoErro)) {
            $resultado = json_decode(file_get_contents($arquivoErro), true);
        }

        if (is_array($resultado)) {
            $status = isset($resultado['status']) ? $resultado['status'] : 'erro';
            $resposta = array(
                'erro' => $status === 'erro',
                'status' => $status,
                'mensagem' => isset($resultado['mensagem']) ? $resultado['mensagem'] : ''
            );

            if ($status === 'concluido' && !empty($resultado['arquivo'])) {
                $nomePdf = basename($resultado['arquivo']);
                $caminhoPdf = OCI_FILA_CONCLUIDOS . DIRECTORY_SEPARATOR . $nomePdf;

                if (!file_exists($caminhoPdf)) {
                    $resposta['erro'] = true;
                    $resposta['status'] = 'erro';
                    $resposta['mensagem'] = 'O serviço concluiu a geração, mas o PDF não foi localizado.';
                } else {
                    $resposta['urlPdf'] = URL . 'Impressos/OCI/Gerados/' . rawurlencode($nomePdf);
                    $resposta['pdfNome'] = $nomePdf;
                }
            }

            echo json_encode($resposta);
            return;
        }

        echo json_encode(array(
            'erro' => false,
            'status' => 'aguardando',
            'mensagem' => 'Aguardando o serviço gerar o PDF.'
        ));
    }
    
    public function addToList($params){               
       $medico =  $params[2];     
       $idPac = $params[3];  
       $data = $params[4];  
       $procedPrincipal = $params[5];  
       $cidPrincipal = Functions::cleanString($params[6]); 
       $cidSecundario = Functions::cleanString($params[7]);  
       
       Daooci::saveToList($medico, $idPac, $data, $procedPrincipal, $cidPrincipal, $cidSecundario);
    }  
    
     public function loadFila($params){          
        $data = $params[2];  
        $medico = $params[3]; 
        $proced = $params[4];                 
        $fila = Daooci::loadFila($data, $medico, $proced);        
        $finalizadas = Daooci::loadFinalizadas($data, $medico, $proced);
        $view = new TGui("LfilaOCI");
        $view->addData("fila", $fila);
        $view->addData("finalizadas", $finalizadas);
        $view->renderize(APP_VIEW_LIST,true);
    }

    public function loadPdfs($params){
        $sessao = AppController::checkSession();
        if (!$sessao) {
            http_response_code(401);
            return;
        }

        $medico = isset($params[2]) ? $params[2] : '';
        $idUsuario = isset($sessao['id']) ? $sessao['id'] : null;
        $nomeUsuario = isset($sessao['username']) ? $sessao['username'] : '';
        $pdfs = Daooci::getPdfsGerados($medico, $idUsuario, $nomeUsuario);
        $view = new TGui("LpdfsOCI");
        $view->addData("pdfs", $pdfs);
        $view->renderize(APP_VIEW_LIST,true);
    }

    public function loadPdfsAssinados($params){
        $sessao = AppController::checkSession();
        if (!$sessao) {
            http_response_code(401);
            return;
        }

        $medico = isset($params[2]) ? $params[2] : '';
        $idUsuario = isset($sessao['id']) ? $sessao['id'] : null;
        $nomeUsuario = isset($sessao['username']) ? $sessao['username'] : '';
        $assinados = Daooci::getPdfsAssinados($medico, $idUsuario, $nomeUsuario);
        $view = new TGui("LpdfsOCIHistorico");
        $view->addData("assinados", $assinados);
        $view->renderize(APP_VIEW_LIST,true);
    }
    
    public function loadProcedSec($params){          
        $data = $params[2];     
        $idPac = $params[3];   
        $medico = $params[4];         
        $procedsSecs = Daooci::loadProcedSec($data, $idPac,$medico);        
        
        if (isset($params[5])) { 
        $listMode = $params[5];     
        }
          if (isset($params[6])) { 
        $status = $params[6];     
        }
        if($listMode == 1)  {
            $view = new TGui("Lprocedsecundariolist");
            $view->addData("proceds", $procedsSecs);
            $view->addData("idPac", $idPac);
            $view->addData("sts", $status);
            $view->renderize(APP_VIEW_LIST,true);
        } else {
            $view = new TGui("Lprocedsecundario");
            $view->addData("proceds", $procedsSecs);
            $view->renderize(APP_VIEW_LIST,true);
        }
    }
    
    public function delProced($params){          
        $id = $params[2];
        $res = Daooci::delProced($id);
    }
    
     public function delFila($params){          
        $id = $params[2];
        $res = Daooci::delFila($id);
    }
    
    public function getCID($param){               
        $idOCI = $param[2];           
        $cids = Daooci::getCID($idOCI);
        echo $cids;
    }  
    
     public function getProcedSec($param){               
        $idOCI = $param[2];           
        $secs = Daooci::getSec($idOCI);
        echo $secs;
    }
}
