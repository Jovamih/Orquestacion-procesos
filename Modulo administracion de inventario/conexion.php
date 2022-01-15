<?php
function conectar(){
    $user="admin";
    $pass="admin12345678";
    $server="procesosnegociodatabase.cuxsffuy95k9.us-east-1.rds.amazonaws.com";
    $db="procesosnegociodatabase";
    $conn=new mysqli($server,$user,$pass,$db);
    if($conn->connect_error){
        die("Error de conexion: " . mysqli_connect_error());
    }else{
        return $conn;
    }
}
?>