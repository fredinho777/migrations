<?php
function createUpdate($className, $params)
{
$content = '<?php
include_once("models/class-'.$className.'.php");
$'.lcfirst($className).' = new '.$className.'((int)$_GET["id"]);
?>
    <h1 class="page-header">Editar '.$className.'</h1>
    <div class="col col-md-12">
        <form class="form-horizontal" action="controllers/controller-'.$className.'.php?action=update" method="post">
        <input type="hidden" value="<?php echo $'.lcfirst($className).'->id; ?>" name="id"/>
            <fieldset>  
                <div class="row">';
            $content .= PHP_EOL."\t\t\t\t\t";
                    foreach ($params as $value) {
        $content .= '<div class="col col-md-4">
                        <label class="control-label" for="'.$value[0].'">'.ucfirst($value[0]).'</label>
                        <div class="controls">
                            <input class="form-control" type="text" name="'.$value[0].'" placeholder="'.ucfirst($value[0]).'" value="<?php echo $'.lcfirst($className).'->'.$value[0].'; ?>"/>        
                        </div>
                    </div>';
                    }    
            $content .= PHP_EOL."\t\t\t\t";               
       $content .= '</div>
            </fieldset>
            <br>
    
            <div class="row">
              <div class="col col-md-4">
                <input type="submit" class="btn btn-primary btn-sm" value="Actualizar"/>         
              </div>
            </div>   
        </form>
    </div>';

    if (!file_exists("../../views/editar-".lcfirst($className).".php")) {
        $classFile = fopen("../../views/editar-".lcfirst($className).".php", "w") or die("Error");

        $text = <<<_END
        $content
        _END;

        fwrite($classFile, $text)or die("No se pudo escribir en el archivo");
        fclose($classFile);
        echo "Edit ".lcfirst($className)." created";
    }
}
?>