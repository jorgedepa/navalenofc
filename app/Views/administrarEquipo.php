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
    <title>Administrar equipo</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">
    <link rel='stylesheet' type='text/css' media='screen' href='styles/administrarEquipo.css'>

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

        <h1><?php echo $equipos[0]['nombre_equipo'] ?></h1>
        <fieldset>
            <legend>Datos del equipo</legend>
            <label for="nombre">Nombre:</label>
            <?php
            echo "<form action='actualizarEquipo' method='post'>";
            echo "<input type='text' name='nombre' id='nombre' value='" . $equipos[0]['nombre_equipo'] . "' onChange='this.form.submit();'>";
            echo "<input type='hidden' name='idEquipo' value='" . $equipos[0]['equipo_id'] . "'>";
            echo "</form>";
            ?>
            <label for="estadio">Estadio:</label>
            <?php
            echo "<form action='actualizarEquipo' method='post'>";
            echo "<input type='text' name='estadio' id='estadio' value='" . $equipos[0]['estadio'] . "' onChange='this.form.submit();'>";
            echo "<input type='hidden' name='idEquipo' value='" . $equipos[0]['equipo_id'] . "'>";
            echo "</form>";
            ?>
        </fieldset>
        <fieldset>
            <legend>Jugadores</legend>
            <table>
                <tr>
                    <th>Nombre</th>
                    <th>Acción</th>
                </tr>
                <?php
                foreach ($jugadores as $key) {
                    echo "<tr>";
                    echo "<td>";
                    echo "<form action='actualizarEquipo' method='post'>";
                    echo "<input type='hidden' name='idJugador' value='" . $key['id_jugador'] . "'>";
                    echo "<input type='hidden' name='idEquipo' value='" . $key['equipo_id'] . "'>";

                    echo "<input type='text' name='nombreJugador' value='" . $key['nombre'] . "' onChange='this.form.submit();'>";
                    echo "</form>";

                    echo "</td>";

                    echo "<td>";
                    echo "<form action='actualizarEquipo' method='post'>";

                    echo "<input type='hidden' name='idJugador' value='" . $key['id_jugador'] . "'>";
                    echo "<input type='hidden' name='idEquipo' value='" . $key['equipo_id'] . "'>";

                    echo "<button type='submit' name='borrar' onClick='this.form.submit();'>Borrar</button>";
                    echo "</form>";

                    echo "</td>";
                    echo "</tr>";
                }


                ?>
            </table>
        </fieldset>
        <h1>Insertar Jugador</h1>
        <table>
            <tr>
                <th>Nombre</th>
                <th></th>
            </tr>
            <tr>
                <?php
                echo "<form action='actualizarEquipo' method='post'>";

                echo "<td>";
                echo "<input type='text' name='nombreI' required>";
                echo "</td>";

                echo "<td>";
                echo "<input type=submit name=enviarI value=Insertar>";
                echo "<input type='hidden' name='idEquipo' value='" . $equipos[0]['equipo_id'] . "'>";
                echo "</td>";
                echo "</form>";


                ?>
            </tr>
        </table>

    </main>
</body>

</html>