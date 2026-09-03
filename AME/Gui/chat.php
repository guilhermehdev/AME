
<?php

$f = new Functions();
$userID = $f->session()['id'];

echo "<input type='hidden' id=\"user\" data-id={$userID}>  
    
<button id=\"open-chat\" class=\"open-button fas fa-comments\" onclick=\"openForm()\"> 
       
        </button>       
        <div class=\"chat-popup\" id=\"chat\">                      
            <div style=\"text-align:right;\">
                <button type=\"button\" class=\"btn cancel chat-close\" onclick=\"closeForm()\">X</button>
            </div>
            <div class=\"form-container\">
               
                <div>
                    <b>Mensagem para {$f->select(Chat::slctUser(), "select mrg-bottom", "slct-chat-user", "slct-chat-user", "\"id\":\"slct-user\"", "id", "nome", "reload", null, "","chat-container", "Chat/getMessages",true,"Selecione um usuário")}</b>
                </div> 
                
                <div id=\"chat-container\" name=\"chat-container\" disabled></div>  
                
                <div class=\"input-group\">
                    <input type=\"text\" class=\"form-control\" placeholder=\"Digite sua mensagem...\" id=\"chat-input\" name=\"chat-input\" required disabled> 
                    <span class=\"input-group-btn\">
                        <button name=\"btn-send-message\" id=\"btn-send-message\" class=\"btn btn-success\" send=\"Chat/saveMessages\" get=\"Chat/getMessages\" data-params=".json_encode(array('from'=>$userID, 'to'=>'slct-chat-user'))."  disabled >Enviar</button> 
                    </span>
                </div><!-- /input-group -->
            </div>
        </div>";