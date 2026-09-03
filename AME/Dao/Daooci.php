<?php
/**
 * Description of Daooci
 *
 * @author Gui
 */
class Daooci {
           
    public static function getTipoOCI($idUser) {       
        $mc = new Maincontroller();        
        $tipo = $mc->doSelect("cod_oci_principal.id", "cod_oci_principal.abrev", "cod_oci_principal", "JOIN serv_oci ON serv_oci.id_oci = cod_oci_principal.id
WHERE serv_oci.id_serv ={$idUser}", "ORDER BY cod_oci_principal.id");       
        return $tipo;
    }
    
    public static function getCID($idOCI) {       
        $mc = new Maincontroller();        
        $cids = $mc->doSelect("cid", "descricao", "cid", "WHERE id_oci_principal={$idOCI}","ORDER BY id");       
        return $cids;
    }  
    
     public static function getCBO($idUser){   
        $sql = "SELECT cbo, SUS FROM servidores WHERE id_usuario ={$idUser}";
        $ds = Maincontroller::doQuery($sql);
        $arr = array();        
        while($row = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $row;
        }   
        return $arr;  
    }  
    
     public static function getSec($idOCI) {       
        $mc = new Maincontroller();        
        $secs = $mc->doSelect("cod", "descricao", "cod_oci_secundario", "WHERE id_cod_principal={$idOCI}","ORDER BY id");       
        return $secs;
    }  
    
      public static function saveToList($medico,$idPac,$data,$procedPrincipal,$cidPrincipal,$cidSecundario) {
         $sql = "INSERT INTO oci_fila (id_medico_solicitante, id_paciente, data, cod_proced_principal,cid_principal,cid_secundario) VALUES (:MEDICO,:IDPAC,:DATA,:PROCEDPRINCIPAL,:CIDPRINCIPAL,:CIDSECUNDARIO)";            
        if(Maincontroller::doQuery($sql,array('MEDICO'=>$medico,'IDPAC'=>$idPac,'DATA'=>Functions::ENdateFormat($data),'PROCEDPRINCIPAL'=>$procedPrincipal,'CIDPRINCIPAL'=>$cidPrincipal,'CIDSECUNDARIO'=>$cidSecundario))){
           
        } 
    } 
    
    public function saveProcedSec($params) {       
        $idPac = $params[2];
        $procedSec = $params[3];
        $qtd = $params[4];
        $cbo = $params[5];
        $sus = $params[6];
        $data = Functions::ENdateFormat($params[7]);    
        
         $sql = "INSERT INTO procedimentos_secundarios (id_paciente, cod_proced_secundario, qtd, cbo,medico_solicitante,data) VALUES (:IDPAC,:CODPROCED,:QTD,:CBO,:SUS,:DATA)";            
        if(Maincontroller::doQuery($sql,array('IDPAC'=>$idPac,'CODPROCED'=>$procedSec,'QTD'=>$qtd,'CBO'=>$cbo,'SUS'=>$sus,'DATA'=>$data))){
           
        } 
    } 
    
     public static function loadProcedSec($data,$idPac ,$medico) {      
        $sql = "SELECT procedimentos_secundarios.id, cod_oci_secundario.cod,cod_oci_secundario.descricao,procedimentos_secundarios.qtd,procedimentos_secundarios.cbo
        FROM procedimentos_secundarios
        JOIN cod_oci_secundario ON procedimentos_secundarios.cod_proced_secundario = cod_oci_secundario.cod 
        WHERE procedimentos_secundarios.data='{$data}' 
        AND procedimentos_secundarios.id_paciente ={$idPac}
        AND procedimentos_secundarios.medico_solicitante='{$medico}'";
        $ds = Maincontroller::doQuery($sql);        
        $arr = array();        
        while($proceds = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $proceds;
        }                
        return $arr;
     }
     
      public static function loadFila($data,$medico,$proced) {      
        $sql = "SELECT oci_fila.id,pacientes.id AS idPac, oci_fila.data,pacientes.nome, pacientes.dtnasc, oci_fila.cid_principal, oci_fila.cid_secundario,oci_fila.status
        FROM oci_fila
        JOIN pacientes ON pacientes.id = oci_fila.id_paciente
        WHERE oci_fila.id_medico_solicitante = '{$medico}'
        AND oci_fila.`data`='{$data}'
        AND oci_fila.cod_proced_principal={$proced}";
        $ds = Maincontroller::doQuery($sql);        
        $arr = array();        
        while($fila = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $fila;
        }                
        return $arr;
     }
     
     public static function delProced($idProced){          
        $sql = "DELETE FROM procedimentos_secundarios WHERE id=:ID";      
        return Maincontroller::doQuery($sql,array('ID'=>$idProced),null,"Erro"); 
    }    
    
    public static function delFila($idFila){  
         $sql = "DELETE FROM oci_fila WHERE id=:ID";      
        return Maincontroller::doQuery($sql,array('ID'=>$idFila),null,"Erro"); 
    }
}