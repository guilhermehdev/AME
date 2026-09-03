<?php
/**
 * Description of Daodocumentos
 *
 * @author Guilherme
 */
class Daodocumentos {
    
    public static function save($tipo,$ordem,$ano,$data,$origem,$destino,$assunto,$conteudo) {
        $sql = "INSERT INTO documentos (tipo,ordem,ano,data,origem,destino,assunto,conteudo) VALUES(:TIPO,:ORDEM,:ANO,:DATA,:ORIGEM,:DESTINO,:ASSUNTO,:CONTEUDO)";
        if(Maincontroller::doQuery($sql,array("TIPO"=>$tipo,"ORDEM"=>$ordem,"ANO"=>$ano,"DATA"=> Functions::ENdateFormat($data),"ORIGEM"=>$origem,"DESTINO"=>$destino,"ASSUNTO"=>$assunto,"CONTEUDO"=>$conteudo)))
            return true;
    }
    
    public static function get($tipo,$id=null,$ordem="",$data="",$ano="",$assunto="") {
        if($id == null){        
            $p1 = ($ordem == "") ? "":"AND ordem={$ordem}";
            $p2 = ($data == "") ? "":"AND data='{$data}'";
            $p3 = ($ano == "") ? "":"AND ano={$ano}";
            $p4 = ($assunto == "") ? "":"AND assunto LIKE '%{$assunto}%'";
            
            $sql = "SELECT documentos.id,documentos.tipo,documentos.status,documentos.ordem,documentos.ano,documentos.data,documentos.assunto,documentos.conteudo,doc_destino.nome AS origem,doc_ac.nome AS destino FROM documentos
                    JOIN doc_ac ON documentos.destino = doc_ac.id
                    JOIN doc_destino ON documentos.origem = doc_destino.id
                    WHERE documentos.tipo=:TIPO {$p1} {$p2} {$p3} {$p4} ORDER BY tipo,ordem DESC";
            $ds = Maincontroller::doQuery($sql,array("TIPO"=>$tipo));
        } else {            
            $sql = "SELECT documentos.id,documentos.tipo,documentos.status,documentos.ordem,documentos.ano,documentos.data,documentos.assunto,documentos.conteudo,doc_destino.nome AS origem,doc_ac.nome AS destino FROM documentos
                    JOIN doc_ac ON documentos.destino = doc_ac.id
                    JOIN doc_destino ON documentos.origem = doc_destino.id
                    WHERE documentos.id=:ID ORDER BY tipo,ordem DESC";
            $ds = Maincontroller::doQuery($sql,array("ID"=>$id));
        }
                   
        $arr = array();        
        while($docs = $ds->fetch(PDO::FETCH_ASSOC)){
            $arr[] = $docs;
        }          
       
        return $arr;                  
    }
    
    public static function slctDestino() {
        $mc = new Maincontroller();        
        $destino = $mc->doSelect("id", "nome", "doc_destino", "", "ORDER BY nome");       
        return $destino; 
    }
    
    public static function slctAC() {
        $mc = new Maincontroller();        
        $ac = $mc->doSelect("id", "nome", "doc_ac", "", "ORDER BY nome");       
        return $ac; 
    }
    
}
