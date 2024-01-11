<?php
if (session()->has('nombreUsuario')) {
    $nombreUsuario = session()->get('nombreUsuario');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Usuarios</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">
    <link rel='stylesheet' type='text/css' media='screen' href='styles/usuarios.css'>

</head>

<body>
    <header>
        <div id="logo">
            <img alt="" src="images/logo.png">
            <a href="inicioAdmin">
                <h2>Navaleno Fútbol Club</h2>
            </a>
        </div>
        <div id=inicioSesion>
            <span><?php echo $nombreUsuario ?></span>
            <a href=cerrar><button>Cerrar Sesión</button></a>
        </div>
    </header>
    <aside>
        <ul>
            <a href="usuarios">
                <li>Usuarios</li>
            </a>
            <a href="equipos">
                <li>Equipos</li>
            </a>
            <a href="calendario">
                <li>Calendario</li>
            </a>
            <a href="administrarNoticia">
                <li>Noticias</li>
            </a>
        </ul>
    </aside>
    <main>
        <h1>Administradores</h1>
        <table>
            <tr>
                <th>Correo electrónico</th>
                <th>Nombre de usuario</th>
                <th>Contraseña</th>
                <th></th>
            </tr>
            <?php foreach ($administradores as $key) {
                echo "<tr>";
                // Correo
                echo "<td>";
                echo "<form action='actualizarUsuario' method='post'>";
                echo "<input type='hidden' name='idUsuario' value='" . $key['id_usuario'] . "'>";
                echo "<input type='text' name='correo' value='" . $key['correo_electronico'] . "' onChange='this.form.submit();'>";
                echo "</form>";
                echo "</td>";
                // Nombre de usuario
                echo "<td>";
                echo "<form action='actualizarUsuario' method='post'>";
                echo "<input type='hidden' name='idUsuario' value='" . $key['id_usuario'] . "'>";
                echo "<input type='text' name='nombreUsuario' value='" . $key['nombre_usuario'] . "' onChange='this.form.submit();'>";
                echo "</form>";
                echo "</td>";
                // Contraseña
                echo "<td>";
                echo "<form action='actualizarUsuario' method='post'>";
                echo "<input type='hidden' name='idUsuario' value='" . $key['id_usuario'] . "'>";
                echo "<input type='password' name='contrasena' value='" . $key['contrasena'] . "' onChange='this.form.submit();'>";
                echo "</form>";
                echo "</td>";

                // Eliminar
                echo "<td>";
                echo "<form action='actualizarUsuario' method='post'>";
                echo "<input type='hidden' name='idUsuario' value='" . $key['id_usuario'] . "'>";
                echo "<button type='submit' name='borrar' onClick='this.form.submit();'>Borrar</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }


            ?>

        </table>
        <h1>Usuarios</h1>
        <table>
            <tr>
                <th>Correo electrónico</th>
                <th>Nombre de usuario</th>
                <th>Contraseña</th>
                <th></th>
            </tr>
            <?php foreach ($usuarios as $key) {
                echo "<tr>";
                // Correo
                echo "<td>";
                echo "<form action='actualizarUsuario' method='post'>";
                echo "<input type='hidden' name='idUsuario' value='" . $key['id_usuario'] . "'>";
                echo "<input type='text' name='correo' value='" . $key['correo_electronico'] . "' onChange='this.form.submit();'>";
                echo "</form>";
                echo "</td>";
                // Nombre de usuario
                echo "<td>";
                echo "<form action='actualizarUsuario' method='post'>";
                echo "<input type='hidden' name='idUsuario' value='" . $key['id_usuario'] . "'>";
                echo "<input type='text' name='nombreUsuario' value='" . $key['nombre_usuario'] . "' onChange='this.form.submit();'>";
                echo "</form>";
                echo "</td>";
                // Contraseña
                echo "<td>";
                echo "<form action='actualizarUsuario' method='post'>";
                echo "<input type='hidden' name='idUsuario' value='" . $key['id_usuario'] . "'>";
                echo "<input type='password' name='contrasena' value='" . $key['contrasena'] . "' onChange='this.form.submit();'>";
                echo "</form>";
                echo "</td>";

                // Eliminar
                echo "<td>";
                echo "<form action='actualizarUsuario' method='post'>";
                echo "<input type='hidden' name='idUsuario' value='" . $key['id_usuario'] . "'>";
                echo "<button type='submit' name='borrar' onClick='this.form.submit();'>Borrar</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }


            ?>
        </table>
        <h1>Añadir usuarios</h1>
        <table>
            <tr>
                <th>Correo</th>
                <th>Nombre de usuario</th>
                <th>Contraseña</th>
                <th>Rol</th>
                <th></th>
            </tr>
            <tr>
                <?php
                echo "<form action='actualizarUsuario' method='post'>";

                echo "<td>";
                echo "<input type='email' name='correoI' required>";
                echo "</td>";

                echo "<td>";
                echo "<input type='text' name='usuarioI' required>";
                echo "</td>";

                echo "<td>";
                echo "<input type='password' name='contrasenaI' required>";
                echo "</td>";

                echo "<td>";
                echo "<select name=rolI required>";
                echo "<option value='' disabled selected>Selecciona una opción</option>";
                echo "<option value=administrador>Administrador</option>";
                echo "<option value=usuario>Usuario</option>";
                echo "</select>";
                echo "</td>";

                echo "<td>";
                echo "<input type=submit name=enviarI value=Insertar>";
                echo "</td>";
                echo "</form>";


                ?>
            </tr>
        </table>

    </main>
</body>

</html>