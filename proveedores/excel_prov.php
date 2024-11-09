<?php

    include("../conexionPDO.php");

    $consulta = "SELECT * FROM proveedores";
    $resultado = $base->query($consulta);

    $archivo = "proveedores.xls";

    header('Content-type: application/vnd.ms-excel'); //exportar como excel
    header('Content-Disposition: attachment; filename='. $archivo); //archivo de descarga
    header('Pragma: no-cache');
    header('Expires: 0');

    echo '<table border = 1';
    echo '<tr>';
    echo '<th colspan=8>Proveedores - El Reparador SRL</th>';
    echo '</tr>';

    echo '<tr> <th> ID. </th><th> CUIT </th><th> Nombre Prov </th><th> Telefono </th><th> Direccion </th><th> Email </th><th> Saldo </th><th> Estado </th> </tr>';

    while($mostrar = $resultado ->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        echo '<td>'. $mostrar['idproveedores']. '</td>';
        echo '<td>'. $mostrar['cuit']. '</td>';
        echo '<td>'. $mostrar['nombre_prov']. '</td>';
        echo '<td>'. $mostrar['tel_prov']. '</td>';
        echo '<td>'. $mostrar['dir_prov']. '</td>';
        echo '<td>'. $mostrar['email_prov']. '</td>';
        echo '<td>'. $mostrar['saldo']. '</td>';
        echo '<td>'. $mostrar['estado_prov']. '</td>';
        echo '</tr>';
    }
    echo '</table>';


?>