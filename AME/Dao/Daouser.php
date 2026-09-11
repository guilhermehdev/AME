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
    
    public static function get($cpf=null,$name=null,$id=null) {
        $where = "";

        if($cpf != null && $cpf != "null") {
            $where = " WHERE CPF='{$cpf}'";
        } 
        if($name != null && $name != "null") {
            $where = " WHERE nome LIKE '{$name}%'";
        }
        if($id != null && $id != "null") {
            $where = " WHERE id ={$id}";
        }
                
        $sql = "SELECT * FROM usuarios {$where} ORDER BY nome";
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
        $email = isset($params[6]) ? Functions::removeQuotes($params[6]) : null;
        $nasc = isset($params[7]) ? Functions::removeQuotes($params[7]) : null;
                      
        if ($params[5] == "true"){
            $cad = 1; 
        } else {
            $cad = 0; 
        }       
        
        $sql = "INSERT INTO usuarios (nome, CPF, pass, cadastros, ativo, retornos, notificacao, impressos, email, dtnasc) VALUES (:NOME,:CPF,:PASS,:CAD,:ATIVO,:RET,:NOT,:IMP,:EMAIL,:NASC)";            
        if(Maincontroller::doQuery($sql,array('NOME'=>$nome,'CPF'=>$cpf,'PASS'=>$pass,'CAD'=>$cad,'ATIVO'=>'1','RET'=>'1','NOT'=>'1','IMP'=>'1','EMAIL'=>$email,'NASC'=>$nasc))){
           
        }        
    }     
    
    public function update($params) {
        $id = $params[2];
        $nome = Functions::removeQuotes(Functions::uppercase($params[3]));
        $cpf = Functions::cleanString($params[4]);
        $pass = $params[5];
        $email = isset($params[7]) ? Functions::removeQuotes($params[7]) : null;
        $nasc = isset($params[8]) ? Functions::removeQuotes($params[8]) : null;
        
        if ($params[6] == "true"){
            $cad = 1; 
        } else {
            $cad = 0; 
        }
               
        $sql = "UPDATE usuarios SET nome=:NOME, CPF=:CPF, pass=:PASS, cadastros=:CAD, email=:EMAIL, dtnasc=:NASC WHERE id=:ID";            
        if(Maincontroller::doQuery($sql,array('NOME'=>$nome,'CPF'=>$cpf,'PASS'=>$pass,'CAD'=>$cad,'EMAIL'=>$email,'ID'=>$id,'NASC'=>$nasc))){
            
        }        
    } 
    
    public static function recoverPass($id, $senhaProvisoria, $novaSenha) {
        $sql = "SELECT senha_provisoria FROM usuarios WHERE id=:ID";
        $ds = Maincontroller::doQuery($sql, array('ID' => $id));
        $row = $ds->fetch(PDO::FETCH_ASSOC);

        if (!$row || $row['senha_provisoria'] === null || $row['senha_provisoria'] != $senhaProvisoria) {
            return false;
        }

        $sql = "UPDATE usuarios SET pass=:PASS, senha_provisoria=NULL WHERE id=:ID";
        return Maincontroller::doQuery($sql, array('PASS' => $novaSenha, 'ID' => $id));
    }

    public static function gerarTokenRecuperacao($cpf, $email, $dtnasc = null) {
        $sql = "SELECT id, email, dtnasc FROM usuarios WHERE CPF=:CPF";
        $ds = Maincontroller::doQuery($sql, array('CPF' => $cpf));
        $row = $ds->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return array('erro' => 'cpf_invalido');
        }

        if (empty($row['email'])) {
            // primeiro cadastro de e-mail: exige confirmar CPF + data de nascimento,
            // para que não baste só saber o CPF pra sequestrar a conta
            $dtnascInformada = $dtnasc ? date('Y-m-d', strtotime($dtnasc)) : null;

            if (!$dtnascInformada || $dtnascInformada != $row['dtnasc']) {
                return array('erro' => 'dtnasc_invalida');
            }

            $sql = "UPDATE usuarios SET email=:EMAIL WHERE id=:ID";
            Maincontroller::doQuery($sql, array('EMAIL' => $email, 'ID' => $row['id']));
        } elseif ($row['email'] != $email) {
            return array('erro' => 'email_invalido');
        }

        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+2 hours'));

        $sql = "UPDATE usuarios SET token_recuperacao=:TOKEN, token_expira=:EXPIRA WHERE id=:ID";
        Maincontroller::doQuery($sql, array('TOKEN' => $token, 'EXPIRA' => $expira, 'ID' => $row['id']));

        return array('id' => $row['id'], 'token' => $token, 'email' => $email);
    }

    public static function validarToken($token) {
        $sql = "SELECT id, token_expira FROM usuarios WHERE token_recuperacao=:TOKEN";
        $ds = Maincontroller::doQuery($sql, array('TOKEN' => $token));
        $row = $ds->fetch(PDO::FETCH_ASSOC);

        if (!$row || strtotime($row['token_expira']) < time()) {
            return false;
        }

        return $row['id'];
    }

    public static function redefinirSenhaPorToken($token, $novaSenha) {
        $id = self::validarToken($token);

        if (!$id) {
            return false;
        }

        $sql = "UPDATE usuarios SET pass=:PASS, token_recuperacao=NULL, token_expira=NULL WHERE id=:ID";
        return Maincontroller::doQuery($sql, array('PASS' => $novaSenha, 'ID' => $id));
    }
    
    public static function delete($id) {                  
        $sql = "DELETE FROM usuarios WHERE id=:ID";            
        return (Maincontroller::doQuery($sql,array('ID'=>$id),null,"Usuário não pode ser excluído, possui registros associados a ele (retornos).")) ;     
    } 
}