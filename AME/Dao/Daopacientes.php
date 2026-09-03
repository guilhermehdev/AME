<?php

/**
 * Description of Daopacientes
 *
 * @author Guilherme
 */
class Daopacientes {
    
    public function save($params) {   
        
        if (!isset($params[2], $params[3])) {
            throw new Exception('Parâmetros obrigatórios não informados.');
        }
             
        $nome = Functions::cleanString(Functions::uppercase(trim($params[2])));
        $dtnasc = $params[3];
        $cpf = isset($params[4]) ? Functions::cleanString($params[4]) : null;
        if ($cpf != null && $cpf != '') {
            $cpf_clean = str_replace(array('.', '-'), '', $cpf);
        } else {
            $cpf_clean = null;
        }            
         
        $pront = (Functions::cleanString($params[5] != "")) ? Functions::cleanString($params[5]) : "";
        $tel = isset($params[6]) ? Functions::cleanString($params[6]) : null;
        $mae = isset($params[7]) ? Functions::cleanString(Functions::uppercase(trim($params[7]))) : "";                  
        $idLogra = isset($params[8]) ? Functions::cleanString($params[8]): null;
        $numero = isset($params[9]) ? Functions::cleanString($params[9]): "";
        $complemento = isset($params[10]) ? Functions::cleanString($params[10]): "";
        $sexo = (Functions::cleanString($params[11]) != "") ? Functions::cleanString(trim($params[11])) : "";               
                            
        if($idLogra != ''){
            $idLogra = $idLogra;
        } else {
             $idLogra = 0;
             $numero = null;
             $complemento = null;
        }         
        
        $sql = "INSERT INTO pacientes (nome,dtnasc,pront,tel,cpf,mae,id_logradouro,numero,complemento,sexo) VALUES (:NOME,:DTNASC,:PRONT,:TEL,:CPF,:MAE,:IDLOGRA,:NUMERO,:COMP,:SEXO)";            
       Maincontroller::doQuery($sql,array('NOME'=>$nome,'DTNASC'=>Functions::ENdateFormat($dtnasc),'PRONT'=>$pront,'TEL'=>$tel,'CPF'=>$cpf_clean,'MAE'=>$mae,'IDLOGRA'=>$idLogra,'NUMERO'=>$numero,'COMP'=>$complemento,'SEXO'=>$sexo));      
    } 
    
    public function update($params) {
        if (!isset($params[2])) {
            return false;
        }
                       
        $id = $params[2];   
        $nome = isset($params[3]) ? Functions::cleanString(Functions::uppercase(trim($params[3]))) : null;        
        $dtnasc = isset($params[4]) ? $params[4] : null;  
        $cpf = isset($params[5]) ? Functions::cleanString($params[5]) : null;
        if ($cpf != null && $cpf != '') {
            $cpf_clean = str_replace(array('.', '-'), '', $cpf);
        } else {
            $cpf_clean = null;
        }
        $pront =  (Functions::cleanString($params[6] != "")) ? Functions::cleanString($params[6]) : "";
        $tel = isset($params[7]) ? Functions::cleanString($params[7]) : null;
        $mae = isset($params[8]) ? Functions::cleanString(Functions::uppercase(trim($params[8]))) : null;                  
        $idLogra = isset($params[9]) ? Functions::cleanString($params[9]): null;
        $numero = isset($params[10]) ? Functions::cleanString($params[10]): "";
        $complemento = isset($params[11]) ? Functions::cleanString($params[11]): "";
        $sexo = (Functions::cleanString($params[12]) != "") ? Functions::cleanString(trim($params[12])) : "";    
         
        if($idLogra != ''){
            $idLogra = $idLogra;
        } else {
             $idLogra = 0;
             $numero =null;
             $complemento = null;
        }       
        
        $sql = "UPDATE pacientes SET nome=:NOME,dtnasc=:DTNASC,pront=:PRONT,tel=:TEL,cpf=:CPF,mae=:MAE,id_logradouro=:IDLOGRA,numero=:NUMERO,complemento=:COMP, sexo=:SEXO WHERE id=:ID";            
        if(Maincontroller::doQuery($sql,array('NOME'=>$nome,'DTNASC'=>Functions::ENdateFormat($dtnasc),'PRONT'=>$pront,'TEL'=>$tel,'ID'=>$id,'CPF'=>$cpf_clean,'MAE'=>$mae,'IDLOGRA'=>$idLogra,'NUMERO'=>$numero,'COMP'=>$complemento,'SEXO'=>$sexo))){
            
        }        
    } 
    
    public static function getAddress($cep=null,$logra=null,$bairro=null){             
            $cep = Functions::removeQuotes($cep);
            $logra = Functions::removeQuotes($logra);
            $bairro = Functions::removeQuotes($bairro);
            
               if($cep != null){
                   $where = "cep='{$cep}'";
               } 
               if($logra != null) {
                   $where = "logradouro  LIKE '%{$logra}%'";
               } 
               if($bairro != null) {
                   $where = "bairro LIKE '%{$bairro}%'";
               }

               $sql = "SELECT * FROM ceps_peruibe WHERE 1=1 AND {$where}";
               $ds = Maincontroller::doQuery($sql);

               $arr = array();        
               while($rows = $ds->fetch(PDO::FETCH_ASSOC)){
                   $arr[] = $rows;
               } 
               return $arr;     
    }           
    
    public static function delete($id) {                  
        $sql = "DELETE FROM pacientes WHERE id=:ID";            
        return (Maincontroller::doQuery($sql,array('ID'=>$id),null,"Paciente não pode ser excluído, possui registros associados a ele (retornos).")) ;     
    } 
    
    public static function get($id=null,$nasc=null,$name=null,$cpf=null) {
                    
        if($id != "null"){
            $where = "WHERE pacientes.id={$id}";
        } 
        if($nasc != "null") {
            $where = "WHERE pacientes.dtnasc='{$nasc}'";
        } 
        if($name != "null") {
            $where = "WHERE pacientes.nome LIKE '{$name}%'";
        }
        if($cpf != "null") {
            $where = "WHERE pacientes.cpf='{$cpf}'";
        }
                
        $sql = "SELECT pacientes.*, ceps_peruibe.cep AS CEP,ceps_peruibe.tipo,ceps_peruibe.logradouro,ceps_peruibe.bairro
                FROM pacientes
                JOIN ceps_peruibe ON pacientes.id_logradouro = ceps_peruibe.id {$where} ORDER BY nome";
        $ds = Maincontroller::doQuery($sql);              
                       
        $arr = array();        
        while($rows = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $rows;
        }                
        return $arr;        
    }
}
