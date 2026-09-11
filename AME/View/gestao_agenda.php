<?php
$f = new Functions();
$title      = $this->getData('title');
$mesAtual   = $this->getData('mesAtual');
$anoAtual   = $this->getData('anoAtual');
$especs     = DaoGestaoAgenda::getEspecs();

$meses = [
    1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',
    5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',
    9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'
];

echo
"<div class=\"col-sm-12\">
    <div class=\"page-header\">
        <h2><small>Painel ></small> {$title}
            <small class=\"pull-right\">
                <button type=\"button\" class=\"btn btn-default btn-sm call-modal\"
                    data-modal-title=\"Configurar Dashboard\"
                    data-modal-type=\"2\" data-modal-size=\"1\"
                    data-modal-cls=\"sm-dialog\"
                    data-modal-href=\"GestaoAgenda/configDashboard\"
                    data-modal-params='{}'>
                    <span class=\"glyphicon glyphicon-cog\"></span> Configurar painel
                </button>
            </small>
        </h2>
    </div>

    <!-- ABAS -->
    <ul class=\"nav nav-tabs\" id=\"tabs-agenda\">
        <li class=\"active\"><a href=\"#tab-registro\" data-toggle=\"tab\">
            <span class=\"glyphicon glyphicon-pencil\"></span> Registro Mensal
        </a></li>
        <li><a href=\"#tab-historico\" data-toggle=\"tab\">
            <span class=\"glyphicon glyphicon-list\"></span> Histórico / Eventos
        </a></li>
        <li><a href=\"#tab-indicadores\" data-toggle=\"tab\">
            <span class=\"glyphicon glyphicon-stats\"></span> Indicadores
        </a></li>
    </ul>

    <div class=\"tab-content mrg-top\">

        <!-- ==========================================
             ABA 1: REGISTRO MENSAL
        =========================================== -->
        <div class=\"tab-pane active\" id=\"tab-registro\">

            <div class=\"col-sm-4\">
                <fieldset class=\"for-panel\">
                    <legend class=\"text-primary\">Buscar / Novo registro</legend>

                    <div class=\"col-sm-12 mrg-bottom\">
                        <label>Especialidade</label>
                        <select class=\"select\" id=\"slct-espec-reg\" name=\"slct-espec-reg\">
                            <option value=\"\">-- Selecione --</option>";

                            foreach ($especs as $e) {
                                echo "<option value=\"{$e['id']}\">{$e['especialidade']}</option>";
                            }

echo "           </select>
                    </div>

                    <div class=\"col-sm-12 mrg-bottom\">
                        <label>Profissional</label>
                        <select class=\"select\" id=\"slct-servidor-reg\" name=\"slct-servidor-reg\">
                            <option value=\"\">-- Selecione a especialidade --</option>
                        </select>
                    </div>

                    <div class=\"col-sm-6 mrg-bottom\">
                        <label>Mês</label>
                        <select class=\"select\" id=\"slct-mes-reg\" name=\"slct-mes-reg\">";

                        foreach ($meses as $num => $nome) {
                            $sel = $num == $mesAtual ? 'selected' : '';
                            echo "<option value=\"{$num}\" {$sel}>{$nome}</option>";
                        }

echo "              </select>
                    </div>

                    <div class=\"col-sm-6 mrg-bottom\">
                        <label>Ano</label>
                        <select class=\"select\" id=\"slct-ano-reg\" name=\"slct-ano-reg\">";
                        for ($y = $anoAtual; $y >= $anoAtual - 3; $y--) {
                            $sel = $y == $anoAtual ? 'selected' : '';
                            echo "<option value=\"{$y}\" {$sel}>{$y}</option>";
                        }
echo "              </select>
                    </div>

                    <div class=\"col-sm-12 mrg-bottom\">
                        <button type=\"button\" id=\"btn-buscar-mensal\" class=\"btn btn-primary btn-block\">
                            <span class=\"glyphicon glyphicon-search\"></span> Buscar / Carregar
                        </button>
                    </div>

                </fieldset>
            </div>

            <!-- Área de resultado/formulário -->
            <div class=\"col-sm-8\" id=\"container-registro-mensal\" name=\"container-registro-mensal\">
                <fieldset class=\"for-panel\">
                    <legend class=\"text-primary\">Dados do mês</legend>
                    <p class=\"text-muted\" style=\"padding:10px;\">
                        Selecione a especialidade, profissional, mês e ano ao lado e clique em Buscar.
                    </p>
                </fieldset>
            </div>

        </div><!-- /tab-registro -->


        <!-- ==========================================
             ABA 2: HISTÓRICO / EVENTOS
        =========================================== -->
        <div class=\"tab-pane\" id=\"tab-historico\">

            <fieldset class=\"for-panel\">
                <legend class=\"text-primary\">Filtros</legend>

                <div class=\"col-sm-3 mrg-bottom\">
                    <label>Especialidade</label>
                    <select class=\"select\" id=\"slct-espec-hist\" name=\"slct-espec-hist\">
                        <option value=\"\">Todas</option>";
                        foreach ($especs as $e) {
                            echo "<option value=\"{$e['id']}\">{$e['especialidade']}</option>";
                        }
echo "              </select>
                </div>

                <div class=\"col-sm-3 mrg-bottom\">
                    <label>Profissional</label>
                    <select class=\"select\" id=\"slct-servidor-hist\" name=\"slct-servidor-hist\">
                        <option value=\"\">Todos</option>
                    </select>
                </div>

                <div class=\"col-sm-2 mrg-bottom\">
                    <label>Mês inicial</label>
                    <select class=\"select\" id=\"slct-mes-ini-hist\" name=\"slct-mes-ini-hist\">
                        <option value=\"\">--</option>";
                        foreach ($meses as $num => $nome) {
                            $sel = $num == $mesAtual ? 'selected' : '';
                            echo "<option value=\"{$num}\" {$sel}>{$nome}</option>";
                        }
echo "              </select>
                </div>

                <div class=\"col-sm-2 mrg-bottom\">
                    <label>Ano inicial</label>
                    <select class=\"select\" id=\"slct-ano-ini-hist\" name=\"slct-ano-ini-hist\">";
                    for ($y = $anoAtual; $y >= $anoAtual - 3; $y--) {
                        $sel = $y == $anoAtual ? 'selected' : '';
                        echo "<option value=\"{$y}\" {$sel}>{$y}</option>";
                    }
echo "              </select>
                </div>

                <div class=\"col-sm-2 mrg-bottom\">
                    <label>&nbsp;</label><br>
                    <button type=\"button\" id=\"btn-buscar-historico\" class=\"btn btn-primary btn-block\">
                        <span class=\"glyphicon glyphicon-search\"></span> Buscar
                    </button>
                </div>

                <!-- Sub-abas: registros mensais vs eventos -->
                <div class=\"col-sm-12 mrg-top\">
                    <ul class=\"nav nav-pills\">
                        <li class=\"active\">
                            <a href=\"#\" id=\"pill-registros\">Registros mensais</a>
                        </li>
                        <li>
                            <a href=\"#\" id=\"pill-eventos\">Eventos</a>
                        </li>
                    </ul>
                </div>

            </fieldset>

            <div class=\"col-sm-12\" id=\"container-historico\" name=\"container-historico\"></div>

        </div><!-- /tab-historico -->


        <!-- ==========================================
             ABA 3: INDICADORES
        =========================================== -->
        <div class=\"tab-pane\" id=\"tab-indicadores\">

            <fieldset class=\"for-panel\">
                <legend class=\"text-primary\">Período</legend>

                <div class=\"col-sm-3 mrg-bottom\">
                    <label>Mês</label>
                    <select class=\"select\" id=\"slct-mes-ind\" name=\"slct-mes-ind\">";
                    foreach ($meses as $num => $nome) {
                        $sel = $num == $mesAtual ? 'selected' : '';
                        echo "<option value=\"{$num}\" {$sel}>{$nome}</option>";
                    }
echo "              </select>
                </div>

                <div class=\"col-sm-2 mrg-bottom\">
                    <label>Ano</label>
                    <select class=\"select\" id=\"slct-ano-ind\" name=\"slct-ano-ind\">";
                    for ($y = $anoAtual; $y >= $anoAtual - 3; $y--) {
                        $sel = $y == $anoAtual ? 'selected' : '';
                        echo "<option value=\"{$y}\" {$sel}>{$y}</option>";
                    }
echo "              </select>
                </div>

                <div class=\"col-sm-2 mrg-bottom\">
                    <label>&nbsp;</label><br>
                    <button type=\"button\" id=\"btn-buscar-indicadores\" class=\"btn btn-primary btn-block\">
                        <span class=\"glyphicon glyphicon-stats\"></span> Gerar
                    </button>
                </div>

            </fieldset>

            <div class=\"col-sm-12\" id=\"container-indicadores\" name=\"container-indicadores\"></div>

        </div><!-- /tab-indicadores -->

    </div><!-- /tab-content -->

</div>";