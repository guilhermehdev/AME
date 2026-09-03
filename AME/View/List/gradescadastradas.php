<?php
$f = new Functions();

if($this->getData('grades')) {
    $grade = $this->getData('grades');
} 

if(count($grade) > 0){
$mesExtenso = Functions::monthExtense($grade[0]['mes']);
echo "  
    
<h4 class=\"mrg-bottom\"><span class=\"text-success\">{$mesExtenso}</span> / <span class=\"text-muted\">{$grade[0]['ano']}</span></h4>
<table class=\"table table-hover mrg-bottom\">
    <thead class=\"bg-black\">
        <tr> 
            <th>#</th>           
            <th>Dia</th>
            <th>Limite diário</th>
        </tr>  

    </thead>              

    <tbody>"; 

    foreach ($grade as $v) {
                
        echo 
        "<tr> 
            <td>
                {$f->btnQuestion("btn-danger", "btn-delGradeAME", "btn-delGradeAME", "Agendasame/delGrade", "<span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span>", "\"idEspec\":\"slct-espec\",\"idProf\":\"slct-prof\",\"idDia\":\"{$v['id_dia']}\",\"mes\":\"{$v['mes']}\",\"ano\":\"{$v['ano']}\"", "Excluir Grade", "Excluir Grade?","reload","Agendasame/getGrade","\"idEspec\":\"slct-espec\",\"idProf\":\"slct-prof\",\"mes\":\"slct-mes-grd\",\"ano\":\"slct-ano-grd\"","grades-geradas")}
            </td>           
            <td>{$v['dia']}</td>
            <td>{$v['limite_diario']}</td>
        </tr>"; 
    }
    
    echo "
     
    </tbody>

</table>";

} else {
    echo "<div class=\"alert alert-danger\"> Nenhum registro encontrado...</div>";
}
