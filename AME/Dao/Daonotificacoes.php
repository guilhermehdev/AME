 <?php

/**
 * Description of Daonotificacoes
 *
 * @author Guilherme
 */
class Daonotificacoes {
    
    public static function getAvisos($idUser) {
        if($idUser != ''){
            $user = "AND avisos.id_usuario ={$idUser}";
            $sts = "";
        } else {
            $sts = "WHERE STATUS = 1";
        }
        
        $sql = "SELECT avisos.id,avisos.texto,avisos.data,avisos.status,usuarios.nome AS usuario,avisos.id_usuario AS iduser FROM avisos
        JOIN usuarios ON avisos.id_usuario = usuarios.id
        {$sts} {$user} ORDER BY DATA DESC";
        $ds = Maincontroller::doQuery($sql);
                               
        $arr = array();        
        while($rows = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $rows;
        }
                         
        return $arr;        
    }
    
    public static function saveaviso($iduser,$msg) {
                
        $sql = "INSERT INTO avisos (texto,id_usuario) VALUES(:MSG,:IDUSER)";
        if(Maincontroller::doQuery($sql,array('IDUSER'=>$iduser,'MSG'=> Functions::removeQuotes($msg)))){
            return true;
        }        
    }
    
    public static function updatests($id,$sts) {
        
        $sql = "UPDATE avisos SET status=:STS WHERE id=:ID";
        if(Maincontroller::doQuery($sql,array('ID'=>$id,'STS'=>$sts))){
            if($sts == 1){
                echo "Mensagem ativada no painel!";
            }else{
                echo "Mensagem desativada!";
            }
        }        
    }


    public static function delavisos($id) {
        
        $sql = "DELETE FROM avisos WHERE id=:ID";
        if(Maincontroller::doQuery($sql,array('ID'=>$id))){
            return true;
        }        
    }
    
    public static function confirmnewmessage($idmsg,$iduser) {
        
        $sql = "INSERT INTO avisos_visualizados (id_aviso,id_usuario) VALUES (:IDMSG,:IDUSER)";
        if(Maincontroller::doQuery($sql,array('IDMSG'=>$idmsg,'IDUSER'=>$iduser))){
            return true;
        } 
        
    }
    
    public static function checknewmessage($idmsg,$iduser) {
               
        $sql = "SELECT COUNT(id) AS n FROM avisos_visualizados WHERE id_aviso={$idmsg} AND id_usuario={$iduser}";
        $ds = Maincontroller::doQuery($sql);
        
        $arr = array();        
        while($rows = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $rows; 
        }
                                        
        return $arr;                  
    }


    public static function getAgendas($data) { 
                       
        $sql = "SELECT * FROM grades WHERE data='{$data}' AND id_unidade=1 ORDER BY especialidade";
        $ds = Maincontroller::doQuery($sql);
                               
        $arr = array();        
        while($rows = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $rows; 
        } 
                                
        return $arr;        
    } 
    
    public static function gradeSemanal($idServidor,$idDia,$idEspec) {
                       
        $sql = "SELECT salas.numero AS sala,periodos.descricao AS periodo,usuarios.nome AS responsavel 
        FROM grade_semanal_medicos 

        JOIN salas ON salas.id = grade_semanal_medicos.id_sala
        JOIN periodos ON periodos.id = grade_semanal_medicos.id_periodo
        JOIN usuarios ON usuarios.id = grade_semanal_medicos.id_responsavel_agenda

        WHERE id_servidor={$idServidor} AND id_dias={$idDia} AND id_espec={$idEspec}";
        $ds = Maincontroller::doQuery($sql);  
                               
        $arr = array();        
        while($rows = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $rows; 
        } 
                                
        return $arr;        
    }  
    
}
