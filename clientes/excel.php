<?php

    include("../conexionPDO.php");

    $consulta = "SELECT * FROM clientes";
    $resultado = $base->query($consulta);

    $archivo = "clientes.xls";

    header('Content-type: application/vnd.ms-excel'); //exportar como excel
    header('Content-Disposition: attachment; filename='. $archivo); //archivo de descarga
    header('Pragma: no-cache');
    header('Expires: 0');

    echo '<table border = 1';
    echo '<tr>';
    echo '<th colspan=8>Clientes El Reparador SRL</th>';
    echo '</tr>';

    echo '<tr> <th> Cod. </th><th> DNI </th><th> Nombre </th><th> Apellido</th><th>(Telefono) </th><th> Domicilio </th><th> Email </th><th> Estado </th> </tr>';

    while($mostrar = $resultado ->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        echo '<td>'. $mostrar['idclientes']. '</td>';
        echo '<td>'. $mostrar['dni_cliente']. '</td>';
        echo '<td>'. $mostrar['nom_cliente']. '</td>';
        echo '<td>'. $mostrar['ape_cliente']. '</td>';
        echo '<td>'. $mostrar['tel_cliente']. '</td>';
        echo '<td>'. $mostrar['dir_cliente']. '</td>';
        echo '<td>'. $mostrar['email_cliente']. '</td>';
        echo '<td>'. $mostrar['estado_cliente']. '</td>';
        echo '</tr>';
    }
    echo '</table>';


?>