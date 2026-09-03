<?php

session_start();

if(isset($_SESSION['ubs'])){
$f = new Functions();
$load = new Loads(); 

echo
"<!DOCTYPE html>

<html lang=\"pt-br\">
    <head>
        <meta charset=\"utf-8\">
        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
        <meta name=\"description\" content=\"\">
        <meta name=\"author\" content=\"\">      
        <link rel=\"shortcut icon\" href=\"".FAVICON."\" type=\"image/x-icon\"/>";
                   
        $load->css();           
       
        echo
        "<link rel=\"stylesheet\" href=\"".APP_PATH."css/custom.css\" type=\"text/css\" media=\"all\" />

        <title>AGENDAMENTOS AME - PSF/UBS</title>
      
    </head> 
    <body>"; 
        
//session_start();
$nome = $_SESSION['ubs']['username'];
$nivel = $_SESSION['ubs']['nivel'];
$idUnidade = $_SESSION['ubs']['unidade'];
$unidade = Daoagendas::getUnidades($idUnidade);

echo 

"<input id=\"URL\" type=\"hidden\" value=\"" . URL . "\">

<style>
    body {
        background-color: #e2e2e2;             
    } 
    .h5, h5 {      
        width: max-content;
    }
    .navbar-default {
        background-image: -webkit-linear-gradient(top,#ccc 0,#f8f8f8 100%);
        background-image: -o-linear-gradient(top,#ccc 0,#f8f8f8 100%);
        background-image: -webkit-gradient(linear,left top,left bottom,from(#666),to(#333));
        background-image: linear-gradient(to bottom,#666 0,#333 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#666', endColorstr='#333', GradientType=0);
        filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
        background-repeat: repeat-x;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 0 rgba(255,255,255,.15), 0 1px 5px rgba(0,0,0,.075);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.15), 0 1px 5px rgba(0,0,0,.075);
       
        margin-top: -1px;
}
</style>

<nav class=\"navbar navbar-default\" data-spy=\"affix\">
        <div class=\"container-fluid\">
            <div class=\"navbar-header\">

                <div class=\"col-sm-3 mrg-bottom\" style=\"width:200px;\">

                    <a class=\"navbar-brand\" href=\"" . URL . "Loginubs/ubs\">
                        <div class=\"col-sm-12\">
                            <img id=\"\" alt=\"". APPNAME . "\" src=\"" . ICON . "\" width=\"150\" height=\"40\">
                        </div>

                    </a>
                    
                </div> 
                
            </div> 
            
            <div class=\"form-horizontal\">                                     
                    <span class=\"text-white diminish\">UNIDADE:</span> <span class=\"diminish\" style=\"color:red;margin-right:20px;\">{$unidade[0]['descricao']}</span>                        
                    <span class=\"text-white diminish\">USUÀRIO:</span> 
                        <span class=\"badge badge-pill badge-danger diminish\">" .$nome. "</span>
                            <a style=\"color:#4BB3E5;\" href=\"" . URL . "Loginubs/logoutubs\">
                                SAIR
                            </a>
                    </span>              
              
            </div>            

        </div>        
</nav>


<div id=\"main-ubs\" class=\"container-fluid\" style=\"margin-left:auto;margin-right:auto;width:80%;background-color:#fff;padding-top:30px;\">

<div class=\"col-sm-12 mrg-bottom\">";
                    
if($nivel == 1) {
echo "<div class=\"col-sm-3\">
        <label>Unidades</label>
        {$f->select(Daoagendas ::slctUnidades(), "select mrg-bottom", "slct-unidade-ubs", "slct-unidade-ubs", "\"id\":\"slct-unidade\"", "id", "descricao", "", null, "", "", "",true,"Selecione a Unidade")}                            
    </div>";
    $idUnidade = "slct-unidade-ubs";
}  

echo "<div class=\"col-sm-1\">";
        Functions::selectYears('slct-ano-ubs','slct-ano-ubs');                            
echo "</div>    
    
    <div class=\"col-sm-2\">

        <label>Mês</label>
            <select class=\"select mrg-bottom call-data\" id=\"slct-mes-ubs\" name=\"slct-mes-ubs\" href=\"Agendasame/showVagasUBS\" data-params='{\"idUnidade\":\"{$idUnidade}\",\"mes\":\"slct-mes-ubs\",\"ano\":\"slct-ano-ubs\",\"nivel\":\"{$nivel}\"}' data-redirect=\"load\" data-redirect-target=\"vagas-ubs\">
            <option value=\"\"> MÊS </option>
            <option value=\"1\"> JANEIRO </option>
            <option value=\"2\"> FEVEREIRO </option>
            <option value=\"3\"> MARÇO </option>
            <option value=\"4\"> ABRIL </option>
            <option value=\"5\"> MAIO </option>
            <option value=\"6\"> JUNHO </option>
            <option value=\"7\"> JULHO </option>
            <option value=\"8\"> AGOSTO </option>
            <option value=\"9\"> SETEMBRO </option>
            <option value=\"10\"> OUTUBRO </option>
            <option value=\"11\"> NOVEMBRO </option>
            <option value=\"12\"> DEZEMBRO </option>
        </select>

    </div>
    
</div>
    
    <div clas=\"mrgtop\" id=\"vagas-ubs\" name=\"vagas-ubs\">";
        
echo "</div>

</div>  

<div class=\"loading-modal\" style=\"background: rgba( 51, 51, 51, 0.0 ) url(".URL_ROOT."img/Spin.png) 50% 50% no-repeat;\"></div>
                   
    <footer class=\"footer\">
    </footer>";
        
    $loadjs = new Loads();
    $loadjs->js();       
        
    echo "<script src=\"" . JS_APP . "\" type=\"text/javascript\"></script>           
               
    </body>
    
</html>";
    
} else {
    header("location: ".URL."Loginubs/login"); 
}