<?php
session_start();
require_once '../conection.php';  // archivo con la conexión PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_tipo = trim($_POST['nom_tipo']);
    $idCli = trim($_POST['idCli']);
    if (!empty($nom_tipo)) {
        try {
            $querySelect = "SELECT nom_tipo FROM tipo_electro WHERE nom_tipo = :nom_tipo";
            $stmt = $conn->prepare($querySelect);
            $stmt->bindParam(':nom_tipo', $nom_tipo, PDO::PARAM_STR);
            $stmt->execute();
            $existe = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existe) {
                ?>
               <!-- Formulario oculto para volver con POST -->
                <form id="volverConIdCli" action="altaElectro.php" method="post" style="display:none;">
                    <input type="hidden" name="n_id" value="<?= $idCli ?>">
                </form>

                <script>
                    alert('Ya se encuentra registrado dicho Electrodoméstico. No puede repetirlo');
                    document.getElementById('volverConIdCli').submit();
                </script>
            <?php
            } else {
                $query = "INSERT INTO tipo_electro (nom_tipo) VALUES (:nom_tipo)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':nom_tipo', $nom_tipo, PDO::PARAM_STR);
                $stmt->execute();
                ?>
                <form id="volverAltaElectro" action="altaElectro.php" method="post" style="display:none;">
                    <input type="hidden" name="n_id" value="<?= $idCli ?>">
                </form>
                <script>
                    alert('Electrodoméstico agregado correctamente.');
                    document.getElementById('volverAltaElectro').submit();
                </script>
                
                <?php
                exit();
            }
        } catch (PDOException $e) {
            echo "Error al guardar el tipo de electrodoméstico: " . $e->getMessage();
        }
    } else {
        echo "El nombre del tipo no puede estar vacío.";
    }
} else {
    echo "Acceso no permitido.";
}
