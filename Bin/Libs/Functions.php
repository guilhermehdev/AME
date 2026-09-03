<?php

class Functions {
    
    function debugToConsole($msg) { 
        echo "<script>console.log(".json_encode($msg).")</script>";
    }
    
    public static function session(){
        session_start();
        return isset($_SESSION['adm']) ? $_SESSION['adm'] : false;
    }
       
    function array_group_by(array $array, $key)
    {
        if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key) ) {
                trigger_error('array_group_by(): The key should be a string, an integer, or a callback', E_USER_ERROR);
                return null;
        }
        $func = (!is_string($key) && is_callable($key) ? $key : null);
        $_key = $key;
        // Load the new array, splitting by the target key
        $grouped = [];
        foreach ($array as $value) {
                $key = null;
                if (is_callable($func)) {
                        $key = call_user_func($func, $value);
                } elseif (is_object($value) && isset($value->{$_key})) {
                        $key = $value->{$_key};
                } elseif (isset($value[$_key])) {
                        $key = $value[$_key];
                }
                if ($key === null) {
                        continue;
                }
                $grouped[$key][] = $value;
        }
        // Recursively build a nested grouping if more parameters are supplied
        // Each grouped array value is grouped according to the next sequential key
        if (func_num_args() > 2) {
                $args = func_get_args();
                foreach ($grouped as $key => $value) {
                        $params = array_merge([ $value ], array_slice($args, 2, func_num_args()));
                        $grouped[$key] = call_user_func_array('array_group_by', $params);
                }
        }
        return $grouped;
    }
    
    public function sessionTime() {
        $seconds = '';
        $mc = new Maincontroller();      
        $conf = $mc->getConfig(CODUN);
        
        if(!empty($conf)) {

            foreach ($conf as $value) {
               $st = $value['sessao'];
            }
            
        } else {
            $st = 120;
        } 

        if(isset($_SESSION)){
            $seconds = time() - $_SESSION['lastAccess']; 

            if($seconds > $st){
                echo "<meta HTTP-EQUIV='Refresh' CONTENT='0;URL=" .URL . "Controllogin/login'>";                           
                @session_destroy();
            } else {              
                $_SESSION['lastAccess'] = time(); 
            }
            return $st;
        }
        
    }
        
        
    public function mainUrl(){        
        return URL;
    }
    
    public function currentUrl(){
        $dominio= $_SERVER['HTTP_HOST'];
        $url = "http://" . $dominio. $_SERVER['REQUEST_URI'];
        return $url;
    }
    
    public static function messages($action,$content,$typeMsg = "primary") {
        $arr = array('action'=>$action,'content'=>$content,'typemsg'=>$typeMsg);        
        echo json_encode($arr);
	exit();
    }    
    
    public function getAppDir($param) {
        $checkDir = new DirectoryIterator($param);
        $arrApps = array();

        foreach ($checkDir as $value) {
            if ($value->isDir() && $value != '.' && $value != '..' && $value != 'Bin' && $value != 'nbproject') {
                $arrApps[] = $value->getFilename();
            }    
        }
        return $arrApps;
    }
    
    public function firstCharUp($texto){
        $textoM = ucwords(strtolower($texto));
        return $textoM;
    }
    
    public static function uppercase($texto){
        $textoM = (strtoupper($texto));
        return $textoM;
    }

    public function saveBRcurrencyFormat($valor) {
        $source = array('.', ',', ' '); 
        $replace = array('', '.', '');
        $valorformatado = str_replace($source, $replace, $valor); //remove os pontos e substitui a virgula pelo ponto
        return $valorformatado; //retorna o valor formatado para gravar no banco
    }

    public function BRcurrencyFormat($valor){
        $v = @number_format($valor, 2, ', ', '.');
        if (!$v) {
            $v = $valor;
        }
            return 'R$ ' . $v;
    }

    public function corHora($horario){
        if(strtotime($horario) < strtotime("12:00:00")){
                return "vagas-morning";							
        }else{
                return "vagas-afternoon";	
        }
    }

    public static function ENdateFormat($data){
        $newdate = implode("-", array_reverse(explode("/", trim($data))));
        return $newdate;
    }

    public static function BRdateFormat($data){      
        $newdate = date("d/m/Y", strtotime($data));
        return $newdate;
    }
    
    public static function BRfullDateTime($data) {
        $newdate .= date("d/m/Y", strtotime($data));
        $newdate .= "  às  ";
        $newdate .= date("H:i:s", strtotime($data));
        return $newdate;
    }
    
    public static function validarCPF($cpf)
{
    // Remove tudo que não for número
    $cpf = preg_replace('/\D/', '', $cpf);

    // Deve ter 11 dígitos
    if (strlen($cpf) != 11) {
        return false;
    }

    // Rejeita sequências repetidas
    if (preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }

    // Valida primeiro dígito
    $soma = 0;
    for ($i = 0, $peso = 10; $i < 9; $i++, $peso--) {
        $soma += intval($cpf[$i]) * $peso;
    }

    $resto = $soma % 11;
    $digito1 = ($resto < 2) ? 0 : 11 - $resto;

    if (intval($cpf[9]) != $digito1) {
        return false;
    }

    // Valida segundo dígito
    $soma = 0;
    for ($i = 0, $peso = 11; $i < 10; $i++, $peso--) {
        $soma += intval($cpf[$i]) * $peso;
    }

    $resto = $soma % 11;
    $digito2 = ($resto < 2) ? 0 : 11 - $resto;

    if (intval($cpf[10]) != $digito2) {
        return false;
    }

    return true;
}
    
    public static function CPF($cpf) {
    // remove tudo que não for número
    $cpf = preg_replace('/\D/', '', $cpf);

    // garante que tem 11 dígitos
    if (strlen($cpf) !== 11) {
        return $cpf; // retorna sem formatar se for inválido
    }
    // aplica a máscara
    return substr($cpf, 0, 3) . '.' .
           substr($cpf, 3, 3) . '.' .
           substr($cpf, 6, 3) . '-' .
           substr($cpf, 9, 2);
}
    public function age($param) {
        $data1 = new DateTime(str_replace("-", "",$param));
 
        $idadeData = $data1->diff(new DateTime());       

        return $idadeData->y;        
    }
    
    public function URL_encode($url){
        $encoded = preg_replace("/\s+/", " ", str_replace("+"," ",(str_replace("_", "/", $url))));
        return $encoded;
    }
    
    public function UniqueID($prefix = "",$more_entropy = false) {
        return uniqid($prefix,$more_entropy);
    }            

    public static function checkDate($dat){ 
       
        try {
            $data = explode('-',$dat); // fatia a string $dat em pedados, usando / como referência
            $y = filter_var($data[0], FILTER_SANITIZE_NUMBER_INT);
            $m = filter_var($data[1], FILTER_SANITIZE_NUMBER_INT);
            $d = filter_var($data[2], FILTER_SANITIZE_NUMBER_INT);
        } catch (Exception $exc) {
            return false;
        } finally {
            if(checkdate($m,$d,$y)){
                return true;
            } else {
                return false;
            } 
        }     
    }
    
    public static function isDate($date) {
    if (empty($date)) return false;

    // Lista dos formatos de data mais comuns
    $formats = [
        'Y-m-d',      // 2025-10-26
        'd/m/Y',      // 26/10/2025
        'd-m-Y',      // 26-10-2025
        'Y/m/d',      // 2025/10/26
        'd.m.Y',      // 26.10.2025
        'Y.m.d',      // 2025.10.26
        'd M Y',      // 26 Oct 2025
        'd M, Y',     // 26 Oct, 2025
    ];

    foreach ($formats as $format) {
        $d = DateTime::createFromFormat($format, $date);
        if ($d && $d->format($format) === $date) {
            return true;
        }
    }

    return false;
}

    
public static function cleanString($string) {
    $cleanstring = trim($string);
    $cleanstring = str_replace(
        [".", ",", "-", "/"], 
        "", 
        $cleanstring
    );

    // remove aspas simples e duplas
    $cleanstring = self::removeQuotes($cleanstring);

    return $cleanstring;
}

public static function removeQuotes($param) {
    return preg_replace("/['\"]/", "", $param);
}

public static function removeParentheses($param) {
    return preg_replace('/[()]/', '', $param);
}
    
    public function validateDate($date, $locale){
        $format = '';
        if($locale === 'EN'){
            $format = 'Y-m-d H:i:s';
        } else if($locale === 'BR') {
            $format = 'd-m-Y H:i:s';
        }
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    
    public function mesExtenso($param) {
        
        switch ($param) {
            case "01":    $mes = 'Janeiro';     break;
            case "02":    $mes = 'Fevereiro';   break;
            case "03":    $mes = 'Março';       break;
            case "04":    $mes = 'Abril';       break;
            case "05":    $mes = 'Maio';        break;
            case "06":    $mes = 'Junho';       break;
            case "07":    $mes = 'Julho';       break;
            case "08":    $mes = 'Agosto';      break;
            case "09":    $mes = 'Setembro';    break;
            case "10":    $mes = 'Outubro';     break;
            case "11":    $mes = 'Novembro';    break;
            case "12":    $mes = 'Dezembro';    break; 
        }
 
        return $mes;
        
    }
	
    public function modal($modalId,$modalType,$modalSize,$modalTitle,$formID,$formName,$formAction,$content,$footer){

        echo 
        "<div id=\"{$modalId}\" class=\"modal fade\" role=\"dialog\">
            <div class=\"modal-dialog modal-{$modalSize}\">
                <div class=\"modal-content panel-{$modalType}\">
                    <div class=\"modal-header panel-heading\">              
                          <h4 class=\"modal-title\">{$modalTitle}</h4>
                    </div>
                    <form id=\"{$formID}\" name=\"{$formName}\" method=\"POST\" action=\"" . URL . "{$formAction}\">
                        <div class=\"modal-body\">                       
                            {$content}                       
                        </div>
                    </form>               
                    <div class=\"modal-footer\">
                        <div class=\"validate-errors\" id=\"errors\">
                        </div>
                        {$footer}	
                    </div>                
                </div>
            </div>
        </div>";
    }
    
    public function alert($title,$msg,$type = 0,$button = "OK",$cssClass = "sm-dialog text-muted") {
              
      echo "<script type=\"text/javascript\">
            var types = [
                BootstrapDialog.TYPE_DEFAULT, 
                BootstrapDialog.TYPE_INFO, 
                BootstrapDialog.TYPE_PRIMARY, 
                BootstrapDialog.TYPE_SUCCESS, 
                BootstrapDialog.TYPE_WARNING, 
                BootstrapDialog.TYPE_DANGER
                ];
                
            var t = types[{$type}];
            
                BootstrapDialog.alert({
                    cssClass: '{$cssClass}',
                    title: '{$title}',
                    message: \"{$msg}\",
                    type: t, 
                    closable: true, 
                    draggable: true, 
                    buttonLabel: '{$button}', 
                    callback: function(result) {                    
                    }
                });
            </script>";
    }
    
    public function button(
        $type ,
        $class ,
        $id ,
        $name ,
        $href ,
        $content ,
        $params = null ,
        $data_redirect = null ,
        $data_redirect_url = null ,
        $data_redirect_params = null ,
        $data_redirect_target = null ,
        $data_modal_title = null ,
        $data_modal_type = null ,
        $data_modal_cls = null ,
        $data_modal_href = null ,
        $data_modal_params = null ,
        $data_modal_confirm = null ,
        $data_modal_question = null ,
        $data_modal_redirect_url = null ,
        $data_modal_redirect_params = null ,
        $data_modal_close = null,
        $onclick = null,
        $form = null,
        $custom=null) {
        
        return "<button class=\"{$class}\" type=\"{$type}\" id=\"{$id}\" name=\"{$name}\" href=\"{$href}\" data-params='{{$params}}' data-redirect=\"{$data_redirect}\" data-redirect-url=\"{$data_redirect_url}\" data-redirect-params='{{$data_redirect_params}}' data-redirect-target=\"{$data_redirect_target}\" data-modal-title=\"{$data_modal_title}\" data-modal-type=\"{$data_modal_type}\" data-modal-cls=\"{$data_modal_cls}\" data-modal-href=\"{$data_modal_href}\" data-modal-params=\"{$data_modal_params}\" data-modal-confirm=\"{$data_modal_confirm}\" data-modal-question=\"{$data_modal_question}\" data-modal-redirect-url=\"{$data_modal_redirect_url}\" data-modal-redirect-params=\"{$data_modal_redirect_params}\" data-modal-close=\"{$data_modal_close}\" onclick=\"{$onclick}\" data-form=\"{$form}\" {$custom}>"
                . "{$content}"
            . "</button>";
        
    }
    
    public function btnQuestion($class ,$id ,$name ,$href ,$content, $modal_params, $modal_title, $modal_question, $redirect, $modal_redirect_url=null, $modal_redirect_params=null, $redirect_target=null) {
        
        return "<button type=\"button\" class=\"btn {$class} call-modal\" id=\"{$id}\" name=\"{$name}\" data-modal-params='{{$modal_params}}' data-modal-title=\"{$modal_title}\" data-modal-confirm=\"true\" data-modal-question=\"{$modal_question}\"  data-modal-type=\"5\" data-modal-cls=\"advice-dialog\" data-modal-href=\"{$href}\" data-redirect=\"{$redirect}\" data-modal-redirect-url=\"{$modal_redirect_url}\" data-modal-redirect-params='{{$modal_redirect_params}}' data-redirect-target=\"{$redirect_target}\" data-modal-close=\"true\">{$content}</button>";
    }
    
    public function btnReset($refresh = false) {
        if($refresh) { 
            $r = 'refresh';            
        } else {
            $r = '';
        }
        return "<span id=\"btn-reset\" class=\"btn btn-primary reset {$r}\" data-toggle=\"popover-info\" title=\"Limpar todos os campos\">Novo </span>";
    }
    
    public function input(
        $type,
        $class,
        $id,
        $name,
        $dataDisplay = null,
        $placeholder = null,
        $required = false,
        $msgRequired = null,
        $value = null,
        $minLength = 0,
        $popoverTitle = null,
        $popoverContent = null,
        $popoverTrigger = "manual",
        $popoverPlacement = null, 
        $urlInsert = null) 
            
            {
        return "<input class=\"{$class}\" type=\"{$type}\" id=\"{$id}\" name=\"{$name}\" data-display=\"{$dataDisplay}\" data-rule-required=\"{$required}\" data-msg-required=\"{$msgRequired}\" placeholder=\"{$placeholder}\" value=\"{$value}\"  data-toggle=\"popover\" title=\"{$popoverTitle}\" data-content=\"{$popoverContent}\" data-trigger=\"{$popoverTrigger}\" data-placement=\"{$popoverPlacement}\" data-popover-offset=\"10,60\" minlength=\"{$minLength}\" min=\"{$minLength}\" data-insert=\"{$urlInsert}\">";
    }
    
    public function textarea($rows,$cols,$class,$id,$name,$dataDisplay,$placeholder,$required,$msgRequired,$value) {
        return "<textarea rows=\"{$rows}\" cols=\"{$cols}\" class=\"{$class}\" id=\"{$id}\" name=\"{$name}\" data-display=\"{$dataDisplay}\" data-rule-required=\"{$required}\" data-msg-required=\"{$msgRequired}\" placeholder=\"{$placeholder}\" data-popover-offset =\"10,120\">{$value}</textarea>";
    }

    public function select(
        $json , 
        $slctclass , 
        $slctname , 
        $slctid , 
        $data_params,
        $data_value, 
        $data_display, 
        $redirect = null, 
        $redirect_url = null, 
        $redirect_params = null, 
        $target_display = null, 
        $href = null, 
        $required = null, 
        $msgRequired = null, 
        $slct3redirect_target = "", 
        $opt_selected = null,
        $optionTitle = "",
        $popoverTitle = null,
        $popoverContent = null,
        $popoverTrigger = "manual",
        $popoverPlacement = null
    ) {
        
        $option = "";
        $newValue = "";
        $newDisplay = "";
        $displayMenber = "";
        $newTitle = "";
        
            $data = json_decode($json); 
                      
            if($opt_selected === null){
                $newDisplay = "---------------";
            }             
            
            if(!empty($data)){             
                
                foreach ($data as $key => $value) { 
                    

                    if($this->validateDate($value->$data_display,'yyyy-MM-dd') === true){                       
                        $displayMenber = $this->BRdateFormat($value->$data_display); 
                    } else if (is_string($value->$data_display)) {
                        $displayMenber = addslashes($value->$data_display);                          
                    } else if (is_null($value->$data_display)) {
                        $displayMenber = " ";                          
                    } else {
                        $displayMenber = $value->$data_display; 
                    }
                    
                    if($optionTitle != "" || $optionTitle != null){
                        $title = $value->$optionTitle;
                    } else {
                        $title = "";
                    }

                    $option.= "<option value=\"" . $value->$data_value . "\" title=\"{$title}\">" . $displayMenber . "</option>";
                                       
                    if($value->$data_value == $opt_selected){
                        $newValue = $value->$data_value;
                        $newDisplay = $displayMenber;
                        $newTitle = $title;
                    }
                }
            }
            
            $select = "<select class=\"{$slctclass}\" name=\"{$slctname}\" id=\"{$slctid}\" data-params={{$data_params}} data-redirect=\"{$redirect}\" data-redirect-url=\"{$redirect_url}\" data-redirect-params={{$redirect_params}} data-redirect-target=\"{$slct3redirect_target}\" data-display=\"{$target_display}\" href=\"{$href}\" data-rule-required=\"{$required}\" data-msg-required=\"{$msgRequired}\" data-toggle=\"popover\" title=\"{$popoverTitle}\" data-content=\"{$popoverContent}\" data-trigger=\"{$popoverTrigger}\" data-placement=\"{$popoverPlacement}\" data-popover-offset =\"10,120\">";
            
        $select .= "<option value=\"" . $newValue . "\" selected=\"selected\" title=\"{$newTitle}\">" . $newDisplay . "</option>";
        $select .= $option;
        $select .= "</select>";
                    
        return $select;       
    }
    
    public function week($param) {
        error_reporting(0);        
                
        $f = new Functions();
        $week = preg_replace('/(\'|")/', "", $param); 
                        
            if($f->checkDate($param) === true){           
                $week = (strftime("%A",strtotime($param)));
            }       
            
            switch ($week) {
            case 2:
                return ('SEGUNDA');             
            case 3:
                return ('TERÇA');
            case 4:
                return ('QUARTA');
            case 5:
                return ('QUINTA');
            case 6:
                return ('SEXTA');
            case 'Monday':
                return 2;
            case 'Tuesday':
                return 3;
            case 'Wednesday':
                return 4;
            case 'Thursday':
                return 5;
            case 'Friday':
                return 6;            
            default:
                break;
            }                 
    }
    
    public static function getWeekday($date) {
        $newDate = date('w', strtotime($date));
         switch ($newDate) {
            case 0:
                return ('DOMINGO'); 
            case 1:
                return ('SEGUNDA'); 
            case 2:
                return ('TERÇA');             
            case 3:
                return ('QUARTA');
            case 4:
                return ('QUINTA');
            case 5:
                return ('SEXTA');
            case 6:
                return ('SÁBADO');
         }
    }
    
    public static function loadXML($param) {
        $link = $param; //link do arquivo xml
        $xml = simplexml_load_file($link); 
        
        return $xml;             
    }
    
    public static function selectYears($id,$name) {
echo"<label>Ano</label><br>
        <select class=\"select\" id=\"{$id}\" name=\"{$name}\">";
               $ano = date('Y'); $ano¹ = $ano + 1; $ano² = $ano + 2; $¹ano = $ano - 1; $²ano = $ano - 2;
         echo "<option value=\"". $ano² ."\">{$ano²}</option>
               <option value=\"". $ano¹ ."\">{$ano¹}</option>
               <option value=\"". $ano ."\" selected>{$ano}</option>
               <option value=\"". $¹ano ."\">{$¹ano}</option>
               <option value=\"". $²ano ."\">{$²ano}</option>";

  echo "</select>";
    }
    
    public static function selectMonths($id,$name) {
    echo "<label class=\"\">Mês</label><br>
            <select class=\"select\" id=\"{$id}\" name=\"{$name}\">
                <option value=\"\"> MÊS </option>
                <option value=\"0\"> JANEIRO </option>
                <option value=\"1\"> FEVEREIRO </option>
                <option value=\"2\"> MARÇO </option>
                <option value=\"3\"> ABRIL </option>
                <option value=\"4\"> MAIO </option>
                <option value=\"5\"> JUNHO </option>
                <option value=\"6\"> JULHO </option>
                <option value=\"7\"> AGOSTO </option>
                <option value=\"8\"> SETEMBRO </option>
                <option value=\"9\"> OUTUBRO </option>
                <option value=\"10\"> NOVEMBRO </option>
                <option value=\"11\"> DEZEMBRO </option>
            </select>";
    }
    
    public static function monthExtense($param) {
        switch ($param) {
            case 1:
                return "JANEIRO";               
            case 2:
                return "FEVEREIRO";               
            case 3:
                return "MARÇO";              
            case 4:
                return "ABRIL";               
            case 5:
                return "MAIO";               
            case 6:
                return "JUNHO";               
            case 7:
                return "JULHO";              
            case 8:
                return "AGOSTO";               
            case 9:
                return "SETEMBRO";               
            case 10:
                return "OUTUBRO";               
            case 11:
                return "NOVEMBRO";               
            case 12:
                return "DEZEMBRO";             
            
        }
    }
    
    public static function dateFull() {
        return Functions::getWeekday(date('Y/m/d')).", ".date('d')." de ".Functions::monthExtense(date('m'))." de ".date('Y');        
    }
    
    public static function hashPass($password) {
        return hash('sha512', self::SALT . $password);
    }
 
    public static function verifyPass($password, $hash) {
        return ($hash == self::hash($password));
    }
    
    public static function incl($path,$filename) {        
        include $path.$filename.".php";
    }
    
    public static function getJSONpost() {
        $json = file_get_contents('php://input');
        $resultado = json_decode($json);
        
        return $resultado;
    }
    
    public static function getPost() {
        $post = file_get_contents('php://input');
                
        return $post;
    }
}