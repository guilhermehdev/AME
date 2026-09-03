<?php

header('Content-Type: text/html; charset=utf-8');
//header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
ini_set('display_errors', 1);
ini_set('log_errors', 1);
date_default_timezone_set('America/Sao_Paulo');
//ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
//error_reporting(E_ALL);

function Myautoload($c){
    $rootPath = $_SERVER['DOCUMENT_ROOT'] .'/Gerenciador/';	
            
    $dir = array(
        $rootPath. 'Bin/Conf/',
        $rootPath. 'Bin/Dao/',
        $rootPath. 'Bin/Gui/',
        $rootPath. 'Bin/Libs/',   
        $rootPath. 'Bin/Model/',
        $rootPath. 'Bin/To/'
    ); 
       
    foreach ($dir as $k){
        if(file_exists($k . $c . '.php')){
            include_once $k . $c . '.php'; 
				
			//echo $k . $c . '.php BIN <br>';
        }
    } 
}

spl_autoload_register('Myautoload');

foreach (SysConfig::getInstance()->getConfs() as $nome => $valor) {
    define($nome, $valor);
}


$checkDir = new DirectoryIterator('.');
$arrApps = array();

foreach ($checkDir as $value) {
    if ($value->isDir() && $value != '.' && $value != '..' && $value != 'Bin' && $value != 'nbproject') {
        $arrApps[] = $value->getFilename();
    }
    
} 

echo "
<!DOCTYPE html>
<html lang=\"pt-br\">
    <head>
        <meta charset=\"utf-8\">
        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
        <meta name=\"description\" content=\"\">
        <meta name=\"author\" content=\"Guilherme\">
        <link rel=\"shortcut icon\" href=\"".FAVICON."\" type=\"image/x-icon\" />";
        
        $css = new Loads();
        $css->css();
        
  echo "<title>".APPNAME."</title>
    </head>  
    
    <body> 

        <div class=\"container\">
                <div class=\"page-header\">
                    <h2>Aplicativos</h2>
                </div>";
  
         echo "<div>";

        for ($i = 0;$i < count($arrApps); $i++) {
          echo 
                "<a name=\"btn-chooseApp\" id=\"btn-chooseApp\" class=\"btn btn-default mrg-right call-data\" href=\"". $arrApps[$i]."\"/>" . "<img class=\"img-selection-app\" src=\"". $arrApps[$i] ."/img/".$arrApps[$i].".png\"><br><span class=\"text-muted\">" . $arrApps[$i] . "</span></a>";
        }
        
        $js = new Loads();
        $js->js();

        echo "</div> 
            
        </div>
    </body>
</html>";