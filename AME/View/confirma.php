<?php
$f = new Functions();
$title = $this->getData('title');
$btn = "";

echo 
"<div class=\"col-sm-12\">
    <div class=\"page-header\">
        <h2><small>Notificações ></small> {$title}</h2>          
</div>
	
<div class=\"col-sm-12\">     
          <div class=\"col-sm-12\">
                <div class=\"col-sm-4\">
                    <label>Especialidade</label>
                    {$f->select(Daoagendas::slctEspecs(), "select mrg-bottom", "slct-espec-whatsapp", "slct-espec-whatsapp", "","id","especialidade", "", null, "","","",true,"Selecione a Especialidade")} 
                </div>
                
                 <div class=\"col-sm-5\">
                    <label>Profissional</label>
                    {$f->select(Daoagendas::slctProf(), "select mrg-bottom", "slct-prof-whatsapp", "slct-prof-whatsapp", "","id","nome", "", null, "","","",true,"Selecione o Profissional")} 
                </div>                

                <div class=\"col-sm-3\">
                    <label>Data da consulta</label>
                            <input type=\"text\" class=\"form-control calendar date mrg-bottom\" id=\"inp-data-whatsapp\" name=\"inp-data-whatsapp\" data-rule-required=\"true\" data-msg-required=\"Selecione a Data\" placeholder=\"Selecione uma data\" value=\"\">
                </div>                
          </div>
          
            <div class=\"col-sm-12\">  
            
                    <div class=\"col-sm-2\">
                    <label>Hora</label>
                            <select class=\"select mrg-bottom\" name=\"slct-hora-whatsapp\" id=\"slct-hora-whatsapp\" class=\"form-select\">
                                    <option value=\"\">Horário</option>"; ?>
<?php
                        $inicio = new DateTime('07:00');
                        $fim    = new DateTime('19:00');

                        while ($inicio <= $fim) {
                            $hora = $inicio->format('H:i');
                            echo "<option value=\"$hora\">$hora</option>";
                            $inicio->modify('+10 minutes');
                        }
                       
        echo "</select>
                </div>

                    <div class=\"col-sm-3\">
                           <label>Nascimento</label><br>
                           <div class=\"input-group\">
                               {$f->input("text", "form-control data-br", "inp-cad-dtnasc", "inp-cad-dtnasc", "", "Data", "", "Digite a Data","",10)}
                               <span class=\"input-group-btn\">
                               <button id=\"btn-fnd-pac-data\" name=\"btn-fnd-pac-data\" class=\"btn btn-primary call-modal\" data-modal-title=\"Selecione o Paciente\" data-modal-type=\"2\" data-modal-size=\"3\" data-modal-cls=\"sv-dialog\" data-modal-href=\"Pacientes/get\" data-modal-params='{\"data\":\"inp-cad-dtnasc\"}' data-check-input=\"inp-cad-dtnasc\" type=\"button\"><span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\">
                               </span> </button>
                               </span>
                           </div>
                   </div>

                   <div class=\"col-sm-4\">
                               <label>Nome</label><br>
                               <div class=\"input-group\">
                                   {$f->input("text", "form-control", "inp-nome", "inp-nome", null, "", true, "Digite o Nome",null,4)}  
                                   <span class=\"input-group-btn\">
                                   <button id=\"btn-fnd-pac-nome\" name=\"btn-fnd-pac-nome\" class=\"btn btn-primary call-modal\" data-modal-title=\"Selecione o Paciente\" data-modal-type=\"2\" data-modal-size=\"3\" data-modal-cls=\"sv-dialog\" data-modal-href=\"Pacientes/get\" data-modal-params='{\"name\":\"inp-nome\"}' data-check-input=\"inp-nome\" type=\"button\"><span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\">
                                   </span> </button>
                                   </span>
                               </div>
                   </div>	                                                                              

                       <div class=\"col-sm-3\">
                           <label>Contato</label><br> 
                               <div class=\"input-group\">
                                       {$f->input("text", "form-control tel", "inp-tel", "inp-tel", "", "(00)00000-0000", true,"Digite um contato")}    
                                       <span class=\"input-group-btn\">
                                           <button id=\"open-whatsapp\" class=\"btn btn-success\">
                                               <i class=\"bi bi-whatsapp\"></i>
                                            </button>
                                       </span>
                               </div>
                        </div>
                </div>
          
</div>";