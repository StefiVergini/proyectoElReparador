<?php
// procesar_retiro.php
include_once "../conexionPDO.php";

if (
  $_SERVER['REQUEST_METHOD'] !== 'POST'
  || empty($_POST['cant_egresa'])
  || !is_array($_POST['cant_egresa'])
) {
  die("Solicitud inválida.");
}

$cantidades = array_map('intval', $_POST['cant_egresa']);

// Inicia transacción
$base->beginTransaction();

try {
  foreach ($cantidades as $idstock => $cant) {
    if ($cant <= 0) continue;      // si ingresó 0 o menos, salto
    // Prepara el UPDATE para no dejar stock negativo
    $sql  = "UPDATE stock
               SET cantidad = cantidad - :c
             WHERE idstock   = :i
               AND cantidad >= :c";
    $stmt = $base->prepare($sql);
    $stmt->execute([
      ':c' => $cant,
      ':i' => $idstock
    ]);
    if ($stmt->rowCount() === 0) {
      // No alcanzaba stock
      throw new Exception("No hay suficiente stock para el artículo $idstock.");
      echo "<script>alert('No hay suficiente stock para el artículo $idstock.'); window.location.href = 'retirarMateriales.php';</script>";
      exit;
    }
  }

  // Si todo bien, confirma
  $base->commit();
  // Redirige de vuelta o muestra mensaje
   echo "<script>alert('Materiales Retirados Correctamente.'); window.location.href = 'inicioElectro.php';</script>";
  exit;
  
} catch (Exception $e) {
  $base->rollBack();
  // Podrías redirigir con ?error=...
  die("Error al retirar materiales: " . $e->getMessage());
}
