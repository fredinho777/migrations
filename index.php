<?php 
include_once("example.php");

if (RUNMIGRATE) {
    (new Example())->runTable();
}else{
    echo "Access Denied !!!";
}

?>