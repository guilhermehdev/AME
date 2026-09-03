<?php

/**
 * Description of Reports
 *
 * @author guilh
 */
class Reports extends extFPDF {
    
     public function printReception(){               
            $view = new TGui("recepcao");
            $view->addData("title", "Impressos Recepção");
            $view->renderize(APP_VIEW);
    }

    public function agendas($param) {       
        $rep = new Reportcontroller();
        $pdf = new Reports('P','mm');
        $mc = new Maincontroller();        
        $idUnidade = $param[2];
        
        if(Functions::removeQuotes($param[3]) != ''){
            $idEspec = Functions::removeQuotes($param[3]);
            $espec = "AND agenda.FKespec={$idEspec}";
        } else {
            $idEspec = null;
        }
        
        if(Functions::removeQuotes($param[4]) != ''){
            $idProf = Functions::removeQuotes($param[4]);
            $pro = "AND agenda.FKprof={$idProf}";
        } else {
            $idProf = null;
        }               
        
        if($param[5] != ''){
            $dtIni = Functions::ENdateFormat(($param[5]));
        } else {
            $dtIni = null;
        } 
        
        if($param[6] != ''){
            $dtFin = Functions::ENdateFormat(($param[6]));
        } else {
            $dtFin = null;
        }  
       
        
        $sql = "SELECT agenda.`data`,agenda.FKespec,especs.especialidade,unidades.descricao AS unidade,servidores.id AS idprof,servidores.nome AS profissional
            
        FROM agenda

        JOIN especs ON agenda.FKespec = especs.id
        JOIN unidades ON agenda.FKunidade = unidades.id
        JOIN servidores ON agenda.FKprof = servidores.id

        WHERE agenda.FKunidade = {$idUnidade}
        AND agenda.`data` BETWEEN '{$dtIni}' AND '{$dtFin}' {$espec} {$pro}

        GROUP BY agenda.FKprof,agenda.FKespec";
        $ds = $mc->doQuery($sql);  
                              	
	$arrEspecs = array();        
        while($row = $ds->fetch(PDO::FETCH_ASSOC)){
            $arrEspecs[] = $row;
        }
                              
        $unidade = $arrEspecs[0]['unidade'];
        $mesEN = strftime('%B',strtotime($arrEspecs[0]['data']));
        $ano = strftime('%Y',strtotime($arrEspecs[0]['data']));
               
        switch ($mesEN) {
            case 'January ':
                $mes = 'JANEIRO'; 
                break;
            case 'February':
                $mes = 'FEVEREIRO';
                break;
            case 'March':
                $mes = 'MARÇO';
                break;
            case 'April':
                $mes = 'ABRIL';
                break;
            case 'May':
                $mes = 'MAIO';
                break;
            case 'June':
                $mes = 'JUNHO';
                break;
            case 'July':
                $mes = 'JULHO';
                break;
            case 'August':
                $mes = 'AGOSTO';
                break;
            case 'September':
                $mes = 'SETEMBRO';
                break;
            case 'October':
                $mes = 'OUTUBRO';
                break;
            case 'November':
                $mes = 'NOVEMBRO';
                break;
            case 'December':
                $mes = 'DEZEMBRO';
                break;
        }
        
        //var_dump($arrEspecs[0]['data']);
        
        $pdf->AddPage();
        $pdf->SetMargins(6, 5, 5, 5);
        $pdf->SetFont('Arial','BU',16);
        
        $pdf->SetTitle("VAGAS AME - ".$unidade,true); 
        
        $pdf->Cell(0,7,utf8_decode('VAGAS PARA AGENDAMENTO DO AME'),0,1,'C');
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(0,7,utf8_decode('UNIDADE: '.$unidade),0,1,'C');
        $pdf->Cell(0,7,utf8_decode('MÊS: '.$mes . " " .$ano),0,1,'C');
        
        $pdf->Ln(5);
        
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(0,7,utf8_decode('FAVOR ENVIAR OS AGENDADOS COM ANTECEDÊNCIA DE NO MÍNIMO 2 DIAS'),0,1,'C');
        
        $pdf->Ln(10);        
             
        foreach ($arrEspecs as $value) {
            
            $sql2 = "SELECT agenda.`data`,agenda.dia,agenda.hora 
            
            FROM agenda

            WHERE agenda.FKunidade ={$idUnidade}

            AND agenda.FKespec = {$value['FKespec']} 
            AND agenda.FKprof = {$value['idprof']} 
            AND agenda.`data` BETWEEN '{$dtIni}' AND '{$dtFin}'
                
            ORDER BY agenda.`data`,agenda.hora
            ";
            $ds2 = $mc->doQuery($sql2);
              	
            $arrVagas = array();        
            while($row2 = $ds2->fetch(PDO::FETCH_ASSOC)){
                $arrVagas[] = $row2;
            }
            
            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(40,7,utf8_decode($value['especialidade'])." - ".utf8_decode($value['profissional']). "  /  VAGAS: " . count($arrVagas),0,1,'L'); 
            $pdf->SetFont('Arial','',10); 
                                
            foreach ($arrVagas as $value) { 
                $pdf->Cell(25,6,utf8_decode("   ".Functions::BRdateFormat($value['data'])." - "),0,0,'L');
                $pdf->Cell(25,6,utf8_decode($value['dia']. " ÀS: "),0,0,'L');
                $pdf->Cell(15,6,utf8_decode($value['hora']),0,0,'L');
                $pdf->Cell(60,6,"---- PACIENTE:__________________________________ NASC:____/____/_____",0,1,'L');
            }
            
            $pdf->Ln(3);
        }      
        
        $rep->renderizeReport($pdf, TRUE, "","agendas-".$unidade);        
    } 
    
    public function perdas($params) {
        $f = new Functions();
        $rep = new Reportcontroller();
        $pdf = new Reports('P','mm');
        
        $idUnidade = $f->cleanString($params[2]);
        $idEspec = $f->cleanString($params[3]);
        $idProf = $f->cleanString($params[4]);
        $mes = $params[5];
        $ano = $params[6];
                        
        if($idEspec != '')
            $espec = "AND perdas.FKespec ={$idEspec}";
            
        if($idProf != '')
            $prof = "AND perdas.FKprof ={$idProf}";
       
        if($idUnidade != '') {
            $uni = "AND perdas.FKunidade ={$idUnidade}";            
        } else {
            $uni = "";
            $unidade = "TODOS";
        }
        
        $qryUnidade = "SELECT DISTINCT perdas.FKunidade AS FKuni,unidades.descricao AS unidade  
        FROM perdas
        JOIN unidades ON perdas.FKunidade = unidades.id
       
        WHERE  
        MONTH(perdas.`data`) =:MES 
        AND YEAR(perdas.`data`) =:ANO
        {$uni}
        {$espec}
        {$prof}";
        
        $dsUni =  Maincontroller::doQuery($qryUnidade, array('MES'=>$mes,'ANO'=>$ano));
        $arrUni = array();        
        while($row = $dsUni->fetch(PDO::FETCH_ASSOC)){            
            $arrUni[] = $row;
        }
        
        if(count($arrUni) > 0) {
        
            if($uni != '') {
                $unidade = $arrUni[0]['unidade'];
            }

            //var_dump($perdas);            

            $pdf->AddPage();
            $pdf->SetTitle("RELATÓRIO DE PERDAS - ".$unidade,true);

            $pdf->SetFont('Arial','BU',13);
            $pdf->Cell(0,7, utf8_decode("RELATÓRIO DE PERDA DE VAGAS")." - ".$f->monthExtense($mes)." ".$ano ,0,1,'C');
            $pdf->Ln(10);

            foreach ($arrUni as $u) { 

                $qryEspec = "SELECT DISTINCT perdas.FKespec,perdas.FKprof,perdas.FKunidade AS FKuni,unidades.descricao AS unidade,servidores.nome AS profissional,especs.especialidade 

                FROM perdas
                JOIN unidades ON perdas.FKunidade = unidades.id
                JOIN servidores ON servidores.id = perdas.FKprof
                JOIN especs ON especs.id = perdas.FKespec
                WHERE  
                MONTH(perdas.`data`) =:MES 
                AND YEAR(perdas.`data`) =:ANO
                AND perdas.FKunidade =:IDUNIDADE
                {$espec}
                {$prof}";

                $dsEspec =  Maincontroller::doQuery($qryEspec, array('MES'=>$mes,'ANO'=>$ano,'IDUNIDADE'=>$u['FKuni']));
                $arrEspec = array();        
                while($row = $dsEspec->fetch(PDO::FETCH_ASSOC)){
                    $arrEspec[] = $row;             
                }

                $pdf->SetFont('Arial','B',12);
                $pdf->Cell(0,7,utf8_decode("UNIDADE: ".$u['unidade']),0,1,'C'); 
                $pdf->Ln(5);

                $totalGeral = 0;

                foreach ($arrEspec as $e) {         
                    $unidade = $u['unidade'];
                    $profissional = $e['profissional']; 
                    $especialidade = $e['especialidade'];
                    $idpro = $e['FKprof'];
                    $idEspec = $e['FKespec'];

                    $pdf->SetFont('Arial','BI',11);
                    $pdf->Cell(0,7,utf8_decode($especialidade)." - ".utf8_decode($profissional),0,1,'L'); 

                    $qryPerdas = "SELECT COUNT(perdas.id) AS perdas,perdas.`data` AS data FROM perdas
                    WHERE 
                    perdas.FKunidade ={$u['FKuni']}
                    AND MONTH(perdas.`data`) =:MES 
                    AND YEAR(perdas.`data`) =:ANO 
                    AND perdas.FKespec =:IDESPEC
                    AND perdas.FKprof =:IDPRO
                    GROUP BY perdas.`data`";

                    $ds =  Maincontroller::doQuery($qryPerdas, array('MES'=>$mes,'ANO'=>$ano,'IDESPEC'=>$idEspec,'IDPRO'=>$idpro));
                    $arrPerdas = array();        
                    while($row = $ds->fetch(PDO::FETCH_ASSOC)){
                        $arrPerdas[] = $row;
                    }

                    $total = 0;

                    $pdf->SetFont('Arial','b',10);
                    $pdf->Cell(30,7,"Data",0,0,'L');
                    $pdf->Cell(40,7,"Vagas perdidas",0,1,'L');

                    foreach ($arrPerdas as $p) {
                        $pdf->SetFont('Arial','',10);
                        $pdf->Cell(30,7,$f->BRdateFormat($p['data']),0,0,'L');
                        $pdf->Cell(40,7,$p['perdas'],0,1,'L');
                        $total += $p['perdas'];
                        $totalGeral += $p['perdas'];
                    }
                    $pdf->SetFont('Arial','b',10);
                    $pdf->Cell(0,2,"Subtotal: ".$total,0,1,'R');
                    $pdf->SetFont('Arial','',10);
                    $pdf->Cell(0,7,"-----------------------------------------------------------------------------------------------------------------------------------------------------------------",0,1,'C');
                    $pdf->Ln(3);
                }

                $pdf->SetFont('Arial','b',12);
                $pdf->Cell(0,2,"Total: ".$totalGeral,0,1,'R');
                $pdf->Cell(0,7,"________________________________________________________________________________________",0,1,'C');
                $pdf->Ln(8);
            }
               
            $rep->renderizeReport($pdf, TRUE, "","perdas-".$unidade);
        } else {
          echo "<script>
                    alert('Nenhum registro encontrado!');
                    window.close();
                </script>";
        }
    }
    
    public function gradeUBS($params) {        
        $f = new Functions();
        $rep = new Reportcontroller();
        $pdf = new FPDF('P','mm','A4');
        $pdf->AddPage();
        $pdf->SetMargins(8, 15, 8, 10);
        $pdf->SetTitle("RELATÓRIO DE GRADES",true);
        
        $idUnidade = $f->cleanString($params[2]);
        $idEspec = $f->cleanString($params[3]);
        $idProf = $f->cleanString($params[4]);
        $mes = $params[5];
        $ano = $params[6];  
        
        if($idEspec != '')
            $espec = "AND grades.id_espec ={$idEspec}";
            
        if($idProf != '')
            $prof = "AND grades.id_servidor ={$idProf}";
               
        if($idUnidade != '') {
            $uni = "AND grades.id_unidade ={$idUnidade}";            
                
            $sql = "SELECT DISTINCT grades.id_unidade AS idUnidade,grades.unidade 
            FROM grades 
            WHERE grades.status =1 
            AND MONTH(grades.`data`) =:MES
            AND YEAR(grades.`data`) =:ANO 
            {$uni} {$prof} {$espec}";
            $dsUni = Maincontroller::doQuery($sql,array('MES'=>$mes,'ANO'=>$ano));

            $arrUni = array();        
            while($row = $dsUni->fetch(PDO::FETCH_ASSOC)){
                $arrUni[] = $row;
            }
                                      
            if(count($arrUni) > 0) {                           

                $pdf->SetFont('Arial','B',14);
                $pdf->Cell(0,7, utf8_decode("RELATÓRIO DE GRADE DE VAGAS | UBS/PSF")." - ".$f->monthExtense($mes)." ".$ano ,0,1,'C');
                $pdf->Ln(10);
            
                $sql = "SELECT distinct unidade,id_espec,id_servidor,profissional,especialidade,totalmes FROM grades
                WHERE grades.status =1 
                AND qtd > 0
                AND MONTH(grades.`data`) =:MES
                AND YEAR(grades.`data`) =:ANO 
                {$uni} {$prof} {$espec}";
                $dsProf = Maincontroller::doQuery($sql,array('MES'=>$mes,'ANO'=>$ano));

                $arrProf = array();        
                while($row = $dsProf->fetch(PDO::FETCH_ASSOC)){
                    $arrProf[] = $row;
                }    
                
                $pdf->SetFont('Arial','BU',16);
                $pdf->Cell(0,7, utf8_decode($arrProf[0]['unidade']) ,0,1,'L');  
                $pdf->Ln(5);
                
                    foreach ($arrProf as $p) {
                        $idespec = $p['id_espec'];
                        $idservidor = $p['id_servidor'];
                        
                        $sql = "SELECT data,dia,qtd FROM grades
                        WHERE grades.status =1 
                        AND MONTH(grades.`data`) =:MES
                        AND YEAR(grades.`data`) =:ANO 
                        AND id_espec=:IDESPEC
                        AND id_servidor=:IDSERVIDOR
                        {$uni} ";
                        $dsVagas = Maincontroller::doQuery($sql,array('MES'=>$mes,'ANO'=>$ano,'IDESPEC'=>$idespec,'IDSERVIDOR'=>$idservidor));

                        $arrVagas = array();        
                        while($row = $dsVagas->fetch(PDO::FETCH_ASSOC)){
                            $arrVagas[] = $row;
                        }  
                    
                        $pdf->SetFont('Arial','BI',14);
                        $pdf->Cell(0,7, utf8_decode($p['profissional']." - ".$p['especialidade']) ,1,1,'L');  
                        $pdf->Ln(5);
                        
                        foreach ($arrVagas as $v) {
                            
                            $pdf->SetFont('Arial','',13);
                            $pdf->MultiCell(0,7, ($f->BRdateFormat($v['data'])." - ".$v['dia'].": ".$v['qtd']." vgs") ,0,'L'); 
                            
                        }                                              
                        $pdf->Ln(2);
                        $pdf->SetFont('Arial','B',13);
                        $pdf->Cell(0,7, "TOTAL: ".$p['totalmes'] ,0,1,'R'); 
                        $pdf->Cell(0,7,"-------------------------------------------------------------------------------------------------------------------------------",0,1,'C');
                        $pdf->Ln(5);

                    }                   
                        
            $rep->renderizeReport($pdf, TRUE, "","grades");
            
            } else {
              echo "<script>
                        alert('Nenhum registro encontrado!');
                        window.close();
                    </script>";
            }

        } else {                                     

            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(0,7, utf8_decode("RELATÓRIO DE GRADE DE VAGAS | UBS/PSF")." - ".$f->monthExtense($mes)." ".$ano ,0,1,'C');
            $pdf->Ln(10);             
            
            $sqlUni = "SELECT DISTINCT id_unidade,unidade FROM grades
            WHERE grades.status =1 
            AND qtd > 0
            AND MONTH(grades.`data`) =:MES
            AND YEAR(grades.`data`) =:ANO";
            $dsUni = Maincontroller::doQuery($sqlUni,array('MES'=>$mes,'ANO'=>$ano));

            $arrUni = array();        
            while($row = $dsUni->fetch(PDO::FETCH_ASSOC)){
                $arrUni[] = $row;
            } 
            
            //var_dump($arrProf);  
            foreach ($arrUni as $u) {
                $sqlPro = "SELECT DISTINCT profissional,especialidade,id_servidor,id_espec,totalmes FROM grades
                WHERE grades.status =1 
                AND qtd > 0
                AND MONTH(grades.`data`) =:MES
                AND YEAR(grades.`data`) =:ANO 
                AND id_unidade =:IDUNIDADE
                {$prof} {$espec} ORDER BY especialidade";
                $dsProf = Maincontroller::doQuery($sqlPro,array('MES'=>$mes,'ANO'=>$ano,'IDUNIDADE'=>$u['id_unidade']));

                $arrPro = array();        
                while($row = $dsProf->fetch(PDO::FETCH_ASSOC)){
                    $arrPro[] = $row;
                }                                             
                $pdf->SetFont('Arial','BU',16);
                $pdf->Cell(0,7, utf8_decode($u['unidade']) ,0,1,'L');  
                $pdf->Ln(5);
                                              
                foreach ($arrPro as $p) {
                    $sqlTot = "SELECT sum(qtd) AS total FROM grades
                    WHERE grades.status =1 
                    AND qtd > 0
                    AND MONTH(grades.`data`)=:MES
                    AND YEAR(grades.`data`)=:ANO 
                    AND id_unidade=:IDUNIDADE
                    AND id_espec =:IDESPEC
                    AND id_servidor =:IDSERVIDOR";
                    $dsTotal = Maincontroller::doQuery($sqlTot,array('MES'=>$mes,'ANO'=>$ano,'IDESPEC'=>$p['id_espec'],'IDSERVIDOR'=>$p['id_servidor'],'IDUNIDADE'=>$u['id_unidade']));
                    $total = $dsTotal->fetch(PDO::FETCH_ASSOC);                    
                    
                    $sql = "SELECT data,dia,qtd FROM grades
                    WHERE grades.status =1 
                    AND qtd > 0
                    AND MONTH(grades.`data`) =:MES
                    AND YEAR(grades.`data`) =:ANO 
                    AND id_unidade=:IDUNIDADE
                    AND id_espec=:IDESPEC
                    AND id_servidor=:IDSERVIDOR";
                    $dsVagas = Maincontroller::doQuery($sql,array('MES'=>$mes,'ANO'=>$ano,'IDUNIDADE'=>$u['id_unidade'],'IDESPEC'=>$p['id_espec'],'IDSERVIDOR'=>$p['id_servidor']));

                    $arrVagas = array();        
                    while($row = $dsVagas->fetch(PDO::FETCH_ASSOC)){
                        $arrVagas[] = $row;
                    }                                        
                    
                    $pdf->SetFont('Arial','BI',14);
                    $pdf->Cell(0,7, utf8_decode($p['especialidade']." - ".$p['profissional']) ,1,1,'L');  
                    $pdf->Ln(5);
                    
                    foreach ($arrVagas as $v) {

                        $pdf->SetFont('Arial','',14);
                        $pdf->MultiCell(0,7, ($f->BRdateFormat($v['data'])."   -   ".$v['dia'].":   ".$v['qtd']." vgs") ,0,'L');                         
                        
                    }
                    
                    $pdf->Ln(2);
                    $pdf->SetFont('Arial','B',13);
                    $pdf->Cell(0,7, "TOTAL: ".$total['total']." vgs",0,1,'R'); 
                    $pdf->Cell(0,7,"-------------------------------------------------------------------------------------------------------------------------------",0,1,'C');
                    $pdf->Ln(5);                    
                } 
            }          
            $rep->renderizeReport($pdf, TRUE, "","grades");            
        }        
    }
    
    public function inventario($param) {
     
        $idUnidade = $param[2];
        $idSala = $param[3];
              
        $rep = new Reportcontroller();
        $pdf = new FPDF('P','mm');
               
        //$salas = Daopatrimonio::getSalas($idUnidade);
        $sala = Daopatrimonio::getSala($idSala);
        $dsUnidade = Daopatrimonio::getUnidades($idUnidade);
        $unidade = utf8_decode($dsUnidade[0]['descricao']);        
      
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 15, 10);
        $pdf->SetTitle("INVENTÁRIO - " . $dsUnidade[0]['descricao'], true);
                
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,7, utf8_decode("INVENTÁRIO")." - " . $unidade ,0,1,'C');
        
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(0,7,date("m/Y") ,0,1,'R');
        $pdf->Ln(10);
             
        $itens = Daopatrimonio::getItens($idUnidade,$idSala); 
        $total = count($itens);
                           
            $pdf->SetFont('Arial','',12);
            $pdf->Cell(30,5,"LOCAL / SALA: ",0,0,'L');
            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(0,5,utf8_decode($sala[0]['descricao']),0,1,'L');
            $pdf->Ln(5);
                                    
            $pdf->SetFont('Arial','IB',11);
            
            $pdf->Cell(10,6,'Qtd','LTRB',0,'L');
            $pdf->Cell(160,6,'Item','LTRB',0,'L');
            $pdf->Cell(23,6,utf8_decode('Patrimônio'),'LTRB',1,'L');
           // $pdf->Cell(20,8,'Estado','LTRB',1,'L');           
            
            foreach ($itens as $i) { 
                $item = utf8_decode($i['item']);                
                $pdf->SetFont('Arial','',10);  
                $pdf->Cell(10,6,$i['qtd'],'LTRB',0,'L');                 
                $pdf->Cell(160,6,$item,'LTRB',0,'L'); 
                $pdf->Cell(23,6,$i['patrimonio'] == 0 ? "SP":$i['patrimonio'],'LTRB',1,'L');
              //  $pdf->Cell(20,11,$i['estado'],'LTRB',1,'L'); 
            }
            $pdf->Ln(5);  
           
                // Position at 1.5 cm from bottom
                $pdf->SetY($pdf -> h - 33);
                // Arial italic 8
                $pdf->SetFont('Arial','I',11);
                // Page number
                $pdf->Cell(0,11,"Total: ".$total." itens",0,1,'C');
                       
               // $pdf->SetFont('Arial','',13);
               // $pdf->Cell(0,11,"Total: ".$total." itens",0,1,'C');
         
            
        $rep->renderizeReport($pdf, TRUE, "","inventario - ".$unidade. " - " .date('m/Y'));
    }
    public function inventarioUnidade($param) {
     
        $idUnidade = $param[2];
                    
        $rep = new Reportcontroller();
        $pdf = new FPDF('P','mm');
               
        $salas = Daopatrimonio::getSalas($idUnidade);
       
        $dsUnidade = Daopatrimonio::getUnidades($idUnidade);
        $unidade = utf8_decode($dsUnidade[0]['descricao']);  
        
        //var_dump(("INVENTÁRIO - ") . utf8_encode($unidade));
        
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 15, 10);
        $pdf->SetTitle("INVENTÁRIO - " . utf8_encode($unidade));
        
        $pdf->SetFont('Arial','B',12);        
        $pdf->Cell(0,7, utf8_decode("INVENTÁRIO")." - " . $unidade ,0,1,'C');  
        
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(0,7,date("m/Y") ,0,1,'R');
        $pdf->Ln(10);
              
        foreach ($salas as $sala) {   
          
            $itens = Daopatrimonio::getItens($idUnidade,$sala['id']); 
            $total = count($itens);

                $pdf->SetFont('Arial','',12);
                $pdf->Cell(30,5,"LOCAL / SALA: ",0,0,'L');
                $pdf->SetFont('Arial','B',12);
                $pdf->Cell(0,5,utf8_decode($sala['descricao']),0,1,'L');
                $pdf->Ln(5);

                $pdf->SetFont('Arial','IB',11);

                $pdf->Cell(10,6,'Qtd','LTRB',0,'L');
                $pdf->Cell(160,6,'Item','LTRB',0,'L');
                $pdf->Cell(23,6,utf8_decode('Patrimônio'),'LTRB',1,'L');           

                foreach ($itens as $i) { 
                    $item = utf8_decode($i['item']);                
                    $pdf->SetFont('Arial','',10);  
                    $pdf->Cell(10,6,$i['qtd'],'LTRB',0,'L');                 
                    $pdf->Cell(160,6,$item,'LTRB',0,'L'); 
                    $pdf->Cell(23,6,$i['patrimonio'] == 0 ? "SP":$i['patrimonio'],'LTRB',1,'L');                
                }
                $pdf->Ln(5);  

                    // Position at 1.5 cm from bottom
                  //  $pdf->SetY($pdf -> h - 33);
                    // Arial italic 8
                    $pdf->SetFont('Arial','I',11);
                    // Page number
                    $pdf->Cell(0,11,"Total: ".$total." itens",0,1,'C');
                    $pdf->Ln(5);  
                   // $pdf->SetFont('Arial','',13);
                   // $pdf->Cell(0,11,"Total: ".$total." itens",0,1,'C');
            }                        
            
        $rep->renderizeReport($pdf, TRUE, "","INVENTARIO - ".$unidade);
    }
    
    public function retornos($param){
        $rep = new Reportcontroller();
        $pdf = new Reports('P','mm');        
                      
        $pdf->AddPage();
        $pdf->SetMargins(10, 5, 5, 0);
        $pdf->SetTitle("RETORNO",true);
        
        $rep->renderizeReport($pdf, TRUE, "","retorno");
    }
    
    public function vagas() {       
        
        $view = new TGui("vagas_external");       
        $view->renderize(APP_VIEW,true);
        
    }
    
    public function graderesumido($params) {
        $mes = $params[5];
        $ano = $params[6]; 
        $f = new Functions();
        $rep = new Reportcontroller();
        $pdf = new FPDF('P','mm','A4');
        $pdf->AddPage();        
        $pdf->SetTitle("RELATÓRIO DE VAGAS - ".$f->monthExtense($mes)." ".$ano,true);

        
        $sql = "SELECT especialidade,profissional,id_servidor,id_espec FROM grades
        WHERE grades.status =1 
        AND qtd > 0
        AND MONTH(grades.`data`) =:MES
        AND YEAR(grades.`data`) =:ANO
        GROUP BY especialidade,profissional
        ORDER BY especialidade,profissional";
        $dsProf = Maincontroller::doQuery($sql,array('MES'=>$mes,'ANO'=>$ano));
        $arrProfEspec = array();        
        while($row = $dsProf->fetch(PDO::FETCH_ASSOC)){
            $arrProfEspec[] = $row;
        }
        
        $pdf->Cell(0,2,'',0,1,'C');
        $pdf->SetFont('Arial','B',20);
        $pdf->Cell(80,12,$f->monthExtense($mes),1,0,'L');
        $pdf->Cell(40,12,$ano,1,0,'L');
        $pdf->Cell(70,12,'VAGAS',1,1,'C');
        
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(55,12,'ESPECIALIDADE',1,0,'L');
        $pdf->Cell(65,12,'PROFISSIONAL',1,0,'L');
        $pdf->Cell(35,12,'AME',1,0,'C');
        $pdf->Cell(35,12,'REG',1,1,'C');
        
        $sqlUni = "SELECT DISTINCT id_unidade,unidade FROM grades
        WHERE grades.status =1 
        AND qtd > 0
        AND MONTH(grades.`data`) =:MES
        AND YEAR(grades.`data`) =:ANO";
        $dsUni = Maincontroller::doQuery($sqlUni,array('MES'=>$mes,'ANO'=>$ano));

        $arrUni = array();        
        while($row = $dsUni->fetch(PDO::FETCH_ASSOC)){
            $arrUni[] = $row;
        }
        
        $arrAME = array();
        $arrREG = array();
        foreach ($arrUni as $t) {
            $sqlTotal = "SELECT totalmes AS total 
            FROM grades
            WHERE grades.status =1 
            AND id_unidade =:UNIDADE                
            AND MONTH(grades.`data`) =:MES
            AND YEAR(grades.`data`) =:ANO
            GROUP BY profissional,especialidade";
            $dsTotal = Maincontroller::doQuery($sqlTotal,array('MES'=>$mes,'ANO'=>$ano,'UNIDADE'=>$t['id_unidade']));
             
            while($row = $dsTotal->fetch(PDO::FETCH_ASSOC)){
                if($t['id_unidade'] == "1") {
                    $arrAME[] = $row;
                } else {
                    $arrREG[] = $row;
                }                
            }
           
        }
                
        foreach ($arrProfEspec as $i) {                                    
            
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(55,12,$i['especialidade'],1,0,'L');
            $pdf->Cell(65,12,$i['profissional'],1,0,'L');
            
            foreach ($arrUni as $u) {
                $sql = "SELECT DISTINCT unidade,especialidade,profissional,id_servidor,id_espec,totalmes 
                FROM grades
                WHERE grades.status =1 
                
                AND MONTH(grades.`data`) =:MES
                AND YEAR(grades.`data`) =:ANO
                AND id_unidade =:UNIDADE
                AND id_servidor =:SERVIDOR
                AND id_espec =:ESPEC";
                $dsVagasUnidade = Maincontroller::doQuery($sql,array('MES'=>$mes,'ANO'=>$ano,'UNIDADE'=>$u['id_unidade'],'SERVIDOR'=>$i['id_servidor'],'ESPEC'=>$i['id_espec']));
                $arrVagasUnidade = array();        
                while($row = $dsVagasUnidade->fetch(PDO::FETCH_ASSOC)){
                    $arrVagasUnidade[] = $row;
                }
                
                foreach ($arrVagasUnidade as $v) {
                    if ($v['unidade'] == 'REGULAÇÃO') {
                        $br = 1;
                    } else {
                        $br = 0;
                    }   
                    $pdf->SetFont('Arial','B',14);
                    $pdf->Cell(35,12,$v['totalmes'],1,$br,'C');                                                           
                                       
                }
            }
            
        }  
        
        foreach ($arrAME as $ame) {
            $totalAME += $ame['total'];
        }
        
        foreach ($arrREG as $reg) {
            $totalREG += $reg['total'];
        }
        
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(120,12,'TOTAL',1,0,'R');
        $pdf->SetFont('Arial','BI',14);
        $pdf->SetTextColor(194,8,8);
        $pdf->Cell(35,12,$totalAME,1,0,'C');
        $pdf->Cell(35,12,$totalREG,1,1,'C');
        
        $rep->renderizeReport($pdf, TRUE, "","resumido ".$f->monthExtense($mes)." ".$ano);
    }
    
    public function SUS(){
  //  ini_set('display_errors', 1);
   // error_reporting(E_ALL); 
    
       $pdf_model = $_SERVER['DOCUMENT_ROOT']
           . '/Gerenciador/AME/Impressos/Recepcao/ModeloSUS.pdf';           
       
        $fields = [
            'nome_cabecalho' => 'GUILHERME HENRIQUE DOS SANTOS,',
            'nome_cartao'    => 'GUILHERME HENRIQUE DOS SANTOS',
            'dtnasc'         => '05/04/1984',
            'sexo'           => 'M',
            'sus'            => '700 0078 7333 7906',
            'cpf'            => '331.830.268-64'
        ];
        
        $pdf = new FPDM($pdf_model);

        $pdf->Load($fields);
        $pdf->Merge();
        header('Content-Type: application/pdf');
        $pdf->Output('I', 'CartaoSUS.pdf');
    }
            
}
