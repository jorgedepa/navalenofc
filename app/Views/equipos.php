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
    <title>Equipos</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">
    <link rel='stylesheet' type='text/css' media='screen' href='styles/equipos.css'>

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

        <h1>Equipos</h1>
        <table>
            <tr>
                <th>Nombre</th>
                <th></th>
            </tr>
            <?php foreach ($equipos as $key) {
                echo "<tr>";
                // Nombre
                echo "<td>";
                echo "<a href='administrarEquipo?id=" . urlencode($key['equipo_id']) .  "'>";
                echo $key['nombre_equipo'];
                echo "</a>";
                echo "</td>";
                // Eliminar
                echo "<td>";
                echo "<form action='gestionarEquipo' method='post'>";
                echo "<input type='hidden' name='idEquipo' value='" . $key['equipo_id'] . "'>";
                echo "<button type='submit' name='borrar' onClick='this.form.submit();'>Borrar</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </table>
        <h1>Insertar equipo</h1>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Estadio</th>
                <th></th>
            </tr>
            <tr>
                <?php
                echo "<form action='gestionarEquipo' method='post'>";

                echo "<td>";
                echo "<input type='text' name='nombreI' required>";
                echo "</td>";

                echo "<td>";
                echo "<input type='text' name='estadioI' required>";
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