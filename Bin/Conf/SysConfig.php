<?php

/**
 * Description of Config class
 *
 * @author abarbosa
 */
class SysConfig {

    private static $instance = null;
    private $confs;

    private function __construct() {
                                                                          
        $this->confs = $conf = array(        
        'URL_ROOT' => "http://" . $_SERVER['SERVER_NAME'] . "/Gerenciador/Bin/",         
        'APPNAME' => "GERENCIADOR", 
        'IMG_ROOT' => "http://" . $_SERVER['SERVER_NAME'] . "/Gerenciador/Bin/img/",      
        'FAVICON' => "http://" . $_SERVER['SERVER_NAME'] . "/Gerenciador/favicon.png",        
        'CSS_PATH' => $_SERVER['DOCUMENT_ROOT']."/Gerenciador/Bin/css/",
        'JS_PATH' => $_SERVER['DOCUMENT_ROOT']."/Gerenciador/Bin/js/",                   
        'DEFAULT_CONTROLLER' => "maincontroller",
        'DEFAULT_METHOD' => "index",
            
        );                
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new SysConfig();
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
