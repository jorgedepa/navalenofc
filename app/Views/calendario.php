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
    <title>Calendario</title>
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
        <h1>Enfrentamientos</h1>
        <table>
            <tr>
                <th>Jornada</th>
                <th>Equipo Local</th>
                <th>Equipo Visitante</th>
                <th>Fecha de partido</th>
                <th>Estado</th>
                <th>Resultado</th>
                <th></th>
            </tr>
            <?php
            foreach ($partidos as $key) {
                echo "<tr>";
                echo "<td>";
                echo $key['jornada'];
                echo "</td>";
                echo "<td>";
                echo $key['equipo_local'];
                echo "</td>";
                echo "<td>";
                echo $key['equipo_visitante'];
                echo "</td>";
                echo "<td>";
                echo $key['fecha_juego'];
                echo "</td>";
                echo "<td>";
                echo $key['estado'];
                echo "</td>";
                echo "<td>";
                echo $key['resultado'];
                echo "</td>";
                echo "<td>";
                echo "<a href='enfrentamiento?id=" . urlencode($key['id_partido']) .  "'><button>Ver más</button></a>";
                echo "</td>";
                echo "</tr>";
            }



            ?>
        </table>
        <h1>Insertar enfrentamiento</h1>
        <table>
            <tr>
                <th>Jornada</th>
                <th>Equipo Local</th>
                <th>Equipo Visitante</th>
                <th>Fecha de partido</th>
                <th></th>
            </tr>
            <tr>
                <form action="insertarPartido" method="post">
                    <td>
                        <select name="jornada" required>
                            <option value="1" selected disabled>Selecciona una Jornada</option>

                            <?php
                            foreach ($jornadas as $key) {
                                echo "<option value={$key['jornada']}>{$key['jornada']}</option>";
                            }
                            ?>
                        </select>

                    </td>
                    <td>
                        <select name="equipoLocal" required>
                            <option value="1" selected disabled>Selecciona un Equipo</option>

                            <?php
                            foreach ($equipos as $key) {
                                echo "<option value={$key['equipo_id']}>{$key['nombre_equipo']}</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select name="equipoVisitante" required>
                            <option value="1" selected disabled>Selecciona un Equipo</option>
                            <?php
                            foreach ($equipos as $key) {
                                echo "<option value={$key['equipo_id']}>{$key['nombre_equipo']}</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <input type="datetime-local" name="fechaPartido" required>
                    </td>
                    <td>
                        <input type="submit" value="Agregar partido">
                    </td>
                </form>
            </tr>
        </table>


    </main>
</body>

</html>