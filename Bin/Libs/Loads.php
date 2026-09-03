<?php

/**
 * Description of Css
 *
 * @author Guilherme
 */
class Loads implements IPrivateTO {
    
    public static function css() {
        $filenames = array();	
        $css = '';
        $handle = '';
        $file = '';              
        
        // open the "css" directory
        if ($handle = opendir(CSS_PATH)) {
            // list directory contents
            while (false !== ($file = readdir($handle))) {							             
                if (is_file(CSS_PATH . $file)) {
                    $ext = pathinfo($file, PATHINFO_EXTENSION);
                    if($ext == 'css'){
                        $filenames[] = $file;
                    }
                }
            }
				
            sort($filenames);

            foreach($filenames as $filename) {
                    $css .= "<link rel=\"stylesheet\" href=\"" . URL_ROOT . "css/" . $filename .
                    "\" type=\"text/css\" media=\"all\" />" . "\n";
            }   
            
            closedir($handle);
            echo $css;         
        } 
    }
                
    public static function js() {

        $js = '';
        $handle = '';
        $file = '';        
        // open the "js" directory
        if ($handle = opendir(JS_PATH)) {
            // list directory contents
            while (false !== ($file = readdir($handle))) {
                // only grab file names
                if (is_file(JS_PATH . $file)) {
                    $ext = pathinfo($file, PATHINFO_EXTENSION);
                    if($ext == 'js'){
                        $filenames[] = $file;
                    }
                }
            }
				
            sort($filenames);

            foreach($filenames as $filename) {
                    $js .= "<script src=\"" . URL_ROOT . "js/" . $filename . 
                    "\" type=\"text/javascript\"></script>" . "\n";
            }
            
            closedir($handle);
            echo $js;
        
		}
	}
        
//        public static function charts() {            
//            echo "<script src=\"" . URL_ROOT . "js/chart/chart.js\" type=\"text/javascript\"></script>";
//        }
	
}
