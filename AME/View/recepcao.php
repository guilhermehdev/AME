<?php

$arquivo = APP_PATH. "/Impressos/Recepcao/";

echo 
"<div class=\"col-sm-12\">
    <div class=\"page-header\">
        <h2><small>Impressos > Recepção</h2>
    </div>
    
    <div class=\"col-sm-12 font14\">          
            <a href=\"{$arquivo}Fisioterapia.pdf\"  target=\"_blank\" ><span class=\"glyphicon glyphicon-new-window\" aria-hidden=\"true\"></span> Encaminhamento fisioterapia</a><br>   
            <a href=\"{$arquivo}Carteirinha.pdf\"  target=\"_blank\" ><span class=\"glyphicon glyphicon-new-window\" aria-hidden=\"true\"></span> Carteirinha retornos</a><br> 
            <a href=\"{$arquivo}Mamografia.pdf\"  target=\"_blank\" ><span class=\"glyphicon glyphicon-new-window\" aria-hidden=\"true\"></span> Formulário mamografia</a><br> 
             <a href=\"{$arquivo}TermoDermato.pdf\"  target=\"_blank\" ><span class=\"glyphicon glyphicon-new-window\" aria-hidden=\"true\"></span> Termo de consentimento Dermato</a><br> 
             <a href=\"{$arquivo}Ressonancia.pdf\"  target=\"_blank\" ><span class=\"glyphicon glyphicon-new-window\" aria-hidden=\"true\"></span> Ressonância magnética</a><br> 
             <a href=\"{$arquivo}InterconsultaFono.pdf\"  target=\"_blank\" ><span class=\"glyphicon glyphicon-new-window\" aria-hidden=\"true\"></span> Ficha de intersonsulta Fonoaudiologia</a><br> 
             <a href=\"{$arquivo}TermoRiscosIdadeFertil.pdf\"  target=\"_blank\" ><span class=\"glyphicon glyphicon-new-window\" aria-hidden=\"true\"></span> Termo de conhecimento para mulheres em idade fértil</a><br> 
    </div>
     

</div>";