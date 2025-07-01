<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("clientes_class.php");

    $id = $_POST['id'];
    $clientesModel = new Clientes($base);

    // Obtener los datos modificables del proveedor
    $cliente = $clientesModel->obtenerUnCli($id);
    if (!empty($cliente)) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Cliente</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/formularios.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
    <main>
    <div class="formulario-contenedor">
            
            <h1>Modificar Cliente</h1>
        
            <form action="guardarModiCli.php" method="post">
                <input type="hidden" name="id" value="<?php echo $cliente->getIdCli(); ?>"> <!-- Campo oculto para ID -->
                
                <div class="form-group">
                    <label class="label" for="dni">DNI</label>
                    <input class="input" type="number" name="dni" min="10000000" max="99999999" value="<?php echo $cliente->getDniCli(); ?>" required>
                </div>
                <div class="form-group">
                    <label class="label" for="nombre">Nombre</label>
                    <input class="input" type="text" name="nombre" value="<?php echo $cliente->getNomCli(); ?>" required>
                </div>
                <div class="form-group">
                    <label class="label" for="ape">Apellido</label>
                    <input class="input" type="text" name="ape" value="<?php echo $cliente->getApeCli(); ?>" required>
                </div>
                <div class="form-group">
                    <label class="label" for="telefono">Teléfono</label>
                    <input class="input" type="text" name="telefono" value="<?php echo $cliente->getTelCli(); ?>" required>
                </div>
                <div class="form-group">
                    <label class="label" for="direccion">Dirección</label>
                    <input class="input" type="text" name="direccion" value="<?php echo $cliente->getDirCli(); ?>" required>
                </div>
                <div class="form-group">
                    <label class="label" for="email">Email</label>
                    <input class="input" type="email" name="email" value="<?php echo $cliente->getEmailCli(); ?>" required>
                </div>

                <div class="button-group">  
                    <input class="boton submit" type="submit" value="Modificar">
                    <button class="boton cancelar"> <a href="inicioClientes.php">Cancelar</a></button>
                </div>
            </form>

        <?php
        } else {
            echo "<h1 style='text-align:center;'>No hay datos para mostrar</h1>";
            echo "<br><button class='boton submit'><a href='inicioClientes.php'>Volver</a></button><br>";
        }
        require '../footer.php';
        ?>
        </div>
    </main>
</body>
</html>