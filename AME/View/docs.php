<?php
$f = new Functions();
$idUser = AppController::checkSession()['id'];

if($this->getData('avisos')) {
    $avisos = $this->getData('avisos');
}


echo 
"<div class=\"col-sm-12\">
    <div class=\"page-header\">
        <h2><small>Documentos > </small>Novo </h2>
    </div>
    
    <div id=\"documentos\" class=\"col-sm-6\">
    
        <fieldset class=\"for-panel\">
            <legend class=\"text-primary\">Digite a nova mensagem</legend> 
            
            <form method=\"POST\" id=\"frm-doc\" name=\"frm-doc\" action=\"".URL."Documentos/save\">
            
                <div class=\"col-sm-4\">

                    <label>Tipo</label>
                    <select class=\"select\" id=\"slct-tipo-doc\" name=\"slct-tipo-doc\" data-required=\"true\" data-msg-required=\"Selecione o Tipo\">
                        <option value=\"\" selected>--------------------</option>
                        <option value=\"0\">Memorando</option>
                        <option value=\"1\">Ofício</option>
                        <option value=\"2\">Circular</option>
                    </select>

                </div>

                <div class=\"col-sm-4\">

                    <label>Ordem</label>
                    <input class=\"form-control\" type=\"number\" min=\"0\" id=\"inp-ordem-doc\" name=\"inp-ordem-doc\" data-required=\"true\" data-msg-required=\"Digite o número de ordem deste documento\">

                </div>

                <div class=\"col-sm-4\">";            
                    Functions::selectYears('slct-ano-doc','slct-ano-doc');
                echo    
                "</div>
                    
                <div class=\"col-sm-3\">

                    <label>Data</label>
                    <input type=\"text\" class=\"form-control calendar date mrg-bottom\" id=\"inp-data-doc\" name=\"inp-data-doc\" data-required=\"true\" data-msg-required=\"Selecione a Data\">

                </div>
                    
                <div class=\"col-sm-5\">

                    <label>Destino</label>
                    {$f->select(Daodocumentos ::slctDestino(), "select mrg-bottom", "inp-origem-doc", "inp-origem-doc", "", "id", "nome", "", null, "", "", "",false,"")}                    

                </div>
                
                <div class=\"col-sm-4\">

                    <label>A/C</label>
                    {$f->select(Daodocumentos ::slctAC(), "select mrg-bottom", "inp-destino-doc", "inp-destino-doc", "", "id", "nome", "", null, "", "", "",false,"")}                                                           

                </div>
                
                <div class=\"col-sm-12\">

                    <label>Assunto</label>
                    <input class=\"form-control\" type=\"text\" id=\"inp-assunto-doc\" name=\"inp-assunto-doc\" data-required=\"true\" data-msg-required=\"Digite o Assunto\">

                </div>

                <div class=\"col-sm-12\">

                    <label>Conteúdo</label>
                    <textarea name=\"editormemo\" id=\"editormemo\" rows=\"40\">   
                    </textarea>  

                </div>        
                
                <div class=\"col-sm-12\">
                
                    <button type=\"submit\" id=\"btn-save-doc\" name=\"btn-save-doc\" class=\"btn btn-success mrg-top\">
                        Salvar
                    </button> 
                    
                </div>
            
            </form>

        </fieldset>
 
    </div>
    
    <div class=\"col-sm-6\">
        
        <div id=\"ultimos-docs\" name=\"ultimos-docs\">  
        
            <fieldset class=\"for-panel\">
            <legend class=\"text-primary\">Últimos documentos criados</legend> 
            

            </fieldset>
                     
        </div>

    </div>

</div>";