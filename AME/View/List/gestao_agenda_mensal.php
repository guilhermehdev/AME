<?php
$f       = new Functions();
$mensal  = $this->getData('mensal');
$eventos = $this->getData('eventos');

$meses = [
    1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',
    5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',
    9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'
];

$tiposEvento = [
    'AUSENCIA'      => 'Ausência',
    'FOLGA'         => 'Folga',
    'REAGENDAMENTO' => 'Reagendamento',
    'FERIAS'        => 'Férias',
    'LICENCA'       => 'Licença',
    'OUTRO'         => 'Outro',
];

// Labels de tipo para exibição
$tipoLabel = [
    'AUSENCIA'      => 'danger',
    'FOLGA'        => 'warning',
    'REAGENDAMENTO' => 'info',
    'FERIAS'        => 'success',
    'LICENCA'       => 'primary',
    'OUTRO'         => 'default',
];

$idMensal        = $mensal ? $mensal['id']              : '';
$vagasAme        = $mensal ? $mensal['vagas_ame']        : 0;
$vagasReg        = $mensal ? $mensal['vagas_reg']        : 0;
$vagasOfertadas  = $mensal ? $mensal['vagas_ofertadas']  : 0;
$presentes       = $mensal ? $mensal['presentes']        : 0;
$faltas          = $mensal ? $mensal['faltas']           : 0;
$obs             = $mensal ? $mensal['observacao']       : '';
$nomeProfissional = $mensal ? (isset($mensal['nome_servidor']) ? $mensal['nome_servidor'] : 'Sem profissional fixo') : '';
$nomeEspec       = $mensal ? $mensal['especialidade']   : '';
$mesNome         = $mensal ? ($meses[(int)$mensal['mes']] . '/' . $mensal['ano']) : '';

// pct aproveitamento
$pct = ($vagasOfertadas > 0) ? round($presentes / $vagasOfertadas * 100) : 0;
$pctClass = $pct >= 80 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');

echo "<fieldset class=\"for-panel\">";

if (!$mensal) {
    echo "<legend class=\"text-primary\">Novo registro</legend>
          <p class=\"text-muted\" style=\"padding:10px;\">Nenhum registro encontrado para esse período. Preencha os dados abaixo para criar.</p>";
} else {
    echo "<legend class=\"text-primary\">{$nomeEspec} — {$nomeProfissional} — {$mesNome}</legend>";

    // Mini resumo no topo
    echo "<div class=\"col-sm-12 mrg-bottom\">
            <div class=\"col-sm-3 text-center\">
                <h4>{$vagasOfertadas}</h4><small class=\"text-muted\">Vagas ofertadas</small>
            </div>
            <div class=\"col-sm-3 text-center\">
                <h4 class=\"text-success\">{$presentes}</h4><small class=\"text-muted\">Presentes</small>
            </div>
            <div class=\"col-sm-3 text-center\">
                <h4 class=\"text-danger\">{$faltas}</h4><small class=\"text-muted\">Faltas</small>
            </div>
            <div class=\"col-sm-3 text-center\">
                <h4 class=\"text-{$pctClass}\">{$pct}%</h4><small class=\"text-muted\">Aproveitamento</small>
            </div>
          </div>";
}

// Formulário de registro mensal
echo "<form class=\"form-horizontal\" method=\"POST\"
        action=\"" . URL . "GestaoAgenda/saveMensal\"
        id=\"frm-mensal\" name=\"frm-mensal\">

    <input type=\"hidden\" name=\"inp-id-mensal\" id=\"inp-id-mensal\" value=\"{$idMensal}\">
    <input type=\"hidden\" name=\"inp-id-espec\"    id=\"inp-id-espec\"    value=\"\">
    <input type=\"hidden\" name=\"inp-id-servidor\" id=\"inp-id-servidor\" value=\"\">
    <input type=\"hidden\" name=\"inp-mes\"         id=\"inp-mes\"         value=\"\">
    <input type=\"hidden\" name=\"inp-ano\"         id=\"inp-ano\"         value=\"\">

    <div class=\"col-sm-6 mrg-bottom\">
        <label>Vagas AME</label>
        {$f->input('number', 'form-control', 'inp-vagas-ame', 'inp-vagas-ame', '', '0', false, '', $vagasAme)}
    </div>

    <div class=\"col-sm-6 mrg-bottom\">
        <label>Vagas REG</label>
        {$f->input('number', 'form-control', 'inp-vagas-reg', 'inp-vagas-reg', '', '0', false, '', $vagasReg)}
    </div>

    <div class=\"col-sm-6 mrg-bottom\">
        <label>Presentes</label>
        {$f->input('number', 'form-control', 'inp-presentes', 'inp-presentes', '', '0', false, '', $presentes)}
    </div>

    <div class=\"col-sm-6 mrg-bottom\">
        <label>Faltas <small class=\"text-muted\">(calculado)</small></label>
        <input type=\"text\" class=\"form-control\" id=\"inp-faltas-preview\" readonly
               value=\"{$faltas}\" style=\"background:#f5f5f5;\">
    </div>

    <div class=\"col-sm-12 mrg-bottom\">
        <label>Observação</label>
        <textarea class=\"form-control\" name=\"inp-observacao\" id=\"inp-observacao\"
                  rows=\"2\" placeholder=\"Ex: Férias, Licença Prêmio...\">" . htmlspecialchars($obs) . "</textarea>
    </div>

    <div class=\"col-sm-12 mrg-bottom\">
        <button type=\"button\" class=\"btn btn-success submit\" id=\"btn-save-mensal\">
            <span class=\"glyphicon glyphicon-floppy-disk\"></span>
            " . ($mensal ? 'Atualizar' : 'Salvar') . "
        </button>
    </div>

</form>
</fieldset>";

// ----------------------------------------
// Seção de eventos (só aparece se já existe registro)
// ----------------------------------------
if ($mensal) {
    echo "<fieldset class=\"for-panel mrg-top\">
            <legend class=\"text-primary\">Eventos neste mês</legend>
            <div class=\"mrg-bottom\">
                <button type=\"button\" class=\"btn btn-primary btn-xs\" id=\"btn-novo-evento\">
                    <span class=\"glyphicon glyphicon-plus\"></span> Adicionar evento
                </button>
            </div>";

    // Formulário de novo evento (oculto por padrão)
    echo "<div id=\"frm-evento-container\" style=\"display:none;\">
        <form class=\"form-horizontal\" method=\"POST\"
              action=\"" . URL . "GestaoAgenda/saveEvento\"
              id=\"frm-evento\" name=\"frm-evento\">

            <input type=\"hidden\" name=\"inp-id-mensal\" value=\"{$idMensal}\">

            <div class=\"col-sm-3 mrg-bottom\">
                <label>Data do evento</label>
                {$f->input('text', 'form-control calendar', 'inp-data-evento', 'inp-data-evento', '', 'dd/mm/aaaa', '1', 'Informe a data')}
            </div>

            <div class=\"col-sm-3 mrg-bottom\">
                <label>Tipo</label>
                <select class=\"select\" name=\"inp-tipo-evento\" id=\"inp-tipo-evento\"
                        data-rule-required=\"1\" data-msg-required=\"Selecione o tipo\">
                    <option value=\"\">-- Selecione --</option>";

                    foreach ($tiposEvento as $val => $label) {
                        echo "<option value=\"{$val}\">{$label}</option>";
                    }

echo "           </select>
            </div>

            <div class=\"col-sm-3 mrg-bottom\" id=\"container-reagend\" style=\"display:none;\">
                <label>Data reagendamento</label>
                {$f->input('text', 'form-control calendar', 'inp-dt-reagend', 'inp-dt-reagend', '', 'dd/mm/aaaa', '', '')}
            </div>

            <div class=\"col-sm-12 mrg-bottom\">
                <label>Descrição</label>
                {$f->input('text', 'form-control', 'inp-descricao-evento', 'inp-descricao-evento', '', 'Descreva o evento', '1', 'Informe a descrição', '', 5)}
            </div>

            <div class=\"col-sm-12 mrg-bottom\">
                <input type=\"hidden\" name=\"inp-show-dashboard\" value=\"0\">
                <label>
                    <input type=\"checkbox\" name=\"inp-show-dashboard\" id=\"inp-show-dashboard\" value=\"1\">
                    Exibir no dashboard
                </label>
            </div>

            <div class=\"col-sm-12 mrg-bottom\">
                <button type=\"button\" class=\"btn btn-success submit\" id=\"btn-save-evento\">
                    <span class=\"glyphicon glyphicon-floppy-disk\"></span> Salvar evento
                </button>
                <button type=\"button\" class=\"btn btn-default\" id=\"btn-cancelar-evento\">
                    Cancelar
                </button>
            </div>

        </form>
    </div>";

    // Lista de eventos existentes
    if (empty($eventos)) {
        echo "<p class=\"text-muted\" style=\"padding:10px;\">Nenhum evento registrado neste mês.</p>";
    } else {
        echo "<table class=\"table table-condensed table-hover mrg-top\">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Descrição</th>
                        <th>Reagend.</th>
                        <th>Dashboard</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($eventos as $ev) {
            $tipo      = $ev['tipo'];
            $labelCls  = isset($tipoLabel[$tipo]) ? $tipoLabel[$tipo] : 'default';
            $tipoNome  = isset($tiposEvento[$tipo]) ? $tiposEvento[$tipo] : $tipo;
            $dataEv    = Functions::BRdateFormat($ev['data_evento']);
            $dtReagend = $ev['dt_reagend'] ? Functions::BRdateFormat($ev['dt_reagend']) : '—';

            echo "<tr>
                    <td>{$dataEv}</td>
                    <td><span class=\"label label-{$labelCls}\">{$tipoNome}</span></td>
                    <td>" . htmlspecialchars($ev['descricao']) . "</td>
                    <td>{$dtReagend}</td>
                    <td class=\"text-center\">
                        <input type=\"checkbox\" class=\"toggle-dashboard-evento\"
                               data-id=\"{$ev['id']}\"
                               " . (!empty($ev['show_dashboard']) ? 'checked' : '') . ">
                    </td>
                    <td>
                        <button type=\"button\"
                            class=\"btn btn-danger btn-xs btn-excluir-evento\"
                            data-id=\"{$ev['id']}\"
                            data-origem=\"mensal\"
                            title=\"Excluir evento\">
                            <span class=\"glyphicon glyphicon-trash\"></span>
                        </button>
                    </td>
                  </tr>";
        }

        echo "  </tbody>
              </table>";
    }

    echo "</fieldset>";
}
