<?php
$f = new Functions();
$token = $this->getData('token');

echo
"<div class=\"col-sm-12\">
    <div class=\"page-header\">
        <h2><small>Login ></small> Redefinir senha</h2>
    </div>

    <form class=\"form-horizontal\" method=\"POST\" action=\"" . URL . "Loginadm/atualizarSenhaToken\" id=\"frm-redefinir-senha\" name=\"frm-redefinir-senha\">

        <input type=\"hidden\" name=\"inp-token\" id=\"inp-token\" value=\"{$token}\">

        <div class=\"col-sm-3\">
            <label>Nova senha</label><br>
            {$f->input("password", "form-control", "inp-senha-nova", "inp-senha-nova", "", "Nova senha", "1", "Informe a nova senha","",6)}
        </div>

        <div class=\"col-sm-3\">
            <label>Confirmar nova senha</label><br>
            {$f->input("password", "form-control", "inp-senha-nova-confirma", "inp-senha-nova-confirma", "", "Confirme a nova senha", "1", "Confirme a nova senha","",6)}
        </div>

        <div class=\"col-sm-12\" style=\"margin-top:15px;\">
            <button type=\"button\" class=\"btn btn-primary submit\">Salvar nova senha</button>
        </div>

    </form>
</div>";
