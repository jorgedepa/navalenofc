<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro</title>
  <link rel="stylesheet" href="styles/registro.css">
  <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">
</head>

<body style="background-image: url(images/estadio-deportes.jpg);">
  <div class="container">
    <img src="./images/logo.png" alt="">
    <form method="POST" action="crearUsuario">
      <input type="text" name="usuario" placeholder="Nombre de usuario" required><br>
      <input type="email" name="correo" placeholder="Correo electrónico" required><br>
      <input type="password" name="contrasena" placeholder="Contraseña" required><br>
      <input type="submit" name="boton" value="Registrarse">
    </form>
  </div>
</body>

</html>