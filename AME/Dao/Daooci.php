<?php
/**
 * Description of Daooci
 *
 * @author Gui
 */
class Daooci {

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
            if ($pdf['nome'] === $nomeArquivo && $pastaPdf === $pastaNormalizada) {
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
        $sql = "SELECT servidores.cbo, servidores.SUS
                FROM servidores
                LEFT JOIN usuarios ON usuarios.CPF = servidores.CPF
                WHERE servidores.id_usuario = :ID_USUARIO
                   OR usuarios.id = :ID_USUARIO
                LIMIT 1";
        $ds = Maincontroller::doQuery($sql, array('ID_USUARIO' => $idUser));

        if (!$ds || $ds->rowCount() === 0) {
            $sql = "SELECT servidores.cbo, servidores.SUS
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

        if (!empty($medico)) {
            $stmt = Maincontroller::doQuery(
                "SELECT nome FROM servidores WHERE SUS = :MEDICO LIMIT 1",
                array('MEDICO' => $medico)
            );
        } elseif (!empty($idUsuario)) {
            $stmt = Maincontroller::doQuery(
                "SELECT servidores.nome, servidores.SUS
                 FROM servidores
                 LEFT JOIN usuarios ON usuarios.CPF = servidores.CPF
                 WHERE servidores.id_usuario = :ID_USUARIO
                    OR usuarios.id = :ID_USUARIO
                 LIMIT 1",
                array('ID_USUARIO' => $idUsuario)
            );
        } else {
            return array();
        }

        if ($stmt && ($servidor = $stmt->fetch(PDO::FETCH_ASSOC))) {
            $nomeMedico = $servidor['nome'];
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
        if (!is_dir($diretorio)) {
            return null;
        }

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

        $adicionarPdf = function($caminhoArquivo, $raiz, $exigirPrefixo) use (&$pdfs, $normalizar, $nomeMedico, $medico, $nomeUsuario, $possuiAssinatura) {
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
                'modificado' => filemtime($caminhoArquivo)
            );
        };

        if ($diretorioBusca !== null) {
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
        if (empty($pdfs)) {
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

        usort($pdfs, function($a, $b) {
            return $b['modificado'] <=> $a['modificado'];
        });

        return $pdfs;
     }

     public static function getPdfsAssinados($medico = '', $idUsuario = null, $nomeUsuario = '') {
        $nomeMedico = '';

        if (!empty($medico)) {
            $stmt = Maincontroller::doQuery(
                "SELECT nome FROM servidores WHERE SUS = :MEDICO LIMIT 1",
                array('MEDICO' => $medico)
            );
        } elseif (!empty($idUsuario)) {
            $stmt = Maincontroller::doQuery(
                "SELECT servidores.nome, servidores.SUS
                 FROM servidores
                 LEFT JOIN usuarios ON usuarios.CPF = servidores.CPF
                 WHERE servidores.id_usuario = :ID_USUARIO
                    OR usuarios.id = :ID_USUARIO
                 LIMIT 1",
                array('ID_USUARIO' => $idUsuario)
            );
        } else {
            return array();
        }

        if ($stmt && ($servidor = $stmt->fetch(PDO::FETCH_ASSOC))) {
            $nomeMedico = $servidor['nome'];
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

        if ($diretorioBusca === null) {
            return array();
        }

        $pdfs = array();
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($diretorioBusca, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $arquivoInfo) {
            if (!$arquivoInfo->isFile() || strtolower($arquivoInfo->getExtension()) !== 'pdf') {
                continue;
            }

            $caminhoArquivo = $arquivoInfo->getPathname();
            $arquivo = $arquivoInfo->getFilename();
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
