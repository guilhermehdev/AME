<?php
/**
 * Controller - Gestão de Agendas Médicas
 * @author Guilherme
 */
class GestaoAgenda {

    private function converterDataAgenda($valor) {
        $valor = trim((string)$valor);

        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $valor, $partes)) {
            $dia = (int)$partes[1];
            $mes = (int)$partes[2];
            $ano = (int)$partes[3];
        } elseif (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $valor, $partes)) {
            $ano = (int)$partes[1];
            $mes = (int)$partes[2];
            $dia = (int)$partes[3];
        } else {
            return false;
        }

        return checkdate($mes, $dia, $ano)
            ? sprintf('%04d-%02d-%02d', $ano, $mes, $dia)
            : false;
    }

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

        if (!is_array($post)) {
            Functions::messages("msg", "Não foi possível ler os dados do formulário. Atualize a tela e tente novamente.", "danger");
            return;
        }

        $servidorPost = isset($post['inp-id-servidor']) ? trim($post['inp-id-servidor']) : '';
        $idServidor = ($servidorPost !== '' && strtolower($servidorPost) !== 'null') ? (int)$servidorPost : null;
        $idEspec    = isset($post['inp-id-espec']) ? (int)$post['inp-id-espec'] : 0;
        $mes        = isset($post['inp-mes']) ? (int)$post['inp-mes'] : 0;
        $ano        = isset($post['inp-ano']) ? (int)$post['inp-ano'] : 0;
        $vagasAme   = isset($post['inp-vagas-ame'])  ? $post['inp-vagas-ame']  : 0;
        $vagasReg   = isset($post['inp-vagas-reg'])  ? $post['inp-vagas-reg']  : 0;
        $presentes  = isset($post['inp-presentes'])  ? $post['inp-presentes']  : 0;
        $obs        = isset($post['inp-observacao']) ? $post['inp-observacao'] : null;
        $showDashboard = !empty($post['inp-show-dashboard']) ? 1 : 0;

        if ($idEspec <= 0 || $mes < 1 || $mes > 12 || $ano <= 0) {
            Functions::messages("msg", "Especialidade, mês ou ano inválido. Selecione o período novamente e tente salvar.", "danger");
            return;
        }

        $res = DaoGestaoAgenda::saveMensal(
            $idServidor, $idEspec, $mes, $ano,
            $vagasAme, $vagasReg, $presentes, $obs, $showDashboard
        );

        if ($res) {
            Functions::messages("msg", "Registro salvo com sucesso!", "success");
        } else {
            Functions::messages("msg", "Erro ao salvar registro.", "danger");
        }
    }

    public function deleteMensal($param) {
        $id = isset($param[2]) ? (int)$param[2] : 0;
        $sucesso = $id > 0 ? DaoGestaoAgenda::deleteMensal($id) : false;

        header('Content-Type: application/json');
        echo json_encode([
            'success' => (bool)$sucesso,
            'mensagem' => $sucesso ? 'Registro mensal excluído.' : 'Não foi possível excluir o registro mensal.'
        ]);
    }

    public function updateMensalDashboard($param) {
        $id = isset($param[2]) ? (int)$param[2] : 0;
        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $showDashboard = is_array($post) && !empty($post['show_dashboard']) ? 1 : 0;
        $resultado = $id > 0 ? DaoGestaoAgenda::updateMensalDashboard($id, $showDashboard) : false;

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $resultado !== false,
            'show_dashboard' => $showDashboard
        ]);
    }

    // ----------------------------------------
    // Eventos
    // ----------------------------------------

    public function saveEvento() {
        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (!is_array($post)) {
            Functions::messages("msg", "Não foi possível ler os dados do evento.", "danger");
            return;
        }

        $idMensal   = isset($post['inp-id-mensal']) ? (int)$post['inp-id-mensal'] : 0;
        $dataEvento = isset($post['inp-data-evento'])
                        ? $this->converterDataAgenda($post['inp-data-evento']) : false;
        $tipo       = isset($post['inp-tipo-evento']) ? trim($post['inp-tipo-evento']) : '';
        $descricao  = isset($post['inp-descricao-evento']) ? trim($post['inp-descricao-evento']) : '';
        $showDashboard = !empty($post['inp-show-dashboard']) ? 1 : 0;
        $dtReagend  = null;
        if (!empty($post['inp-dt-reagend'])) {
            $dtReagend = $this->converterDataAgenda($post['inp-dt-reagend']);
            if (!$dtReagend) {
                Functions::messages("msg", "Data de reagendamento inválida.", "danger");
                return;
            }
        }

        if ($idMensal <= 0) {
            Functions::messages("msg", "Registro mensal inválido.", "danger");
            return;
        }

        if (!$dataEvento) {
            Functions::messages("msg", "Data do evento inválida.", "danger");
            return;
        }

        if (!$tipo) {
            Functions::messages("msg", "Selecione o tipo do evento.", "danger");
            return;
        }

        if (!$descricao) {
            Functions::messages("msg", "Informe a descrição do evento.", "danger");
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
        $this->checkSession();
        $id = isset($param[2]) ? (int)$param[2] : 0;
        $sucesso = $id > 0 ? DaoGestaoAgenda::deleteEvento($id) : false;

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => (bool)$sucesso,
            'mensagem' => $sucesso ? 'Evento removido.' : 'Não foi possível excluir o evento.'
        ]);
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
        $dataIni = isset($param[2]) ? $param[2] : date('Y-m-d');
        $dataFim = isset($param[3]) ? $param[3] : $dataIni;
        $incluirFixos = $dataIni === date('Y-m-d') && $dataFim === date('Y-m-d');
        $eventos = DaoGestaoAgenda::getEventosDashboard($dataIni, $dataFim, $incluirFixos);
        $v = new TGui("gestao_agenda_dashboard_eventos");
        $v->addData("eventos", $eventos);
        $v->renderize(APP_VIEW_LIST, true);
    }

    public function getAlertasOcorrencias() {
        $this->checkSession();
        $hoje = date('Y-m-d');
        $amanha = date('Y-m-d', strtotime('+1 day'));

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(DaoGestaoAgenda::getAlertasOcorrencias($hoje, $amanha));
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

    public function getObservacoesDashboard() {
        $observacoes = DaoGestaoAgenda::getObservacoesDashboard();
        $v = new TGui("gestao_agenda_dashboard_observacoes");
        $v->addData("observacoes", $observacoes);
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
