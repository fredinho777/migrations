<?php
include_once(BASE_PATH."/models/class-Model.php");
include_once(BASE_PATH."/modules/class-Utils.php");

class Migration extends Model {

    public $tablename = "";
    public $arrnCampos = [];
 
    public function createTable($params){
        $query = "CREATE table ".$this->tablename."
            (id INT(12) UNSIGNED AUTO_INCREMENT PRIMARY KEY,";

        foreach ($params as $key => $value) {
            if ($value[1] == "text") {
                $query .= " ".$value[0]." ".$value[1]." NOT NULL";
            }else{
                $query .= " ".$value[0]." ".$value[1]."(".$value[2].") NOT NULL";
            }
            if (count($params) != $key+1) {
                $query .= ",";
            }
        }

        $query .= ",
            datecreated varchar(64), 
            dateupdated varchar(64)
        )";

        $result = $this->conn->query($query);
        $status = $this->conn->errorInfo();
        
        if ($result == false) {
            echo $status[2]." ... <br>";
            return [
                "result" => $result,
                "status"=> $status
            ];
        }else{
            echo "Table ".$this->tablename." creada con exito ... <br>";
        }   
    }

    public function getColumns(){
        $query = "SHOW COLUMNS FROM ".$this->tablename;
        return $this->conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function runAlterTable($responseCrated, $params){
        $response =  $this->alterTable($responseCrated, $params);
        foreach ($response as $key => $value) {
            echo "Nuevo campo creado ".$value["field"]." ...<br>";
        }
    }

    public function alterTable($response, $params){      
        $totalFields = count($params);
        if ($response["result"] == false) {
            
            $columns = $this->getColumns();
            
            array_shift($columns);//-- sacamos el id
            $columns = array_slice($columns, 0,-2);//-- sacamos los campos de created and update dare
            
            $totalColumns = count($columns);
            $flag = false;
            if ($totalFields > $totalColumns) {

                foreach ($params as $key => $value) {
                    
                    if ($columns[$key]["Field"] != $value[0] ) {
                        
                        if ($flag == false) {
                            
                            $query = "ALTER TABLE ".$this->tablename."  ADD ";

                            if ($value[1] == "text") {
                                $query .= " ".$value[0]." ".$value[1]." NOT NULL ";
                            }else{
                                $query .= " ".$value[0]." ".$value[1]."(".$value[2].") NOT NULL ";
                            }

                            $query .= "AFTER `".$params[$key-1][0]."`";
                            
                                $result = $this->conn->query($query);
                                $status = $this->conn->errorInfo();

                            self::alterTable($response, $params);
                            $flag = true;

                            array_push($this->arrnCampos,  [
                                "result" => $result,
                                "status"=> $status,
                                "field"=> $value[0]
                            ]);

                        }  
                    }
                  
                }  
            }   
        }
        return $this->arrnCampos;
    }
}

?>