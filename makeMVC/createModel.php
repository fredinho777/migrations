<?php
function createModel($className, $tableName, $params)
{
    
$content = '<?php
    include_once("class-AdministrableItem.php");
    include_once(BASE_PATH."/modules/class-Utils.php");

    class '.$className.' extends AdministrableItem {';

    $content .= PHP_EOL;
    $content .= PHP_EOL."\t\t";
    $content .= 'public $id;';
    $content .= PHP_EOL."\t\t";

    foreach ($params as $value) {
        $content .= 'public $'.$value[0].';';
        $content .= PHP_EOL."\t\t";
    }
    $content .= 'public $datecreated;';
    $content .= PHP_EOL."\t\t";
    $content .= 'public $dateupdated;';
    $content .= PHP_EOL."\t\t";
        
$content .='public function __construct($id = null, $tablename = "'.$tableName.'", $itemtype = ITEM_TYPE_'.strtoupper($className).') {
            $this->requiresfolder = false;
            $this->debbug = false;
            parent::__construct($id, $tablename, $itemtype);
        }

        public function add($query = null, $queryParams = []) {

            if(empty($query)) {
                $query = "INSERT INTO ".$this->tablename." VALUES (
                    null,'.PHP_EOL."\t\t\t\t\t";
                    foreach ($params as $key => $value) {
                        $content .= ':'.$value[0].',';
                        $content .= PHP_EOL."\t\t\t\t\t";
                    }    
                    $content .= ":datecreated,";  
                    $content .= PHP_EOL."\t\t\t\t\t";        
                    $content .= ":dateupdated";  
                    $content .= PHP_EOL."\t\t\t\t\t";        
   $content .= ')";
            }
            if(empty($queryParams)) {
                $queryParams = ['.PHP_EOL."\t\t\t\t\t";  
                    foreach ($params as $key => $value) {
                        $content .= '":'.$value[0].'"=>$this->'.$value[0].',';
                        $content .= PHP_EOL."\t\t\t\t\t";
                    }  
                    $content .= '":datecreated"=>$this->datecreated,';  
                    $content .= PHP_EOL."\t\t\t\t\t";
                    $content .= '":dateupdated"=>$this->dateupdated';  
                    $content .= PHP_EOL."\t\t\t\t\t";
   $content .= '];
            }
  
            return parent::add($query, $queryParams);
        }
        
        public function update($query = null, $queryParams = []) {
            
            if(empty($query)) {
                $query = "UPDATE ".$this->tablename." SET'.PHP_EOL."\t\t\t\t\t";
                    foreach ($params as $key => $value) {
                        $content .= $value[0].' = :'.$value[0].',';
                        $content .= PHP_EOL."\t\t\t\t\t";
                    } 
                    $content .= 'dateupdated = :dateupdated';  
                    $content .= PHP_EOL."\t\t\t\t\t";
   $content .= 'WHERE id = ".$this->id;
            }
            if(empty($queryParams)) {
                $queryParams = ['.PHP_EOL."\t\t\t\t\t";
                    foreach ($params as $key => $value) {
                        $content .= '":'.$value[0].'"=>$this->'.$value[0].',';
                        $content .= PHP_EOL."\t\t\t\t\t";
                    }
                    $content .= '":dateupdated"=>$this->dateupdated';  
                    $content .= PHP_EOL."\t\t\t\t\t";
   $content .= '];
            }
            
            return parent::update($query, $queryParams);
        }

        
        //Fetches and Paginates results
        public function getAll($params = array()) {   
            $whereused = false;
            
            /*
             * -- BASE QUERY
             */
            $query = "SELECT SQL_CALC_FOUND_ROWS * FROM ".$this->tablename;      
            
            //SEARCH ALGORITHM
            if(!empty($params["search"])) {
                $query .= " WHERE '.$params[0][0].' LIKE :search";              
                $whereused = true;
            }

            if(!empty($params["from"])) {
                if($whereused) {
                    $query .= " AND datecreated >=  :from";
                } else {
                    $query .= " WHERE datecreated >= :from";
                    $whereused = true;
                }
            }
            
            if(!empty($params["to"])) { 
                if($whereused) {
                    $query .= " AND datecreated <= :to";
                } else {
                    $query .= " WHERE datecreated <= :to";
                    $whereused = true;
                }
            } 
            
            $query .= " ORDER BY ".$this->tablename.".id DESC";

            //For pagination
            if(!empty($params["limit"]) && !empty($params["set"])) {
                $set = ($params["set"] - 1) * $params["limit"];
                $query .= " LIMIT ".$set.", ".$params["limit"];
            }
            
            $q = $this->conn->prepare($query);
            if(!empty($params["search"])) {
                $q->bindValue(":search", "%".$params["search"]."%");
            }
            if(!empty($params["from"])) {
                $q->bindValue(":from", "".$params["from"]."");
            }
            if(!empty($params["to"])) {
                $q->bindValue(":to", "".$params["to"]."");
            }

            if($q->execute()) {
                $array = $q->fetchAll(PDO::FETCH_ASSOC);
                if(!empty($params["returnCount"])) {
                    $countQuery = "SELECT FOUND_ROWS()";
                    $theCount = $this->conn->query($countQuery)->fetch(PDO::FETCH_COLUMN);
                    $array["totalCount"] = $theCount;
                }
                return $array;
            } else {
                $this->errors[] = "Could not execute the prepared query";
                return false;
            }
        }
    }';

    if (!file_exists(BASE_PATH."/models/class-".$className.".php")) {
        $classFile = fopen(BASE_PATH."/models/class-".$className.".php", "w") or die("Error");

        $text = <<<_END
        $content
        _END;

        fwrite($classFile, $text)or die("No se pudo escribir en el archivo");
        fclose($classFile);
        echo "Class ".$className." created";
    }
}
?>