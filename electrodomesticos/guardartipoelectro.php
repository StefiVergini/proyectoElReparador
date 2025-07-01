<?php
session_start();
require_once '../conection.php';  // archivo con la conexión PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_tipo = trim($_POST['nom_tipo']);

    if (!empty($nom_tipo)) {
        try {
            $querySelect = "SELECT nom_tipo FROM tipo_electro WHERE nom_tipo = :nom_tipo";
            $stmt = $conn->prepare($querySelect);
            $stmt->bindParam(':nom_tipo', $nom_tipo, PDO::PARAM_STR);
            $stmt->execute();
            $existe = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existe) {
                echo "<script>
                        alert('Ya se encuentra registrado dicho Electrodoméstico. No se puede repetir');
                        window.location.href='inicioElectro.php';
                      </script>";
            } else {
                $query = "INSERT INTO tipo_electro (nom_tipo) VALUES (:nom_tipo)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':nom_tipo', $nom_tipo, PDO::PARAM_STR);
                $stmt->execute();
                echo "<script>
                        alert('Electrodoméstico agregado correctamente.');
                        window.location.href='inicioElectro.php';
                      </script>";
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
