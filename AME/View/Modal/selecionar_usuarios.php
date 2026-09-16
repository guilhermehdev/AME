<?php
$f = new Functions(); 
if($this->getData('users')){    
    $users = $this->getData('users'); 
    $buscado = $this->getData('buscado');   

echo
"Exibindo resultados da busca para:<span class=\"\"> <b><i>{$f->uppercase($buscado)}</i></b></span>
    <br><br>
<table class=\"table table-active table-condensed\">
    <thead class=\"\">

    </thead>
    <tbody>";

foreach ($users as $u) {
    $paramsUsuario = htmlspecialchars(
        json_encode([
            'nome' => $u['nome'], 'cpf' => $u['CPF'], 'cad' => $u['cadastros'],
            'pass' => $u['pass'], 'email' => $u['email'], 'nasc' => $u['dtnasc']
        ], JSON_UNESCAPED_UNICODE),
        ENT_QUOTES,
        'UTF-8'
    );
echo    
        "<tr class=\"bg-success\">"
            . "<th></th>"            
            . "<th>Nome</th>"                    
            . "<th>CPF</th>"   
        . "</tr>
        <tr>"        
            . "<td class=\"text-nowrap\">
                    <button class=\"btn btn-primary\" name=\"btn-select-usuario\" id=\"btn-select-usuario-{$u['id']}\" data-id=\"{$u['id']}\" data-params=\"{$paramsUsuario}\" data-modal-close=\"true\">
                <span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>
                    </button>
               </td>"
            . "<td class=\"text-nowrap\">{$u['nome']}</td>"
            . "<td>{$u['CPF']}</td>" 
           
    . "</tr><tr><td colspan=5><hr></td></tr>";
    }
    
echo 
    "</tbody>        
</table>";

} else {
    echo "Nenhum registro encontrado!";
}
