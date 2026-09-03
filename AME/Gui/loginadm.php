<?php 

$f = new Functions();

echo "<html>
        <head>
            <title>AME - Área Administrativa</title>
            <link rel=\"shortcut icon\" href=\"".FAVICON."\" type=\"image/x-icon\">";

 $load = new Loads();    
        $load->css(); 
        
echo "</head>
      <body>";
  

        echo "<div id=\"login-dark\" class=\"container\">

            <div class=\"login-form\">        

                <div class=\"row\">

                    <div class=\"col-md-12\" style=\"margin-top:10%;\">

                            <div class=\"text-center text-primary\">                    
                            <h4><a href=\"".URL."\"><img id='appIcon' alt=\"". APPNAME . "\" src=\"" . ICON . "\" width='100' height='45'></a> <br><br>Área Administrativa</h4>
                            </div>
                        <br>
                    </div>

                </div>

                <div class=\"row\">

                    <div class=\"center-block\" style=\"width:250px;\">                
                        <form id=\"frm-login\" method=\"POST\" action=\"" . URL . "Loginadm/auth\">
                                                                                  
                            <label class=\"text-white\">Usuário</label>
                        {$f->select(Daoagendas ::slctUser(), "select mrg-bottom", "slct-user-login", "slct-user-login", "\"id\":\"slct-prof\"", "id", "nome", "", null, "", "","",true,"Selecione o Profissional")}

                            <label class=\"text-white\">Senha</label>
                            <div class=\"input-group\">
                                <input type=\"password\" name=\"pass\" id=\"pass\" class=\"form-control\" placeholder=\"Senha\" aria-label=\"\"  data-rule-required=\"true\" data-msg-required=\"Digite sua senha!\" data-popover-offset=\"10,60\">
                                <span class=\"input-group-btn\">
                                  <button id=\"btn-submit\" type=\"\" class=\"btn btn-primary submit\">OK</button>
                                </span>
                            </div>     
                        </form>                   
                                                    
                           <button href=\"".URL."Loginadm/recuperar\"  id=\"btn-recuperar-senha\" name=\"btn-recuperar-senha\" class=\"btn btn-primary call-data\" data-params='{\"id\":\"slct-user-login\"}' data-redirect=\"load\" data-redirect-target=\"main\"> Recuperar senha</button>          
                           
                    </div>
                </div>

            </div>   
        </div>";
        
        $load->js();
        
   echo "</body>
    
</html>";