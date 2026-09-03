
<script  type="text/javascript">
    //$('.inp-data-retorno').mask('99/99/9999',{placeholder:""});
    $('.inp-data-retorno').datepicker({
            format: 'dd/mm/yyyy',
            todayHighlight: true, 
            autoclose: true,
            showOnFocus: true,
            language: "pt-BR",
            orientation: "auto right",
            clearBtn: true,
            todayBtn: "linked",
           // daysOfWeekDisabled: "0,6",
            daysOfWeekHighlighted: "0,6"
        });
        
        $('.inp-data-retorno').on('changeDate', function() { 
            var href = GLOBAL_URL+$(this).attr('href')+'/';
            var params = $(this).data('params');
            var posturl = GLOBAL_URL+$(this).data('post-url')+'/';
            var postparams = $(this).data('post-params');             

             $.ajax({       
                 type: "GET",
                 url: href+eachParams(params) ,   
                 success: function (result) { 
                     $('#container-pendentes').load(posturl+eachParams(postparams) , function(){
                         $('#container-pendentes').fadeIn(500);
                     });
                 }
                 }).done(function() {
                     callModal(null,'sm-dialog','Mensagem',3,0,'Salvo com sucesso!','', false,'');
                 });          
        });
        
        $('.inp-data-retorno').on('focusin', function() { 
            var id = $(this).attr('id');
            var obj  = document.getElementById(id);
            var vl = $(obj).val();

            obj.addEventListener('blur', function(){
                $(obj).val(vl);
            });
        });
               
</script>

<?php

$f = new Functions();
$alertas = $this->getData('alertas');
$mes = $this->getData('mes');
$ano = $this->getData('ano');
$n = count($alertas);

if($n > 0){
    
echo 
"<fieldset class=\"for-panel\">
    <legend><span class=\"text-primary\">Resultados para retornos em </span>{$f->monthExtense($mes)}<span class=\"text-muted\">/</span>{$ano}<span class=\"text-muted\"> - </span><i><span class=\"text-warning\"> {$n} </span><span class=\"text-muted\"><small>encontrados</small></span></i></legend> 

        <table class=\"table table-hover mrg-bottom\">
            <thead class=\"\">
                <tr> 
                    <th style=\"width:40px;\">Conclusão</th>
                    <th style=\"width:180px;\">Paciente</th>
                    <th style=\"width:60px;\">Nascimento</th>
                    <th style=\"width:100px;\">Tel</th>
                    <th style=\"width:60px;\">Data 1ª consulta</th> 
                    <th style=\"width:60px;\">Retorno em</th> 
                    <th style=\"width:60px;\">Data retorno</th>
                    <th style=\"width:160px;\">Obs</th>
                   
                </tr>

            </thead>              

            <tbody>";

 foreach ($alertas as $a) { 
     $c = $a['conclusao'] == 0 ? "<span class=\"text-danger font-bold\">PENDENTE</span>" : "<span class=\"text-success font-bold\">AGENDADO</span>";
     if($a['data_retorno'] == null) {
         $clr = "#999";
         $cls = "font-italic";
         $drn = "Clique para selecionar";
     } else {
         $clr = "#337ab7";
         $cls = "font-bold";
         $drn = Functions::BRdateFormat($a['data_retorno']);
     }
 
     $dataretorno = "<input type=\"text\" class=\"form-control calendar inp-data-retorno {$cls}\" style=\"color:{$clr};font-size:15px;\" value=\"{$drn}\"  id=\"inp-data-retorno-{$a['id']}\" name=\"inp-data-retorno-{$a['id']}\" href=\"Retornos/updateDataRetorno\" data-params='{\"id\":\"{$a['id']}\",\"dtretorno\":\"inp-data-retorno-{$a['id']}\"}'  data-post-url=\"Retornos/getAlerts\" data-post-params='{\"mes\":\"slct-mes-pendente\",\"ano\":\"slct-ano-pendente\",\"idservidor\":\"slct-prof-pendente\"}'>";   
     
     $obs = "<div class=\"input-group\">"
             
                    . "<input type=\"text\" class=\"form-control\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"{$a['obs']}\" value=\"{$a['obs']}\"  id=\"inp-retorno-obs-{$a['id']}\" name=\"inp-retorno-obs-{$a['id']}\">"
                        
                        . "<span class=\"input-group-btn\">
                 
                                <button id=\"btn-update-obs-{$a['id']}\" name=\"btn-update-obs-{$a['id']}\" class=\"btn btn-success call-data btn-update-obs\" type=\"button\" href=\"Retornos/updateObs\" data-params='{\"id\":\"{$a['id']}\",\"obs\":\"inp-retorno-obs-{$a['id']}\"}' data-redirect=\"load\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Salvar Observação\"  data-post-url=\"Retornos/getAlerts\" data-post-params='{\"mes\":\"slct-mes-pendente\",\"ano\":\"slct-ano-pendente\",\"idservidor\":\"slct-prof-pendente\"}'>
                                
                                    <span class=\"glyphicon glyphicon-floppy-disk\" aria-hidden=\"true\"></span>
                                
                                </button>
                            </span>
                    </div>"; 
     
         echo 
                "<tr>        
                    <td>{$c}</td> 
                    <td>{$a['paciente']}</td>
                    <td>{$f->BRdateFormat($a['nascimento'])}</td>                    
                    <td>{$a['tel']}</td>
                    <td>{$f->BRdateFormat($a['consulta'])}</td>  
                    <td>{$a['retorno_em']}</td>     
                    <td>{$dataretorno}</td> 
                    <td>{$obs}</td> 
                </tr>"; 
     
 }
    
} else {
    echo "<b class=\"text-danger mrg-left\">Nenhum registro encontrado!</b>";
}         

//Loads::js();