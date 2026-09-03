<?php

/**
 * Description of Maincontroller
 *
 * @author Guilherme
 */

class Maincontroller implements IPrivateTO {
    
    public function index() {
        $da = new Daoagendas();
        
        $view = new TGui("index");
        $view->renderize(APP_VIEW);

        $da->checkDateAgendas();
    }
    
    public function getConfig($param) {
        
        if($param != null) {
            
            $id = $param;
            
            $sql = "SELECT unidades.id AS idUnidade,unidades.descricao AS unidade,config.unidade_endereco AS end,contatos.numero AS contato,session_time AS sessao
            FROM config

            JOIN unidades ON config.unidade_id = unidades.id
            LEFT JOIN contatos ON config.unidade_id_contato = contatos.id_pessoa
            
            WHERE config.unidade_id =:ID
            ";

            $ds = $this->doQuery($sql,array('ID'=>$id));

            $arrConfig = array();        
            while($conf = $ds->fetch(PDO::FETCH_ASSOC)){
                $arrConfig[] = $conf;
            }

            return $arrConfig;
        
        }
    }
    
    public function hidedash() {
        $view = new TGui("index");        
        $view->renderize(APP_VIEW,true);
    }
    
    public static function doQuery($sql,$params = null,$header = null,$msg = null) {      
        $serverFilter = filter_input_array(INPUT_SERVER,FILTER_DEFAULT);       
        $conn = Conn::getConn();
        $stm =  $conn->prepare($sql); 
        
        if($params != null){        
            foreach ($params as $key => &$value) {
                $stm->bindParam(':'.$key,$value, PDO::PARAM_STR);
            }        
        }
       
        try {
            
            $stm->execute();
            
            if($header != null && $header != 'back'){
                header("location: ". URL.$header);               
            } else if($header == 'back'){
                header("location: ". $serverFilter['HTTP_REFERER']);  
            }
            return $stm;            
            
        } catch (Exception $exc) { 
            if($msg != null) {
              //  echo $msg;
                return $msg;
            }else{
            //    echo $exc->getMessage();
               return false;
            }
        } 
    }
                       
    public function doSelect($id,$value,$table,$queryparams,$order){
        $main = new Maincontroller();              
        $arr = array();
        
        try {
             $sql = "SELECT DISTINCT " . $id .  " AS id," . $value . " FROM " . $table . " " . $queryparams . " " . $order;        
      
            $ds = $main->doQuery($sql);
        
            while ($data = $ds->fetch(PDO::FETCH_ASSOC)) {
                $arr[] = $data;                       
            }    
                       
            return json_encode($arr); 
            
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }

    } 
    
    public function objectArray($ds,$class) {
        $arrData = array();        
        while($data = $ds->fetchObject($class)){
            $arrData[] = $data;
        }
        
        return $arrData;
    }
    
    public function getLogged() {
        if (isset($_SESSION['usuarioObj'])){    
            $user = $_SESSION['usuarioObj'];
            if ($user instanceof User){
                $logged = $user->getNome();
                return $logged;
            }        
        }
    }
    
    public function appoMenu($nSelects,$slct2Method,$slct3Method,$slct1Name = "slctPro",$slct2Name = "slctEspec",$slct3Name = "slctData",$slct2Container = "",$slct3Container = "main", $slct2redirect_url = "", $slct2redirect_params = "", $slct3redirect_url = "", $slct3redirect_params = "",$slct2redirect_target = "", $slct3redirect_target = "") { 
     
        //$json,  $slctclass  ,$slctname  ,$slctid  ,$data_params  ,$data_value  ,$data_display  ,$redirect  ,$redirect_url  ,$redirect_params  ,$target_display  ,$href  ,$required  ,$msgRequired  ,$opt_selected = null
        
        $f = new Functions();
        $sPro = $this->doSelect("profissionais.id", "profissionais.nome","profissionais "
                . "JOIN `profissional-especialidade` ON `profissional-especialidade`.id_Profissional = profissionais.id "
                . "JOIN `especs-procedimentos` ON `profissional-especialidade`.id_Especialidade = `especs-procedimentos`.id_Especialidade "
                . "GROUP BY profissionais.id","", ""
                . "ORDER BY `profissional-especialidade`.id_Especialidade,profissionais.nome");
        
        $selectPro = $f->select($sPro, "select call-data", $slct1Name , $slct1Name, "\"id\":\"{$slct1Name}\"", "id", "nome", $slct2Name, "" , "","especialidade","Controlvagas/selectespec", true, "Campo necessário");
        
        $selectEspec = $f->select("", "select call-data",$slct2Name ,$slct2Name , "\"p1\":\"{$slct1Name}\",\"p2\":\"{$slct2Name}\"", "id", "especialidade", $slct3Name, $slct2redirect_url, $slct2redirect_params, $slct2Container, $slct2Method, true, "Campo necessário", $slct2redirect_target);
        
	$selectData = $f->select("", "select-data call-data", $slct3Name, $slct3Name, "\"p1\":\"{$slct1Name}\",\"p2\":\"{$slct2Name}\",\"p3\":\"{$slct3Name}\"", "data", "data", "reload", $slct3redirect_url, $slct3redirect_params, $slct3Container, $slct3Method, true, "Campo necessário", $slct3redirect_target);
             
        if ($nSelects == 1) {
            return "
                    <tr>
                        <td>                                        
                            <h5 class=\"text-primary\"><span class=\"glyphicon glyphicon-briefcase text-primary\"></span> Profissional</h5>                          
                            {$selectPro}                                     
                        </td>
                    </tr>                   
                    ";
        } elseif ($nSelects == 2) {
            return "
                    <tr>
                        <td>                                        
                            <h5 class=\"text-primary\"><span class=\"glyphicon glyphicon-briefcase text-primary\"></span> Profissional</h5>                          
                            {$selectPro}                                     
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h5 class=\"text-primary\"><span class=\"glyphicon glyphicon-education text-primary\"></span> Especialidade</h5>
                            {$selectEspec}
                        </td>
                    </tr>                
                   ";
        } elseif ($nSelects == 3) {
            return "
                    <tr>
                        <td>                                        
                            <h5 class=\"text-primary\"><span class=\"glyphicon glyphicon-briefcase text-primary\"></span> Profissional</h5>                          
                            {$selectPro}                                     
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h5 class=\"text-primary\"><span class=\"glyphicon glyphicon-education text-primary\"></span> Especialidade</h5>
                            {$selectEspec}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h5 class=\"text-primary\"><span class=\"glyphicon glyphicon-calendar text-primary\"></span> Data</h5>
                            {$selectData}
                        </td>
                    </tr>
                    ";
        }
    }
}