<?php

class Paciente {
  
    public $CPF = null;
    public $CNS = null;
    public $Nome = null;
    public $NomeMae = null;
    public $NomePai = null;
    public $MunicipioNascimento = null;
    public $DataNascimento = null;
    public $NomeSocial = null;
}

class CADSUS {

    private function apiCADSUS($cpf) {
        // 1. Limpa qualquer máscara, mantendo apenas números
        $numeroLimpo = preg_replace('/[^\d]/', '', $cpf);

        $nomeMetodo = "";
        $tagParametro = "";
        $urlEndpoint = "";

        if (strlen($numeroLimpo) === 11) {
            $nomeMetodo = "consultarProfissionalPorCpf";
            $tagParametro = "cpf";
            $urlEndpoint = "http://cnescns.datasus.gov.br/cartao/services/consulta/cpf";
        } else {
            //("Documento inválido. Apenas CPF (11 dígitos) é aceito.");
            return null;
        }

        // 4. Monta o XML injetando o método e a tag corretos dinamicamente
        $soapEnvelope = '<?xml version="1.0"?>' . "\n" .
            '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" ' .
            'xmlns:xsd="http://www.w3.org/2001/XMLSchema" ' .
            'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ' .
            'xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/">' . "\n" .
            '<SOAP-ENV:Body SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">' . "\n" .
            "<NS1:{$nomeMetodo} xmlns:NS1=\"http://servicos.cartao.webservice.cnes.datasus.gov.br/\">" . "\n" .
            "<login xsi:type=\"xsd:string\">SCNES.VISUAL</login>" . "\n" .
            "<senha xsi:type=\"xsd:string\">_SCNES#8$25#</senha>" . "\n" .
            "<{$tagParametro} xsi:type=\"xsd:string\">{$numeroLimpo}</{$tagParametro}>" . "\n" .
            "</NS1:{$nomeMetodo}>" . "\n" .
            '</SOAP-ENV:Body>' . "\n" .
            '</SOAP-ENV:Envelope>';

        // 5. Envio da requisição usando cURL
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $urlEndpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $soapEnvelope);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: text/xml; charset=utf-8',
            'User-Agent: Borland SOAP 1.2',
            'Connection: keep-alive',
            'Pragma: no-cache',
            'SOAPAction: ""'
        ]);

        $responseBody = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            error_log('Exceção durante a requisição cURL: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }      
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            $paciente = $this->formatData($responseBody);

            if ($paciente === null) {
                error_log("Nenhum dado de paciente encontrado na resposta.");
            }
         
            return $paciente;
            
        } else {
            error_log("Erro na requisição: {$httpCode}");
            return null;
        }
    }


    private function formatData($xmlSoap) {
        // Captura o conteúdo da tag <return> usando Expressão Regular
        if (!preg_match('/<return>([\s\S]*?)<\/return>/i', $xmlSoap, $matches)) {
            return null;
        }

        // Decodifica entidades HTML
        $xmlInterno = html_entity_decode($matches[1], ENT_QUOTES | ENT_XML1, 'UTF-8');

        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($xmlInterno);        
        $retorno = $doc->retorno;

        if (!$retorno) {
            return null;
        }
        
        $dados = new Paciente();
        
        $dados->CPF                 = (string)$retorno->cpf;
        $dados->CNS                 = (string)$retorno->cns;
        $dados->Nome                = (string)$retorno->nome;
        $dados->NomeMae             = (string)$retorno->nomeMae;
        $dados->NomePai             = (string)$retorno->nomePai;
        $dados->MunicipioNascimento = (string)$retorno->municipioNascimento;
        $dados->DataNascimento      = (string)$retorno->dtNascimento;
        $dados->NomeSocial          = (string)$retorno->nomeSocial;

        return $dados;
    }

  
    public static function consultaCADSUS($cpf) {
        $cadsus = new self();
        return $cadsus->apiCADSUS($cpf);
    }
}