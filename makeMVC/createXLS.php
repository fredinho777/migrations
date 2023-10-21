<?php
function createXLS($className, $params)
{
    $content = '<?php
    error_reporting(E_ALL);
    ini_set("display_errors", TRUE);
    ini_set("display_startup_errors", TRUE);
    date_default_timezone_set("Europe/London");
    include_once("../config.php");
    include_once("../models/class-'.$className.'.php");
    include_once("../modules/class-HTML.php");
    require_once ("../models/PHPExcel.php");
    
    $'.lcfirst($className).'Obj = new '.$className.'();
    
    $search = isset($_GET["search"]) ? $_GET["search"] : "";
    $from = isset($_GET["from"]) ? $_GET["from"] : "";
    $to = isset($_GET["to"]) ? $_GET["to"] : "";
    
    
    $params = [
        "search"=>$search,
        "from"=>$from,
        "to"=>$to
    ];
    
    $'.lcfirst($className).' = $'.lcfirst($className).'Obj->getAll($params);
    
    // Create new PHPExcel object
    //================================================================================
    $objPHPExcel = new PHPExcel();
    
    // Set document properties
    $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                                 ->setLastModifiedBy("Maarten Balliauw")
                                 ->setTitle("Office 2007 XLSX Test Document")
                                 ->setSubject("Office 2007 XLSX Test Document")
                                 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                                 ->setKeywords("office 2007 openxml php")
                                 ->setCategory("Test result file");
    //================================================================================
    
        $objPHPExcel->setActiveSheetIndex(0)'
        .PHP_EOL."\t\t\t\t\t\t\t";

        $j=65;
        foreach ($params as $key=> $item) {            
            $content .= '->setCellValue("'.chr($j).'1", "'.$item[0].'")';
            if ($key == count($params)-1) {
                $content .= ';';
                $content .= PHP_EOL."\t\t";
            }else{
                $content .= PHP_EOL."\t\t\t\t\t\t\t";
            }
            $j++;   
        }
    
        $content .= '$i = 1;
        foreach($'.lcfirst($className).' as $item) {   

            $objPHPExcel->setActiveSheetIndex(0)'
            .PHP_EOL."\t\t\t\t\t\t\t";

        $j=65;
        foreach ($params as $key=> $item) {            
            $content .= '->setCellValue("'.chr($j).'". ($i+1), $item["'.$item[0].'"])';
            if ($key == count($params)-1) {
                $content .= ';';
                $content .= PHP_EOL."\t\t";
            }else{
                $content .= PHP_EOL."\t\t\t\t\t\t\t";
            }
            $j++;   
        }
        $content .= '$i++;      
        }//-- end foreach
    
    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle("'.lcfirst($className).'");
    
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);
    
    // Redirect output to a client’s web browser (Excel5)
    header("Content-Type: application/vnd.ms-excel");
    header(\'Content-Disposition: attachment;filename="'.lcfirst($className).'-\'.date(\'d-m-y-H-i-s\').\'.xls"\');
    header("Cache-Control: max-age=0");
    // If you are serving to IE 9, then the following may be needed
    header("Cache-Control: max-age=1");
    
    // If you are serving to IE over SSL, then the following may be needed
    header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    header ("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT"); // always modified
    header ("Cache-Control: cache, must-revalidate"); // HTTP/1.1
    header ("Pragma: public"); // HTTP/1.0
    
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
    $objWriter->save("php://output");
    exit;   
    
    ?>
    ';

    if (!file_exists(BASE_PATH."/views/".lcfirst($className)."-XLS.php")) {
        $classFile = fopen(BASE_PATH."/views/".lcfirst($className)."-XLS.php", "w") or die("Error");

        $text = <<<_END
        $content
        _END;

        fwrite($classFile, $text)or die("No se pudo escribir en el archivo");
        fclose($classFile);
        echo lcfirst($className)."-XLS created";
    }
}
?>