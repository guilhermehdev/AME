<?php

/**
 * Description of AppController
 *
 * @author Guilherme
 */
class AppController implements IPrivateTO {
    
    public function index() { 
        if(AppController::checkSession()){
            $view = new TGui("dashboard");  
            $view->addData("title", 'Dashboard');
            $view->renderize(APP_VIEW); 
           
        } else {
            header("location: ".URL."Loginadm/login");
        }
    } 
    
    public static function checkSession() {       
        session_start();
        if(isset($_SESSION['adm'])) {          
            return $_SESSION['adm'];
        } else {
            session_unset();
        }
    }
    
    public static function menuAdm() {
        $f = new Functions();
        
        session_start();
        if(isset($_SESSION['adm'])) {
            $user = $_SESSION['adm']['username'];
            $cad = $_SESSION['adm']['cadastros'];
            $cadPac = $_SESSION['adm']['cadpac'];               
            $ret = $_SESSION['adm']['retornos'];
            $oci = $_SESSION['adm']['oci'];
            $notificacao = $_SESSION['adm']['notificacao'];
            $impressos = $_SESSION['adm']['impressos'];
            
            $menu='';
            
        if ($cad == 1) {
                        
            $menu.= "<div class=\"panel panel-default\">
                        <a data-toggle=\"collapse\" id=\"\" data-parent=\"#accordion\" href=\"#collapse1\">
                            <div id=\"sidebar-menu\" class=\"panel-heading\">                        
                                <h4 class=\"panel-title\">                            
                                    <span class=\"glyphicon glyphicon-\">
                                    </span> Cadastros                      
                                </h4>                        
                            </div>
                        </a>

                        <div id=\"collapse1\" class=\"panel-collapse collapse\">
                            <div class=\"panel-body\">
                                <table class=\"table\">";                       
                            
                            $menu.=     
                                    "<tr>
                                        <td>

                                            {$f->button("button", "btn btn-link call-data", "btn-menu-paciente", "btn-menu-paciente", "Pacientes/cad/0", "<span class=\"glyphicon glyphicon-plus-sign text-primary\"></span> Pacientes", null,"header")}
                                        </td>
                                    </tr>";
                                            
                            $menu.= "<tr>
                                        <td>

                                            {$f->button("button", "btn btn-link call-data", "btn-menu-servidor", "btn-menu-servidor", "Servidores/cad", "<span class=\"glyphicon glyphicon-briefcase text-primary\"></span> Profissionais", null,"header")}
                                        </td>
                                    </tr>";                                            
                                                                        
                            $menu.= "<tr>
                                        <td>
                                            {$f->button("button", "btn btn-link call-data", "btn-menu-patrimonio", "btn-menu-patrimonio", "Patrimonio/index", "<span class=\"glyphicon glyphicon-th-list text-primary\"></span> Patrimônio", null,"header")}
                                        </td>
                                    </tr>";
                                            
                            $menu.= "<tr>
                                        <td>

                                            {$f->button("button", "btn btn-link call-data", "btn-menu-usuario", "btn-menu-usuario", "Servidores/cadUsuarios", "<span class=\"glyphicon glyphicon-user text-primary\"></span> Usuários", null,"header")}
                                        </td>
                                    </tr>";                          
                                                      
                       $menu.= "</table>
                            </div>
                        </div>
                        
                    </div>";
        }
            
        if ($ret == 1) {
            
            $menu.= "<div class=\"panel panel-default\">
                    <a data-toggle=\"collapse\" id=\"\" data-parent=\"#accordion\" href=\"#collapse2\">
                        <div id=\"sidebar-menu\" class=\"panel-heading\">                        
                            <h4 class=\"panel-title\">                            
                                <span class=\"glyphicon glyphicon-\">
                                </span> Retornos                      
                            </h4>                        
                        </div>
                    </a>

                    <div id=\"collapse2\" class=\"panel-collapse collapse\">
                        <div class=\"panel-body\">
                            <table class=\"table\">
                                <tr>
                                    <td>
                                        {$f->button("button", "btn btn-link call-data", "btn-menu-retornos", "btn-menu-retornos", "Retornos/index", "<span class=\"glyphicon glyphicon-plus text-primary\"></span> Novo", null,"header")}                                            
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        {$f->button("button", "btn btn-link call-data", "btn-menu-consulta-retornos", "btn-menu-consulta-retornos", "Retornos/consulta", "<span class=\"glyphicon glyphicon-search text-primary\"></span> Consultar", null,"header")}                                            
                                    </td>
                                </tr> 

                            </table>
                        </div>
                    </div>

                </div>";               
        }    
        
        if($oci ==1){
            
             $menu .= "<div class=\"panel panel-default\">
                    <a data-toggle=\"collapse\" id=\"\" data-parent=\"#accordion\" href=\"#collapse8\">
                        <div id=\"sidebar-menu\" class=\"panel-heading\">                        
                            <h4 class=\"panel-title\">                            
                                <span class=\"glyphicon glyphicon-\">
                                </span> OCI                      
                            </h4>                        
                        </div>
                    </a>

                    <div id=\"collapse8\" class=\"panel-collapse collapse\">
                        <div class=\"panel-body\">
                            <table class=\"table\">
                                <tr>
                                    <td>
                                       {$f->button("button", "btn btn-link call-data", "btn-menu-oci", "btn-menu-oci", "OCI/home", "<span class=\"glyphicon glyphicon-save-file text-primary\"></span> Cadastro", null,"header")}
                                    </td>
                                </tr> 
                            </table>
                        </div>
                    </div>                
              </div>";
        }
        
        if($notificacao == 1){
            
            $menu .= "<div class=\"panel panel-default\">
                    <a data-toggle=\"collapse\" id=\"\" data-parent=\"#accordion\" href=\"#collapse7\">
                        <div id=\"sidebar-menu\" class=\"panel-heading\">                        
                            <h4 class=\"panel-title\">                            
                                <span class=\"glyphicon glyphicon-\">
                                </span> Notificações                      
                            </h4>                        
                        </div>
                    </a>

                    <div id=\"collapse7\" class=\"panel-collapse collapse\">
                        <div class=\"panel-body\">
                            <table class=\"table\">
                                  <tr>
                                    <td>
                                       {$f->button("button", "btn btn-link call-data", "btn-menu-notificacao-agendas", "btn-menu-notificacao-agendas", "Notificacoes/agenda", "<span class=\"glyphicon glyphicon-calendar text-primary\"></span> Agenda", null,"header")}
                                    </td>
                                </tr> 
                                <tr>
                                    <td>
                                       {$f->button("button", "btn btn-link call-data", "btn-menu-avisos", "btn-menu-avisos", "Notificacoes/painel", "<span class=\"glyphicon glyphicon-alert text-primary\"></span> Painel", null,"header")}
                                    </td>
                                </tr> 
                                  <tr>
                                    <td>
                                       {$f->button("button", "btn btn-link call-data", "btn-menu-confirmar", "btn-menu-confirmar", "Notificacoes/confirmar", " <i class=\"bi bi-whatsapp\"></i> Confirmação de consulta", null,"header")}
                                    </td>
                                </tr> 
                            </table>
                        </div>
                    </div>                      
                </div>";   
        }        
          
          if($impressos == 1){              
               
             $menu .= "<div class=\"panel panel-default\">
                    <a data-toggle=\"collapse\" id=\"\" data-parent=\"#accordion\" href=\"#collapse9\">
                        <div id=\"sidebar-menu\" class=\"panel-heading\">                        
                            <h4 class=\"panel-title\">                            
                                <span class=\"glyphicon glyphicon-\">
                                </span> Impressos                    
                            </h4>                        
                        </div>
                    </a>

                    <div id=\"collapse9\" class=\"panel-collapse collapse\">
                        <div class=\"panel-body\">
                            <table class=\"table\">
                                <tr>
                                    <td>
                                       {$f->button("button", "btn btn-link call-data", "btn-menu-oci", "btn-menu-oci", "OCI/impressos", "<span class=\"glyphicon glyphicon-file text-primary\"></span> OCI", null,"header")}
                                    </td>
                                </tr> 
                                  <tr>
                                    <td>
                                       {$f->button("button", "btn btn-link call-data", "btn-menu-fisio", "btn-menu-fisio", "Reports/printReception", "<span class=\"glyphicon glyphicon-file text-primary\"></span> Recepção", null,"header")}
                                    </td>
                                </tr> 
                            </table>
                        </div>
                    </div>      
                    
                   
              </div>";
       
          }
                    
          return $menu;
            
        } else {
            session_start();
            session_destroy();
        }        
    }  
}