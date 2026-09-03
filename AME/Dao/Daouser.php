<?php
/**
 * Description of Daouser
 *
 * @author Gui
 */
class Daouser {
    
    public static function chkUser($id,$pass) {
        $mc = new Maincontroller();        
        $sql = "SELECT * FROM usuarios WHERE pass=:PASS AND id=:ID AND ativo=1";
        $ds = $mc->doQuery($sql,array('PASS'=>$pass,'ID'=>$id));         
        $user = $ds->fetch(PDO::FETCH_ASSOC);                      
        return $user ;
    }
    
    public static function get($cpf=null,$name=null) {
       
        if($cpf != "null") {
            $where = " WHERE CPF='{$cpf}'";
        } 
        if($name != "null") {
            $where = " WHERE nome LIKE '{$name}%'";
        }
                
        $sql = "SELECT id, nome, CPF, pass, cadastros FROM usuarios {$where} ORDER BY nome";
        $ds = Maincontroller::doQuery($sql);
                       
        $arr = array();        
        while($rows = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $rows;
        }                
        return $arr;        
    }
    
    public function save($params) {
        $nome = Functions::removeQuotes(Functions::uppercase($params[2]));
        $cpf = Functions::cleanString($params[3]);
        $pass = Functions::cleanString($params[4]); 
                      
        if ($params[5] == "true"){
            $cad = 1; 
        } else {
            $cad = 0; 
        }       
        
        $sql = "INSERT INTO usuarios (nome, CPF, pass, cadastros, ativo, retornos, notificacao, impressos) VALUES (:NOME,:CPF,:PASS,:CAD,:ATIVO,:RET,:NOT,:IMP)";            
        if(Maincontroller::doQuery($sql,array('NOME'=>$nome,'CPF'=>$cpf,'PASS'=>$pass,'CAD'=>$cad,'ATIVO'=>'1','RET'=>'1','NOT'=>'1','IMP'=>'1'))){
           
        }        
    }     
    
    public function update($params) {
        $id = $params[2];
        $nome = Functions::removeQuotes(Functions::uppercase($params[3]));
        $cpf = Functions::cleanString($params[4]);
        $pass = $params[5];
        
        if ($params[6] == "true"){
            $cad = 1; 
        } else {
            $cad = 0; 
        }
               
        $sql = "UPDATE usuarios SET nome=:NOME, CPF=:CPF, pass=:PASS, cadastros=:CAD WHERE id=:ID";            
        if(Maincontroller::doQuery($sql,array('NOME'=>$nome,'CPF'=>$cpf,'PASS'=>$pass,'CAD'=>$cad,'ID'=>$id))){
            
        }        
    } 
    
    public function recoverPass(){
        
    }
    
    public static function delete($id) {                  
        $sql = "DELETE FROM usuarios WHERE id=:ID";            
        return (Maincontroller::doQuery($sql,array('ID'=>$id),null,"Usuário não pode ser excluído, possui registros associados a ele (retornos).")) ;     
    } 
}
