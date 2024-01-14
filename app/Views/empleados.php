<?php
if (session()->has('nombreUsuario')) {
    $nombreUsuario = session()->get('nombreUsuario');
}
$dia1 = session()->get('dia1');
$dia2 = session()->get('dia2');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Empleados</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">
    <link rel='stylesheet' type='text/css' media='screen' href='styles/empleados.css'>

</head>

<body>
    <header>
        <div id="logo">
            <img alt="" src="images/logo.png">
            <a href="entrar">
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
            <a href="inicioResponsable">
                <li>Inicio</li>
            </a>
            <a href="empleados">
                <li>Empleados</li>
            </a>
        </ul>
    </aside>
    <main>
        <h1>Empleados de la empresa</h1>
        <table>
            <tr>
                <th>Correo</th>
                <th>Nombre de usuario</th>
                <th>Contraseña</th>
                <th>Rol</th>
                <th></th>
            </tr>

            <?php
            foreach ($empleados as $key) {
                echo "<tr>";

                // Correo electrónico
                echo "<td>";
                echo "<form action='actualizarEmpleado' method='post'>";
                echo "<input type='hidden' name='idUsuario' value='" . $key['id_usuario'] . "'>";
                echo "<input type='email' name='correo' value='" . $key['correo_electronico'] . "' onChange='this.form.submit();'>";
                echo "</form>";
                echo "</td>";

                // Nombre de usuario
                echo "<td>";
                echo "<form action='actualizarEmpleado' method='post'>";
                echo "<input type='hidden' name='idUsuario' value='" . $key['id_usuario'] . "'>";
                echo "<input type='text' name='usuario' value='" . $key['nombre_usuario'] . "' onChange='this.form.submit();'>";
                echo "</form>";
                echo "</td>";

                // contraseña
                echo "<td>";
                echo "<form action='actualizarEmpleado' method='post'>";
                echo "<input type='hidden' name='idUsuario' value='" . $key['id_usuario'] . "'>";
                echo "<input type='password' name='contrasena' value='" . $key['contrasena'] . "' onChange='this.form.submit();'>";
                echo "</form>";
                echo "</td>";

                // rol
                echo "<td>";
                echo "<form action='actualizarEmpleado' method='post'>";
                echo "<input type='hidden' name='idUsuario' value='" . $key['id_usuario'] . "'>";
                echo "<select name=rol onChange='this.form.submit();'>";
                if ($key['rol'] == "trabajador") {
                    echo "<option value=trabajador selected>trabajador</option>";
                    echo "<option value=responsable>responsable</option>";
                } elseif ($key['rol'] == "responsable") {
                    echo "<option value=trabajador>trabajador</option>";
                    echo "<option value=responsable selected>responsable</option>";
                }
                echo "</select>";
                echo "</form>";
                echo "</td>";

                // Borrar
                echo "<td>";
                echo "<form action='actualizarEmpleado' method='post'>";
                echo "<input type='hidden' name='idUsuario' value='" . $key['id_usuario'] . "'>";
                echo "<button type='submit' name='borrar' onClick='this.form.submit();'>Borrar</button>";
                echo "</form>";
                echo "</td>";


                echo "</tr>";
            }
            ?>
        </table>
        <!-- Sección para insertar un nuevo empleado -->
        <h1>INSERTAR REGISTRO</h1>
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
                echo "<form action='actualizarEmpleado' method='post'>";

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
                echo "<option value=trabajador>trabajador</option>";
                echo "<option value=responsable>responsable</option>";
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