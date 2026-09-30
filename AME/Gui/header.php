<!DOCTYPE html>

<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" href="<?php echo FAVICON;?>" type="image/x-icon"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css"> 
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        
        <?php $load = new Loads();    
            $load->css();           
        ?>
        <link rel="stylesheet" href="<?php echo APP_PATH."css/custom.css";?>" type="text/css" media="all" />     

        <title><?php echo APPNAME; ?></title>
                      
    </head>
    
<?php
$sessaoAtual = AppController::checkSession();
$nomeUsuarioAtual = isset($sessaoAtual['username']) ? $sessaoAtual['username'] : '';
$idUsuarioAtual = isset($sessaoAtual['id']) ? $sessaoAtual['id'] : '';
$temaEscuroAtivo = !empty($sessaoAtual['darkTheme']);
$classeTema = $temaEscuroAtivo ? 'theme-dark' : '';
$rotuloTema = $temaEscuroAtivo ? 'Tema claro' : 'Tema escuro';
$iconeTema = $temaEscuroAtivo ? 'glyphicon-sunglasses' : 'glyphicon-adjust';
$usuarioExibicao = $nomeUsuarioAtual !== '' ? $nomeUsuarioAtual . ', Sair' : '';
?>

    <body class="<?php echo $classeTema; ?>" data-dark-theme="<?php echo $temaEscuroAtivo ? '1' : '0'; ?>">

<?php
echo "

<nav class=\"navbar navbar-default\" data-spy=\"affix\">
    <div class=\"container-fluid\">
        <div class=\"navbar-header\">               
            <a class=\"navbar-brand\" href=\"" . URL . "\">
                <div class=\"col-sm-12\">
                    <img id=\"appIcon\" alt=\"". APPNAME . "\" src=\"" . ICON . "\" width=\"150\" height=\"50\">

                </div>
            </a>             
        </div>
        
        <div class=\"nav navbar-nav navbar-right mrg-bottom\" style=\"padding-right:15px;padding-left:15px;margin-top:14px;\">
            <span class=\"label label-danger\"></span>
            <button type=\"button\" id=\"btn-toggle-dark-theme\" class=\"btn btn-link theme-toggle\" title=\"{$rotuloTema}\" aria-label=\"{$rotuloTema}\" data-dark-theme=\"{$temaEscuroAtivo}\">
                <span class=\"glyphicon {$iconeTema}\" aria-hidden=\"true\"></span>
                <span class=\"theme-toggle-label\">{$rotuloTema}</span>
            </button>
            <a style=\"color:#fff;\" href=\"".URL."Loginadm/logoutadm/{$idUsuarioAtual}\">".
                $usuarioExibicao
          ."</a>
        </div>

    </div>        
</nav>";

$ac = new AppController(); 
$f = new Functions();

echo 
"
<div class=\"container-fluid\">
    <input id=\"URL\" type=\"hidden\" value=\"" . URL . "\">
        <div class=\"row\">    
        
            <div id=\"sidebar\" class=\"col-md-2 sidebar collapse in width\">

                <div class=\"panel-group\" id=\"accordion\"> 
                        
                {$ac->menuAdm()}              
                
            </div>
        </div>
            <div class=\"container-fluid col-md-offset-2\" id=\"main\" name=\"main\">";                
