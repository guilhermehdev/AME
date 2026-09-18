<?php
/**
 * Description of Daooci
 *
 * @author Gui
 */
class Daooci {

    private static function normalizarNomePasta($valor) {
        $valor = preg_replace('/\s+/', ' ', trim($valor));
        return function_exists('mb_strtolower')
            ? mb_strtolower($valor, 'UTF-8')
            : strtolower($valor);
    }

    private static function normalizarPastaRelativa($pasta) {
        $pasta = trim(str_replace('\\', '/', (string)$pasta), '/');
        $partes = array();

        if ($pasta === '') {
            return false;
        }

        foreach (explode('/', $pasta) as $parte) {
            $parte = trim($parte);
            if ($parte === '' || $parte === '.' || $parte === '..' ||
                !preg_match('/^[\pL\pN _-]+$/u', $parte)) {
                return false;
            }
            $partes[] = $parte;
        }

        return $partes;
    }

    private static function buscarOciDoPdf($nomeArquivo) {
        $nomeBase = pathinfo(basename($nomeArquivo), PATHINFO_FILENAME);
        if (!preg_match('/^(\d+)-/', $nomeBase, $partes)) {
            return null;
        }

        $stmt = Maincontroller::doQuery(
            "SELECT oci.id, oci.id_medico, oci.id_autorizador, oci.`data`,
                    COALESCE(oci.assinado, 0) AS assinado,
                    executante.nome AS nome_executante,
                    autorizador.nome AS nome_autorizador,
                    COALESCE(executante.oci_autorizador, 0) AS nivel_executante,
                    COALESCE(autorizador.oci_autorizador, 0) AS nivel_autorizador
             FROM oci
             LEFT JOIN servidores executante ON executante.SUS = oci.id_medico
             LEFT JOIN servidores autorizador ON autorizador.SUS = oci.id_autorizador
             WHERE oci.id = :ID_OCI
             LIMIT 1",
            array('ID_OCI' => (int)$partes[1])
        );

        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
    }

    private static function nivelAutorizadorValido($nivelExecutante, $nivelAutorizador) {
        $nivelExecutante = (int)$nivelExecutante;
        $nivelAutorizador = (int)$nivelAutorizador;

        if ($nivelExecutante === 0) {
            return in_array($nivelAutorizador, array(1, 2), true);
        }

        return $nivelExecutante === 1 && $nivelAutorizador === 2;
    }

    private static function localizarPdfOrigem($diretorio, $nomeInformado) {
        $nomeInformado = basename((string)$nomeInformado);
        $caminho = rtrim($diretorio, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $nomeInformado;
        if (is_file($caminho)) {
            return array($nomeInformado, $caminho);
        }

        if (!is_dir($diretorio)) {
            return null;
        }

        $baseInformada = pathinfo($nomeInformado, PATHINFO_FILENAME);
        $baseSemAssinatura = preg_replace('/(?:[ _-]+)(?:assinado|signed)(?:[ _-].*)?$/iu', '', $baseInformada);
        $baseSemAssinatura = trim($baseSemAssinatura, ' _-');

        foreach (scandir($diretorio) as $arquivo) {
            if ($arquivo === '.' || $arquivo === '..' ||
                strtolower(pathinfo($arquivo, PATHINFO_EXTENSION)) !== 'pdf') {
                continue;
            }

            if (strcasecmp(pathinfo($arquivo, PATHINFO_FILENAME), $baseSemAssinatura) === 0) {
                return array($arquivo, rtrim($diretorio, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $arquivo);
            }
        }

        return null;
    }

    /**
     * Recebe a assinatura de uma OCI e encaminha o arquivo para a próxima etapa.
     * $etapa deve ser "executante" ou "autorizador".
     */
    public static function processarPdfAssinado($nomeOriginal, $conteudo, $pasta, $etapa, $medicoSUS, $medicoAutorizador) {
        if (!is_string($conteudo) || substr($conteudo, 0, 4) !== '%PDF') {
            return array('erro' => true, 'mensagem' => 'O conteúdo recebido não é um PDF válido.');
        }

        if (!in_array($etapa, array('executante', 'autorizador'), true)) {
            return array('erro' => true, 'mensagem' => 'Etapa de assinatura inválida. Atualize a página e tente novamente.');
        }

        $partesPasta = self::normalizarPastaRelativa($pasta);
        if ($partesPasta === false && trim((string)$pasta) === '' && $etapa === 'executante') {
            // Compatibilidade com arquivos antigos que ainda estão na raiz de Gerados.
            $partesPasta = array();
        }
        if ($partesPasta === false || (count($partesPasta) < 2 && !empty($partesPasta))) {
            return array('erro' => true, 'mensagem' => 'A pasta do PDF é inválida.');
        }

        $oci = self::buscarOciDoPdf($nomeOriginal);
        if (!$oci) {
            return array('erro' => true, 'mensagem' => 'Não foi possível identificar a OCI deste PDF.');
        }

        if (!empty($oci['assinado'])) {
            return array('erro' => true, 'mensagem' => 'Esta OCI já concluiu as duas etapas de assinatura.');
        }

        $raizOci = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Impressos' . DIRECTORY_SEPARATOR . 'OCI' . DIRECTORY_SEPARATOR;
        $raizGerados = $raizOci . 'Gerados' . DIRECTORY_SEPARATOR;
        $raizPendentesAutorizador = $raizOci . 'PendentesAutorizador' . DIRECTORY_SEPARATOR;
        $raizAssinados = $raizOci . 'Assinados' . DIRECTORY_SEPARATOR;

        if ($etapa === 'executante') {
            if ((string)$medicoSUS !== (string)$oci['id_medico']) {
                return array('erro' => true, 'mensagem' => 'Este PDF não pertence ao médico executante conectado.');
            }
            if (!self::nivelAutorizadorValido($oci['nivel_executante'], $oci['nivel_autorizador']) ||
                empty($oci['id_autorizador']) ||
                empty($oci['nome_autorizador'])) {
                return array('erro' => true, 'mensagem' => 'A OCI precisa estar atribuída a um autorizador permitido para o nível do executante.');
            }
            $raizOrigem = $raizGerados;
            $nomePastaEsperado = $oci['nome_executante'];
        } else {
            if ((string)$medicoSUS !== (string)$oci['id_autorizador'] ||
                (int)$medicoAutorizador !== (int)$oci['nivel_autorizador'] ||
                !self::nivelAutorizadorValido($oci['nivel_executante'], $oci['nivel_autorizador'])) {
                return array('erro' => true, 'mensagem' => 'Este PDF não está atribuído ao médico autorizador conectado.');
            }
            $raizOrigem = $raizPendentesAutorizador;
            $nomePastaEsperado = $oci['nome_autorizador'];
        }

        if (!empty($partesPasta) && self::normalizarNomePasta($partesPasta[0]) !== self::normalizarNomePasta($nomePastaEsperado)) {
            return array('erro' => true, 'mensagem' => 'A pasta informada não corresponde ao médico desta etapa.');
        }

        $subpasta = implode(DIRECTORY_SEPARATOR, $partesPasta);
        $diretorioOrigem = $raizOrigem . $subpasta . DIRECTORY_SEPARATOR;
        $origem = self::localizarPdfOrigem($diretorioOrigem, $nomeOriginal);
        if (!$origem) {
            return array('erro' => true, 'mensagem' => 'O PDF não foi encontrado na pasta pendente selecionada.');
        }

        if (sha1_file($origem[1]) === sha1($conteudo)) {
            return array('erro' => true, 'mensagem' => 'Este arquivo é igual ao PDF original e ainda não contém a assinatura.');
        }

        if ($etapa === 'executante') {
            // Organiza a próxima etapa por autorizador, executante e data.
            $nomeDestino = basename($origem[0]);
            $dataDestino = !empty($partesPasta) ? end($partesPasta) : '';
            if (!preg_match('/^\d{2}-\d{2}-\d{4}$/', $dataDestino)) {
                preg_match('/_(\d{2}-\d{2}-\d{4})/', pathinfo($origem[0], PATHINFO_FILENAME), $dataNoNome);
                $dataDestino = isset($dataNoNome[1]) ? $dataNoNome[1] : '';
            }
            if ($dataDestino === '') {
                $timestampOci = strtotime($oci['data']);
                $dataDestino = $timestampOci ? date('d-m-Y', $timestampOci) : date('d-m-Y');
            }
            $partesDestino = array($oci['nome_autorizador'], $oci['nome_executante'], $dataDestino);
            $raizDestino = $raizPendentesAutorizador;
            $mensagem = 'Assinatura do executante registrada. PDF encaminhado ao médico autorizador para a segunda assinatura.';
            $etapaSeguinte = 'autorizador';
            $ociAtualizada = false;
        } else {
            $nomeBase = pathinfo($origem[0], PATHINFO_FILENAME);
            $nomeBaseSeguro = preg_replace('/[^A-Za-z0-9_-]+/', '_', $nomeBase);
            $nomeDestino = trim($nomeBaseSeguro, '_') . '_assinado_' . date('Ymd_His') . '_' . substr(sha1(uniqid('', true)), 0, 6) . '.pdf';
            $partesDestino = array($oci['nome_autorizador'], end($partesPasta));
            $raizDestino = $raizAssinados;
            $mensagem = 'Assinatura do médico autorizador registrada. OCI concluída.';
            $etapaSeguinte = 'concluido';
            $ociAtualizada = true;
        }

        $diretorioDestino = $raizDestino . implode(DIRECTORY_SEPARATOR, $partesDestino) . DIRECTORY_SEPARATOR;
        if (!is_dir($diretorioDestino) && !mkdir($diretorioDestino, 0755, true)) {
            return array('erro' => true, 'mensagem' => 'Não foi possível criar a pasta de destino da assinatura.');
        }

        $caminhoDestino = $diretorioDestino . $nomeDestino;
        if (is_file($caminhoDestino)) {
            return array('erro' => true, 'mensagem' => 'Já existe um PDF para esta OCI na pasta de destino. Verifique a lista antes de reenviar.');
        }
        if (file_put_contents($caminhoDestino, $conteudo, LOCK_EX) === false) {
            return array('erro' => true, 'mensagem' => 'Não foi possível salvar o PDF assinado.');
        }

        if ($ociAtualizada) {
            $atualizacao = Maincontroller::doQuery(
                'UPDATE oci SET assinado = 1 WHERE id = :ID_OCI',
                array('ID_OCI' => (int)$oci['id'])
            );
            if ($atualizacao === false) {
                unlink($caminhoDestino);
                return array('erro' => true, 'mensagem' => 'O PDF foi assinado, mas não foi possível atualizar o status da OCI.');
            }
        }

        if (is_file($origem[1]) && !unlink($origem[1])) {
            if (!$ociAtualizada) {
                unlink($caminhoDestino);
                return array('erro' => true, 'mensagem' => 'A assinatura foi recebida, mas não foi possível mover o PDF para a pasta do autorizador.');
            }
        }

        $urlBase = $etapa === 'executante' ? 'Impressos/OCI/PendentesAutorizador/' : 'Impressos/OCI/Assinados/';
        $segmentosUrl = array();
        foreach (array_merge($partesDestino, array($nomeDestino)) as $segmento) {
            $segmentosUrl[] = rawurlencode($segmento);
        }

        return array(
            'erro' => false,
            'mensagem' => $mensagem,
            'etapaSeguinte' => $etapaSeguinte,
            'nomeOrigem' => $origem[0],
            'arquivo' => URL . $urlBase . implode('/', $segmentosUrl),
            'ociAtualizada' => (bool)$ociAtualizada
        );
    }

    public static function atualizarAssinadoPorArquivo($nomeArquivo, $assinado = 1) {
        $nomeBase = pathinfo(basename($nomeArquivo), PATHINFO_FILENAME);

        // Os PDFs gerados pelo sistema usam o ID da OCI antes do primeiro hífen:
        // 11185-NOME DO PACIENTE.pdf
        if (!preg_match('/^(\d+)-/', $nomeBase, $partes)) {
            return false;
        }

        try {
            Maincontroller::doQuery(
                "UPDATE oci SET assinado = :ASSINADO WHERE id = :ID_OCI",
                array(
                    'ASSINADO' => $assinado ? 1 : 0,
                    'ID_OCI' => (int) $partes[1]
                )
            );
        } catch (Exception $ex) {
            return false;
        }

        return true;
    }

    public static function excluirPdfGerado($nomeArquivo, $pasta, $idUsuario = null, $nomeUsuario = '', $medico = '') {
        $nomeArquivo = basename($nomeArquivo);
        $pasta = trim(str_replace('\\', '/', $pasta), '/');

        if ($nomeArquivo === '' || strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION)) !== 'pdf') {
            return array('erro' => true, 'mensagem' => 'Arquivo PDF inválido.');
        }

        $partesPasta = array();
        if ($pasta !== '') {
            foreach (explode('/', $pasta) as $parte) {
                $parte = trim($parte);
                if ($parte === '' || $parte === '.' || $parte === '..' ||
                    !preg_match('/^[\pL\pN _-]+$/u', $parte)) {
                    return array('erro' => true, 'mensagem' => 'Pasta do PDF inválida.');
                }
                $partesPasta[] = $parte;
            }
        }

        // Autoriza somente PDFs que já foram listados para o usuário logado.
        $pdfsPermitidos = self::getPdfsGerados($medico, $idUsuario, $nomeUsuario);
        $pastaNormalizada = implode('/', $partesPasta);
        $permitido = false;

        foreach ((array) $pdfsPermitidos as $pdf) {
            $pastaPdf = isset($pdf['pasta']) ? trim(str_replace('\\', '/', $pdf['pasta']), '/') : '';
            if ((!isset($pdf['etapa']) || $pdf['etapa'] === 'executante') &&
                $pdf['nome'] === $nomeArquivo && $pastaPdf === $pastaNormalizada) {
                $permitido = true;
                break;
            }
        }

        if (!$permitido) {
            return array('erro' => true, 'mensagem' => 'Esse PDF não pertence ao usuário logado ou já foi assinado.');
        }

        $diretorioBase = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Impressos' . DIRECTORY_SEPARATOR . 'OCI' . DIRECTORY_SEPARATOR . 'Gerados' . DIRECTORY_SEPARATOR;
        $caminho = $diretorioBase . ($pastaNormalizada !== '' ? str_replace('/', DIRECTORY_SEPARATOR, $pastaNormalizada) . DIRECTORY_SEPARATOR : '') . $nomeArquivo;

        if (!is_file($caminho) || !unlink($caminho)) {
            return array('erro' => true, 'mensagem' => 'Não foi possível excluir o PDF.');
        }

        // Se a OCI estava marcada por alguma tentativa anterior, a exclusão
        // devolve o registro ao estado pendente.
        self::atualizarAssinadoPorArquivo($nomeArquivo, 0);

        return array('erro' => false, 'mensagem' => 'PDF excluído com sucesso.');
    }
           
    public static function getTipoOCI($idUser) {       
        $mc = new Maincontroller();        
        $tipo = $mc->doSelect("cod_oci_principal.id", "cod_oci_principal.abrev", "cod_oci_principal", "JOIN serv_oci ON serv_oci.id_oci = cod_oci_principal.id
WHERE serv_oci.id_serv ={$idUser}", "ORDER BY cod_oci_principal.id");       
        return $tipo;
    }
    
    public static function getCID($idOCI) {       
        $mc = new Maincontroller();        
        $cids = $mc->doSelect("cid", "descricao", "cid", "WHERE id_oci_principal={$idOCI}","ORDER BY id");       
        return $cids;
    }  
    
     public static function getCBO($idUser){   
        $sql = "SELECT servidores.cbo, servidores.SUS, servidores.nome,
                       COALESCE(servidores.oci_autorizador, 0) AS oci_autorizador
                FROM servidores
                LEFT JOIN usuarios ON usuarios.CPF = servidores.CPF
                WHERE servidores.id_usuario = :ID_USUARIO
                   OR usuarios.id = :ID_USUARIO
                LIMIT 1";
        $ds = Maincontroller::doQuery($sql, array('ID_USUARIO' => $idUser));

        if (!$ds || $ds->rowCount() === 0) {
            $sql = "SELECT servidores.cbo, servidores.SUS, servidores.nome,
                           COALESCE(servidores.oci_autorizador, 0) AS oci_autorizador
                    FROM servidores
                    JOIN usuarios ON usuarios.id = :ID_USUARIO
                    WHERE UPPER(servidores.nome) LIKE CONCAT(
                        '%',
                        UPPER(TRIM(REPLACE(REPLACE(REPLACE(usuarios.nome, 'DRª ', ''), 'DRA ', ''), 'DR ', ''))),
                        '%'
                    )
                    ORDER BY servidores.nome
                    LIMIT 1";
            $ds = Maincontroller::doQuery($sql, array('ID_USUARIO' => $idUser));
        }

        $arr = array();        
        while($row = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $row;
        }   
        return $arr;  
    }  
    
     public static function getSec($idOCI) {       
        $mc = new Maincontroller();        
        $secs = $mc->doSelect("cod", "descricao", "cod_oci_secundario", "WHERE id_cod_principal={$idOCI}","ORDER BY id");       
        return $secs;
    }  
    
      public static function saveToList($medico,$idPac,$data,$procedPrincipal,$cidPrincipal,$cidSecundario) {
         $sql = "INSERT INTO oci_fila (id_medico_solicitante, id_paciente, data, cod_proced_principal,cid_principal,cid_secundario) VALUES (:MEDICO,:IDPAC,:DATA,:PROCEDPRINCIPAL,:CIDPRINCIPAL,:CIDSECUNDARIO)";            
        if(Maincontroller::doQuery($sql,array('MEDICO'=>$medico,'IDPAC'=>$idPac,'DATA'=>Functions::ENdateFormat($data),'PROCEDPRINCIPAL'=>$procedPrincipal,'CIDPRINCIPAL'=>$cidPrincipal,'CIDSECUNDARIO'=>$cidSecundario))){
           
        } 
    } 
    
    public function saveProcedSec($params) {       
        $idPac = $params[2];
        $procedSec = $params[3];
        $qtd = $params[4];
        $cbo = $params[5];
        $sus = $params[6];
        $data = Functions::ENdateFormat($params[7]);    
        
         $sql = "INSERT INTO procedimentos_secundarios (id_paciente, cod_proced_secundario, qtd, cbo,medico_solicitante,data) VALUES (:IDPAC,:CODPROCED,:QTD,:CBO,:SUS,:DATA)";            
        if(Maincontroller::doQuery($sql,array('IDPAC'=>$idPac,'CODPROCED'=>$procedSec,'QTD'=>$qtd,'CBO'=>$cbo,'SUS'=>$sus,'DATA'=>$data))){
           
        } 
    } 
    
     public static function loadProcedSec($data,$idPac ,$medico) {      
        $sql = "SELECT procedimentos_secundarios.id, cod_oci_secundario.cod,cod_oci_secundario.descricao,procedimentos_secundarios.qtd,procedimentos_secundarios.cbo
        FROM procedimentos_secundarios
        JOIN cod_oci_secundario ON procedimentos_secundarios.cod_proced_secundario = cod_oci_secundario.cod 
        WHERE procedimentos_secundarios.data='{$data}' 
        AND procedimentos_secundarios.id_paciente ={$idPac}
        AND procedimentos_secundarios.medico_solicitante='{$medico}'";
        $ds = Maincontroller::doQuery($sql);        
        $arr = array();        
        while($proceds = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $proceds;
        }                
        return $arr;
     }
     
      public static function loadFila($data,$medico,$proced) {      
        $sql = "SELECT oci_fila.id,pacientes.id AS idPac, oci_fila.data,pacientes.nome, pacientes.dtnasc, oci_fila.cid_principal, oci_fila.cid_secundario,oci_fila.status
        FROM oci_fila
        JOIN pacientes ON pacientes.id = oci_fila.id_paciente
        WHERE oci_fila.id_medico_solicitante = '{$medico}'
        AND oci_fila.`data`='{$data}'
        AND oci_fila.cod_proced_principal={$proced}";
        $ds = Maincontroller::doQuery($sql);        
        $arr = array();        
        while($fila = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $fila;
        }                
        return $arr;
     }

     public static function loadFinalizadas($data,$medico,$proced) {
        $sql = "SELECT oci.id, oci.num_apac, oci.`data`, pacientes.nome, pacientes.dtnasc,
                       cod_oci_principal.abrev AS procedimento
                FROM oci
                JOIN pacientes ON pacientes.id = oci.id_paciente
                JOIN cod_oci_principal ON cod_oci_principal.id = oci.id_cod_principal
                WHERE oci.id_medico = :MEDICO
                  AND oci.`data` = :DATA
                  AND oci.id_cod_principal = :PROCED
                  AND oci.status = 'CONC'
                ORDER BY pacientes.nome";

        $ds = Maincontroller::doQuery($sql,array(
            'MEDICO' => $medico,
            'DATA' => $data,
            'PROCED' => $proced
        ));

        $arr = array();
        if ($ds) {
            while($oci = $ds->fetch(PDO::FETCH_ASSOC)){
                $arr[] = $oci;
            }
        }

        return $arr;
     }

     public static function getPdfsGerados($medico = '', $idUsuario = null, $nomeUsuario = '') {
        $nomeMedico = '';
        $medicoSUS = $medico;
        $medicoAutorizador = 0;

        if (!empty($idUsuario)) {
            $stmt = Maincontroller::doQuery(
                "SELECT servidores.nome, servidores.SUS,
                        COALESCE(servidores.oci_autorizador, 0) AS oci_autorizador
                 FROM servidores
                 LEFT JOIN usuarios ON usuarios.CPF = servidores.CPF
                 WHERE servidores.id_usuario = :ID_USUARIO
                    OR usuarios.id = :ID_USUARIO
                 LIMIT 1",
                array('ID_USUARIO' => $idUsuario)
            );
        } elseif (!empty($medico)) {
            $stmt = Maincontroller::doQuery(
                "SELECT nome, SUS, COALESCE(oci_autorizador, 0) AS oci_autorizador
                 FROM servidores WHERE SUS = :MEDICO LIMIT 1",
                array('MEDICO' => $medico)
            );
        } else {
            return array();
        }

        if ($stmt && ($servidor = $stmt->fetch(PDO::FETCH_ASSOC))) {
            $nomeMedico = $servidor['nome'];
            $medicoSUS = $servidor['SUS'];
            $medicoAutorizador = (int)$servidor['oci_autorizador'];
        } elseif (!empty($nomeUsuario)) {
            // Fallback para usuários que ainda não estão vinculados por id_usuario
            // à tabela servidores. Ex.: DRª JANAINA -> JANAINA.
            $nomeMedico = preg_replace(
                '/^dr[^\\s]*[\\s]+/iu',
                '',
                trim($nomeUsuario)
            );
        } else {
            return array();
        }

        $diretorio = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Impressos' . DIRECTORY_SEPARATOR . 'OCI' . DIRECTORY_SEPARATOR . 'Gerados' . DIRECTORY_SEPARATOR;
        $diretorioAssinados = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Impressos' . DIRECTORY_SEPARATOR . 'OCI' . DIRECTORY_SEPARATOR . 'Assinados' . DIRECTORY_SEPARATOR;
        $diretorioExiste = is_dir($diretorio);

        $normalizar = function($valor) {
            $valor = preg_replace('/\s+/', ' ', trim($valor));
            return function_exists('mb_strtolower')
                ? mb_strtolower($valor, 'UTF-8')
                : strtolower($valor);
        };

        $nomeMedicoOriginal = trim($nomeMedico);
        $nomeMedico = $normalizar($nomeMedicoOriginal);
        $pdfs = array();
        $diretorioBusca = null;

        // Procura a pasta do médico sem depender de maiúsculas, acentos ou
        // pequenas diferenças de espaços no nome.
        if ($diretorioExiste) {
            foreach (scandir($diretorio) as $pasta) {
                $caminhoPasta = $diretorio . $pasta;
                if ($pasta === '.' || $pasta === '..' || !is_dir($caminhoPasta)) {
                    continue;
                }

                $pastaNormalizada = $normalizar($pasta);
                if ($pastaNormalizada === $nomeMedico ||
                    (empty($medico) && strpos($pastaNormalizada, $nomeMedico) === 0)) {
                    $diretorioBusca = rtrim($caminhoPasta, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
                    break;
                }
            }
        }

        $possuiAssinatura = function($nomeArquivo, $pastaRelativa) use ($diretorioAssinados) {
            $pastaRelativa = str_replace('/', DIRECTORY_SEPARATOR, $pastaRelativa);
            $diretorioBuscaAssinados = rtrim($diretorioAssinados . $pastaRelativa, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

            if (!is_dir($diretorioBuscaAssinados)) {
                return false;
            }

            $nomeBase = pathinfo($nomeArquivo, PATHINFO_FILENAME);
            $nomeBaseSeguro = preg_replace('/[^A-Za-z0-9_-]+/', '_', $nomeBase);
            $nomeBaseSeguro = trim($nomeBaseSeguro, '_');
            $prefixos = array_filter(array_unique(array(
                $nomeBaseSeguro . '_assinado',
                $nomeBase . '_assinado'
            )));

            foreach (scandir($diretorioBuscaAssinados) as $arquivoAssinado) {
                if ($arquivoAssinado === '.' || $arquivoAssinado === '..') {
                    continue;
                }

                $caminhoAssinado = $diretorioBuscaAssinados . $arquivoAssinado;
                if (!is_file($caminhoAssinado) || strtolower(pathinfo($arquivoAssinado, PATHINFO_EXTENSION)) !== 'pdf') {
                    continue;
                }

                $nomeAssinado = pathinfo($arquivoAssinado, PATHINFO_FILENAME);
                foreach ($prefixos as $prefixo) {
                    if (stripos($nomeAssinado, $prefixo . '_') === 0 || strcasecmp($nomeAssinado, $prefixo) === 0) {
                        return true;
                    }
                }
            }

            return false;
        };

        $adicionarPdf = function($caminhoArquivo, $raiz, $exigirPrefixo) use (&$pdfs, $normalizar, $nomeMedico, $medico, $medicoSUS, $nomeUsuario, $possuiAssinatura) {
            if (strtolower(pathinfo($caminhoArquivo, PATHINFO_EXTENSION)) !== 'pdf') {
                return;
            }

            $arquivo = basename($caminhoArquivo);
            $nomeSemExtensao = pathinfo($arquivo, PATHINFO_FILENAME);
            $nomeArquivoNormalizado = $normalizar($nomeSemExtensao);

            if ($exigirPrefixo) {
                $corresponde = strpos($nomeArquivoNormalizado, $nomeMedico . '_') === 0;

                if (!$corresponde && empty($medico) && !empty($nomeUsuario)) {
                    $primeiroNome = strtok($nomeMedico, ' ');
                    $corresponde = strpos($nomeArquivoNormalizado, $primeiroNome . ' ') === 0
                        || strpos($nomeArquivoNormalizado, $primeiroNome . '_') === 0;
                }

                if (!$corresponde) {
                    return;
                }
            }

            $ociPdf = self::buscarOciDoPdf($arquivo);
            if (!$ociPdf || !empty($ociPdf['assinado']) ||
                (string)$ociPdf['id_medico'] !== (string)$medicoSUS) {
                return;
            }

            $relativo = ltrim(substr($caminhoArquivo, strlen($raiz)), DIRECTORY_SEPARATOR);
            $partesCaminho = explode(DIRECTORY_SEPARATOR, $relativo);
            $data = '';

            foreach ($partesCaminho as $parteCaminho) {
                if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $parteCaminho)) {
                    $data = $parteCaminho;
                    break;
                }
            }

            if ($data === '') {
                preg_match('/_(\d{2}-\d{2}-\d{4})$/', $nomeSemExtensao, $partesData);
                $data = isset($partesData[1]) ? $partesData[1] : '';
            }

            $relativoUrl = str_replace(DIRECTORY_SEPARATOR, '/', $relativo);
            $segmentosUrl = array();
            foreach (explode('/', $relativoUrl) as $segmento) {
                $segmentosUrl[] = rawurlencode($segmento);
            }

            $pastaRelativa = dirname($relativoUrl);

            if ($possuiAssinatura($arquivo, $pastaRelativa === '.' ? '' : $pastaRelativa)) {
                return;
            }

            $pdfs[] = array(
                'nome' => $arquivo,
                'url' => URL . 'Impressos/OCI/Gerados/' . implode('/', $segmentosUrl),
                'pasta' => $pastaRelativa === '.' ? '' : $pastaRelativa,
                'data' => $data,
                'etapa' => 'executante',
                'modificado' => filemtime($caminhoArquivo)
            );
        };

        if ($diretorioExiste && $diretorioBusca !== null) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($diretorioBusca, FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $arquivoInfo) {
                if ($arquivoInfo->isFile()) {
                    $adicionarPdf($arquivoInfo->getPathname(), $diretorio, false);
                }
            }
        }

        // Compatibilidade temporária com PDFs antigos que ainda estão na raiz.
        if ($diretorioExiste && empty($pdfs)) {
            foreach (scandir($diretorio) as $arquivo) {
                if ($arquivo === '.' || $arquivo === '..') {
                    continue;
                }
                $caminhoArquivo = $diretorio . $arquivo;
                if (is_file($caminhoArquivo)) {
                    $adicionarPdf($caminhoArquivo, $diretorio, true);
                }
            }
        }

        // PDFs já assinados pelo executante aguardam a assinatura final na pasta do autorizador.
        if ($medicoAutorizador > 0 && !empty($medicoSUS)) {
            $diretorioPendentes = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Impressos' . DIRECTORY_SEPARATOR . 'OCI' . DIRECTORY_SEPARATOR . 'PendentesAutorizador' . DIRECTORY_SEPARATOR;
            if (is_dir($diretorioPendentes)) {
                $diretorioAutorizador = null;
                foreach (scandir($diretorioPendentes) as $pasta) {
                    $caminhoPasta = $diretorioPendentes . $pasta;
                    if ($pasta !== '.' && $pasta !== '..' && is_dir($caminhoPasta) &&
                        $normalizar($pasta) === $nomeMedico) {
                        $diretorioAutorizador = rtrim($caminhoPasta, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
                        break;
                    }
                }

                if ($diretorioAutorizador !== null) {
                    $iteratorPendentes = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($diretorioAutorizador, FilesystemIterator::SKIP_DOTS)
                    );

                    foreach ($iteratorPendentes as $arquivoInfo) {
                        if (!$arquivoInfo->isFile() || strtolower($arquivoInfo->getExtension()) !== 'pdf') {
                            continue;
                        }

                        $arquivo = $arquivoInfo->getFilename();
                        $ociPdf = self::buscarOciDoPdf($arquivo);
                        if (!$ociPdf || !empty($ociPdf['assinado']) ||
                            (string)$ociPdf['id_autorizador'] !== (string)$medicoSUS ||
                            !self::nivelAutorizadorValido($ociPdf['nivel_executante'], $ociPdf['nivel_autorizador'])) {
                            continue;
                        }

                        $relativo = ltrim(substr($arquivoInfo->getPathname(), strlen($diretorioPendentes)), DIRECTORY_SEPARATOR);
                        $relativoUrl = str_replace(DIRECTORY_SEPARATOR, '/', $relativo);
                        $segmentosUrl = array();
                        foreach (explode('/', $relativoUrl) as $segmento) {
                            $segmentosUrl[] = rawurlencode($segmento);
                        }

                        $data = '';
                        foreach (explode('/', $relativoUrl) as $parteCaminho) {
                            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $parteCaminho)) {
                                $data = $parteCaminho;
                                break;
                            }
                        }

                        $pdfs[] = array(
                            'nome' => $arquivo,
                            'url' => URL . 'Impressos/OCI/PendentesAutorizador/' . implode('/', $segmentosUrl),
                            'pasta' => dirname($relativoUrl) === '.' ? '' : dirname($relativoUrl),
                            'data' => $data,
                            'etapa' => 'autorizador',
                            'modificado' => filemtime($arquivoInfo->getPathname())
                        );
                    }
                }
            }
        }

        usort($pdfs, function($a, $b) {
            return $b['modificado'] <=> $a['modificado'];
        });

        return $pdfs;
     }

     public static function getPdfsAssinados($medico = '', $idUsuario = null, $nomeUsuario = '') {
        $nomeMedico = '';
        $medicoSUS = $medico;

        if (!empty($idUsuario)) {
            $stmt = Maincontroller::doQuery(
                "SELECT servidores.nome, servidores.SUS
                 FROM servidores
                 LEFT JOIN usuarios ON usuarios.CPF = servidores.CPF
                 WHERE servidores.id_usuario = :ID_USUARIO
                    OR usuarios.id = :ID_USUARIO
                 LIMIT 1",
                array('ID_USUARIO' => $idUsuario)
            );
        } elseif (!empty($medico)) {
            $stmt = Maincontroller::doQuery(
                "SELECT nome, SUS FROM servidores WHERE SUS = :MEDICO LIMIT 1",
                array('MEDICO' => $medico)
            );
        } else {
            return array();
        }

        if ($stmt && ($servidor = $stmt->fetch(PDO::FETCH_ASSOC))) {
            $nomeMedico = $servidor['nome'];
            $medicoSUS = $servidor['SUS'];
        } elseif (!empty($nomeUsuario)) {
            $nomeMedico = preg_replace('/^dr[^\s]*[\s]+/iu', '', trim($nomeUsuario));
        } else {
            return array();
        }

        $diretorio = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Impressos' . DIRECTORY_SEPARATOR . 'OCI' . DIRECTORY_SEPARATOR . 'Assinados' . DIRECTORY_SEPARATOR;
        if (!is_dir($diretorio)) {
            return array();
        }

        $normalizar = function($valor) {
            $valor = preg_replace('/\s+/', ' ', trim($valor));
            return function_exists('mb_strtolower')
                ? mb_strtolower($valor, 'UTF-8')
                : strtolower($valor);
        };

        $nomeMedico = $normalizar($nomeMedico);
        $diretorioBusca = null;

        foreach (scandir($diretorio) as $pasta) {
            $caminhoPasta = $diretorio . $pasta;
            if ($pasta === '.' || $pasta === '..' || !is_dir($caminhoPasta)) {
                continue;
            }

            $pastaNormalizada = $normalizar($pasta);
            if ($pastaNormalizada === $nomeMedico ||
                (empty($medico) && strpos($pastaNormalizada, $nomeMedico) === 0)) {
                $diretorioBusca = rtrim($caminhoPasta, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
                break;
            }
        }

        $pdfs = array();
        if ($diretorioBusca !== null) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($diretorioBusca, FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $arquivoInfo) {
                if (!$arquivoInfo->isFile() || strtolower($arquivoInfo->getExtension()) !== 'pdf') {
                    continue;
                }

                $caminhoArquivo = $arquivoInfo->getPathname();
                $arquivo = $arquivoInfo->getFilename();
                if (!empty($medicoSUS)) {
                    $ociPdf = self::buscarOciDoPdf($arquivo);
                    $ehExecutante = $ociPdf && (string)$ociPdf['id_medico'] === (string)$medicoSUS;
                    $ehAutorizador = $ociPdf &&
                        (string)$ociPdf['id_autorizador'] === (string)$medicoSUS &&
                        self::nivelAutorizadorValido($ociPdf['nivel_executante'], $ociPdf['nivel_autorizador']);
                    if (!$ehExecutante && !$ehAutorizador) {
                        continue;
                    }
                }

                $relativo = ltrim(substr($caminhoArquivo, strlen($diretorio)), DIRECTORY_SEPARATOR);
                $relativoUrl = str_replace(DIRECTORY_SEPARATOR, '/', $relativo);
                $segmentosUrl = array();

                foreach (explode('/', $relativoUrl) as $segmento) {
                    $segmentosUrl[] = rawurlencode($segmento);
                }

                $data = '';
                foreach (explode('/', $relativoUrl) as $parteCaminho) {
                    if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $parteCaminho)) {
                        $data = $parteCaminho;
                        break;
                    }
                }

                if ($data === '') {
                    preg_match('/_(\d{2}-\d{2}-\d{4})/', pathinfo($arquivo, PATHINFO_FILENAME), $partesData);
                    $data = isset($partesData[1]) ? $partesData[1] : '';
                }

                $pastaRelativa = dirname($relativoUrl);
                $pdfs[] = array(
                    'nome' => $arquivo,
                    'url' => URL . 'Impressos/OCI/Assinados/' . implode('/', $segmentosUrl),
                    'pasta' => $pastaRelativa === '.' ? '' : $pastaRelativa,
                    'data' => $data,
                    'modificado' => filemtime($caminhoArquivo)
                );
            }
        }

        // O arquivo final fica na pasta do autorizador, mas deve aparecer no histórico
        // dos dois médicos vinculados à OCI: executante e autorizador.
        if (!empty($medicoSUS)) {
            $stmtOcisConcluidas = Maincontroller::doQuery(
                "SELECT oci.id
                 FROM oci
                 LEFT JOIN servidores executante ON executante.SUS = oci.id_medico
                 LEFT JOIN servidores autorizador ON autorizador.SUS = oci.id_autorizador
                 WHERE COALESCE(oci.assinado, 0) = 1
                   AND (oci.id_medico = :MEDICO_EXECUTANTE
                        OR (oci.id_autorizador = :MEDICO_AUTORIZADOR
                            AND ((COALESCE(executante.oci_autorizador, 0) = 0
                                  AND COALESCE(autorizador.oci_autorizador, 0) IN (1, 2))
                                 OR (COALESCE(executante.oci_autorizador, 0) = 1
                                     AND COALESCE(autorizador.oci_autorizador, 0) = 2))))",
                array(
                    'MEDICO_EXECUTANTE' => $medicoSUS,
                    'MEDICO_AUTORIZADOR' => $medicoSUS
                )
            );
            $idsOcisConcluidas = array();
            if ($stmtOcisConcluidas) {
                while ($linhaOci = $stmtOcisConcluidas->fetch(PDO::FETCH_ASSOC)) {
                    $idsOcisConcluidas[(string)$linhaOci['id']] = true;
                }
            }

            $incluidos = array();
            foreach ($pdfs as $pdf) {
                $incluidos[$pdf['url']] = true;
            }

            if (!empty($idsOcisConcluidas)) {
                $iteratorTodos = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($diretorio, FilesystemIterator::SKIP_DOTS)
                );

                foreach ($iteratorTodos as $arquivoInfo) {
                if (!$arquivoInfo->isFile() || strtolower($arquivoInfo->getExtension()) !== 'pdf') {
                    continue;
                }

                $arquivo = $arquivoInfo->getFilename();
                if (!preg_match('/^(\d+)-/', pathinfo($arquivo, PATHINFO_FILENAME), $partesId) ||
                    !isset($idsOcisConcluidas[(string)$partesId[1]])) {
                    continue;
                }

                $caminhoArquivo = $arquivoInfo->getPathname();
                $relativo = ltrim(substr($caminhoArquivo, strlen($diretorio)), DIRECTORY_SEPARATOR);
                $relativoUrl = str_replace(DIRECTORY_SEPARATOR, '/', $relativo);
                $segmentosUrl = array();
                foreach (explode('/', $relativoUrl) as $segmento) {
                    $segmentosUrl[] = rawurlencode($segmento);
                }

                $url = URL . 'Impressos/OCI/Assinados/' . implode('/', $segmentosUrl);
                if (isset($incluidos[$url])) {
                    continue;
                }

                $data = '';
                foreach (explode('/', $relativoUrl) as $parteCaminho) {
                    if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $parteCaminho)) {
                        $data = $parteCaminho;
                        break;
                    }
                }

                $pastaRelativa = dirname($relativoUrl);
                $pdfs[] = array(
                    'nome' => $arquivo,
                    'url' => $url,
                    'pasta' => $pastaRelativa === '.' ? '' : $pastaRelativa,
                    'data' => $data,
                    'modificado' => filemtime($caminhoArquivo)
                );
                $incluidos[$url] = true;
                }
            }
        }

        usort($pdfs, function($a, $b) {
            return $b['modificado'] <=> $a['modificado'];
        });

        return $pdfs;
     }
     
     public static function delProced($idProced){         
        $sql = "DELETE FROM procedimentos_secundarios WHERE id=:ID";      
        return Maincontroller::doQuery($sql,array('ID'=>$idProced),null,"Erro"); 
    }    
    
    public static function delFila($idFila){  
         $sql = "DELETE FROM oci_fila WHERE id=:ID";      
        return Maincontroller::doQuery($sql,array('ID'=>$idFila),null,"Erro"); 
    }
}
