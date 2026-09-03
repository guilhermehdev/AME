<?php
/**
 * Description of Chat
 *
 * @author Guilherme
 */
class Chat implements IPrivateTO {
    
    public static function slctUser() {
        $user = Functions::session()['id'];
        $mc = new Maincontroller();        
        $arr = $mc->doSelect("id", "nome", "usuarios", "WHERE id != {$user}", "ORDER BY nome");       
        return $arr;
    }
    
    public function getUsers() {
        return self::slctUser();
    }
    
    public function saveMessages() {
        $data = filter_input_array(INPUT_POST,FILTER_DEFAULT);
        
        $msg = $data['msg'];
        $from = $data['from'];
        $to = $data['to'];
        
        $sql = "INSERT INTO chat (id_from,id_to,message) VALUES (:FROM,:TO,:MSG)";
        if(Maincontroller::doQuery($sql,array("FROM"=>$from,"TO"=>$to,"MSG"=>$msg))){
            echo $msg;
        }        
    }
    
    public function getMessages($params) {                      
        $from = Functions::cleanString($params[2]);
        $to = Functions::cleanString($params[3]);
        
                
        if($to !== '') {                        
            $sql = "SELECT * FROM chat WHERE id_from =:FROM AND id_to=:TO OR id_from =:TO AND id_to=:FROM ORDER BY sent_on";
            $ds = Maincontroller::doQuery($sql,array("FROM"=>$from,"TO"=>$to));
        } else {          
            $sql = "SELECT * FROM chat WHERE id_to=:FROM AND checked=0 ORDER BY sent_on";
            $ds = Maincontroller::doQuery($sql,array("FROM"=>$from)); 
        }    
                
        $arr = array();        
        while($row = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $row;
        } 
                   
        echo json_encode($arr);
        
        usleep(500000);
    }
    
    public function updateMessage() {
        $data = filter_input_array(INPUT_POST,FILTER_DEFAULT);
        
        $id = $data['id'];
        $checked = $data['checked'];    
        
        $sql = "UPDATE chat SET checked=:CHK WHERE id=:ID";
        if(Maincontroller::doQuery($sql,array("CHK"=>$checked,"ID"=>$id))){
            
        }
    }
    
    public function deleteMessage() {
        $data = filter_input_array(INPUT_POST,FILTER_DEFAULT);
                
        $id = $data['id'];     
        
        $getMessage = "SELECT message FROM chat WHERE id=:ID";
        $ds = Maincontroller::doQuery($getMessage,array("ID"=>$id)); 
        $msg = $ds->fetch(PDO::FETCH_ASSOC);
               
        $sql = "UPDATE chat SET message=:NEWMSG WHERE id=:ID";
        if(Maincontroller::doQuery($sql,array("NEWMSG"=>'<i>[mensagem excluída]</i> '.$msg['message'],"ID"=>$id))){
            echo $id;
        }
    }
    
    public function updateUserLog() {
        $data = filter_input_array(INPUT_POST,FILTER_DEFAULT);
        
        $log = $data['logged'];
        $id = Functions::session()['id'];   
        
        $sql = "UPDATE usuarios SET islogged=:BOOL WHERE id=:ID";
        if(Maincontroller::doQuery($sql,array("BOOL"=>$log,"ID"=>$id))){
            
        }
    }
    
    public function IsUserLoggedIn(){
        $session = Functions::session();                      
        if ($session != null) // check session here
            echo "true";
        else
            echo "false";
    }    
}
