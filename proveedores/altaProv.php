<?php
    include("../header.php");
    include("../conexionPDO.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Proveedor</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/formularios.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
    <main>
        <div class="formulario-contenedor">
            <h1>Nuevo Proveedor</h1>
        
            <form action="guardarAltaProv.php" method="post">
                
                <div class="form-group">
                    <label class="label" for="cuit">CUIT</label>
                    <input class="input" type="number" name="cuit" min="10000000000" max="99999999999" required>
                </div>
                <div class="form-group">
                    <label class="label" for="nombre">Nombre del Proveedor</label>
                    <input class="input" type="text" name="nombre" id="nombre" required>
                </div>
                <div class="form-group">
                    <label class="label" for="telefono">Teléfono</label>
                    <input class="input" type="text" name="telefono" id="telefono" required>
                </div>
                
                <div class="form-group">
                    <label class="label" for="direccion">Dirección</label>
                    <input class="input" type="text" name="direccion" id="direccion" required>
                </div>
                <div class="form-group">
                    <label class="label" for="email">Email</label>
                    <input class="input" type="email" name="email" id="email" required>
                </div>
                <div class="button-group">
                    <input class="boton submit" type="submit" value="Agregar">
                    <button class="boton cancelar"> <a href="inicioProv.php">Cancelar</a></button>
                </div>
            </form>
            <?php
                require '../footer.php';
            ?>
        </div>
    </main>
</body>
</html>