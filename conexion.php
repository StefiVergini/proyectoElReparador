<?php
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db = 'el_reparador_db';

    $conn = new mysqli($host, $user, $pass, $db);

    if($conn -> connect_error){
        die("Algo ha salido mal con la conexion a la DB".$conn->connect_error);
    }else{
        echo 'La conexion fue exitosa!';
    }

?>