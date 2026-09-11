<?php
/**
 * DAO - Gestão de Agendas Médicas
 * @author Guilherme
 */
class DaoGestaoAgenda {

    // ----------------------------------------
    // Selects para popular dropdowns
    // ----------------------------------------

    public static function getEspecs() {
        $sql = "SELECT DISTINCT e.id, e.especialidade
                FROM especs e
                INNER JOIN serv_espec se ON se.id_espec = e.id
                WHERE e.ativo = 1
                ORDER BY e.especialidade";
        $ds = Maincontroller::doQuery($sql);
        $arr = [];
        while ($row = $ds->fetch(PDO::FETCH_ASSOC)) $arr[] = $row;
        return $arr;
    }

    public static function getServidoresByEspec($idEspec) {
        $sql = "SELECT s.id, s.nome
                FROM servidores s
                INNER JOIN serv_espec se ON se.id_servidor = s.id
                WHERE se.id_espec = :IDESPEC
                ORDER BY s.nome";
        $ds = Maincontroller::doQuery($sql, ['IDESPEC' => $idEspec]);
        $arr = [];
        while ($row = $ds->fetch(PDO::FETCH_ASSOC)) $arr[] = $row;
        return $arr;
    }

    // ----------------------------------------
    // Registro mensal
    // ----------------------------------------

    public static function getMensal($idServidor, $idEspec, $mes, $ano) {
        $sql = "SELECT am.*,
                       s.nome AS nome_servidor,
                       e.especialidade
                FROM agenda_mensal am
                LEFT JOIN servidores s ON s.id = am.id_servidor
                INNER JOIN especs e ON e.id = am.id_espec
                WHERE am.id_espec = :IDESPEC
                  AND am.mes = :MES
                  AND am.ano = :ANO
                  AND (am.id_servidor = :IDSERVIDOR OR (:IDSERVIDOR IS NULL AND am.id_servidor IS NULL))";
        $ds = Maincontroller::doQuery($sql, ['IDESPEC'=> $idEspec, 'MES'=> $mes, 'ANO'=> $ano, 'IDSERVIDOR' => $idServidor ?: null, ]);
        $row = $ds->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function saveMensal($idServidor, $idEspec, $mes, $ano, $vagasAme, $vagasReg, $presentes, $obs) {
        // Upsert: insere ou atualiza se já existir
        $sql = "INSERT INTO agenda_mensal
                  (id_servidor, id_espec, mes, ano, vagas_ame, vagas_reg, presentes, observacao)
                VALUES
                  (:IDSERV, :IDESPEC, :MES, :ANO, :AME, :REG, :PRES, :OBS)
                ON DUPLICATE KEY UPDATE
                  vagas_ame   = VALUES(vagas_ame),
                  vagas_reg   = VALUES(vagas_reg),
                  presentes   = VALUES(presentes),
                  observacao  = VALUES(observacao)";
        return Maincontroller::doQuery($sql, ['IDSERV'  => $idServidor ?: null, 'IDESPEC' => $idEspec, 'MES'     => $mes, 'ANO'     => $ano, 'AME'     => (int)$vagasAme,   'REG'     => (int)$vagasReg, 'PRES'    => (int)$presentes, 'OBS'     => $obs ?: null, ]);
    }

    public static function deleteMensal($id) {
        $sql = "DELETE FROM agenda_mensal WHERE id = :ID";
        return Maincontroller::doQuery($sql, ['ID' => $id]);
    }

    // ----------------------------------------
    // Eventos
    // ----------------------------------------

    public static function getEventosByMensal($idMensal) {
        $sql = "SELECT * FROM agenda_eventos
                WHERE id_mensal = :IDMENSAL
                ORDER BY data_evento ASC";
        $ds = Maincontroller::doQuery($sql, ['IDMENSAL' => $idMensal]);
        $arr = [];
        while ($row = $ds->fetch(PDO::FETCH_ASSOC)) $arr[] = $row;
        return $arr;
    }

    public static function saveEvento($idMensal, $dataEvento, $tipo, $descricao, $dtReagend) {
        $sql = "INSERT INTO agenda_eventos
                  (id_mensal, data_evento, tipo, descricao, dt_reagend)
                VALUES
                  (:IDMENSAL, :DATA, :TIPO, :DESC, :REAGEND)";
        return Maincontroller::doQuery($sql, [
            'IDMENSAL' => $idMensal,
            'DATA'     => $dataEvento,
            'TIPO'     => $tipo,
            'DESC'     => $descricao,
            'REAGEND'  => $dtReagend ?: null,
        ]);
    }

    public static function deleteEvento($id) {
        $sql = "DELETE FROM agenda_eventos WHERE id = :ID";
        return Maincontroller::doQuery($sql, ['ID' => $id]);
    }

    // ----------------------------------------
    // Histórico
    // ----------------------------------------

    public static function getHistorico($idEspec = null, $idServidor = null,
                                        $mesIni = null, $anoIni = null,
                                        $mesFim = null, $anoFim = null) {
        $where = "WHERE 1=1";
        $params = [];

        if ($idEspec) {
            $where .= " AND am.id_espec = :IDESPEC";
            $params['IDESPEC'] = $idEspec;
        }
        if ($idServidor) {
            $where .= " AND am.id_servidor = :IDSERV";
            $params['IDSERV'] = $idServidor;
        }
        if ($anoIni && $mesIni) {
            $where .= " AND (am.ano > :ANOINI OR (am.ano = :ANOINI AND am.mes >= :MESINI))";
            $params['ANOINI'] = $anoIni;
            $params['MESINI'] = $mesIni;
        }
        if ($anoFim && $mesFim) {
            $where .= " AND (am.ano < :ANOFIM OR (am.ano = :ANOFIM AND am.mes <= :MESFIM))";
            $params['ANOFIM'] = $anoFim;
            $params['MESFIM'] = $mesFim;
        }

        $sql = "SELECT am.id, am.mes, am.ano,
                       am.vagas_ofertadas, am.presentes, am.faltas, am.observacao,
                       s.nome AS nome_servidor,
                       e.especialidade,
                       COUNT(ae.id) AS total_eventos
                FROM agenda_mensal am
                LEFT JOIN servidores s ON s.id = am.id_servidor
                INNER JOIN especs e ON e.id = am.id_espec
                LEFT JOIN agenda_eventos ae ON ae.id_mensal = am.id
                {$where}
                GROUP BY am.id
                ORDER BY am.ano DESC, am.mes DESC, e.especialidade, s.nome";

        $ds = Maincontroller::doQuery($sql, $params);
        $arr = [];
        while ($row = $ds->fetch(PDO::FETCH_ASSOC)) $arr[] = $row;
        return $arr;
    }

    public static function getEventosHistorico($idEspec = null, $idServidor = null,
                                               $tipo = null, $dataIni = null, $dataFim = null) {
        $where = "WHERE 1=1";
        $params = [];

        if ($idEspec) {
            $where .= " AND am.id_espec = :IDESPEC";
            $params['IDESPEC'] = $idEspec;
        }
        if ($idServidor) {
            $where .= " AND am.id_servidor = :IDSERV";
            $params['IDSERV'] = $idServidor;
        }
        if ($tipo) {
            $where .= " AND ae.tipo = :TIPO";
            $params['TIPO'] = $tipo;
        }
        if ($dataIni) {
            $where .= " AND ae.data_evento >= :DATAINI";
            $params['DATAINI'] = $dataIni;
        }
        if ($dataFim) {
            $where .= " AND ae.data_evento <= :DATAFIM";
            $params['DATAFIM'] = $dataFim;
        }

        $sql = "SELECT ae.*,
                       s.nome AS nome_servidor,
                       e.especialidade
                FROM agenda_eventos ae
                INNER JOIN agenda_mensal am ON am.id = ae.id_mensal
                LEFT JOIN servidores s ON s.id = am.id_servidor
                INNER JOIN especs e ON e.id = am.id_espec
                {$where}
                ORDER BY ae.data_evento DESC";

        $ds = Maincontroller::doQuery($sql, $params);
        $arr = [];
        while ($row = $ds->fetch(PDO::FETCH_ASSOC)) $arr[] = $row;
        return $arr;
    }

    // ----------------------------------------
    // Indicadores
    // ----------------------------------------

    public static function getIndicadores($mes, $ano) {
        $sql = "SELECT e.especialidade,
                       s.nome AS nome_servidor,
                       am.vagas_ame,
                       am.vagas_reg,
                       am.vagas_ofertadas,
                       am.presentes,
                       am.faltas,
                       am.observacao,
                       ROUND(am.faltas / NULLIF(am.vagas_ofertadas, 0) * 100, 1) AS pct_faltas,
                       COUNT(ae.id) AS total_eventos
                FROM agenda_mensal am
                LEFT JOIN servidores s ON s.id = am.id_servidor
                INNER JOIN especs e ON e.id = am.id_espec
                LEFT JOIN agenda_eventos ae ON ae.id_mensal = am.id
                WHERE am.mes = :MES AND am.ano = :ANO
                GROUP BY am.id
                ORDER BY e.especialidade, s.nome";
        $ds = Maincontroller::doQuery($sql, ['MES' => $mes, 'ANO' => $ano]);
        $arr = [];
        while ($row = $ds->fetch(PDO::FETCH_ASSOC)) $arr[] = $row;
        return $arr;
    }

    // ----------------------------------------
    // Dashboard
    // ----------------------------------------

    public static function getDashboard($mes, $ano) {
        $sql = "SELECT e.id AS id_espec,
                       e.especialidade,
                       s.id AS id_servidor,
                       s.nome AS nome_servidor,
                       am.vagas_ofertadas,
                       am.presentes,
                       am.faltas,
                       am.observacao,
                       ROUND(am.presentes / NULLIF(am.vagas_ofertadas, 0) * 100, 0) AS pct_aproveit,
                       COUNT(ae.id) AS total_eventos
                FROM agenda_dashboard_config dc
                INNER JOIN especs e ON e.id = dc.id_espec
                INNER JOIN agenda_mensal am ON am.id_espec = e.id
                  AND am.mes = :MES AND am.ano = :ANO
                LEFT JOIN servidores s ON s.id = am.id_servidor
                LEFT JOIN agenda_eventos ae ON ae.id_mensal = am.id
                WHERE dc.visivel = 1
                GROUP BY am.id
                ORDER BY dc.ordem, e.especialidade, s.nome";
        $ds = Maincontroller::doQuery($sql, ['MES' => $mes, 'ANO' => $ano]);
        $arr = [];
        while ($row = $ds->fetch(PDO::FETCH_ASSOC)) $arr[] = $row;
        return $arr;
    }

    public static function getDashboardConfig() {
        $sql = "SELECT dc.id_espec, dc.visivel, dc.ordem, e.especialidade
                FROM agenda_dashboard_config dc
                INNER JOIN especs e ON e.id = dc.id_espec
                ORDER BY dc.ordem";
        $ds = Maincontroller::doQuery($sql);
        $arr = [];
        while ($row = $ds->fetch(PDO::FETCH_ASSOC)) $arr[] = $row;
        return $arr;
    }

    public static function saveDashboardConfig($configs) {
        // $configs = array de ['id_espec' => x, 'visivel' => 0|1, 'ordem' => n]
        foreach ($configs as $c) {
            $sql = "UPDATE agenda_dashboard_config
                    SET visivel = :VIS, ordem = :ORDEM
                    WHERE id_espec = :IDESPEC";
            Maincontroller::doQuery($sql, [
                'VIS'     => (int)$c['visivel'],
                'ORDEM'   => (int)$c['ordem'],
                'IDESPEC' => (int)$c['id_espec'],
            ]);
        }
        return true;
    }
}