<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("stock_class.php");

    $id = $_POST['id'];
    $stockModel = new Stock($base);

    // Obtener los datos modificables del proveedor
    $stock = $stockModel->obtenerUnArt($id);
    if (!empty($stock)) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Artículo</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="../static/styles/formularios.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
</head>
<body>
    <main>
    <div class="formulario-contenedor">
            
            <h1>Modificar Artículo</h1>
        
            <form action="guardarModiStock.php" method="post"> 
                <input type="hidden" name="id" value="<?php echo $stock->getIdStock(); ?>"> 
                
                <div class="form-group">
                    <label class="label" for="nombre">Nombre del Artículo</label>
                    <input class="input" type="text" name="nombre" value="<?php echo $stock->getDescArt(); ?>" required>
                </div>
                <div class="form-group">
                    <label class="label" for="cant">Cantidad</label>
                    <input class="input" type="number" name="cant" value="<?php echo $stock->getCantidad(); ?>" required>
                </div>
                <div class="form-group">
                    <label class="label" for="tipoStock">Tipo de Artículo</label>
                    <select name="tipoStock" id="tipoStock">
                        <option value="<?php echo $stock->getTipoStock(); ?>" selected><?php echo $stock->getTipoStock(); ?></option>

                        <?php
                        
                        $opciones = ["Herramientas", "Electronica", "Insumos", "Libreria", "Otros"];

                        
                        foreach ($opciones as $opcion) {
                            if ($opcion !== $stock->getTipoStock()) {
                                echo "<option value=\"$opcion\">$opcion</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                <label class="label" for="idproveedor">Proveedor</label>
                    <select name="idproveedor" id="idproveedor">
                        <option value="<?php echo $stock->getIdProv(); ?>" selected><?php echo $stock->getNomProv(); ?></option>
                        <?php 
                        $proveedores = (new Stock($base))->leerProv();
                        foreach ($proveedores as $prov) {
                            if($prov !== $stock->getIdProv()){ ?>
                            <option value="<?= $prov->getIdProv(); ?>"><?= $prov->getNomProv(); ?></option>
                        <?php }} ?>
                    </select>
                </div>
                
                <div class="button-group">  
                    <input class="boton submit" type="submit" value="Modificar">
                    <button class="boton cancelar"> <a href="inicioStock.php">Cancelar</a></button>
                </div>
            </form>

        <?php
        } else {
            echo "<h1 style='text-align:center;'>No hay datos para mostrar</h1>";
            echo "<br><button class='boton submit'><a href='inicioStock.php'>Volver</a></button><br>";
        }
        ?>
        </div>

    </main>
    <?php
    require '../footer.php';
    ?>
</body>
</html>
