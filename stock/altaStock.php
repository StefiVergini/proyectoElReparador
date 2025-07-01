<?php
    include("../header.php");
    include("../conexionPDO.php");
    include("stock_class.php");
    $proveedores = (new Stock($base))->leerProv();
    $idProveedorSeleccionado = $_GET['idproveedor'] ?? ''; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Articulos</title>
    <link rel="stylesheet" href="../static/styles/style.css" /> 
    <link rel="stylesheet" href="../static/styles/tablas.css" />
    <script>
        function agregarFila() {
            let table = document.getElementById("tablaArticulos");
            let row = table.insertRow();
            row.innerHTML = `
                <td class ='tabla-data'><input class="input" style="border: 1px solid black; margin:1px" type="text" name="descripcion_art[]" required></td>
                <td class ='tabla-data'>
                    <select class="input" style="border: 1px solid black; margin:1px" name="tipo_stock[]" required>
                        <option value="Herramientas">Herramientas</option>
                        <option value="Electronica">Electronica</option>
                        <option value="Insumos">Insumos</option>
                        <option value="Libreria">Libreria</option>
                        <option value="Otros">Otros</option>
                    </select>
                </td>
                <td class ='tabla-data'>0</td> <!-- Cantidad en 0 -->
                <td class ='tabla-data'><button class="btn-iconos" type="button" onclick="eliminarFila(this)"><img src='../static/images/borrar.png' alt='eliminar' title='Eliminar Fila' width='20' height='20'></button></td>
            `;
        }

        function eliminarFila(btn) {
            let row = btn.parentNode.parentNode;
            row.parentNode.removeChild(row);
        }
        function redirigirSiEsAgregar(select) {
            if (select.value === "agregar_proveedor") {
                window.location.href = "../proveedores/altaProv.php";
            }
        }
    </script>
</head>
<body>
    <h1 class="titulo">Agregar Artículos</h1>

    <form action="guardarAltaStock.php" method="POST">
        <div class="div-con-botones">
            <select class="btn" name="idproveedor" required onchange="redirigirSiEsAgregar(this)">
                <option value="">Seleccione Proveedor</option>
                <?php foreach ($proveedores as $prov) { ?>
                    <option value="<?= $prov->getIdProv(); ?>"><?= $prov->getNomProv(); ?></option>
                    
                <?php } ?>
                <option value="agregar_proveedor">Agregar Proveedor</option>
            </select>
        </div>
        <div class="table-container">
            <table border="1" id="tablaArticulos" class="tabla">
                <thead>
                    <tr>
                        <th class="tabla-head">Descripción</th>
                        <th class="tabla-head">Tipo de Stock</th>
                        <th class="tabla-head">Cantidad</th>
                        <th class="tabla-head">Eliminar Fila</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Se llena dinámicamente con js -->
                </tbody>
            </table>
            <br>
            <button type='submit' class='btn' onclick="agregarFila()"> 
                   
                    Agregar Articulo +
            </button>
            <br><br>
            <div class="button-group">
                    <input class="boton submit" type="submit" value="Guardar">
                    <button class="boton cancelar" type="button" onclick="window.location.href='inicioStock.php'">
                        Cancelar
                    </button>
            </div>
        </form>
    </div>
    <?php
    require "../footer.php";
    ?>
</body>
</html>
