<?php
$f = new Functions();
$title = $this->getData('title');
$avisos = Daonotificacoes::getAvisos('');
$hoje = Functions::dateFull();
$idUser = AppController::checkSession()['id'];


if(AppController::checkSession()){    

echo 
"<div class=\"col-sm-12\">
    <div class=\"page-header\">
        <h1>{$title} - <small>{$hoje}</small></h1>
    </div>
            
    <div class=\"col-sm-12\">
        <fieldset class=\"for-panel\">
            <legend class=\"\">Painel de Avisos</legend>

            <div class=\"mrg-top\" id=\"painel\" style=\"font: 16px Tahoma; cursor: default; max-height: 450px; width: 100%; overflow-x: hidden;padding-left: 10px;  padding-right: 10px\">";
        
                foreach ($avisos as $a) {
                    
                    $vermsg = (Daonotificacoes::checknewmessage($a['id'], $idUser));
                    if($vermsg[0]['n'] == 0 && $a['iduser'] != $idUser) {
                        
                        $user = "<b>".$a['usuario']."</b>";
                        $new = "<a id=\"link-confirm-new-message-{$a['id']}\" data-id=\"{$a['id']}\" name=\"link-confirm-new-message\" data-params='{\"idmsg\":\"{$a['id']}\",\"iduser\":\"{$idUser}\"}'><b><span class=\"label label-success\"><span style=\"color:#FFFF00;\" class=\"glyphicon glyphicon-star\"></span><span class=\"\"> Nova mensagem &nbsp;<span style=\"color:#FFFF00;\" class=\"glyphicon glyphicon-ok\"></span></span></span></b></a>";

                    } else {
                        if($a['iduser'] != $idUser){ 
                            $new = "<span class=\"label label-default\"><i>Lida</i> <span id=\"message-checked\" class=\"glyphicon glyphicon-ok\"></span></span>";
                        } else {                            
                            $new = "<span class=\"label label-orange\"><i>Você</i></span>";
                        } 
                        $user = "<b><span class=\"\">{$a['usuario']}</span></b>";
                    }
                                                            
                  echo "<div class=\"mrg-bottom\">{$user} - 
                            <i>
                                <span class=\"text-muted\">Postou em: </span>
                                <span class=\"text-primary\"><b>".$f->BRfullDateTime($a['data'])."</b></span> -->
                                <span id=\"container-message-{$a['id']}\">"; 
                                    echo $new;
                           echo "</span>
                               
                            </i>
                            
                        </div>
                        
                        <span>".preg_replace("/{bar}/",'/',$a['texto'])."</span><br><hr class=\"hr-default mrg-bottom mrg-top\"><br>";
                }           
               
      echo "</div>

        </fieldset>

    </div>
            
</div>";
      
} else {
    header("location: ".URL."Loginadm/login");
}