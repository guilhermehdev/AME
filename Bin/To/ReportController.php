<?php

/**
 * Description of Reportcontroller
 *
 * @author Guilherme
 */
class Reportcontroller implements IPrivateTO  {
            
    public function renderizeReport(FPDF $pdf,$data,$dest,$filename) {
            
        if(!empty($data)) {
                                    
            $pdf->Output($dest,$filename.".pdf");
            
        } else {
         echo 
            "<!DOCTYPE html>                
                <script type=\"text/javascript\">
                    alert('Não existem dados para serem Impressos!');
                    window.close();
                </script>";
        }
    }
            
}