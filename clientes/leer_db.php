<?php

    include("../conexionPDO.php");

      $busquedaRealizada = false;
          if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['busqueda'])) {
             $busqueda = $_POST['busqueda'];
             $sql = "SELECT * FROM clientes WHERE idclientes LIKE '$busqueda%' OR dni_cliente LIKE '$busqueda%' OR nom_cliente LIKE '$busqueda%' OR ape_cliente LIKE '$busqueda%' OR dir_cliente LIKE '$busqueda%' OR tel_cliente LIKE '$busqueda%'";
             $busquedaRealizada = true;
          }else{
            $sql = "SELECT * FROM clientes";
            $busquedaRealizada = true;
          }

          try{
            $resultado=$base->prepare($sql);
            $resultado -> execute();
        
          }catch(Exception $e){
            echo $e-> getMessage();
            echo "Línea del error: " . $e->getLine();
          }finally{
              $base=null;
          }
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Acceso</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="style.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  </head>
  <body>

    <?php
      require '../header.php';
    ?>
    <h3>Clientes</h3>

    <div class="search-box" style="text-align: center">
      <form method="post" action="" id="search-form">
         <fieldset style="border: 0px;">
            <input type="search" class="input_buscador" id="search-input" name="busqueda" placeholder="Buscar..."  />
            <input class="btn_buscador" name="buscar" type="submit" value="Buscar" />
            <i class="search-icon"></i>
         </fieldset>
      </form>
   </div>

   <div class="desplegable" style="text-align: center;">
    <button class="btn" style="position: absolute;top: 50%;">Exportar</button>
      <div class="link">
        <a target="_blank" href="pdf.php">Pdf</a>
        <a class="excel" href="excel.php">Excel</a>
      </div>
   </div>

     
   <div class="div_contenedor" style="text-align: center;">
      <button class="btn" style="position: absolute;top: 52%;">
        <a href="clientes_agregar.php" class="agregarCliente">Agregar nuevo cliente<img src="../static/images/agregar.png" style="width: 16px;" ></a>
      </button>
    </div>

    <br>

        <script type="text/javascript">
          function confirmarBaja(idClientes){
            
            if (confirm("¿Eliminar cliente? ")){
              window.location.href = "clientes_borrar.php?id=" + idClientes
            }

          }
        </script>
          
    

    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
      <table border="1" width="100%">
        <tr>
          <th> Código </th>
          <th> DNI </th>
          <th> Nombre </th>
          <th> Apellido </th>
          <th> Teléfono </th>
          <th> Domicilio </th>
          <th> Email </th>
          <th> Estado </th>
          <th colspan="2"> Acciones </th> 
        </tr>

        <?php
          /*include("../conexionPDO.php");
          $consulta = "SELECT * FROM clientes";
          $resultado = $base->query($consulta);*/
          while($mostrar = $resultado ->fetch(PDO::FETCH_ASSOC)) {?>
        <tr> 
          <td> <?php echo $mostrar['idclientes']; ?></td>
          <td> <?php echo $mostrar['dni_cliente']; ?></td>
          <td> <?php echo $mostrar['nom_cliente']; ?></td>
          <td> <?php echo $mostrar['ape_cliente']; ?></td>
          <td> <?php echo $mostrar['tel_cliente']; ?></td>
          <td> <?php echo $mostrar['dir_cliente']; ?></td>
          <td> <?php echo $mostrar['email_cliente']; ?></td>
          <td> <?php echo $mostrar['estado_cliente']; ?></td>
          <td><a href="clientes_modificar.php?id=<?php echo $mostrar['idclientes']?>& doc=<?php echo $mostrar['dni_cliente']?>& nom=<?php echo $mostrar['nom_cliente']?>& ape=<?php echo $mostrar['ape_cliente']?>& contac=<?php echo $mostrar['tel_cliente']?>& correo=<?php echo $mostrar['email_cliente']?>& dom=<?php echo $mostrar['dir_cliente'];?>& est=<?php echo $mostrar['estado_cliente']?>" class="modificar_a">Modificar<img src="../static/images/editar.png" style="width: 18px;"> </a></td>
          <!--<td> Agregar <a href="clientes_agregar.php"><img src="../static/images/agregar.png" style="width: 16px;"></td>-->
          <td><a href="#" onclick="confirmarBaja(<?php echo $mostrar['idclientes']?>)" class="borrar_a" onclick="confirmarBaja()">Baja<img src="../static/images/borrar.png" style="width: 16px;"></td>
        </tr>


      <?php
        } 
      ?>
      
      </table> 
    </form>
    <?php
      require '../footer.php';
    ?>
</body>
</html>
