<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("proveedores_class.php");

    $id = $_POST['id'];
    $proveedoresModel = new Proveedores($base);

    // Obtener los datos modificables del proveedor
    $proveedor = $proveedoresModel->obtenerUnProv($id);
    if (!empty($proveedor)) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Proveedor</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/formularios.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
    <main>
    <div class="formulario-contenedor">
            
            <h1>Modificar Proveedor</h1>
        
            <form action="guardarModiProv.php" method="post"> <!-- Cambia a tu script de guardado -->
                <input type="hidden" name="id" value="<?php echo $proveedor->getIdProv(); ?>"> <!-- Campo oculto para ID -->
                
                <div class="form-group">
                    <label class="label" for="cuit">CUIT</label>
                    <input class="input" type="number" name="cuit" min="10000000000" max="99999999999" value="<?php echo $proveedor->getCuit(); ?>" required>
                </div>
                <div class="form-group">
                    <label class="label" for="nombre">Nombre del Proveedor</label>
                    <input class="input" type="text" name="nombre" value="<?php echo $proveedor->getNomProv(); ?>" required>
                </div>
                <div class="form-group">
                    <label class="label" for="telefono">Teléfono</label>
                    <input class="input" type="text" name="telefono" value="<?php echo $proveedor->getTelProv(); ?>" required>
                </div>
                <div class="form-group">
                    <label class="label" for="direccion">Dirección</label>
                    <input class="input" type="text" name="direccion" value="<?php echo $proveedor->getDirProv(); ?>" required>
                </div>
                <div class="form-group">
                    <label class="label" for="email">Email</label>
                    <input class="input" type="email" name="email" value="<?php echo $proveedor->getEmailProv(); ?>" required>
                </div>
                <div class="form-group">
                    <label class="label" for="saldo">Saldo $</label>
                    <input class="input" type="text" name="saldo" value=" <?php echo $proveedor->getSaldo(); ?>" required>
                </div>

            <!-- <label for="estado">Estado:</label>
                <input type="radio" name="estado" value="1" <?php //echo ($proveedor->getEstadoProv() ? 'checked' : ''); ?>> Activo
                <input type="radio" name="estado" value="0" <?php //echo (!$proveedor->getEstadoProv() ? 'checked' : ''); ?>> Inactivo
                <br>
            -->  
                <div class="button-group">  
                    <input class="boton submit" type="submit" value="Modificar">
                    <button class="boton cancelar"> <a href="inicioProv.php">Cancelar</a></button>
                </div>
            </form>

        <?php
        } else {
            echo "<h1 style='text-align:center;'>No hay datos para mostrar</h1>";
            echo "<br><button class='boton submit'><a href='inicioProv.php'>Volver</a></button><br>";
        }
        require '../footer.php';
        ?>
        </div>
    </main>
</body>
</html>
