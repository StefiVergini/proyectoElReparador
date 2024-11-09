<?php 

  require '../header.php';

  include("../conexionPDO.php");

  if(isset($_POST['insertar'])){
    $id="";
    $dni_cliente=$_POST["dni_cliente"];
    $nom_cliente=$_POST["nom_cliente"];
    $ape_cliente=$_POST["ape_cliente"];
    $tel_cliente=$_POST["tel_cliente"];
    $dir_cliente=$_POST["dir_cliente"];
    $email_cliente=$_POST["email_cliente"];
    $estado_cliente=$_POST["estado_cliente"];

    // Verificar si el DNI ya existe en la base de datos
    $sql = "SELECT COUNT(*) FROM clientes WHERE dni_cliente = :dni_cliente";
    $resultado = $base->prepare($sql);
    $resultado->execute(['dni_cliente' => $dni_cliente]);
    $contador = $resultado->fetchColumn();

    if ($contador > 0) {

      echo "<script>alert('Error, el número de DNI ya se encuentra registrado');</script>";
    } else {

    $sql = "INSERT INTO clientes(dni_cliente, nom_cliente, ape_cliente, tel_cliente,
    dir_cliente, email_cliente, estado_cliente)VALUES (:dni_cliente, :nom_cliente, :ape_cliente, :tel_cliente, :dir_cliente, :email_cliente, :estado_cliente)";

    $resultado = $base->prepare($sql);
    $resultado->execute(array(":dni_cliente"=> $dni_cliente, ":nom_cliente" => $nom_cliente, ":ape_cliente" => $ape_cliente, ":tel_cliente" => $tel_cliente, ":dir_cliente" => $dir_cliente, ":email_cliente" => $email_cliente, ":estado_cliente" => $estado_cliente));

    echo "<script>alert('Cliente agregado con éxito');</script>";

    header("Location:leer_db.php");
    }

  }

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Agregar Cliente</title>
    <link rel="stylesheet" href="../static/styles/style.css" />
    <link rel="stylesheet" href="style.css" />
    <script src="../static/js/funciones_select_nav.js"></script>
  </head>
  <body>

    <h4>
        Nuevo cliente
    </h4>
    <form action="<?php echo $_SERVER['PHP_SELF'];  ?>" method="post">
        <br> 
        <label class="label" for="">DNI / CUIT:</label>
        <input class="input" data-maxlength="8" oninput="this.value=this.value.slice(0,this.dataset.maxlength)" type="number" name="dni_cliente" id="" required>
        <label class="label" for="nombre">Nombre / Razón social:</label>
        <input class="input" type="text" name="nom_cliente" id="" required>
        <label class="label" for="apellido">Apellido:</label>
        <input class="input" type="text" name="ape_cliente" id="" required>
        <br>
        <br>
        <label class="label" for="telefono">Teléfono:</label>
        <input class="input" type="number" name="tel_cliente" id="" required>
        <label class="label" for="emai">Mail:</label>
        <input class="input" type="email" name="email_cliente" id="">
        <label class="label" for="domicilio">Dirección:</label>
        <input class="input" type="text" name="dir_cliente" id="">
        <br>
        <br>
        <label class="label" for="estado">Estado:</label>
        <select class="input" type="text" name="estado_cliente" id="" required>
          <option value="1">1</option>
          <option value="0">0</option>
        </select>
        <!--<label for="pass">Observaciones:</label>
        <input type="text" name="" id="pass">-->
        <br>
        <br>
        <div class="botones">
          <button class="btn" style="margin-right: 5px" type="submit" value="Guardar" name="insertar">Guardar</button>
          <button class="btn" style="margin-left: 5px"><a href="leer_db.php">Cancelar</a></button>
        </div>

    </form>

    <br>
    <br>

    <?php
      require '../footer.php';
    ?>
</body>
</html>