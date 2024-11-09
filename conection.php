<?php
try{
    //variables

    $hostDB='localhost';
    $nombreDB='el_reparador_db';
    $usuarioDB='root';
    $contrasenaDB='';

    //conexion con la base de datos
    $hostPDO="mysql:host=$hostDB;dbname=$nombreDB";

    //nueva instancia de conexion a la base de datos
    $conn = new PDO($hostPDO, $usuarioDB, $contrasenaDB);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //$conn->exec("SET CHARACTER SET utf8");
    //echo "Se ha conectado satisfactoriamente a la base de datos $nombreDB";
}catch(Excepcion $e){
     
    echo "<h1>Error: No se pudo conectar a la base de datos: $nombreDB</h1>";
    echo "<h2>Motivo: {$e->getMessage()} </h2>";
}

?>