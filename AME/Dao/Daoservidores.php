<?php

/**
 * Description of Daopacientes
 *
 * @author Guilherme
 */
class Daoservidores {
    
    public function save($params) {
        $nome = Functions::removeQuotes(Functions::uppercase($params[2]));
        $cpf = Functions::cleanString($params[3]);
        $setor = $params[4];
        $unidade = $params[5];
               
        
        $sql = "INSERT INTO servidores (nome, CPF, id_setor, id_unidade) VALUES (:NOME,:CPF,:SETOR,:UNIDADE)";            
        if(Maincontroller::doQuery($sql,array('NOME'=>$nome,'CPF'=>$cpf,'SETOR'=>$setor,'UNIDADE'=>$unidade))){
           
        }        
    } 
    
    public function update($params) {
        $id = $params[2];
        $nome = Functions::removeQuotes(Functions::uppercase($params[3]));
        $cpf = Functions::cleanString($params[4]);
        $setor = $params[5];
        $unidade = $params[6];    
        
        $sql = "UPDATE servidores SET nome=:NOME,CPF=:CPF,id_setor=:SETOR,id_unidade=:UNIDADE WHERE id=:ID";            
        if(Maincontroller::doQuery($sql,array('NOME'=>$nome,'CPF'=>$cpf,'SETOR'=>$setor,'UNIDADE'=>$unidade,'ID'=>$id))){
            
        }        
    } 
    
    public static function delete($id) {                  
        $sql = "DELETE FROM servidores WHERE id=:ID";            
        return (Maincontroller::doQuery($sql,array('ID'=>$id),null,"Servidor não pode ser excluído, possui registros associados a ele (retornos).")) ;     
    } 
    
    public static function get($cpf=null,$name=null) {
       
        if($cpf != "null") {
            $where = " WHERE CPF='{$cpf}'";
        } 
        if($name != "null") {
            $where = " WHERE nome LIKE '{$name}%'";
        }
                
        $sql = "SELECT * FROM servidores {$where} ORDER BY nome";
        $ds = Maincontroller::doQuery($sql);
                       
        $arr = array();        
        while($rows = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $rows;
        }                
        return $arr;        
    }
}
