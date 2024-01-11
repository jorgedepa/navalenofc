<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">
    <link rel="stylesheet" href="styles/login.css">
    <title>Acceso y registro</title>
</head>

<body style="background-image: url(images/estadio-deportes.jpg);">
    <div class="container">
        <img src="images/logo.png" alt="">
        <form method="POST" action="entrar">
            <input type="email" name="correo" placeholder="Tu correo" required><br>
            <input type="password" name="contrasena" placeholder="Contraseña" required><br>
            <button type="submit">Iniciar Sesión</button>
            <hr>
        </form>
        <a href="registro"><button>Registrarse</button></a>
    </div>
</body>

</html>
