<?php
require '../conexionPDO.php';
//var_dump($_GET['idcliente']);
if (isset($_GET['idcliente']) && !empty($_GET['idcliente'])) {
    $idCliente = intval($_GET['idcliente']);
    
    $sqlElectro = "SELECT DISTINCT r.id_reparacion, e.idelectrodomesticos, e.marca, e.modelo, t.nom_tipo
                   FROM electrodomesticos as e
                   INNER JOIN tipo_electro as t ON e.tipo_electro = t.idtipo_electro
                   INNER JOIN reparaciones as r ON r.idelectrodomesticos = e.idelectrodomesticos
                   LEFT JOIN atencion_presupuesto as a ON a.id_reparacion = r.id_reparacion
                   WHERE e.idclientes = :idcliente AND (r.estado_reparacion = 1 
                   OR a.estado_presup IN ('Presupuesto a Enviar', 'Presupuesto enviado', 'Presupuesto confirmado'))";

    $stmt = $base->prepare($sqlElectro);
    $stmt->bindParam(':idcliente', $idCliente, PDO::PARAM_INT);
    $stmt->execute();
    $electrodomesticos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($electrodomesticos) {
        echo '<option value="">Seleccione un electrodoméstico</option>';
        foreach ($electrodomesticos as $electro) {
            echo '<option value="' . htmlspecialchars($electro['idelectrodomesticos']) . '" 
            data-repa="' . htmlspecialchars($electro['id_reparacion']) . '"
            data-marca="' . htmlspecialchars(ucwords($electro['marca'])) . '" 
            data-modelo="' . htmlspecialchars($electro['modelo']) . '"
            data-tipo="' . htmlspecialchars(ucwords($electro['nom_tipo'])) . '">
            ' . htmlspecialchars(ucwords($electro['nom_tipo'])) . ' ' . 
            htmlspecialchars(ucwords($electro['marca'])) . ' - ' .
            htmlspecialchars(strtoupper($electro['modelo'])) . '
          </option>';
        }
    } else {
        echo '<option value="">No se encontraron electrodomésticos</option>';
    }
} else {
    echo '<option value="">Error al procesar la solicitud</option>';
}
