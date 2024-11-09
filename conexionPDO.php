<?php
    $dsn = 'mysql:host=localhost;port=3306;dbname=el_reparador_db';
    $username = 'root';
    $password = '';
    $options = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'",
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    );
    
    try {
        $base = new PDO($dsn, $username, $password, $options);
        $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "Conexion Exitosa!";
    } catch (PDOException $e) {
        echo $e->getMessage();
        die();
    }
?>