<?php
/**
 * Description of CRUD
 *
 * @author guilh
 */
class CRUD {
    
    public static function insertORupdate($param) {       
        $f = new Functions();
        $formData = filter_input_array(INPUT_POST,FILTER_DEFAULT);
        $tableName = $param[2]; 
                       
        try { 
            
            foreach ($formData as $key => $value) {
            if($key == 'id') {
                $id = $key;
            }
            $keys[] = $key;

                if($f->checkDate($f->ENdateFormat($value))){
                    $values[] = $f->ENdateFormat($value);
                } elseif (Functions::isCurrency(Functions::BRLtoMYSQL($value)) ? $values[] = Functions::BRLtoMYSQL($value): null){

                } else {
                    $values[] = $value;
                }            
            }  
                       
           
            if(isset($id)) {                    
                for($i=0; $i < count($keys);$i++) {
                    $sql = "UPDATE {$tableName} SET `".$keys[$i]."`=:".$keys[$i]." WHERE id={$id}";
                    Maincontroller::doQuery($sql,array($keys[$i]=>$values[$i]));
                }
                return true;
            } else {
                $sql = "INSERT INTO {$tableName} (`".implode("`,`", $keys)."`) VALUES (:".implode(",:",$keys).")";
                $arrQuery = array();
               
                for($i=0; $i < count($keys);$i++) {
                    $arrQuery[$keys[$i]] = $values[$i]; 
                }
                                
                Maincontroller::doQuery($sql,$arrQuery);
                return true; 
            }
            
        } catch (Exception $exc) {
            //Functions::messages("msg",$exc->getCode(),"danger");
        }    
    }
    
    public static function select($table,$fields='*',$join_where=null,$orderBy=null,$optional=null) {
        if($fields != '*'){
            $arrFields = implode(",", $fields);
        } else {
            $arrFields = '*';
        }
        
        $sql = "SELECT {$arrFields} FROM {$table} {$join_where} {$orderBy} {$optional}";
        $ds = Maincontroller::doQuery($sql);
        
        //var_dump($ds);
                       
        $arr = array();        
        while($rows = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $rows;
        }
                         
        return $arr;
    }
    
    public static function delete($table,$id) {                
        $sql = "DELETE FROM {$table} WHERE id=:ID";
        if(Maincontroller::doQuery($sql,array("ID"=>$id))){
            return true;
        }
        
    }
    
}
