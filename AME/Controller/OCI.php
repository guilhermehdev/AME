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

        $sessao = AppController::checkSession();
        if (!$sessao) {
            http_response_code(401);
            echo json_encode(array('erro' => true, 'mensagem' => 'Sessão expirada. Faça login novamente.'));
            return;
        }

        $base64 = isset($_POST['pdf']) ? $_POST['pdf'] : '';
        $nomeOriginal = isset($_POST['original']) ? basename($_POST['original']) : '';
        $pastaRecebida = isset($_POST['pasta']) ? trim(str_replace('\\', '/', $_POST['pasta'])) : '';
        $etapa = isset($_POST['etapa']) ? $_POST['etapa'] : '';
        $base64 = preg_replace('/^data:application\/pdf;base64,/', '', $base64);
        $conteudo = base64_decode(str_replace(' ', '+', $base64), true);

        if ($conteudo === false || substr($conteudo, 0, 4) !== '%PDF' || $nomeOriginal === '') {
            http_response_code(422);
            echo json_encode(array('erro' => true, 'mensagem' => 'O PDF assinado ou o arquivo de origem é inválido.'));
            return;
        }

        $dadosMedico = Daooci::getCBO(isset($sessao['id']) ? $sessao['id'] : null);
        if (empty($dadosMedico) || empty($dadosMedico[0]['SUS'])) {
            http_response_code(403);
            echo json_encode(array('erro' => true, 'mensagem' => 'Não foi possível identificar o médico conectado.'));
            return;
        }

        $resultado = Daooci::processarPdfAssinado(
            $nomeOriginal,
            $conteudo,
            $pastaRecebida,
            $etapa,
            $dadosMedico[0]['SUS'],
            isset($dadosMedico[0]['oci_autorizador']) ? $dadosMedico[0]['oci_autorizador'] : 0
        );

        if (!empty($resultado['erro'])) {
            http_response_code(422);
        }

        echo json_encode($resultado);
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

        $sessao = AppController::checkSession();
        if (!$sessao) {
            http_response_code(401);
            echo json_encode(array('erro' => true, 'mensagem' => 'Sessão expirada. Faça login novamente.'));
            return;
        }

        $pastaRecebida = isset($_POST['pasta']) ? trim(str_replace('\\', '/', $_POST['pasta'])) : '';
        $etapa = isset($_POST['etapa']) ? $_POST['etapa'] : '';
        if ($pastaRecebida === '' || !isset($_FILES['arquivo'])) {
            http_response_code(422);
            echo json_encode(array('erro' => true, 'mensagem' => 'Selecione um PDF assinado e informe a pasta do lote.'));
            return;
        }

        $arquivoUpload = $_FILES['arquivo'];
        if (!isset($arquivoUpload['error']) || $arquivoUpload['error'] !== UPLOAD_ERR_OK) {
            http_response_code(422);
            echo json_encode(array('erro' => true, 'mensagem' => 'Não foi possível receber o PDF assinado.'));
            return;
        }

        $nomeRecebido = isset($arquivoUpload['name'])
            ? str_replace('\\', '/', $arquivoUpload['name'])
            : '';
        $nomeOriginal = basename($nomeRecebido);
        if ($nomeOriginal === '' || strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION)) !== 'pdf') {
            http_response_code(422);
            echo json_encode(array('erro' => true, 'mensagem' => 'O arquivo enviado precisa ser um PDF válido.'));
            return;
        }

        $conteudo = file_get_contents($arquivoUpload['tmp_name']);
        if ($conteudo === false || substr($conteudo, 0, 4) !== '%PDF') {
            http_response_code(422);
            echo json_encode(array('erro' => true, 'mensagem' => 'O arquivo enviado não é um PDF válido.'));
            return;
        }

        $dadosMedico = Daooci::getCBO(isset($sessao['id']) ? $sessao['id'] : null);
        if (empty($dadosMedico) || empty($dadosMedico[0]['SUS'])) {
            http_response_code(403);
            echo json_encode(array('erro' => true, 'mensagem' => 'Não foi possível identificar o médico conectado.'));
            return;
        }

        $resultado = Daooci::processarPdfAssinado(
            $nomeOriginal,
            $conteudo,
            $pastaRecebida,
            $etapa,
            $dadosMedico[0]['SUS'],
            isset($dadosMedico[0]['oci_autorizador']) ? $dadosMedico[0]['oci_autorizador'] : 0
        );

        if (!empty($resultado['erro'])) {
            http_response_code(422);
            echo json_encode($resultado);
            return;
        }

        $resultado['arquivos'] = array(isset($resultado['nomeOrigem']) ? $resultado['nomeOrigem'] : $nomeOriginal);
        echo json_encode($resultado);
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
