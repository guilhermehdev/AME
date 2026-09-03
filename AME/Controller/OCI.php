<?php
/**
 * Description of Documentos
 *
 * @author Guilherme
 */
class OCI {
    
     public function home(){     
        $view = new TGui("homeoci");
        $view->addData("title", "APAC - OCI");            
        $view->renderize(APP_VIEW);
    }
    
    public function impressos(){               
        $view = new TGui("printoci");
        $view->addData("title", "Imprimir guias APAC");
        $view->renderize(APP_VIEW);
    } 
    
    public function addToList($params){               
       $medico =  $params[2];     
       $idPac = $params[3];  
       $data = $params[4];  
       $procedPrincipal = $params[5];  
       $cidPrincipal = Functions::cleanString($params[6]); 
       $cidSecundario = Functions::cleanString($params[7]);  
       
       Daooci::saveToList($medico, $idPac, $data, $procedPrincipal, $cidPrincipal, $cidSecundario);
    }  
    
     public function loadFila($params){          
        $data = $params[2];  
        $medico = $params[3]; 
        $proced = $params[4];                 
        $fila = Daooci::loadFila($data, $medico, $proced);        
        $view = new TGui("LfilaOCI");
        $view->addData("fila", $fila);
        $view->renderize(APP_VIEW_LIST,true);
    }
    
    public function loadProcedSec($params){          
        $data = $params[2];     
        $idPac = $params[3];   
        $medico = $params[4];         
        $procedsSecs = Daooci::loadProcedSec($data, $idPac,$medico);        
        
        if (isset($params[5])) { 
        $listMode = $params[5];     
        }
          if (isset($params[6])) { 
        $status = $params[6];     
        }
        if($listMode == 1)  {
            $view = new TGui("Lprocedsecundariolist");
            $view->addData("proceds", $procedsSecs);
            $view->addData("idPac", $idPac);
            $view->addData("sts", $status);
            $view->renderize(APP_VIEW_LIST,true);
        } else {
            $view = new TGui("Lprocedsecundario");
            $view->addData("proceds", $procedsSecs);
            $view->renderize(APP_VIEW_LIST,true);
        }
    }
    
    public function delProced($params){          
        $id = $params[2];
        $res = Daooci::delProced($id);
    }
    
     public function delFila($params){          
        $id = $params[2];
        $res = Daooci::delFila($id);
    }
    
    public function getCID($param){               
        $idOCI = $param[2];           
        $cids = Daooci::getCID($idOCI);
        echo $cids;
    }  
    
     public function getProcedSec($param){               
        $idOCI = $param[2];           
        $secs = Daooci::getSec($idOCI);
        echo $secs;
    }
}
