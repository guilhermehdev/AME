<?php

header('Content-Type: text/html; charset=utf-8');
ini_set('display_errors', 1);
ini_set('log_errors', 1);
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

error_reporting(E_ALL);

function autoload($c){ 
            
$dir = array(    
   
    '../Bin/Dao/',    
    '../Bin/Libs/',     
    '../Bin/Model/',
    '../Bin/To/',

    'Conf/',
    'Controller/',
    'Dao/',
    'Model/',
    'Reports/'

    ); 
       
    foreach ($dir as $k){
        if(file_exists($k . $c . '.php')){
            require_once $k . $c . '.php'; 
			//echo $k . $c . '.php <br>';
        }
    }    
}

spl_autoload_register('autoload');

foreach (Config::getInstance()->getConfs() as $nome => $valor) {
    define($nome, $valor);
}  

$app = new TApp();
try {    
    $app->execute(false);
} catch (Exception $exc) {
    $erro = new Errorcontroller($exc);
}