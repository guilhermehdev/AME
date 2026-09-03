<?php

$arquivo = APP_PATH. "/Impressos/OCI/";

echo 
"<div class=\"col-sm-12\">
    <div class=\"page-header\">
        <h2><small>Impressos > OCI ></small> APACs</h2>
    </div>
    
    <div class=\"col-sm-12 font14\">          
            <a href=\"{$arquivo}OCIOftalmologia.pdf\"  target=\"_blank\" ><span class=\"glyphicon glyphicon-new-window\" aria-hidden=\"true\"></span> OCI Oftalmologia</a><br>
            <a href=\"{$arquivo}OCICardiologia.pdf\"  target=\"_blank\" ><span class=\"glyphicon glyphicon-new-window\" aria-hidden=\"true\"></span> OCI Cardiologia</a><br>
            <a href=\"{$arquivo}OCIOrtopedia.pdf\"  target=\"_blank\" ><span class=\"glyphicon glyphicon-new-window\" aria-hidden=\"true\"></span> OCI Ortopedia</a><br>
            <a href=\"{$arquivo}OCIAudiometria.pdf\"  target=\"_blank\" ><span class=\"glyphicon glyphicon-new-window\" aria-hidden=\"true\"></span> OCI Audiometria</a><br>
            <a href=\"{$arquivo}OCIRisco_cirurgico.pdf\"  target=\"_blank\" ><span class=\"glyphicon glyphicon-new-window\" aria-hidden=\"true\"></span> OCI Risco cirurgico</a><br>
            <a href=\"{$arquivo}OCIOtorrino.pdf\"  target=\"_blank\" ><span class=\"glyphicon glyphicon-new-window\" aria-hidden=\"true\"></span> OCI Videolaringoscopia</a><br>
    </div>
     

</div>";