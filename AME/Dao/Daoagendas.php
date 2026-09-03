<?php

/**
 * @author Guilherme
 */
class Daoagendas {

    public static function slctUnidades() {
        $mc = new Maincontroller();        
        $unidades = $mc->doSelect("id", "descricao", "unidades", "WHERE status =1", "ORDER BY descricao");       
        return $unidades; 
    }
    
     public static function slctUnidadesAB() {
        $mc = new Maincontroller();        
        $unidades = $mc->doSelect("id", "descricao", "unidades", "WHERE status =1 AND descricao LIKE 'USAFA%'", "ORDER BY descricao");       
        return $unidades; 
    }
    
    public static function getUnidades($id=false) {
        if($id) {
            $where = "AND id={$id}";
        }
        $sql = "SELECT id,descricao FROM unidades WHERE status=1 {$where} ORDER BY descricao";
        $ds = Maincontroller::doQuery($sql);        
        $arr = array();        
        while($unidades = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $unidades;
        }                
        return $arr;
    }
    
    public static function slctEspecs() {
        $mc = new Maincontroller();        
        $especs = $mc->doSelect("id", "especialidade", "especs", "WHERE ativo=1", "ORDER BY especialidade");       
        return $especs;
    }
    
    public static function slctProf($idEspec = "") {
        $query = "";
        if ($idEspec != ""){
            $query = "WHERE id_espec={$idEspec}";
        }
        $mc = new Maincontroller();        
        $profs = $mc->doSelect("id", "nome", "servidores", $query, "ORDER BY nome"); 
        return $profs;
    }
    
     public static function getProfByEspec($idEspec) {
        $mc = new Maincontroller();        
        $profs = $mc->doSelect("servidores.id AS id", "servidores.nome AS nome", "serv_espec", "JOIN servidores ON servidores.id = serv_espec.id_servidor WHERE serv_espec.id_espec ={$idEspec}", "ORDER BY nome");               
        
        return $profs;
    }
    
    public static function slctDias() {
        $mc = new Maincontroller();        
        $arr = $mc->doSelect("id", "dia", "dias", "", "ORDER BY id");       
        return $arr;
    }
    
    public static function slctUser() {
        $mc = new Maincontroller();        
        $arr = $mc->doSelect("id", "nome", "usuarios", "WHERE ativo=1", "ORDER BY nome");       
        return $arr;
    }
    
    public static function slctSalas() {
        $mc = new Maincontroller();
        
        $arr = $mc->doSelect("id", "numero", "salas", "", "ORDER BY numero");
       
        return $arr;
    }
    
    public static function slctPeriodo() {
        $mc = new Maincontroller();
        
        $arr = $mc->doSelect("id", "descricao", "periodos", "", "ORDER BY descricao");
       
        return $arr;
    }
    
    public static function slctPacientes() {
        $mc = new Maincontroller();
        
        $sql = "SELECT id,nome AS name FROM pacientes ORDER BY nome";
        $ds = $mc->doQuery($sql); 
               
        $arr = array();        
        while($log = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $log;
        }
       
        return $arr;                 
    }

    public static function slctPacientesByDate($date) {
        $mc = new Maincontroller();
        $formattedDate = Functions::ENdateFormat(Functions::removeQuotes($date));
        
        $sql = "SELECT id,nome,dtnasc,pront,tel FROM pacientes WHERE dtnasc='{$formattedDate}' ORDER BY nome";
        $ds = $mc->doQuery($sql); 
               
        $arr = array();        
        while($log = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $log;
        }       
        return $arr;                 
    }
    
    public static function getPerdas($mes,$ano,$idUnidade=false,$idEspec=false,$idProf=false) {
                
        if($idEspec != "")
            $espec = "AND perdas.FKespec ={$idEspec}";
            
        if($idProf != "")
            $prof = "AND perdas.FKprof ={$idProf}";
                   
        if($idUnidade != "") 
            $uni = "AND perdas.FKunidade ={$idUnidade}";           
        
        
        $sql = "SELECT perdas.id,perdas.FKespec,perdas.FKprof,perdas.`data`,perdas.FKunidade AS FKuni,unidades.descricao AS unidade,servidores.nome AS profissional,especs.especialidade 

        FROM perdas
        JOIN unidades ON perdas.FKunidade = unidades.id
        JOIN servidores ON servidores.id = perdas.FKprof
        JOIN especs ON especs.id = perdas.FKespec
        WHERE  
        MONTH(perdas.`data`) =:MES
        AND YEAR(perdas.`data`) =:ANO
        
        {$uni}
        {$espec}
        {$prof}
            
        ORDER BY unidades.descricao,especs.especialidade,servidores.nome"; 
        
        $ds =  Maincontroller::doQuery($sql, array('MES'=>$mes,'ANO'=>$ano));
        $arr = array();        
        while($row = $ds->fetch(PDO::FETCH_ASSOC)){            
            $arr[] = $row;
        }
        
        return $arr;
    }
    
    public static function delPerdas($id) {
        $sql = "DELETE FROM perdas WHERE id=:ID";
        if(Maincontroller::doQuery($sql, array('ID'=>$id))){
            return true;
        }
    }

    public static function getAgendas($idUnidade,$idEspec=null,$idProf=null,$dtIni=null,$dtFin=null,$mes=null,$ano=null){
	$main = new Maincontroller();
        
        if($idEspec != ''){
            $espec = "AND agenda.FKespec={$idEspec}";
        }
        if($idProf != ''){
            $pro = "AND agenda.FKprof={$idProf}";
        }
        
        if($dtIni != null && $dtFin != null){
            $dates = "AND agenda.`data` BETWEEN '{$dtIni}' AND '{$dtFin}'";
        }
	
        $sql = "SELECT agenda.id AS idAgenda,unidades.id AS idUnidade,servidores.id AS idProf,especs.id AS idEspec,unidades.descricao AS unidade,agenda.`data`,agenda.dia,agenda.hora,servidores.nome AS profissional,especs.especialidade,agenda.obs

        FROM agenda 

        JOIN servidores ON agenda.FKprof = servidores.id
        JOIN especs ON agenda.FKespec = especs.id
        JOIN unidades ON agenda.FKunidade = unidades.id

        WHERE unidades.id =:ID {$dates} {$espec} {$pro} ORDER BY agenda.`data` DESC, especs.especialidade";
        
        $ds = $main->doQuery($sql,array('ID'=>$idUnidade));
              	
	$arr = array();        
        while($agendas = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $agendas;
        }              
                         
        return $arr;	
    }
    
    public static function getVagasUBSDashboard() {
        $f = new Functions();
        $ano =  date('Y');
        $curmonth = date('m');
        $nextmonth = date('m')+1;
        
        $sqlgetAgenda = "SELECT agenda.id,agenda.`data`,agenda.FKespec,agenda.FKunidade,especs.especialidade,unidades.descricao AS unidade,servidores.id AS idprof,servidores.nome AS profissional

        FROM agenda

        JOIN especs ON agenda.FKespec = especs.id
        JOIN unidades ON agenda.FKunidade = unidades.id
        JOIN servidores ON agenda.FKprof = servidores.id

        WHERE status = 1
        AND MONTH(agenda.`data`) BETWEEN :CURMONTH AND :NEXTMONTH 
        AND YEAR(agenda.`data`)=:YEAR       

        GROUP BY agenda.FKunidade";
               
        $ds = Maincontroller::doQuery($sqlgetAgenda,array('CURMONTH'=>$curmonth,'NEXTMONTH'=>$nextmonth,'YEAR'=>$ano));
                              	
	$arr0 = array();        
        while($row = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr0[] = $row;
        }
                
        echo "
            <div class=\"\">
                <h4><span class=\"text-muted\">AGENDAMENTOS AGUARDANDO CONFIRMAÇÃO </span><button id=\"refresh-agendamentos-aguardando\" class=\"btn btn-default\"><span class=\"glyphicon glyphicon-refresh\"></span></button></h4> 
            </div>";
                
        if(count($arr0) > 0){ 

                foreach ($arr0 as $v0) {

                    $sqlEspecProf = "SELECT agenda.id,agenda.FKprof,agenda.FKespec,servidores.nome,especs.especialidade 

                    FROM agenda

                    JOIN servidores ON agenda.FKprof = servidores.id
                    JOIN especs ON agenda.FKespec = especs.id
                   
                    WHERE agenda.FKunidade ={$v0['FKunidade']}
                   
                    AND MONTH(agenda.`data`) BETWEEN {$curmonth} AND {$nextmonth}
                    AND YEAR(agenda.`data`)={$ano} 
                    AND status = 1                                

                    GROUP BY agenda.FKespec
                    ORDER BY agenda.FKespec
                    ";
                    $ds2 = Maincontroller::doQuery($sqlEspecProf);

                    $arrsqlEspecProf = array(); 

                    while($row2 = $ds2->fetch(PDO::FETCH_ASSOC)){
                        $arrsqlEspecProf[] = $row2;
                    }
                    
        echo "
                <div class=\"\">
                
                    <fieldset class=\"for-panel mrg-top mrg-bottom\">

                        <legend class=\"text-primary\">
                            <h4>{$v0['unidade']}</h4>
                        </legend>                       

                    <form name=\"frm-vagasubs-{$v0['id']}\" method=\"POST\" action=\"\">"; 

                        foreach ($arrsqlEspecProf as $idProf) {
                                
                            $sqlVagas = "SELECT agenda.id,agenda.`data`,agenda.dia,agenda.hora,agenda.nasc,agenda.nome,agenda.status,servidores.nome AS profissional,especs.especialidade 

                            FROM agenda
                            JOIN servidores ON agenda.FKprof = servidores.id
                            JOIN especs ON agenda.FKespec = especs.id

                            WHERE agenda.FKunidade ={$v0['FKunidade']}

                            AND agenda.FKprof = {$idProf['FKprof']}
                            AND agenda.FKespec = {$idProf['FKespec']}
                            AND MONTH(agenda.`data`) BETWEEN {$curmonth} AND {$nextmonth}
                            AND YEAR(agenda.`data`)={$ano}
                            AND status = 1                                

                            ORDER BY agenda.FKespec,agenda.`data`,agenda.hora"; 
                            $ds3 = Maincontroller::doQuery($sqlVagas);

                            $arrVagas = array(); 

                            while($row3 = $ds3->fetch(PDO::FETCH_ASSOC)){
                                $arrVagas[] = $row3;
                            }
                            $totalVagas = "<b class=\"text-orange\">".count($arrVagas)."</b><small> AGENDAMENTOS</small>";
                            
                  echo "<div class=\"\" style=\"margin-bottom:20px;\">
                            
                            <h5><span class=\"text-success\">{$idProf['especialidade']}</span> | <span class=\"text-danger\">{$idProf['nome']}</span> - {$totalVagas}</h5>";
                     echo "<HR>
                            <table class=\"table table-hover mrg-bottom\">
                                <thead class=\"\">
                                    <tr> 
                                        <th>Data</th>
                                        <th>Dia</th>                        
                                        <th>Hora</th> 
                                        <th style=\"width:120px;\">Nascimento</th>
                                        <th>Paciente</th>                           
                                        <th>Status</th>
                                        <th>Confirmar<br>agendamento</th>
                                    </tr>

                                </thead>              

                                <tbody>";
                            
                            foreach ($arrVagas as $vaga) {                                                                
                                
                                if($vaga['status'] == 1) {
                                    $sts = "<span class=\"text-danger\">AGUARDANDO</span>";
                                    $disable = '';                                   
                                } 

                                if($vaga['nasc'] == '0000-00-00') {
                                    $nasc = '';
                                } else {
                                    $nasc = $f->BRdateFormat($vaga['nasc']);
                                }
                                
                        echo "  
                                    <tr>
                                       <td>
                                            {$f->BRdateFormat($vaga['data'])}
                                        </td>
                                        <td>
                                            {$vaga['dia']}
                                        </td>
                                        <td>
                                            {$vaga['hora']}
                                        </td>
                                        <td>
                                            <input type=\"text\" class=\"form-control date\" id=\"inp-dtnasc-{$vaga['id']}\" name=\"inp-dtnasc\" data-id=\"{$vaga['id']}\" minlength=\"10\" placeholder=\"Nascimento\" value=\"{$nasc}\" {$disable}>
                                        </td>
                                        <td>                                         
                                            <input type=\"text\" class=\"form-control\" id=\"inp-paciente-{$vaga['id']}\" name=\"inp-paciente\" data-id=\"{$vaga['id']}\" minlength=\"5\" placeholder=\"Nome\" value=\"{$vaga['nome']}\" {$disable}>

                                        </td>                               
                                        <td>
                                            <span id=\"pac-status-{$vaga['id']}\">{$sts}</span>
                                        </td>
                                        <td>
                                            <button class=\"btn btn-success call-data\" id=\"btn-agenda-pac\" name=\"btn-agenda-pac-{$vaga['id']}\" type=\"button\" data-id=\"{$vaga['id']}\" data-url=\"".URL."\">
                                                <span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>
                                            </button>
                                            <button class=\"btn btn-warning call-data\" id=\"btn-del-agenda-pac\" name=\"btn-del-agenda-pac-{$vaga['id']}\" type=\"button\" data-id=\"{$vaga['id']}\" data-url=\"".URL."\">
                                                <span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>
                                                
                                            </button>
                                        </td>
                                        </tr>";
                                
                            }
                                  echo "<tr>
                                            <td>
                                                <br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan=\"7\" style=\"background-color:#E2E2E2;\">
                                                <br>
                                            </td>
                                        </tr>
                                        
                                    </tbody>

                                </table>
                                
                        </div>";
                         
                        }

              echo "</form>

                </fieldset>
                    
            </div>";

                }           
        }        
    }
    
    public static function getVagasUBS($idUnidade,$mes,$ano,$nivel=0) {        
        $f = new Functions(); 
        
        $sqlgetAgenda = "SELECT agenda.id,agenda.`data`,agenda.FKespec,especs.especialidade,unidades.descricao AS unidade,servidores.id AS idprof,servidores.nome AS profissional

        FROM agenda

        JOIN especs ON agenda.FKespec = especs.id
        JOIN unidades ON agenda.FKunidade = unidades.id
        JOIN servidores ON agenda.FKprof = servidores.id

        WHERE agenda.FKunidade =:IDUNIDADE

        AND MONTH(agenda.`data`)=:MES 
        AND YEAR(agenda.`data`)=:YEAR       

        GROUP BY agenda.FKprof,agenda.FKespec";
               
        $ds = Maincontroller::doQuery($sqlgetAgenda,array('IDUNIDADE'=>$idUnidade,'MES'=>$mes,'YEAR'=>$ano));
                              	
	$arr0 = array();        
        while($row = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr0[] = $row;
        }
                
        echo "<br>
            <div class=\"page-header\">
                <h2><span class=\"text-muted\">AGENDAMENTOS - <span class=\"text-primary\">{$f->monthExtense($mes)}</span> / <span class=\"text-success\">{$ano}</span></span></h2> 
            </div>";
                
    if(count($arr0) > 0){
        
        foreach ($arr0 as $v0) {
            
            $sqlVagas = "SELECT agenda.id,agenda.`data`,agenda.dia,agenda.hora,agenda.nasc,agenda.nome,agenda.status 
            
            FROM agenda

            WHERE agenda.FKunidade ={$idUnidade}

            AND agenda.FKespec = {$v0['FKespec']} 
            AND agenda.FKprof = {$v0['idprof']} 
            AND MONTH(agenda.`data`)={$mes} AND YEAR(agenda.`data`)={$ano} 
                
            ORDER BY agenda.`data`,agenda.hora
            ";
            $ds2 = Maincontroller::doQuery($sqlVagas);
                                      	
            $arr1 = array(); 
            
            while($row2 = $ds2->fetch(PDO::FETCH_ASSOC)){
                $arr1[] = $row2;
            }
            
            $totalVagas = "<h4><b class=\"text-orange\">".count($arr1)."</b><small> - VAGAS</small></h4>";
            
            echo "<br>                           
                
            <fieldset class=\"for-panel mrg-top\">
                <legend class=\"text-primary\">{$v0['especialidade']} - <span class=\"text-danger\">{$v0['profissional']}</span>{$totalVagas}</legend>
                    
            <form name=\"frm-vagasubs-{$v0['id']}\" method=\"POST\" action=\"" . URL . "servidor/save\">
                    
                <table class=\"table table-hover mrg-bottom\">
                    <thead class=\"\">
                        <tr> 
                            <th>Data</th>
                            <th>Dia</th>                        
                            <th>Hora</th> 
                            <th style=\"width:120px;\">Nascimento</th>
                            <th>Paciente</th>                           
                            <th>Status</th>";
                            if($nivel == 1) {
                                echo "<th>Confirmar<br>agendamento</th>";
                            }
                   echo "</tr>

                    </thead>              

                    <tbody>";                  
            
                    foreach ($arr1 as $v1) {
                        if($v1['status'] == 0) {
                            $sts = "<span class=\"text-danger\">DISPONÍVEL</span>";
                            $disable = '';
                            $class = "danger";
                            $glyphicon = "user";
                        } else if($v1['status'] == 1) {
                            $sts = "<span class=\"text-primary\">ENVIADO</span>";
                            $disable = '';
                            $class = "primary";
                            $glyphicon = "ok";
                        } else if($v1['status'] == 2) {
                            $sts = "<span class=\"text-success\">AGENDADO</span>";
                            $disable = 'disabled';
                            $class = "success";
                            $glyphicon = "saved";
                        }
                        
                        if($v1['nasc'] == '0000-00-00') {
                            $nasc = '';
                        } else {
                            $nasc = $f->BRdateFormat($v1['nasc']);
                        }
                        
                        echo              
                           "<tr>
                               <td>
                                    {$f->BRdateFormat($v1['data'])}
                                </td>
                                <td>
                                    {$v1['dia']}
                                </td>
                                <td>
                                    {$v1['hora']}
                                </td>
                                <td>
                                    <input type=\"text\" class=\"form-control date\" id=\"inp-dtnasc-{$v1['id']}\" name=\"inp-dtnasc\" data-id=\"{$v1['id']}\" minlength=\"10\" placeholder=\"Nascimento\" value=\"{$nasc}\" {$disable}>
                                </td>
                                <td> 
                                    <div class=\"input-group\"> 
                                        <input type=\"text\" class=\"form-control\" id=\"inp-paciente-{$v1['id']}\" name=\"inp-paciente\" data-id=\"{$v1['id']}\" minlength=\"5\" placeholder=\"Nome\" value=\"{$v1['nome']}\" {$disable}>
                                    <span class=\"input-group-btn\">
                                    
                                            <button id=\"btn-send-pac\" name=\"btn-send-pac-{$v1['id']}\" class=\"btn btn-{$class}\" type=\"button\" data-id=\"{$v1['id']}\" data-url=\"".URL."\" {$disable}>
                                        <span id=\"btn-send-icon-{$v1['id']}\" class=\"glyphicon glyphicon-{$glyphicon} text-white\" aria-hidden=\"true\" {$disable}></span>
                                            </button>
                                            
                                        </span>
                                </td>                               
                                <td>
                                    <span id=\"pac-status-{$v1['id']}\">{$sts}</span>
                                </td>";
                                    
                        if($nivel == 1) {
                           echo "<td>
                                    <button class=\"btn btn-success call-data\" id=\"btn-agenda-pac\" name=\"btn-agenda-pac-{$v1['id']}\" type=\"button\" data-id=\"{$v1['id']}\" data-url=\"".URL."\">
                                        <span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>
                                    </button>
                                    <button class=\"btn btn-warning call-data\" id=\"btn-del-agenda-pac\" name=\"btn-del-agenda-pac-{$v1['id']}\" type=\"button\" data-id=\"{$v1['id']}\" data-url=\"".URL."\">
                                        <span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>
                                    </button>
                                </td>";
                        }
                                    
                       echo"</tr>";
                    }

                      echo "<tr>
                                <td colspan=\"6\">
                                   <hr>
                                </td>                            
                  
                    </tbody>

                </table>
                
            </form>
                
        </fieldset>";
                
        }
    } else {
        echo "<div class=\"alert alert-danger\"> Nenhum registro encontrado...</div>";
    }
}

        
    public static function updatevagaubs($id,$nasc,$nome,$sts) {
        
        $sql = "UPDATE agenda SET nasc=:NASC,nome=:NOME,status=:STS WHERE id=:ID";
        Maincontroller::doQuery($sql, array('ID'=>$id,'NASC'=>$nasc,'NOME'=>$nome,'STS'=>$sts));
        
    }
    
    public static function updateAgendaUBS($id,$sts) {
        
        $sql = "UPDATE agenda SET status=:STS WHERE id=:ID";
        Maincontroller::doQuery($sql, array('ID'=>$id,'STS'=>$sts));
        
    }
    
    public static function getGrade($idEspec,$idProf,$mes=false,$ano=false){
        
        if($mes != false && $ano != false) {
            $data = "AND espec_dias_servidor.mes={$mes}
                    AND espec_dias_servidor.ano={$ano}";
        }
        
        $sql = "SELECT distinct espec_dias_servidor.*,especs.especialidade,servidores.nome,dias.dia
        FROM espec_dias_servidor
        JOIN especs ON especs.id = espec_dias_servidor.id_espec
        JOIN servidores ON servidores.id = espec_dias_servidor.id_servidor
        JOIN dias ON dias.id = espec_dias_servidor.id_dia
       
        WHERE espec_dias_servidor.id_espec =:IDESPEC 
        AND espec_dias_servidor.id_servidor =:IDSERVIDOR         
        {$data}
        ORDER BY espec_dias_servidor.id_dia";
        
        //var_dump($sql);
        
        $ds = Maincontroller::doQuery($sql,array('IDESPEC'=>$idEspec,'IDSERVIDOR'=>$idProf));
                        
        $arr = array();        
        while($row = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $row;
        }              
                         
        return $arr;
    }
    
    public static function getGradeReport($mes,$ano,$idUnidade=false,$idEspec=false,$idProf=false) {
         if($idEspec != "")
            $espec = "AND grade_vagas.id_espec ={$idEspec}"; 
            
        if($idProf != "")
            $prof = "AND grade_vagas.id_servidor ={$idProf}";
                   
        if($idUnidade != "") 
            $uni = "AND grade_vagas.id_unidade ={$idUnidade}"; 
        
        $sql = "SELECT grade_vagas.`data`,unidades.descricao AS unidade,especs.especialidade,servidores.nome AS profissional,dias.dia,grade_vagas.nvagas_dia AS qtd,grade_vagas.total_mes AS totalmes 
            
        FROM grade_vagas  
        
        JOIN unidades ON grade_vagas.id_unidade = unidades.id
        JOIN especs ON grade_vagas.id_espec = especs.id
        JOIN servidores ON grade_vagas.id_servidor = servidores.id
        JOIN dias ON grade_vagas.id_dia = dias.id

        WHERE MONTH(grade_vagas.`data`) =:MES
        AND YEAR(grade_vagas.`data`) =:ANO
        {$uni} {$espec} {$prof}

        ORDER BY unidades.descricao,grade_vagas.id_espec,grade_vagas.id_servidor,grade_vagas.`data`";
        $ds = Maincontroller::doQuery($sql,array('MES'=>$mes,'ANO'=>$ano));
                        
        $arr = array();        
        while($row = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $row;
        }  
                                
        return $arr;
    }
    
    public static function getDist($idUnidade,$idEspec,$idProf,$mes,$ano){
        
        $sql = "SELECT total_mes,nvagas_dia,id_dia,data,vagas_utilizadas FROM grade_vagas
        WHERE id_unidade=:IDUNI AND id_espec=:IDESPEC AND id_servidor=:IDPROF AND MONTH(`data`)=:MES AND YEAR(`data`)=:ANO";
        
        $ds = Maincontroller::doQuery($sql,array('IDUNI'=>$idUnidade,'IDESPEC'=>$idEspec,'IDPROF'=>$idProf,'MES'=>$mes,'ANO'=>$ano));
                        
        $arr = array();        
        while($row = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $row;
        }  
                                
        return $arr;
    }
    
    public static function getVagasUtilizadasDia($idEspec,$idProf,$idDia,$mes,$ano){
        
        $sql = "SELECT MAX(grade_vagas.vagas_utilizadas) AS vgut

        FROM grade_vagas

        WHERE grade_vagas.id_espec=:IDESPEC 

        AND grade_vagas.id_servidor=:IDPROF 

        AND grade_vagas.id_dia=:IDDIA

        AND MONTH(grade_vagas.`data`)=:MES 

        AND YEAR(grade_vagas.`data`)=:ANO";
        
        $ds = Maincontroller::doQuery($sql,array('IDESPEC'=>$idEspec,'IDPROF'=>$idProf,'IDDIA'=>$idDia,'MES'=>$mes,'ANO'=>$ano));
                        
        $arr = array();        
        while($row = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $row;
        }              
                     
        return $arr;
    }
    
    public static function getVagasLimitesDia($idEspec,$idProf,$idDia,$mes,$ano){
        
        $sql = "SELECT espec_dias_servidor.limite_diario AS lim 

        FROM espec_dias_servidor

        WHERE espec_dias_servidor.id_espec=:IDESPEC 

        AND espec_dias_servidor.id_servidor=:IDPROF 

        AND espec_dias_servidor.id_dia=:IDDIA

        AND espec_dias_servidor.mes=:MES 

        AND espec_dias_servidor.ano=:ANO";
        
        $ds = Maincontroller::doQuery($sql,array('IDESPEC'=>$idEspec,'IDPROF'=>$idProf,'IDDIA'=>$idDia,'MES'=>$mes,'ANO'=>$ano));
                        
        $arr = array();        
        while($row = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $row;
        }              
                     
        return $arr;
    }
                
    public function delete($idAgenda){
        $main = new Maincontroller();	
	$sql = "DELETE FROM agenda WHERE id ={$idAgenda}";
        $main->doQuery($sql); 
    }    
    
    public function delGrade($idEspec,$idProf,$idDia,$mes=false,$ano=false){
        $main = new Maincontroller();
	
	$sql0 = "DELETE FROM espec_dias_servidor 
        WHERE id_espec=:IDESPEC
        AND id_servidor=:IDPROF
        AND id_dia=:IDDIA";
        $main->doQuery($sql0,array('IDESPEC'=>$idEspec,'IDPROF'=>$idProf,'IDDIA'=>$idDia)); 
        
        $sql1 = "DELETE FROM grade_vagas 
        WHERE id_espec=:IDESPEC
        AND id_servidor=:IDPROF
        AND id_dia=:IDDIA
        AND MONTH(`data`)=:MES
        AND YEAR(`data`)=:ANO";
        $main->doQuery($sql1,array('IDESPEC'=>$idEspec,'IDPROF'=>$idProf,'IDDIA'=>$idDia,'MES'=>$mes,'ANO'=>$ano)); 
        
    }  
    
    //Encaixes
    
     public function saveEncaixe($idEspec,$idServidor,$data,$qtd){
        $main = new Maincontroller();
        
        $sql = "INSERT INTO encaixes (id_espec,id_prof,data,qtd) VALUES (:IDESPEC,:IDSERVIDOR,:DATA,:QTD)";
        $main->doQuery($sql,array("IDESPEC"=>$idEspec,"IDSERVIDOR"=>$idServidor,"DATA"=>$data,"QTD"=>$qtd)); 
        return true;
    }
    
    public static function getEncaixes($dataIni,$dataFin,$idEspec,$idServidor){
        $sql = "SELECT encaixes.id, encaixes.`data` AS data, servidores.nome AS prof, especs.especialidade AS espec, encaixes.qtd AS encaixes, encaixes.id_espec, encaixes.id_prof 
                FROM encaixes
                JOIN servidores ON servidores.id = encaixes.id_prof
                JOIN especs ON especs.id = encaixes.id_espec
                WHERE encaixes.`data` BETWEEN '{$dataIni}' AND '{$dataFin}' AND encaixes.id_espec ={$idEspec} AND encaixes.id_prof ={$idServidor}"; 
                     
        $ds = Maincontroller::doQuery($sql);
        
        $arr = array(); 
            while($row = $ds->fetch(PDO::FETCH_ASSOC)){
                $arr[] = $row;
            }       
        return $arr;
    }
    
     public function delEncaixe($idEncaixe){
        $main = new Maincontroller();	
	$sql = "DELETE FROM encaixes WHERE id ={$idEncaixe}";
        $main->doQuery($sql); 
    }   
    
    //------------------------------------------------------------------------------------------------

    public function save($idUnidade,$idEspec,$idPro,$data,$vagas,$hora,$obs){
        $main = new Maincontroller();
        $f = new Functions();
        
        foreach ($data as $d) { 
                                          
            for($i=0;$i<$vagas;$i++) {

                    $sql = "INSERT INTO agenda (data,dia,hora,FKespec,FKprof,FKunidade,obs) VALUES (:DATA,:DIA,:HORA,:FKESPEC,:FKPROF,:FKUNIDADE,:OBS)";
                    $main->doQuery($sql,array("DATA"=>$d,"DIA"=>$f->getWeekday($f->ENdateFormat($d)),"HORA"=>$hora,"FKESPEC"=>$idEspec,"FKPROF"=>$idPro,"FKUNIDADE"=>$idUnidade,"OBS"=>$obs));                

            }
        }
        return true;
    } 
    
    public function savegrid($dia,$espec,$prof,$limite,$mes,$ano){
        $main = new Maincontroller();
        
        $sql = "INSERT INTO espec_dias_servidor (id_dia,id_servidor,id_espec,limite_diario,mes,ano) VALUES (:IDDIA,:IDSERVIDOR,:IDESPEC,:LIM,:MES,:ANO)";
        $main->doQuery($sql,array("IDDIA"=>$dia,"IDSERVIDOR"=>$prof,"IDESPEC"=>$espec,"LIM"=>$limite,"MES"=>$mes,"ANO"=>$ano)); 
        return true;
    }
    
    public function savedist($idUnidade,$idEspec,$idProf,$idDia,$totalMes,$nvagasDia,$data,$vagasUtilizadas){
        $main = new Maincontroller();
        $f = new Functions();
        
        $sqlSearch = "SELECT id_unidade,id_espec,id_servidor 
        FROM grade_vagas
        WHERE `data`= '{$data}' AND id_unidade={$idUnidade} AND id_espec={$idEspec} AND id_servidor={$idProf}";
        $ds = $main->doQuery($sqlSearch);
        
        $row = $ds->fetch(PDO::FETCH_ASSOC);
                
        if($row > 0) {
            $sql = "UPDATE grade_vagas SET nvagas_dia={$nvagasDia},total_mes={$totalMes},vagas_utilizadas={$vagasUtilizadas}
            WHERE `data`= '{$data}' AND id_unidade={$idUnidade} AND id_espec={$idEspec} AND id_servidor={$idProf}";
            $main->doQuery($sql);
            return true;
        } else {            
            $sql = "INSERT INTO grade_vagas (id_unidade,id_espec,id_servidor,id_dia,total_mes,nvagas_dia,data,vagas_utilizadas) VALUES ({$idUnidade},{$idEspec},{$idProf},{$idDia},{$totalMes},{$nvagasDia},'{$f->ENdateFormat($data)}',{$vagasUtilizadas})";                
            $main->doQuery($sql);
            return true;
        }    
    }
    
    public function saveperdas($idUnidade,$idEspec,$idProf,$data,$nvagas){
        $main = new Maincontroller();
        
        foreach ($data as $d) { 
                                          
            for($i=0;$i<$nvagas;$i++) {
                $sql = "INSERT INTO perdas (data,FKespec,FKprof,FKunidade) VALUES (:DATA,:FKESPEC,:FKPROF,:FKUNIDADE)";
                    $main->doQuery($sql,array("DATA"=>$d,"FKESPEC"=>$idEspec,"FKPROF"=>$idProf,"FKUNIDADE"=>$idUnidade)); 
            }
            
        }
        return true;               
    }
}
