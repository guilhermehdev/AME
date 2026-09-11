<?php
$config = $this->getData('config');

echo "<form method=\"POST\" action=\"" . URL . "GestaoAgenda/saveDashboardConfig\"
      id=\"frm-dashboard-config\" name=\"frm-dashboard-config\">

    <p class=\"text-muted\">Marque as especialidades que devem aparecer no painel do dashboard e defina a ordem de exibição.</p>

    <table class=\"table table-condensed table-hover\">
        <thead>
            <tr>
                <th>Visível</th>
                <th>Especialidade</th>
                <th>Ordem</th>
            </tr>
        </thead>
        <tbody>";

foreach ($config as $i => $c) {
    $checked = $c['visivel'] ? 'checked' : '';
    echo "<tr>
            <td>
                <input type=\"checkbox\" name=\"vis_{$c['id_espec']}\"
                       id=\"vis_{$c['id_espec']}\" {$checked}>
            </td>
            <td><label for=\"vis_{$c['id_espec']}\">{$c['especialidade']}</label></td>
            <td>
                <input type=\"number\" class=\"form-control input-sm\"
                       style=\"width:70px;\"
                       name=\"ord_{$c['id_espec']}\"
                       value=\"{$c['ordem']}\">
            </td>
          </tr>";
}

echo "  </tbody>
    </table>

    <button type=\"button\" class=\"btn btn-success\" id=\"btn-save-dashboard-config\">
        <span class=\"glyphicon glyphicon-floppy-disk\"></span> Salvar configuração
    </button>

</form>

<script>
\$(function () {
    \$('#btn-save-dashboard-config').on('click', function () {
        var configs = [];";

foreach ($config as $c) {
    echo "
        configs.push({
            id_espec: {$c['id_espec']},
            visivel:  \$('#vis_{$c['id_espec']}').is(':checked') ? 1 : 0,
            ordem:    parseInt(\$('[name=ord_{$c['id_espec']}]').val()) || 0
        });";
}

echo "
        \$.ajax({
            type: 'POST',
            url: GLOBAL_URL + 'GestaoAgenda/saveDashboardConfig',
            data: { configs: JSON.stringify(configs) },
            success: function (result) {
                messagesHandler(result);
            }
        });
    });
});
</script>";