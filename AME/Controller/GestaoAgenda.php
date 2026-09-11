<?php
/**
 * Controller - Gestão de Agendas Médicas
 * @author Guilherme
 */
class GestaoAgenda {

    private function checkSession() {
        if (!AppController::checkSession()) {
            header("location: " . URL . "Loginadm/login");
            exit;
        }
    }

    // ----------------------------------------
    // Tela principal (3 abas)
    // ----------------------------------------

    public function index() {
        $this->checkSession();
        $v = new TGui("gestao_agenda");
        $v->addData("title", "Gestão de Agendas");
        $v->addData("mesAtual", date('n'));
        $v->addData("anoAtual", date('Y'));
        $v->renderize(APP_VIEW);
    }

    // ----------------------------------------
    // Aba 1: Registro Mensal
    // ----------------------------------------

    public function getServidoresByEspec($param) {
        $idEspec = $param[2];
        $data = DaoGestaoAgenda::getServidoresByEspec($idEspec);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function getMensal($param) {
        $idServidor = $param[2] != 'null' ? $param[2] : null;
        $idEspec    = $param[3];
        $mes        = $param[4];
        $ano        = $param[5];

        $mensal  = DaoGestaoAgenda::getMensal($idServidor, $idEspec, $mes, $ano);
        $eventos = $mensal ? DaoGestaoAgenda::getEventosByMensal($mensal['id']) : [];

        $v = new TGui("gestao_agenda_mensal");
        $v->addData("mensal",  $mensal);
        $v->addData("eventos", $eventos);
        $v->renderize(APP_VIEW_LIST, true);
    }

    public function saveMensal() {
        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        $idServidor = !empty($post['inp-id-servidor']) ? $post['inp-id-servidor'] : null;
        $idEspec    = $post['inp-id-espec'];
        $mes        = $post['inp-mes'];
        $ano        = $post['inp-ano'];
        $vagasAme   = $post['inp-vagas-ame']   ?? 0;
        $vagasReg   = $post['inp-vagas-reg']   ?? 0;
        $presentes  = $post['inp-presentes']   ?? 0;
        $obs        = $post['inp-observacao']  ?? null;

        $res = DaoGestaoAgenda::saveMensal(
            $idServidor, $idEspec, $mes, $ano,
            $vagasAme, $vagasReg, $presentes, $obs
        );

        if ($res) {
            Functions::messages("msg", "Registro salvo com sucesso!", "success");
        } else {
            Functions::messages("msg", "Erro ao salvar registro.", "danger");
        }
    }

    // ----------------------------------------
    // Eventos
    // ----------------------------------------

    public function saveEvento() {
        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        $idMensal   = $post['inp-id-mensal'];
        $dataEvento = Functions::validateDate($post['inp-data-evento'], 'BR', 'EN');
        $tipo       = $post['inp-tipo-evento'];
        $descricao  = $post['inp-descricao-evento'];
        $showDashboard = !empty($post['inp-show-dashboard']) ? 1 : 0;
        $dtReagend  = !empty($post['inp-dt-reagend'])
                        ? Functions::validateDate($post['inp-dt-reagend'], 'BR', 'EN')
                        : null;

        if (!$dataEvento) {
            Functions::messages("msg", "Data do evento inválida.", "danger");
            return;
        }

        $res = DaoGestaoAgenda::saveEvento($idMensal, $dataEvento, $tipo, $descricao, $dtReagend, $showDashboard);

        if ($res) {
            Functions::messages("msg", "Evento registrado!", "success");
        } else {
            Functions::messages("msg", "Erro ao salvar evento.", "danger");
        }
    }

    public function deleteEvento($param) {
        $id = $param[2];
        DaoGestaoAgenda::deleteEvento($id);
        Functions::messages("msg", "Evento removido.", "success");
    }

    public function updateEventoDashboard($param) {
        $id = $param[2];
        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $showDashboard = !empty($post['show_dashboard']) ? 1 : 0;
        DaoGestaoAgenda::updateEventoDashboard($id, $showDashboard);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'show_dashboard' => $showDashboard]);
    }

    // ----------------------------------------
    // Aba 2: Histórico / Eventos
    // ----------------------------------------

    public function getHistorico($param) {
        $idEspec    = $param[2] != 'null' ? $param[2] : null;
        $idServidor = $param[3] != 'null' ? $param[3] : null;
        $mesIni     = $param[4] != 'null' ? $param[4] : null;
        $anoIni     = $param[5] != 'null' ? $param[5] : null;
        $mesFim     = $param[6] != 'null' ? $param[6] : null;
        $anoFim     = $param[7] != 'null' ? $param[7] : null;

        $historico = DaoGestaoAgenda::getHistorico(
            $idEspec, $idServidor, $mesIni, $anoIni, $mesFim, $anoFim
        );

        $v = new TGui("gestao_agenda_historico");
        $v->addData("historico", $historico);
        $v->renderize(APP_VIEW_LIST, true);
    }

    public function getEventosHistorico($param) {
        $idEspec    = $param[2] != 'null' ? $param[2] : null;
        $idServidor = $param[3] != 'null' ? $param[3] : null;
        $tipo       = $param[4] != 'null' ? $param[4] : null;
        $dataIni    = !empty($param[5]) && $param[5] != 'null'
                        ? Functions::validateDate($param[5], 'BR', 'EN') : null;
        $dataFim    = !empty($param[6]) && $param[6] != 'null'
                        ? Functions::validateDate($param[6], 'BR', 'EN') : null;

        $eventos = DaoGestaoAgenda::getEventosHistorico(
            $idEspec, $idServidor, $tipo, $dataIni, $dataFim
        );

        $v = new TGui("gestao_agenda_eventos");
        $v->addData("eventos", $eventos);
        $v->renderize(APP_VIEW_LIST, true);
    }

    public function getTodosEventos() {
        $eventos = DaoGestaoAgenda::getEventosHistorico();
        $v = new TGui("gestao_agenda_todos_eventos");
        $v->addData("eventos", $eventos);
        $v->renderize(APP_VIEW_LIST, true);
    }

    // ----------------------------------------
    // Aba 3: Indicadores
    // ----------------------------------------

    public function getIndicadores($param) {
        $mes = $param[2];
        $ano = $param[3];

        $dados = DaoGestaoAgenda::getIndicadores($mes, $ano);

        // Agrupa por especialidade para facilitar a view
        $agrupado = [];
        foreach ($dados as $row) {
            $espec = $row['especialidade'];
            if (!isset($agrupado[$espec])) {
                $agrupado[$espec] = [
                    'total_ofertadas' => 0,
                    'total_presentes' => 0,
                    'total_faltas'    => 0,
                    'profissionais'   => [],
                ];
            }
            $agrupado[$espec]['total_ofertadas'] += $row['vagas_ofertadas'];
            $agrupado[$espec]['total_presentes'] += $row['presentes'];
            $agrupado[$espec]['total_faltas']    += $row['faltas'];
            $agrupado[$espec]['profissionais'][]  = $row;
        }

        $v = new TGui("gestao_agenda_indicadores");
        $v->addData("indicadores", $agrupado);
        $v->addData("mes", $mes);
        $v->addData("ano", $ano);
        $v->renderize(APP_VIEW_LIST, true);
    }

    // ----------------------------------------
    // Dashboard
    // ----------------------------------------

    public function getEventosDashboard($param) {
        $dataIni = $param[2] ?? date('Y-m-d');
        $dataFim = $param[3] ?? $dataIni;
        $incluirFixos = $dataIni === date('Y-m-d') && $dataFim === date('Y-m-d');
        $eventos = DaoGestaoAgenda::getEventosDashboard($dataIni, $dataFim, $incluirFixos);
        $v = new TGui("gestao_agenda_dashboard_eventos");
        $v->addData("eventos", $eventos);
        $v->renderize(APP_VIEW_LIST, true);
    }

    public function getDatasComEventos($param) {
        $ano = isset($param[2]) ? (int)$param[2] : (int)date('Y');
        $datas = DaoGestaoAgenda::getDatasComEventos($ano . '-01-01', $ano . '-12-31');
        header('Content-Type: application/json');
        echo json_encode($datas);
    }

    public function getDashboard($param) {
        $mes = $param[2];
        $ano = $param[3];

        $dados = DaoGestaoAgenda::getDashboard($mes, $ano);

        // Agrupa por especialidade
        $agrupado = [];
        foreach ($dados as $row) {
            $agrupado[$row['especialidade']][] = $row;
        }

        $v = new TGui("gestao_agenda_dashboard");
        $v->addData("dados", $agrupado);
        $v->addData("mes", $mes);
        $v->addData("ano", $ano);
        $v->renderize(APP_VIEW_LIST, true);
    }

    public function saveDashboardConfig() {
        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $configs = json_decode($post['configs'], true);

        if (DaoGestaoAgenda::saveDashboardConfig($configs)) {
            Functions::messages("msg", "Configuração salva!", "success");
        } else {
            Functions::messages("msg", "Erro ao salvar configuração.", "danger");
        }
    }

    public function configDashboard() {
        $this->checkSession();
        $config = DaoGestaoAgenda::getDashboardConfig();
        $v = new TGui("gestao_agenda_config");
        $v->addData("config", $config);
        $v->renderize(APP_VIEW_MODAL, true);
    }
}
