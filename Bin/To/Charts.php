<?php
/**
 * Description of Charts
 *
 * @author Guilherme
 */
class Charts implements IPrivateTO {
        
    function collumsChart($xColName,$yColName,$data,$title='Título',$width='600',$height='400') {
        Loads::js();
        Loads::charts(); 
        
        $json = json_decode($data);
               
    echo "<script>
            google.charts.load('current', {packages: ['corechart']}); 
            
            function drawChart() { 
            
                var data = google.visualization.arrayToDataTable([";       
            echo "['{$xColName}', '{$yColName}'],";
                     
                foreach ($json as $key => $value) {
                    echo "['{$key}',  ".(int)$value."],";

                }      
      echo "]);
               
                var options = {title: '{$title}',width: {$width}, height: {$height}}; 

                var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));      
                chart.draw(data, options);
            }
            google.charts.setOnLoadCallback(drawChart); 
        </script>";
    }
    
}
