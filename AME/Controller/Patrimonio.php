<?php

/**
 * Description of Patrimonio
 *
 * @author Guilherme
 */
class Patrimonio {
    
    public function index() {
        session_start();
        if(isset($_SESSION['adm'])){
            $view = new TGui("patrimonio");
            $view->addData("title", "Patrimônio");
            $view->renderize(APP_VIEW); 
        } else {
            header("location: ".URL."Loginadm/login");
        }
    }
    
    public function getSalas($param) {
        $idUnidade = $param[2];
        
        $data = Daopatrimonio::getSalas($idUnidade);
        
        $view = new TGui("salas");
        $view->addData("data", $data);
        $view->addData("idUnidade", $idUnidade);
        $view->renderize(APP_VIEW_LIST,true);         
    }
    
    public function getItens($params) {
        $idUnidade = $params[2];
        $idSala = $params[3];
        
        $data = Daopatrimonio::getItens($idUnidade,$idSala);
        
        if(count($data) > 0 ){
            $view = new TGui("itens");
            $view->addData("data", $data);       
            $view->renderize(APP_VIEW_LIST,true);  
        }       
    }
       
    public function getItemByPatrimonio($param) {        
        $patrimonio = $param[2];                
                
        $view = new TGui("busca_patrimonio");         
           
        $results = Daopatrimonio::getPatrimonio($patrimonio);           
        $view->addData("dados", $results);
        $view->renderize(APP_VIEW_MODAL,true);
        
    }
    
    public function save() {           
        $post = filter_input_array(INPUT_POST,FILTER_DEFAULT);
        $arr = array(); 
        
        foreach ($post as $value) {
            $arr[] = $value;                          
        }
                        
        $idUnidade = $arr[0];
        $idSalas = $arr[1];
        $idItens = $arr[2];
        $patrimonio = $arr[3];
        $estado = $arr[4];
        $qtd = $arr[5];
              
        $res = Daopatrimonio::savePatrimonio($idUnidade, $idSalas, $idItens, $patrimonio, $estado, $qtd);          
        
        if($res == false){
            Functions::messages ("modal", "Patrimônio já cadastrado!","danger");  
        } else {
            Functions::messages ("modal", "Salvo!","success");  
        }            
    }
    
    public function delItem($param) {
        $id = $param[2];        
        if(Daopatrimonio::delItem($id)){
           // Functions::messages ("modal", "Excluido com sucesso!");
        }
    }
    
}
