<?php
$f = new Functions();

if($this->getData('retornos')) {
    $retorno = $this->getData('retornos');
}
if($this->getData('oci')) {
    $oci = $this->getData('oci');
}

if(count($retorno) > 0) {
echo   
"<table class=\"table table-hover mrg-bottom\">
    <thead class=\"\">    
        <tr>           
            <th>Data da consulta</th>
            <th>Médico</th>                        
            <th>Responsável</th>  
            <th>Desfecho</th>            
            <th>Tempo de retorno</th> 
            <th>Data do retorno</th> 
            <th>USAFA</th> 
        </tr>
    </thead>              

    <tbody>";

    foreach ($retorno as $r) {        
        $dsf = $r['desfecho'] == 0 ? "ALTA PARA UBS" : "RETORNO";
        $dr = $r['data_retorno'] == null ? "" : $f->BRdateFormat($r['data_retorno']);
        
        echo 
        "<tr style='border-bottom:1px solid #ccc'>             
                <td>{$f->BRdateFormat($r['data_consulta'])}</td>
                <td>{$r['medico']}</td>            
                <td>{$r['responsavel']}</td>
                <td>{$dsf}</td>
                <td>{$r['tempo_retorno']}</td>
                <td>{$dr}</td>
                <td>{$r['USAFA']}</td>
         </tr>"; 
    }
    
      foreach ($oci as $o) {
          echo 
            "<tr style='border-bottom:1px solid #ccc'>    
                <td>{$f->BRdateFormat($o['ociData'])}</td>      
                <td>{$o['medico']}</td>
                <td>-----</td>
                <td>{$o['oci']}</td>     
                <td>-----</td>
                <td>-----</td>
                <td>-----</td>    
            </tr>"; 
      }  
    
    echo "
     
    </tbody>

</table>";
} else {
    echo "<b class=\"text-default mrg-left\">Nenhum registro encontrado!</b>";
}