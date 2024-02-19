<?php
function createController($className, $params)
{

$content = '<?php
include_once("../config.php");
include_once("../models/class-'.$className.'.php");

if(!empty($_GET["action"])) {
    $item = new '.$className.'();
    switch($_GET["action"]) {
        case "add":
            
            if(!empty($_POST["'.$params[0][0].'"])) {';

                $content .= PHP_EOL;
                $content .= PHP_EOL."\t\t\t\t";

                foreach ($params as $value) {
                    $content .= '$item->'.$value[0].' = $_POST["'.$value[0].'"];';
                    $content .= PHP_EOL."\t\t\t\t";
                }

                $content .= '$item->datecreated = date("Y-m-d H:i:s");';
                $content .= PHP_EOL."\t\t\t\t";
                $content .= '$item->dateupdated = date("Y-m-d H:i:s");';
                $content .= PHP_EOL."\t\t\t\t";
               
                $content .='$id = $item->add();              
                
                if($id) {
                    $msg = MSG_INSERT_SUCCESS;
                    header("Location: ../editar-'.lcfirst($className).'?msg=".$msg."&id=".$id);
                    break;
                } else {
                    $msg = MSG_INSERT_FAILED;
                }
            } else {
                $msg = MSG_EMPTY_FIELD;
            }
            header("Location: ../nuevo-'.lcfirst($className).'&msg=".$msg);
            break;  

        case "delete":
            if(!empty($_GET["id"])) {
                $item->id = (int)$_GET["id"];
                if($item->remove()) {
                    $msg = MSG_DELETE_SUCCESS;
                } else {
                    $msg = MSG_DELETE_FAILED;
                }
            }
            header("Location: ../'.lcfirst($className).'?msg=".$msg);
            break;

        case "update":
            if(!empty($_POST["'.$params[0][0].'"])) {
                                
                $item->id = (int)$_POST["id"];
                $item->getById(true);';
                                                
                $content .= PHP_EOL;
                $content .= PHP_EOL."\t\t\t\t";

                foreach ($params as $value) {
                    $content .= '$item->'.$value[0].' = $_POST["'.$value[0].'"];';
                    $content .= PHP_EOL."\t\t\t\t";
                }

                $content .= '$item->dateupdated = date("Y-m-d H:i:s");';
                $content .= PHP_EOL."\t\t\t\t";                   

                $content .='if($item->update() === true) {
                    $msg = MSG_UPDATE_SUCCESS;
                } else {
                    $msg = MSG_UPDATE_FAILED;
                }
            } else {
                $msg = MSG_EMPTY_FIELD;
            }
            header("Location: ../editar-'.lcfirst($className).'?id=".$item->id."&msg=".$msg);
            break;

    }
} else {
    echo "Missing Controller Action";
    exit;
}';

    if (!file_exists(BASE_PATH."/controllers/controller-".$className.".php")) {
        $classFile = fopen(BASE_PATH."/controllers/controller-".$className.".php", "w") or die("Error");

        $text = <<<_END
        $content
        _END;

        fwrite($classFile, $text)or die("No se pudo escribir en el archivo");
        fclose($classFile);
        echo "Controller ".$className." created";
    }
}
?>