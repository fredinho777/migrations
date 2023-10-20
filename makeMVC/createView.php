<?php
function createView($className, $params, $createXLS)
{

$content = '<?php
include_once("models/class-Admin.php");
include_once("models/class-'.$className.'.php");

$'.lcfirst($className).'Obj = new '.$className.'();

$search = isset($_GET["search"]) ? $_GET["search"] : "";
$from = isset($_GET["from"]) ? $_GET["from"] : "";
$to = isset($_GET["to"]) ? $_GET["to"] : "";
$set = isset($_GET["set"]) ? $_GET["set"] : 1;
$limit = 100;
?>
<h1>
    <span class="hidden-sm hidden-xs">'.$className.'</span>
    <span class="admin-buttons">';
        $content .= PHP_EOL."\t\t";
        if ($createXLS) { 
            $content .= '<a href="views/'.lcfirst($className).'-xls.php?search=<?php echo $search ?>&from=<?php echo $from ?>&to=<?php echo $to ?>" class="btn btn-sm btn-warning" role="button">';
            $content .= PHP_EOL."\t\t\t";    
            $content .= 'Exportar Xls';
            $content .= PHP_EOL."\t\t";
            $content .= '</a>';
            $content .= PHP_EOL."\t\t";
        }
        
        $content .= '<a href="nuevo-'.lcfirst($className).'" class="btn btn-sm btn-primary" role="button">
            Nuevo '.preg_replace('/(?<=\w)(\p{Lu})/u', ' $1', $className).'
        </a>
    </span>
</h1>

<form id="search-item" action="" method="get">
    <div class="row"> 
        <div class="col-md-4">
            <input class="form-control" type="text" name="search" placeholder="Nombre" value="<?php echo $search; ?>"/>
        </div>  
        <div class="col-md-2">
            <input class="form-control datepicker" type="text" name="from" placeholder="Desde" value="<?php echo $from; ?>"/>
        </div>  
        <div class="col-md-2">
            <input class="form-control datepicker" type="text" name="to" placeholder="Hasta" value="<?php echo $to; ?>"/>
        </div>  

        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
            <input type="submit" class="btn btn-md btn-primary" value="Buscar"/>            
        </div>
    </div>
</form>
<hr/>

<?php
$params = [
    "search"=>$search, 
    "set"=>$set, 
    "limit"=>$limit, 
    "from"=>$from, 
    "to"=>$to, 
    "returnCount"=>true
];

$'.lcfirst($className).' = HTML::renderPaginationBar($'.lcfirst($className).'Obj, $params);

if(!empty($'.lcfirst($className).')) {
    ?>
    <table class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bordered-table table-hover">
        <thead>
            <tr style="text-align: center;">';
                $content .= PHP_EOL."\t\t\t\t";

                foreach ($params as $value) {
                    $content .= '<th>'.$value[0].'</th>';
                    $content .= PHP_EOL."\t\t\t\t";
                }
                
                $content .= '<th>Opciones</th>
            </tr>
        </thead>
        <?php
        foreach($'.lcfirst($className).' as $item) {
            ?>
             <tr>';
                $content .= PHP_EOL."\t\t\t\t";

                foreach ($params as $key => $value) {
                    $content .= ($key == 0) ? '<td><a href="editar-'.lcfirst($className).'?id=<?php echo $item["id"]; ?>"><?php echo $item["'.$value[0].'"] ?></a></td>' : '<td><?php echo $item["'.$value[0].'"] ?></td>';
                    $content .= PHP_EOL."\t\t\t\t";
                }  
                               
                $content .= '<td class="centered">
                    <a href="editar-'.lcfirst($className).'?id=<?php echo $item["id"]; ?>" class="btn btn-primary btn-xs"><span class="fa fa-edit fa-fw"></span></a>
                    <a class="btn btn-danger btn-xs deletelink" href="controllers/controller-'.$className.'.php?action=delete&id=<?php echo $item["id"]; ?>"><span class="fa fa-trash-o fa-fw"></span></a>           
                </td>
            </tr>
            <?php
        }
    ?>
    </table>
   <?php
} else {
    HTML::renderUserMessage("No se han encontrado registros en la base de datos", "info");
}';

    if (!file_exists("../../views/".lcfirst($className).".php")) {
        $classFile = fopen("../../views/".lcfirst($className).".php", "w") or die("Error");

        $text = <<<_END
        $content
        _END;

        fwrite($classFile, $text)or die("No se pudo escribir en el archivo");
        fclose($classFile);
        echo "View ".lcfirst($className)." created";
    }
}
?>