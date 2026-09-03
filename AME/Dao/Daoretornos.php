<?php

/**
 * Description of Daoretornos
 *
 * @author Guilherme
 */
class Daoretornos {
    
    public static function save($idPaciente, $idUSAFA, $dataConsulta, $idProfissional, $desfecho, $tempoRetorno, $dataRetorno,$alerta_mes,$conclusao) {
        if ($dataRetorno == null) {
            $dr = null;
        } else {
            $dr = Functions::ENdateFormat($dataRetorno);
        }
                                
        $sql = "INSERT INTO retornos (id_paciente,data_consulta,id_servidor,id_responsavel,desfecho,tempo_retorno,data_retorno,alerta_mes,conclusao,unidade) VALUES (:IDPACIENTE,:DATACONSULTA,:IDPROFISSIONAL,:RESPONSAVEL,:DESFECHO,:TEMPORETORNO,:DATARETORNO,:ALERTA,:CONC,:UNIDADE)";
        if(Maincontroller::doQuery($sql, array('IDPACIENTE'=>$idPaciente,'DATACONSULTA'=> Functions::ENdateFormat($dataConsulta),'IDPROFISSIONAL'=>$idProfissional,'RESPONSAVEL'=>AppController::checkSession()['id'],'DESFECHO'=>$desfecho,'TEMPORETORNO'=>$tempoRetorno,'DATARETORNO'=>$dr,'ALERTA'=>$alerta_mes,'CONC'=>$conclusao,'UNIDADE'=>$idUSAFA))){
             return true;
        }               
    }
    
     public static function edit($id,$idUSAFA,$dtconsulta,$medico,$desfecho,$tempo,$dtretorno,$alerta_mes,$conclusao) {                           
        if ($dtretorno == null || Functions::removeQuotes($dtretorno) == '') {
            $dr = null;
        } else {
            $dr = Functions::ENdateFormat($dtretorno);
        }
        
        $sql = "UPDATE retornos SET data_consulta=:DATACONSULTA,id_servidor=:IDPROFISSIONAL,desfecho=:DESFECHO,tempo_retorno=:TEMPORETORNO,data_retorno=:DATARETORNO,alerta_mes=:ALERTAMES,conclusao=:CONCLUSAO,unidade=:UNIDADE WHERE id=:ID";
        Maincontroller::doQuery($sql,array('ID'=>$id,'DATACONSULTA'=> Functions::ENdateFormat($dtconsulta),'IDPROFISSIONAL'=>$medico,'DESFECHO'=>$desfecho,'TEMPORETORNO'=> Functions::removeQuotes($tempo),'DATARETORNO'=>$dr,'ALERTAMES'=>$alerta_mes,'CONCLUSAO'=>$conclusao,'UNIDADE'=>$idUSAFA));            
        
    }
    
     public static function getOCI($idPaciente) {
            if($idPaciente != null){
            $sql = "SELECT oci.num_apac, oci.`data` AS ociData, pacientes.nome, pacientes.dtnasc, cod_oci_principal.descricao AS oci, servidores.nome AS medico
            FROM oci 
            JOIN pacientes ON oci.id_paciente = pacientes.id
            JOIN cod_oci_principal ON cod_oci_principal.id = oci.id_cod_principal
            JOIN servidores ON servidores.SUS = oci.id_medico
            WHERE oci.id_paciente = {$idPaciente}  
            ORDER BY oci.`data` DESC";

            $ds = Maincontroller::doQuery($sql);

            $arr = array();        
            while($row = $ds->fetch(PDO::FETCH_ASSOC)){
                $arr[] = $row;
            }              

        return $arr;
        }
     }
    
    public static function get($idPaciente) {
       
        if($idPaciente != null){
            $sql = "SELECT retornos.id,retornos.data_consulta,servidores.nome AS medico,pacientes.id AS idpac,pacientes.nome AS paciente,pacientes.dtnasc AS nascimento,usuarios.nome AS responsavel,retornos.desfecho,retornos.tempo_retorno,retornos.data_retorno,retornos.id_servidor,pacientes.tel,unidades.descricao AS USAFA,unidades.id AS idUnidade
            FROM retornos
            JOIN servidores ON retornos.id_servidor = servidores.id
            JOIN pacientes ON retornos.id_paciente = pacientes.id
            JOIN usuarios ON retornos.id_responsavel = usuarios.id
            JOIN unidades ON unidades.id = retornos.unidade

            WHERE retornos.id_paciente = {$idPaciente}

            ORDER BY retornos.data_consulta DESC";

            $ds = Maincontroller::doQuery($sql);

            $arr = array();        
            while($row = $ds->fetch(PDO::FETCH_ASSOC)){
                $arr[] = $row;
            }              

        return $arr;
        }
    }
    
     public static function getAlerts($mes,$ano,$idservidor) { 
            $sql = "SELECT * FROM vretornos WHERE MONTH(alerta_mes) =:MES AND YEAR(alerta_mes) =:ANO
                AND id_servidor =:IDSERVIDOR GROUP BY paciente ORDER BY conclusao,consulta,paciente";
            $ds = Maincontroller::doQuery($sql,array('MES'=>$mes,'ANO'=>$ano,'IDSERVIDOR'=>$idservidor));
            $arr = array();        
            while($row = $ds->fetch(PDO::FETCH_ASSOC)){
                $arr[] = $row;
            }              
            return $arr;         
     }   
    
    public static function getAll() {  
        $sql = "SELECT * FROM vretornos WHERE desfecho =1";
        $ds = Maincontroller::doQuery($sql);

        $arr = array();        
        while($row = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $row;
        }              
        return $arr;
    }
    
    public static function updateDataRetorno($id,$data) {
        if($data == null){
            $sql = "UPDATE retornos SET data_retorno=NULL,conclusao=0 WHERE id=:ID";
            $ds = Maincontroller::doQuery($sql,array('ID'=>$id));
        } else {
            $sql = "UPDATE retornos SET data_retorno=:DTRETORNO,conclusao=1 WHERE id=:ID";
            $ds = Maincontroller::doQuery($sql,array('ID'=>$id,'DTRETORNO'=>Functions::ENdateFormat($data)));
        }       
    }    
    
    public static function updateObs($id, $obs) {
        $sql = "UPDATE retornos SET obs=:OBS WHERE id=:ID";
        $ds = Maincontroller::doQuery($sql,array('ID'=>$id,'OBS'=>$obs));
    }
    
    public static function del($id) {
        $sql = "DELETE FROM retornos WHERE id=:ID";
        if(Maincontroller::doQuery($sql,array('ID'=>$id))){
            return true;
        }        
    }
}