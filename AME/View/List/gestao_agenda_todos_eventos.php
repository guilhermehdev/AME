<?php
$eventos = $this->getData('eventos');
$grupo = [];
foreach ($eventos as $evento) {
    $especialidade = $evento['especialidade'];
    $profissional = $evento['nome_servidor'] ?: 'Sem profissional fixo';
    $grupo[$especialidade][$profissional][] = $evento;
}

if (!$grupo) {
    echo '<p class="text-muted" style="padding:15px;">Nenhum evento cadastrado.</p>';
    return;
}

foreach ($grupo as $especialidade => $profissionais) {
    echo '<h4 class="text-primary">' . htmlspecialchars($especialidade) . '</h4>';
    foreach ($profissionais as $profissional => $lista) {
        echo '<h5 style="padding-left:15px;"><strong>' . htmlspecialchars($profissional) . '</strong></h5>';
        echo '<table class="table table-condensed table-hover table-bordered">';
        echo '<tr class="active"><th>Data</th><th>Tipo</th><th>Descrição</th><th>Reagendado para</th><th>Dashboard</th><th>Ações</th></tr>';
        foreach ($lista as $evento) {
            $data = Functions::BRdateFormat($evento['data_evento']);
            $reagendado = $evento['dt_reagend'] ? Functions::BRdateFormat($evento['dt_reagend']) : '—';
            echo '<tr><td>' . $data . '</td><td>' . htmlspecialchars($evento['tipo']) . '</td><td>' .
                htmlspecialchars($evento['descricao']) . '</td><td>' . $reagendado . '</td><td class="text-center">' .
                '<input type="checkbox" class="toggle-dashboard-evento" data-id="' . $evento['id'] . '" ' .
                (!empty($evento['show_dashboard']) ? 'checked' : '') . '></td><td>' .
                '<button type="button" class="btn btn-danger btn-xs call-data" href="GestaoAgenda/deleteEvento" ' .
                'data-params=\'{"id":"' . $evento['id'] . '"}\' data-redirect="load" ' .
                'data-redirect-target="container-historico" data-redirect-url="GestaoAgenda/getTodosEventos">' .
                '<span class="glyphicon glyphicon-trash"></span></button></td></tr>';
        }
        echo '</table>';
    }
}
