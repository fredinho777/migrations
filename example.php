<?php 
$baseRoute = file_exists('../../../gestion') ? '../../../gestion/' : '../../../';
include_once($baseRoute."config.php");
include_once("class-Migration.php");
include_once("makeMVC/createModel.php");
include_once("makeMVC/createController.php");
include_once("makeMVC/createView.php");
include_once("makeMVC/createAdd.php");
include_once("makeMVC/createUpdate.php");
include_once("makeMVC/createXLS.php");

Class Example extends Migration{
    public function runTable(){
        $createXLS = true;

        $className = get_class($this);
        
        $this->tablename = strtolower(preg_replace('/(?<=\w)(\p{Lu})/u', '_$1', $className));
        
        $params = [
            ["example", "varchar", 255],
        ];
    
        $responseCrated = parent::createTable($params);
        parent::runAlterTable($responseCrated, $params);

        createModel($className, $this->tablename, $params);
        createController($className, $params);
        createView($className, $params, $createXLS);
        createAdd($className, $params);
        createUpdate($className, $params);
        $createXLS ? createXLS($className, $params) : '';
    }    
}
?>