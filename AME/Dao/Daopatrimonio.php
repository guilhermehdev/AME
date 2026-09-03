<?php
/**
 * Description of Daopatrimonio
 *
 * @author Guilherme
 */
class Daopatrimonio {
    
    public static function slctSalas($id) {
        $mc = new Maincontroller(); 
                        
        $salas = $mc->doSelect("id", "descricao", "salas", "WHERE id_unidade={$id[2]}", "ORDER BY numero");
                        
        echo $salas; 
    }
    
    public static function slctItens() {
        $mc = new Maincontroller();
        
        $itens = $mc->doSelect("id", "descricao", "itens", "", "ORDER BY descricao");
       
        return $itens; 
    }
    
    public static function getUnidades($idUnidade) {
       
        $sql = "SELECT id,descricao FROM unidades WHERE id=:IDUNIDADE";
        $ds = Maincontroller::doQuery($sql, array('IDUNIDADE'=>$idUnidade));
        
        $arr = array();        
        while($rows = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $rows;
        }                
        return $arr;
    }
    
    public static function getPatrimonio($patrimonio) {
       
        $sql = "SELECT unidades.descricao AS unidade, salas.descricao AS sala, itens.descricao AS item, inventario.patrimonio AS patrimonio 
            FROM inventario JOIN salas ON inventario.id_sala = salas.id
            JOIN unidades ON unidades.id = inventario.id_unidade
            JOIN itens ON itens.id = inventario.id_item
            WHERE patrimonio =:PATRIMONIO";
        $ds = Maincontroller::doQuery($sql, array('PATRIMONIO'=>$patrimonio));
        
        $arr = array();        
        while($rows = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $rows;
        }                
        return $arr;
    }
    
    public static function savePatrimonio($idUnidade,$idSalas,$idItens,$patrimonio,$estado,$qtd) {        
        if($patrimonio != '' && $patrimonio != 0){
            $qtd = 1;            
        } else {
            $patrimonio = NULL;
            if($qtd == '' || !$qtd ){
                $qtd = 1;
            }
        }
               
        $sql = "INSERT INTO inventario (id_unidade,id_sala,id_item,patrimonio,estado,quantidade) VALUES (:IDUNIDADE,:IDSALA,:IDITEM,:PATRIMONIO,:ESTADO,:QTD)";

        $res = Maincontroller::doQuery($sql, array('IDUNIDADE'=>$idUnidade,'IDSALA'=>$idSalas,'IDITEM'=>$idItens,'PATRIMONIO'=>$patrimonio,'ESTADO'=>$estado,'QTD'=>$qtd));
             
        return $res;               
    }
    
    public static function getSalas($idUnidade) {
        
        $sql = "SELECT DISTINCT salas.id AS id,salas.numero AS sala,salas.descricao 
        FROM inventario
        JOIN salas ON salas.id = inventario.id_sala 
        WHERE inventario.id_unidade =:IDUNIDADE 
        ORDER BY numero";
        
        $ds = Maincontroller::doQuery($sql,array('IDUNIDADE'=>$idUnidade));
        
        $arr = array();        
        while($rows = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $rows;
        }                
        return $arr;
    }
    
    public static function getSala($idSala) {
        
        $sql = "SELECT DISTINCT id, numero, descricao 
        FROM salas        
        WHERE id =:IDSALA
        ORDER BY id";
        
        $ds = Maincontroller::doQuery($sql,array('IDSALA'=>$idSala));
        
        $arr = array();        
        while($rows = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $rows;
        }                
        return $arr;
    }
    
    public static function getItens($idUnidade,$idSalas) {
        
        $sql = "SELECT *    
        FROM itens_salas                
        WHERE id_unidade =:IDUNIDADE 
        AND id_sala =:IDSALAS 
        ORDER BY patrimonio";
        
        $ds = Maincontroller::doQuery($sql,array('IDUNIDADE'=>$idUnidade,'IDSALAS'=>$idSalas));
        
        $arr = array();        
        while($rows = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $rows;
        }                
        return $arr;
    }
    
    public static function delItem($id) {        
        $sql = "DELETE FROM inventario WHERE id=:ID";
        Maincontroller::doQuery($sql, array('ID'=>$id));
        return true;
    }
    
}
