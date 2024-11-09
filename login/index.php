<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Acceso</title>
  <link rel="stylesheet" href="../static/styles/grid.css" />
</head>

<body>
  <main>
    <div class="container">
      <h1 class="title">Bienvenido</h1>

      <?php
      session_start();
      if (isset($_SESSION['error'])){
        echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
        unset($_SESSION['error']);
      }
      ?>




      <form action="login.php" method="post">
        <div class="datos">
          <label for="email">Correo electrónico</label>
          <input class="input" type="email" id="email" name="email" placeholder="email@example.com.ar" required />
        </div>
        <div class="datos">
          <label for="password">Contraseña</label>
          <input class="input" type="password" id="password" name="password" placeholder="**********">
        </div>
        <div class="cont">
          <button class="button" type="submit">Login</button>
        </div>
        <p>¿No tenes cuenta? Contactate con el administrador</p>
      </form>
    </div>
  </main>
  <?php
  require "../footer.php";
  ?>
</body>

</html>