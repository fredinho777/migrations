<?php 
include_once("example.php");

if (RUNMIGRATE) {
    (new ExampleMigrate())->runTable();
}else{
    echo "Access Denied !!!";
}

?>