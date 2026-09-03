<?php
/**
 * Description of Retornos
 *
 * @author Guilherme
 */
class Retornos implements IPrivateTO {
    
    public function index() {
        
        if(AppController::checkSession()){
            $view = new TGui("retornos");
            $view->addData("title", "Cadastrar retorno");
            $view->renderize(APP_VIEW);
        } else {
            header("location: ".URL."Loginadm/login");
        }       
    }
    
    public function consulta() {
        
        if(AppController::checkSession()){
            $view = new TGui("consultaretornos");            
            $view->renderize(APP_VIEW);
        } else {
            header("location: ".URL."Loginadm/login");
        }       
    }
    
    public function save() {
        $post = filter_input_array(INPUT_POST,FILTER_DEFAULT);
        $arr = array(); 
        
        foreach ($post as $value) {
            $arr[] = $value;                          
        }
                
        $idPaciente = $arr[0];
        $idUSAFA = $arr[3];
        $dataConsulta = $arr[4];
        $idProfissional = $arr[5];
        $desfecho = $arr[6];
        $tempoRetorno = $arr[7];
        $dataRetorno = $arr[8];        
        
        if($dataRetorno != '') {
            $conc = 1;
        } else {
            $conc = 0;
        }   
        
        if ($desfecho == 0) {           
            $tr = "";            
        } else {           
            $tr = $tempoRetorno;            
        }
        
        if(Daoretornos::save($idPaciente, $idUSAFA,$dataConsulta, $idProfissional, $desfecho, $tr, $dataRetorno, $this->setAlerts($dataConsulta, $tempoRetorno),$conc)){
            //Functions::messages ("modal", "Salvo com sucesso!");
        }
    } 
    
    public function edit($param) {
         
        $id = $param[2];    
        $idUSAFA =  $param[3]; 
        $dataConsulta = $param[4]; 
        $medico = $param[5];
        $desfecho = $param[6];
        $tempoRetorno = Functions::cleanString($param[7]);   
        $dataRetorno = Functions::cleanString($param[8]);  
      
        if($dataRetorno != '') {
            $conc = 1;
        } else {
            $conc = 0;
        } 
        
        if ($desfecho == 0) {           
            $tr = "";            
        } else {           
            $tr = $tempoRetorno;            
        }
               
        if(Daoretornos::edit($id,$idUSAFA,$dataConsulta,$medico,$desfecho,$tr,$dataRetorno,$this->setAlerts($dataConsulta, $tempoRetorno),$conc)){
            
        }
    }
    
    public function get($param) {
        $idPac = $param[2];
                
        $retornos = Daoretornos::get($idPac);
        $oci = Daoretornos::getOCI($idPac);
        
        $view = new TGui("retornos");      
        $view->addData("retornos", $retornos);
        $view->addData("oci", $oci);
        $view->renderize(APP_VIEW_LIST,true);
        
    }
    
     public function alerts() { 
        if(AppController::checkSession()){
            $view = new TGui("alertas");  
            $view->addData("title", 'Retornos pendentes');
            $view->renderize(APP_VIEW); 
        } else {
            header("location: ".URL."Loginadm/login");
        }
    } 
    
     public static function getAlerts($p) { 
         $mes = $p[2];
         $ano = $p[3];
         $idservidor = $p[4];
         
         $alertas = Daoretornos::getAlerts($mes, $ano,$idservidor);
         
        $view = new TGui("Lalertas");      
        $view->addData("alertas", $alertas);
        $view->addData("mes", $mes);
        $view->addData("ano", $ano);
        $view->renderize(APP_VIEW_LIST,true);
      
     }    
     
    public function updateDataRetorno($p) {
         $id = $p[2];
         $data = $p[3];  
        
         if(Functions::cleanString($data) == ''){
             Daoretornos::updateDataRetorno($id, null, 0);
         } else {
             Daoretornos::updateDataRetorno($id, $data, 1);
         }         
    }
    
    public function updateObs($p) {
        $id = $p[2];
        $obs = Functions::uppercase(Functions::cleanString($p[3]));  
     
        Daoretornos::updateObs($id, $obs);                 
    }
    
     public static function setAlerts($consulta,$retorno_em) {  
        //$retornos = Daoretornos::getAll();           
        //foreach ($retornos as $value) {
            $ret ="";
            $tmp = Functions::ENdateFormat($retorno_em);
            switch ($tmp) {
                case "1 SEMANA":
                    $ret =  date('Y-m-d', strtotime('+7 days', strtotime(Functions::ENdateFormat($consulta))));                   
                    break;   
                 case "2 SEMANAS":
                    $ret =  date('Y-m-d', strtotime('+14 days', strtotime(Functions::ENdateFormat($consulta))));                  
                    break; 
                 case "3 SEMANAS":
                    $ret =  date('Y-m-d', strtotime('+21 days', strtotime(Functions::ENdateFormat($consulta))));                    
                    break; 
                 case "4 SEMANAS":
                    $ret =  date('Y-m-d', strtotime('+28 days', strtotime(Functions::ENdateFormat($consulta))));                    
                    break; 
                 case "1 MÊS":
                    $ret =  date('Y-m-d', strtotime('+1 months', strtotime(Functions::ENdateFormat($consulta))));                  
                    break; 
                 case "2 MESES":
                    $ret =  date('Y-m-d', strtotime('+2 months', strtotime(Functions::ENdateFormat($consulta))));                    
                    break; 
                 case "3 MESES":
                    $ret =  date('Y-m-d', strtotime('+3 months', strtotime(Functions::ENdateFormat($consulta))));                    
                    break; 
                 case "4 MESES":
                    $ret =  date('Y-m-d', strtotime('+4 months', strtotime(Functions::ENdateFormat($consulta))));                   
                    break; 
                 case "5 MESES":
                    $ret =  date('Y-m-d', strtotime('+5 months', strtotime(Functions::ENdateFormat($consulta))));                    
                    break; 
                 case "6 MESES":
                    $ret =  date('Y-m-d', strtotime('+6 months', strtotime(Functions::ENdateFormat($consulta))));                   
                    break; 
                 case "7 MESES":
                    $ret =  date('Y-m-d', strtotime('+7 months', strtotime(Functions::ENdateFormat($consulta))));                   
                    break; 
                 case "8 MESES":
                    $ret =  date('Y-m-d', strtotime('+8 months', strtotime(Functions::ENdateFormat($consulta))));                   
                    break; 
                 case "9 MESES":
                    $ret =  date('Y-m-d', strtotime('+9 months', strtotime(Functions::ENdateFormat($consulta))));                   
                    break; 
                 case "10 MESES":
                    $ret =  date('Y-m-d', strtotime('+10 months', strtotime(Functions::ENdateFormat($consulta))));                   
                    break; 
                 case "11 MESES":
                    $ret =  date('Y-m-d', strtotime('+11 months', strtotime(Functions::ENdateFormat($consulta))));                 
                    break; 
                 case "1 ANO":
                    $ret =  date('Y-m-d', strtotime('+1 year', strtotime(Functions::ENdateFormat($consulta))));                         
                    break;  
                 case "RETORNO COM EXAMES":
                    $ret =  null;                         
                    break;  
                case "":
                    $ret =  null;                         
                    break;  
            }              
            return $ret;
             //Daoretornos::edit($value['id'], $value['consulta'], $value['id_servidor'], 1, $value['retorno_em'],null,$ret,0); 
        //}              
    }
          
    public function del($param) {
        $id = $param[2];        
        Daoretornos::del($id);
    }
    
    public function getconsulta($param) {
        $id = $param[2];
        $nasc = $param[3];
                
        $pac = Daopacientes::get($id, $nasc,'null'); 
        
        foreach ($pac as $p) {
            $retornos[] = Daoretornos::get($p['id']);
        }
                
        $view = new TGui("Lconsultaretornos");      
        $view->addData("retornos", $retornos);
        $view->addData("paciente", $pac);
        $view->renderize(APP_VIEW_LIST,true);
    }
    
    public function formedit($param) {        
        if(AppController::checkSession()){
            $view = new TGui("retornos");      
            $view->addData("data", $param);        
            $view->renderize(APP_VIEW,true); 
        } else {
            header("location: ".URL."Loginadm/login");
        }
        
    }
}
