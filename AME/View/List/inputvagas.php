<?php
$f = new Functions();
$idInp = 0;
$arrLimite = array();

if($this->getData('grades')) {
    $grade = $this->getData('grades');  
    
    $espec = $grade[0]['especialidade'];
    $prof = $grade[0]['nome'];
    $idEspec = $grade[0]['id_espec'];
    $idProf = $grade[0]['id_servidor'];
    $mes = $this->getData('mes') + 1;
    $ano = $this->getData('ano');
       
}

if(count($grade) > 0){

$unidades = Daoagendas::getUnidades();

$utseg = Daoagendas::getVagasUtilizadasDia($idEspec, $idProf, 2, $mes, $ano)[0]['vgut'] == null ? 0 : Daoagendas::getVagasUtilizadasDia($idEspec, $idProf, 2, $mes, $ano)[0]['vgut'];
$utter = Daoagendas::getVagasUtilizadasDia($idEspec, $idProf, 3, $mes, $ano)[0]['vgut'] == null ? 0 : Daoagendas::getVagasUtilizadasDia($idEspec, $idProf, 3, $mes, $ano)[0]['vgut'];
$utqua = Daoagendas::getVagasUtilizadasDia($idEspec, $idProf, 4, $mes, $ano)[0]['vgut'] == null ? 0 : Daoagendas::getVagasUtilizadasDia($idEspec, $idProf, 4, $mes, $ano)[0]['vgut'];
$utqui = Daoagendas::getVagasUtilizadasDia($idEspec, $idProf, 5, $mes, $ano)[0]['vgut'] == null ? 0 : Daoagendas::getVagasUtilizadasDia($idEspec, $idProf, 5, $mes, $ano)[0]['vgut'];
$utsex = Daoagendas::getVagasUtilizadasDia($idEspec, $idProf, 6, $mes, $ano)[0]['vgut'] == null ? 0 : Daoagendas::getVagasUtilizadasDia($idEspec, $idProf, 6, $mes, $ano)[0]['vgut'];

$limseg = Daoagendas::getVagasLimitesDia($idEspec, $idProf, 2, $mes, $ano)[0]['lim'] == null ? 0 : Daoagendas::getVagasLimitesDia($idEspec, $idProf, 2, $mes, $ano)[0]['lim'];
$limter = Daoagendas::getVagasLimitesDia($idEspec, $idProf, 3, $mes, $ano)[0]['lim'] == null ? 0 : Daoagendas::getVagasLimitesDia($idEspec, $idProf, 3, $mes, $ano)[0]['lim'];
$limqua = Daoagendas::getVagasLimitesDia($idEspec, $idProf, 4, $mes, $ano)[0]['lim'] == null ? 0 : Daoagendas::getVagasLimitesDia($idEspec, $idProf, 4, $mes, $ano)[0]['lim'];
$limqui = Daoagendas::getVagasLimitesDia($idEspec, $idProf, 5, $mes, $ano)[0]['lim'] == null ? 0 : Daoagendas::getVagasLimitesDia($idEspec, $idProf, 5, $mes, $ano)[0]['lim'];
$limsex = Daoagendas::getVagasLimitesDia($idEspec, $idProf, 6, $mes, $ano)[0]['lim'] == null ? 0 : Daoagendas::getVagasLimitesDia($idEspec, $idProf, 6, $mes, $ano)[0]['lim'];

echo 

"<div class=\"col-sm-10 mrg-bottom\"> 
        <h4>  
            <div class=\"col-sm-2\"> LIMITE DIÁRIO </div>
            
            <div class=\"col-sm-2\">
                <span class=\"text-muted\">SEGUNDA </span><br>
                <span class=\"text-primary\" id=\"seg\">{$utseg}</span>
                <span class=\"text-muted\" id=\"seg\">/</span>
                <span class=\"text-success\" id=\"limiteseg\">{$limseg}</span>
            </div>
            <div class=\"col-sm-2\">
                <span class=\"text-muted\">TERÇA </span><br>
                <span class=\"text-primary\" id=\"ter\">{$utter}</span>
                <span class=\"text-muted\" id=\"seg\">/</span>
                <span class=\"text-success\" id=\"limiteter\">{$limter}</span>
            </div>
            <div class=\"col-sm-2\">
                <span class=\"text-muted\">QUARTA </span><br>
                <span class=\"text-primary\" id=\"qua\">{$utqua}</span>
                <span class=\"text-muted\" id=\"seg\">/</span>
                <span class=\"text-success\" id=\"limitequa\">{$limqua}</span>
            </div>
            <div class=\"col-sm-2\">
                <span class=\"text-muted\">QUINTA </span><br>
                <span class=\"text-primary\" id=\"qui\">{$utqui}</span>
                <span class=\"text-muted\" id=\"seg\">/</span>
                <span class=\"text-success\" id=\"limitequi\">{$limqui}</span>
            </div>
            <div class=\"col-sm-2\">
                <span class=\"text-muted\">SEXTA </span><br>
                <span class=\"text-primary\" id=\"sex\">{$utsex}</span>
                <span class=\"text-muted\" id=\"seg\">/</span>
                <span class=\"text-success\" id=\"limitesex\">{$limsex}</span>
            </div>
        </h4>
        
    </div>
    
    <div class=\"col-sm-12 mrg-bottom\"><hr></div>
    
    <form name=\"frm-dist\" id=\"frm-dist\" method=\"POST\" action=\"" . URL . "distribuidor/save\"> "; 
    
foreach ($unidades as $u) {
            
echo 
    "<div class=\"col-sm-3\">
        
        <table class=\"table table-bordered mrg-bottom\">
            <thead>

            </thead>              

            <tbody>

                <tr style=\"background:#337ab7;\">
                    <td colspan=\"7\">
                        <span class=\"\" style=\"color:#fff;\"><b>{$u['descricao']}</b></span>
                    </td>
                </tr>
                <tr>";

                $vagas = Daoagendas::getDist($u['id'], $idEspec, $idProf,$mes,$ano);
                $idInp ++;

                $nvagas = array();
                $totalMes = array();
                $idDia = array();
                $arrNvagas = array();                    

                foreach ($vagas as $n) {                        
                    $totalMes[] = $n['total_mes'];                       
                    $arrNvagas[$n['id_dia']] = $n['nvagas_dia'];                       
                }

                foreach ($grade as $g) {                                                                       

                    if($g['id_dia'] == 1){
                        $dia = "dom";
                        $nvaga = $arrNvagas[1]; 

                    }
                    if($g['id_dia'] == 2){
                        $dia = "seg";
                        $nvaga = $arrNvagas[2]; 

                    }
                    if($g['id_dia'] == 3){
                        $dia = "ter";
                        $nvaga = $arrNvagas[3];

                    }
                    if($g['id_dia'] == 4){
                        $dia = "qua";
                        $nvaga = $arrNvagas[4];

                    }
                    if($g['id_dia'] == 5){
                        $dia = "qui";
                        $nvaga = $arrNvagas[5]; 

                    }
                    if($g['id_dia'] == 6){
                        $dia = "sex";
                        $nvaga = $arrNvagas[6];

                    }
                    if($g['id_dia'] == 7){
                        $dia = "sab"; 
                        $nvaga = $arrNvagas[7];

                    }
                                                            
                    if ($totalMes[0] == '') {
                        $totmes = 0;
                    } else {
                        $totmes = $totalMes[0];
                    }
                    
                    if($nvaga == null){
                        $nvaga = 0;
                    } else {
                        $nvaga = $nvaga;
                    }

                    echo 

                    "<td>

                        <span class=\"text-primary\" style=\"font-size:12px;\">{$g['dia']}</span>

                        <input class=\"{$dia}\" style=\"font-size:14px;width:34px;font-weight:bold;color:red;\" data-mes=\"slct-mes-dist\" data-ano=\"slct-ano-dist\" data-limite=\"{$g['limite_diario']}\" data-uniqueid=\"{$idInp}\" data-idunidade=\"{$u['id']}\" data-idespec=\"{$g['id_espec']}\" data-idprof=\"{$g['id_servidor']}\" data-iddia=\"{$g['id_dia']}\" type=\"number\" id=\"vagas\" name=\"vagas\" min=\"0\" value=\"{$nvaga}\" onchange=\"\">

                    </td>"; 
                }

           echo "</tr>

                <tr>
                    <td colspan=\"7\"> 
                        <input type=\"hidden\" id=\"idunidade-{$u['id']}\" name=\"idunidade\" value=\"{$u['id']}\">

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class=\"text-muted\">Total mensal:</span> <span class=\"text-orange\" id=\"total-{$u['id']}\" name=\"total-{$u['id']}\" style=\"font-weight:bold;\">{$totmes}</span>

                    </td>
                </tr>

            </tbody>

        </table>
        
    </div>"; 
                            
        }
        
    echo "<div class=\"col-sm-12 mrg-bottom\"><hr>
            <button id=\"btn-salvar-distribuido\" name=\"btn-salvar-distribuido\" class=\"btn btn-success\" type=\"button\">
                Salvar Todos
            </button>
        </div>
        
    </form>";
        
} else {
    echo "<div class=\"alert alert-danger\"> Nenhuma grade encontrada...</div>";
}