<?php

/**
 * Configurações gerais do sistema
 *testando.......
 * @author Guilherme
 */
class Config {

    private static $instance = null;
    private $confs;

    private function __construct() {
        
        $arr = "AME";
                                                            
        $this->confs = $conf = array(
            
        'APPNAME' => "AME - PERUIBE",
        
        'APP_IMG' => "https://" . $_SERVER['SERVER_NAME'] . "/Gerenciador/".$arr."/img/",
        'APP_PATH' => "https://" . $_SERVER['SERVER_NAME'] . "/Gerenciador/".$arr."/",     
        'APP_VIEW' => "../".$arr."/View/",
        'APP_VIEW_LIST' => "../".$arr."/View/List/",
        'APP_VIEW_MODAL' => "../".$arr."/View/Modal/",
        'APP_REPORT' => "../".$arr."/Reports/",
        'CSS_PATH' => $_SERVER['DOCUMENT_ROOT']."/Gerenciador/Bin/css/",
        'FAVICON' => "https://" . $_SERVER['SERVER_NAME'] . "/Gerenciador/".$arr."/img/favicon.png",
        'GUI_PATH' => $_SERVER['DOCUMENT_ROOT'] . "/Gerenciador/".$arr."/Gui/",
        'ICON' => "https://" . $_SERVER['SERVER_NAME'] . "/Gerenciador/".$arr."/img/".$arr.".png",
        'JS_PATH' => $_SERVER['DOCUMENT_ROOT']."/Gerenciador/Bin/js/",
        'JS_APP' => "https://" . $_SERVER['SERVER_NAME'] . "/Gerenciador/".$arr."/js/app.js",
        'URL' => "https://" . $_SERVER['SERVER_NAME'] . "/Gerenciador/".$arr."/",
        'URL_ROOT' => "https://" . $_SERVER['SERVER_NAME'] . "/Gerenciador/Bin/",
        'XML_PATH' => "https://" . $_SERVER['SERVER_NAME'] . "/Gerenciador/".$arr."/xml/", 	
        'OCI_FILA_ENTRADA' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'FilaOCI' . DIRECTORY_SEPARATOR . 'Entrada',
        'OCI_FILA_CONCLUIDOS' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Impressos' . DIRECTORY_SEPARATOR . 'OCI' . DIRECTORY_SEPARATOR . 'Gerados',
        'OCI_FILA_ERROS' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'FilaOCI' . DIRECTORY_SEPARATOR . 'Erros',
        'HOST' => "localhost",
        'PORT' => 3306,
        'DB_NAME' => "ame",
        'USER_NAME' => "ame",
        'PASSWORD' => "Ame*12345",
	    
        'DEFAULT_CONTROLLER' => "AppController",
        'DEFAULT_METHOD' => "index",

        // Configurações de envio de e-mail (preencher com as credenciais reais)
        'SMTP_HOST' => "smtp.gmail.com",
        'SMTP_PORT' => 587,
        'SMTP_SECURE' => "tls",
        'SMTP_USER' => "amedeperuibe@gmail.com",
        'SMTP_PASS' => "erqphplcoyfvstlm",
        'MAIL_FROM' => "amedeperuibe@gmail.com",
        'MAIL_FROM_NAME' => "AME - Peruíbe"
                         
        ); 
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Config();
        }
        return self::$instance;
    }

    function getConfs($name = null) {
        if ($name && isset($this->confs[$name])) {
            return $this->confs[$name];
        }
        return $this->confs;
    }

    function setConfs($confs, $merge = false) {
        if ($merge) {
            $this->confs = array_merge($confs, $this->confs);
        } else {
            $this->confs = $confs;
        }
    }            
    
}
