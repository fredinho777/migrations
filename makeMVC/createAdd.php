<?php
function createAdd($className, $params)
{
    $content = '<h1 class="page-header">Nuevo '.preg_replace('/(?<=\w)(\p{Lu})/u', ' $1', $className).'</h1>
    <div class="col col-md-12">
        <form class="form-horizontal" action="controllers/controller-'.$className.'.php?action=add" method="post">
            <fieldset>  
                <div class="row">';
                foreach ($params as $value) {
                    $content .= PHP_EOL."\t\t\t\t\t";
        $content .= '<div class="col col-md-4">
                        <label class="control-label" for="'.$value[0].'">'.str_replace("_", " ", ucfirst($value[0])).'</label>
                        <div class="controls">
                            <input class="form-control" type="text" name="'.$value[0].'" placeholder="'.str_replace("_", " ", ucfirst($value[0])).'"/>        
                        </div>
                    </div>';
                    }    
            $content .= PHP_EOL."\t\t\t\t";               
       $content .= '</div>
            </fieldset>
            <br>
    
            <div class="row">
              <div class="col col-md-4">
                <input type="submit" class="btn btn-primary btn-sm" value="Guardar"/>         
              </div>
            </div>   
        </form>
    </div>';

    if (!file_exists(BASE_PATH."/views/nuevo-".lcfirst($className).".php")) {
        $classFile = fopen(BASE_PATH."/views/nuevo-".lcfirst($className).".php", "w") or die("Error");

        $text = <<<_END
        $content
        _END;

        fwrite($classFile, $text)or die("No se pudo escribir en el archivo");
        fclose($classFile);
        echo "New ".lcfirst($className)." created";
    }
}
?>