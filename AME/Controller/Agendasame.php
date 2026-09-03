<?php
/**
 * Description of Controlagendas
 *
 * @author Guilherme
 */
class Agendasame implements IPrivateTO {
    
    public function agendas(){
        session_start();
        if(isset($_SESSION['adm'])){
            $view = new TGui("agendas");
            $view->addData("title", "Vagas para UBS/PSF");
            $view->renderize(APP_VIEW);
        } else {
            header("location: ".URL."Loginadm/login");
        }
    }
    
    public function grade(){
        session_start();
        if(isset($_SESSION['adm'])){
            $view = new TGui("grade");
            $view->addData("title", "Grade de Especialidades");
            $view->renderize(APP_VIEW);
        } else {
            header("location: ".URL."Loginadm/login");
        }
    }
    
    public function perdas(){
        session_start();
        if(isset($_SESSION['adm'])){
            $view = new TGui("perdas");
            $view->addData("title", "Perda de Vagas UBS/PSF");
            $view->renderize(APP_VIEW);
        } else {
            header("location: ".URL."Loginadm/login");
        }
    } 
    
    public function encaixes(){ 
        $view = new TGui("encaixes");
        $view->addData("title", "Acolhimento");
        $view->renderize(APP_VIEW);       
    } 
    
    public function saveEncaixes($params) {
        $da = new Daoagendas();        
      
        $idEspec = $params[2];
        $idProf = $params[3];
        $data = $params[4];
        $qtd = $params[5];
                     
        if($da->saveEncaixe($idEspec,$idProf,$data,$qtd)){
           
        }
    }
    
    public function getEncaixes($params) {
        $da = new Daoagendas();
              
        $idEspec = $params[2];
        $idServidor = $params[3];
        $dataIni = ($params[4]);
        $dataFin = ($params[5]);
        
        $res = $da->getEncaixes($dataIni, $dataFin, $idEspec, $idServidor);  
      
        $view = new TGui("Lencaixes");  
        $view->addData("encaixes", $res);
        $view->renderize(APP_VIEW_LIST,true);
    }
    
    public function delEncaixe($param) {
        $da = new Daoagendas();
        
        $idEncaixe = $param[2];
        
        $da->delEncaixe($idEncaixe);
    }
    
    public function getProfByEspec($param){ 
        $da = new Daoagendas(); 
         
        $espec = $param[2];
        $data = $da->getProfByEspec($espec);            
        
        echo $data;
    } 
    
    public function getPerdas($params) {
        $f = new Functions();
               
        $idUnidade = $f->cleanString($params[2]);
        $idEspec = $f->cleanString($params[3]);
        $idProf = $f->cleanString($params[4]);
        $mes = $params[5];
        $ano = $params[6];
        
        $perdas = Daoagendas::getPerdas($mes, $ano, $idUnidade, $idEspec, $idProf);
        
        $view = new TGui("perdasubs");
        $view->addData("perdas", $perdas);
        $view->renderize(APP_VIEW_LIST,true);
    }
    
    public function delperdas($param) {
        $id = $param[2];        
        if(Daoagendas::delPerdas($id)) {
            
        }
    }
        
    public function getAgendas($param) {
                       
        $idUnidade = $param[2];
        
        if($param[3] != ''){
            $idEspec = Functions::removeQuotes($param[3]);
        } else {
            $idEspec = null;
        }
        
        if($param[4] != ''){
            $idProf = Functions::removeQuotes($param[4]);
        } else {
            $idProf = null;
        }               
        
        if($param[5] != ''){
            $dtIni = Functions::ENdateFormat(($param[5]));
        } else {
            $dtIni = null;
        } 
        
        if($param[6] != ''){
            $dtFin = Functions::ENdateFormat(($param[6]));
        } else {
            $dtFin = null;
        }  
        
        $vagas = Daoagendas::getAgendas($idUnidade,$idEspec,$idProf,$dtIni,$dtFin);
        
        $view = new TGui("agendasgeradas");
        $view->addData("title", "Cadastro de Agendas");
        $view->addData("vagas", $vagas);
        $view->renderize(APP_VIEW_LIST,true);
    }
            
    public function getDist($params) {
        $idUnidade = $params[2];
        $idEspec = $params[3];
        $idProf = $params[4];
        
        $dist = Daoagendas::getDist($idUnidade, $idEspec, $idProf);
        
        return $dist;
    }
    
    public function getGrade($params){       
        $idEspec = $params[2];
        $idProf = $params[3];
        $mes = $params[4];
        $ano = $params[5];
                    
        $grade = Daoagendas::getGrade($idEspec, $idProf, $mes+1, $ano);
                        
        $view = new TGui("gradescadastradas");      
        $view->addData("grades", $grade);
        $view->renderize(APP_VIEW_LIST,true);
    }
    
    public function inputVagas($params) {
        $idEspec = $params[2];
        $idProf = $params[3];
        $mes = $params[4];
        $ano = $params[5];
        
        $grade = Daoagendas::getGrade($idEspec, $idProf, $mes+1, $ano);
        
        $view = new TGui("inputvagas");      
        $view->addData("grades", $grade);
        $view->addData("mes", $mes);
        $view->addData("ano", $ano);
        $view->renderize(APP_VIEW_LIST,true);
    }
    
    public static function unidades() {              
        $unidades = Daoagendas::slctUnidades();        
        return $unidades;
    }
    
    public static function especs() {     
        $especs = Daoagendas::slctEspecs();        
        return $especs;       
    }
    
    public static function prof() {
        $profs = Daoagendas::slctProf();
        return $profs;
    }
    
    public function pacientes() {              
        $pacs = Daoagendas::slctPacientes();        
        echo json_encode($pacs);
    }

    public function pacientesByDate($params) { 
        $f = new Functions;
        $date = $params[2];               
        $pacs = Daoagendas::slctPacientesByDate($date);                  
        
        if($pacs) {
        echo "
        <script>
            $('[name=btn-select-paciente]').on('click', function () {
                var id_pac = $(this).data('id');
                var params = $(this).data('params');
                $('#container-retornos').fadeIn('slow');
                $('#retornos-cadastrados').load(GLOBAL_URL + 'Retornos/get/' + id_pac);
                $('#inp-pac').val(params.nome);
                $('#id_pac').val(id_pac);
            });
        </script>
        
        Exibindo resultados da busca para:<span class=\"text-orange\"> <b><i>{$f->uppercase($buscado)}</i></b></span>
        <br><br>
        <table class=\"table table-active table-condensed\">
            <thead class=\"\">
            </thead>
            <tbody>";

        foreach ($pacs as $p) {
        echo    
                "<tr class=\"bg-success\">"
                    . "<th></th>"            
                    . "<th>Nome</th>"                    
                    . "<th>Nascimento</th>"                    
                    . "<th>Prontuário</th>" 
                    . "<th>Contato</th>"                       
                . "</tr>
                <tr>"        
                    . "<td class=\"text-nowrap\">
                            <button class=\"btn btn-primary\" name=\"btn-select-paciente\" id=\"btn-select-paciente-{$p['id']}\" data-id=\"{$p['id']}\" data-params='{\"nome\":\"{$p['nome']}\",\"dtnasc\":\"{$p['dtnasc']}\",\"pront\":\"{$p['pront']}\",\"contato\":\"{$p['tel']}\"}' data-modal-close=\"true\">
                        <span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>
                            </button>
                    </td>"
                    . "<td class=\"text-nowrap\">{$p['nome']}</td>"
                    . "<td>{$f->BRdateFormat($p['dtnasc'])}</td>"                 
                    . "<td>{$p['pront']}</td>"
                    . "<td>{$p['tel']}</td>"              
                
            . "</tr><tr><td colspan=5><hr></td></tr>";
            }
    
        echo 
            "</tbody>        
        </table>";
        } else {
            echo "Nenhum registro encontrado...";
        }
    }
    
    public function editAgenda($params) {
        $da = new Daoagendas();	
	    $idAgenda = $params[2];      	
        $agenda = $da->getAgenda($idAgenda);
	    $agendados = $da->countAgendados($idAgenda);        
        $stsvagas = $da->getVagas($idAgenda);
	
        $view = new TGui("editagenda");
        $view->addData("Agendados", $agendados);  
        $view->addData("Agenda", $agenda);      
        $view->addData("StsVagas", $stsvagas);
        $view->renderize(APP_VIEW_LIST,true);
    }
        
    public function save($params) {
        $da = new Daoagendas();
        $f = new Functions();
        
        $idUnidade = $params[2];
        $idEspec = $params[3];
        $idProf = $params[4]; 
        $vagas = isset($params[5]) ? $params[5] : FALSE;
           if (!$vagas || trim($vagas)== "") {
               Functions::messages("msg",'Digite a qtd de vagas!',"danger");          
           }
        $hora = isset($params[6]) ? $f->cleanString($params[6]) : FALSE;
           if (!$hora || trim($hora)== "") {
               Functions::messages("msg",'Digite a hora!',"danger");          
           }       
      
        $obs = $f->cleanString($params[7]);
        $arrlength = count($params);
        $datas = array();
        
        for($x = 8; $x < $arrlength + 2; $x++) {
            $datas[] = $f->cleanString($params[$x]);
        }
                      
        if($da->save($idUnidade,$idEspec,$idProf,$datas,$vagas,$hora,$obs)){
            echo "Salvo com sucesso!";
        }
            
    }
    
     public function saveperdas($params){
        $da = new Daoagendas();
        $f = new Functions();

        $idUnidade = $params[2];
        $idEspec = $params[3];
        $idProf = $params[4]; 
        $vagas = isset($params[5]) ? $params[5] : FALSE;
        
           if (!$vagas || trim($vagas)== "") {
               Functions::messages("msg",'Digite a qtd de vagas!',"danger");          
           } 
       
        $arrlength = count($params);
        $datas = array();
        
        for($x = 6; $x < $arrlength + 2; $x++) {
            $datas[] = $f->cleanString($params[$x]);
        }

        if($da->saveperdas($idUnidade,$idEspec,$idProf,$datas,$vagas)){
            echo 'Salvo com sucesso!';
        }
    }
    
    public function savegrid($params){
        $da = new Daoagendas();
        
        $idDias = $params[2];
        $idEspec = $params[3];
        $idProf = $params[4];
        $limite = $params[5];
        $mes = $params[6];
        $ano = $params[7];
        
        if($da->savegrid($idDias, $idEspec, $idProf, $limite, $mes+1, $ano)){
           
        }
    }
           
    public function savedist(){
        $da = new Daoagendas();       
        $post = filter_input_array(INPUT_POST,FILTER_DEFAULT);
        $arr = array(); 
                
        foreach ($post as $value) {
            $arr[] = $value;                          
        }
                                                   
        $idUnidade = $arr[0];
        $idEspec = $arr[1];
        $idProf = $arr[2];
        $idDia = $arr[3];
        $totalMes = $arr[4];
        $nvagasDia = $arr[5];
        $data = $arr[6]; 
        $vagasUtilizadas = $arr[7];
              
        if($da->savedist($idUnidade, $idEspec, $idProf, $idDia, $totalMes, $nvagasDia, $data, $vagasUtilizadas)){
            
        }                   
    }
    
    public function showVagasUBS($params){
        $idUnidade = $params[2];
        $mes = $params[3];
        $ano = $params[4];
        $nivel = $params[5];
        
        $view = new TGui("vagasubs");              
        $view->addData("dados", array($idUnidade,$mes,$ano,$nivel));
        $view->renderize(APP_VIEW_LIST,true);
    }
    
    public function updatevagaubs(){
        $da = new Daoagendas();       
        $post = filter_input_array(INPUT_POST,FILTER_DEFAULT);
        $arr = array(); 
               
        foreach ($post as $value) {
            $arr[] = $value;                          
        }
                                                   
        $id = $arr[0];
        $nasc = Functions::ENdateFormat($arr[1]);
        $nome = Functions::uppercase($arr[2]); 
        $sts = $arr[3];
        
        if($da->updatevagaubs($id, $nasc, $nome, $sts)){
            
        }
                   
    }
    
    public function updateAgendaUBS() {
        $da = new Daoagendas();       
        $post = filter_input_array(INPUT_POST,FILTER_DEFAULT);
        $arr = array(); 
               
        foreach ($post as $value) {
            $arr[] = $value;                          
        }
                                                   
        $id = $arr[0];       
        $sts = $arr[1];
        
        if($da->updateAgendaUBS($id,$sts)){
            
        }
    }
    
    public function delete($params){
        $da = new Daoagendas();        
       
        $idAgenda = $params[2];	        
        
        $da->delete($idAgenda);
       
    }  
    
    public function delGrade($params){
        $da = new Daoagendas();        
       
        $idEspec = $params[2];
        $idProf = $params[3];
        $idDia = $params[4];       
        $mes = $params[5];
        $ano = $params[6];
        
        $da->delGrade($idEspec, $idProf, $idDia, $mes, $ano);
       
    }   
    
    public function vagasUBSReport() {
        session_start();
        if(isset($_SESSION['adm'])){
            $view = new TGui("vagasubsreport");
            $view->addData("title", "Grade de Vagas - UBS/PSF");
            $view->renderize(APP_VIEW);
        } else {
            header("location: ".URL."Loginadm/login");
        }
        
    }
    
     public function perdasUBSReport() {
        session_start();
        if(isset($_SESSION['adm'])){
            $view = new TGui("perdasubsreport");
            $view->addData("title", "Perda de Vagas - UBS/PSF");
            $view->renderize(APP_VIEW);
        } else {
            header("location: ".URL."Loginadm/login");
        }
        
    }
    
    public function semanal() {
        if(AppController::checkSession()){
            $view = new TGui("semanal");
            $view->addData("title", "Grade semanal");
            $view->renderize(APP_VIEW);
        }
    }
        
}