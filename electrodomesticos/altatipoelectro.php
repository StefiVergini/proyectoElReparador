<?php
// INCLUIMOS LAS DEPENDENCIAS Y ARCHIVOS NECESARIOS
include("../header.php");
include("../conexionPDO.php");
include("electro_class.php");
include("../clientes/clientes_class.php");
include("../empleados/empleados_class.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Reparacion</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/formularios.css" />
    <script src="../static/js/funciones_empleados.js"></script>
    <script src="../static/js/funciones_select_nav.js"></script>



    <style>
        .section-btns {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .section-btns button,
        .section-btns form {
            margin: 0;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 3fr));
            gap: 40px;
            padding: 40px;
        }

        .reject-btn {
            background-color: #f45752;
        }

        .reject-btn:hover {
            background-color: red;
        }

        h3 {
            color: var(--azulPrincipal);
        }
    </style>
</head>

<body>
    <main>
        <div class="formulario-contenedor">
            <h1 style="display: flex; justify-content: center;">Agregar nuevo tipo de electrodoméstico</h1>
            <br>
            <hr>
            <br>
            <div class="section-btns">
                <form action="guardartipoelectro.php" method="POST">
                    <h3 for="nom_tipo">Nombre del Tipo </h3>
                    <input type="text" id="nom_tipo" name="nom_tipo" style="width: 100%; max-width: 600px; padding: 10px; font-size: 18px;" required>

                    <br>
                    <br>

                    <div class="button-group">
                        <button class="boton submit" type="submit">Agregar</button>
                        <button class="boton cancelar" type="button" onclick="window.location.href='altaElectro.php'">Cancelar</button>
                    </div>
                </form>
            </div>

</body>


</html>