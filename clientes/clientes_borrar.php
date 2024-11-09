<?php
    include("../conexionPDO.php");
    $id = $_GET['id'];
    $base->query("DELETE FROM clientes WHERE idclientes = '$id'");
    header("Location:leer_db.php");

?>