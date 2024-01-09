<?php
$server = "KRUPA\SQLEXPRESS";
$connection = array("Database"=>"project","UID"=>"sa","PWD"=>"12345","CharacterSet"=>"UTF-8");
$con = sqlsrv_connect($server,$connection);

if($con){
    //echo 'Connection done';
}else{
    die(print_r(sqlsrv_errors(),true));
}
?>