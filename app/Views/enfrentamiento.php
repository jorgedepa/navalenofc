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
    <title>Enfrentamiento</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">
    <link rel='stylesheet' type='text/css' media='screen' href='styles/enfrentamiento.css'>

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
        <div id="contenedorPartido">
            <div id="divPartido">
                <div id="nombreEquipoLocal"><?php echo $nombreEquipoLocal[0]['nombre_equipo']; ?></div>
                <?php echo "<div><img src=images/equipos/" . $nombreEquipoLocal[0]['equipo_local_id'] . ".png></div>"; ?>
                <div id="resultadoLocal"><?php echo $golesEquipoLocal; ?></div>
                <div id="contenedorEstado">
                    <div><?php echo ucfirst($estado); ?></div>
                </div>
                <div id="resultadoVisitante"><?php echo $golesEquipoVisitante; ?></div>
                <?php echo "<div><img src=images/equipos/" . $nombreEquipoVisitante[0]['equipo_visitante_id'] . ".png></div>"; ?>
                <div id="nombreEquipoVisitante"><?php echo $nombreEquipoVisitante[0]['nombre_equipo']; ?></div>

            </div>
        </div>
        <form action="finalizarPartido" method="post">
            <input type="submit" value="Finalizar Partido" />
        </form>
        <div id="mapa">
            <div id="alineacionLocal">
                <?php
                if (count($equipoLocal) < 11) {
                    echo "<table>";
                    echo "<tr>";
                    echo "<th>{$nombreEquipoLocal[0]['nombre_equipo']}</th>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<th>Alineación titular</th>";
                    echo "</tr>";
                    echo "<form action='insertarAlineacion' method='post'>";

                    foreach ($jugadoresLocal as $key) {
                        echo "<tr>";
                        echo "<td>";
                        echo "<input type=checkbox id=" . $key['id_jugador']  . " name=jugadores[] value=" . $key['id_jugador'] . ">";
                        echo "<label for=" . $key['id_jugador'] . ">" . $key['nombre'] . "</label><br>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    echo "<tr>";
                    echo "<td>";
                    echo "<input type=submit name=alinearLocal value=Alinear>";
                    echo "</td>";
                    echo "</tr>";
                    echo "</form>";
                    echo "</table>";
                } else {
                    echo "<table>";
                    echo "<tr>";
                    echo "<th colspan=3>{$nombreEquipoLocal[0]['nombre_equipo']}</th>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<th colspan=3>Alineación titular</th>";
                    echo "</tr>";

                    foreach ($equipoLocal as $key) {
                        echo "<form action='actualizarAlineacion' method='post'>";
                        if ($key['titular'] == 1) {
                            echo "<tr>";

                            echo "<td>";
                            echo "<input type='hidden' name='jugadorTitular' value={$key['id_jugador']}>";
                            echo "<select name=jugadorSeleccionado onChange='this.form.submit();'>";
                            echo "<option value={$key['id_jugador']} selected disabled>{$key['nombre_jugador']}</option>";
                            foreach ($equipoLocal as $suplente) {
                                if ($suplente['titular'] == 0) {
                                    echo "<option value={$suplente['id_jugador']}>{$suplente['nombre_jugador']}</option>";
                                }
                            }
                            echo "</select>";
                            echo "</td>";
                            echo "</form>";
                            echo "<form action='insertarGol' method='post'>";
                            echo "<input type='hidden' name='jugadorTitular' value={$key['id_jugador']}>";
                            echo "<input type='hidden' name='nombreEquipoLocal' value={$nombreEquipoLocal[0]['nombre_equipo']}>";
                            echo "<td>";
                            echo "<input type='number' name='minutosGol' min='0' max='90'  required>";
                            echo "</td>";

                            echo "<td>";
                            echo "<input type='submit' name='accion' value='gol'>";
                            echo "</td>";
                            echo "</form>";


                            echo "</tr>";
                        }
                    }

                    echo "<tr>";
                    echo "<th colspan=3>Suplentes</th>";
                    echo "</tr>";

                    foreach ($equipoLocal as $key) {
                        if ($key['titular'] == 0) {
                            echo "<tr>";
                            echo "<td colspan=3>{$key['nombre_jugador']}</td>";
                            echo "</tr>";
                        }
                    }

                    echo "</table>";
                }

                ?>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 500" width="800" height="500">
                <!-- Césped dividido en 12 secciones verticales con colores alternados -->
                <rect x="0" y="0" width="66.6667" height="500" fill="#6DBE45" />
                <rect x="66.6667" y="0" width="66.6667" height="500" fill="#8ED56F" />
                <rect x="133.3334" y="0" width="66.6667" height="500" fill="#6DBE45" />
                <rect x="200" y="0" width="66.6667" height="500" fill="#8ED56F" />
                <rect x="266.6667" y="0" width="66.6667" height="500" fill="#6DBE45" />
                <rect x="333.3334" y="0" width="66.6667" height="500" fill="#8ED56F" />
                <rect x="400" y="0" width="66.6667" height="500" fill="#6DBE45" />
                <rect x="466.6667" y="0" width="66.6667" height="500" fill="#8ED56F" />
                <rect x="533.3334" y="0" width="66.6667" height="500" fill="#6DBE45" />
                <rect x="600" y="0" width="66.6667" height="500" fill="#8ED56F" />
                <rect x="666.6667" y="0" width="66.6667" height="500" fill="#6DBE45" />
                <rect x="733.3334" y="0" width="66.6666" height="500" fill="#8ED56F" />

                <!-- Línea de banda -->
                <rect x="20" y="20" width="760" height="460" fill="none" stroke="#FFF" stroke-width="5" />

                <!-- Línea de centro -->
                <line x1="400" y1="20" x2="400" y2="480" stroke="#FFF" stroke-width="5" />

                <!-- Área pequeña de portería izquierda -->
                <rect x="20" y="180" width="40" height="140" fill="none" stroke="#FFF" stroke-width="5" />

                <!-- Área grande de portería izquierda -->
                <rect x="20" y="130" width="100" height="240" fill="none" stroke="#FFF" stroke-width="5" />

                <!-- Área pequeña de portería derecha -->
                <rect x="740" y="180" width="40" height="140" fill="none" stroke="#FFF" stroke-width="5" />

                <!-- Área grande de portería derecha -->
                <rect x="680" y="130" width="100" height="240" fill="none" stroke="#FFF" stroke-width="5" />

                <!-- Centro del campo -->
                <circle cx="400" cy="250" r="7" fill="#FFF" />
                <circle cx="400" cy="250" r="100" fill="none" stroke="#FFF" stroke-width="5" />

                <!-- Jugadores en la mitad izquierda del campo -->
                <circle cx="50" cy="250" r="18" fill="#0b3d27" />

                <circle cx="140" cy="100" r="18" fill="#0b3d27" />
                <circle cx="140" cy="200" r="18" fill="#0b3d27" />
                <circle cx="140" cy="300" r="18" fill="#0b3d27" />
                <circle cx="140" cy="400" r="18" fill="#0b3d27" />

                <circle cx="250" cy="100" r="18" fill="#0b3d27" />
                <circle cx="250" cy="200" r="18" fill="#0b3d27" />
                <circle cx="250" cy="300" r="18" fill="#0b3d27" />
                <circle cx="250" cy="400" r="18" fill="#0b3d27" />

                <circle cx="350" cy="200" r="18" fill="#0b3d27" />
                <circle cx="350" cy="300" r="18" fill="#0b3d27" />

                <!-- Jugadores en la mitad derecha del campo -->
                <circle cx="750" cy="250" r="18" fill="#871414" />

                <circle cx="660" cy="100" r="18" fill="#871414" />
                <circle cx="660" cy="200" r="18" fill="#871414" />
                <circle cx="660" cy="300" r="18" fill="#871414" />
                <circle cx="660" cy="400" r="18" fill="#871414" />

                <circle cx="550" cy="100" r="18" fill="#871414" />
                <circle cx="550" cy="200" r="18" fill="#871414" />
                <circle cx="550" cy="300" r="18" fill="#871414" />
                <circle cx="550" cy="400" r="18" fill="#871414" />

                <circle cx="450" cy="200" r="18" fill="#871414" />
                <circle cx="450" cy="300" r="18" fill="#871414" />
            </svg>
            <div id="alineacionVisitante">
                <?php
                if (count($equipoVisitante) < 11) {
                    echo "<table>";
                    echo "<tr>";
                    echo "<th>{$nombreEquipoVisitante[0]['nombre_equipo']}</th>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<th>Alineación titular</th>";
                    echo "</tr>";
                    echo "<form action='insertarAlineacion' method='post'>";

                    foreach ($jugadoresVisitante as $key) {
                        echo "<tr>";
                        echo "<td>";
                        echo "<input type=checkbox id=" . $key['id_jugador']  . " name=jugadoresV[] value=" . $key['id_jugador'] . ">";
                        echo "<label for=" . $key['id_jugador'] . ">" . $key['nombre'] . "</label><br>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    echo "<tr>";
                    echo "<td>";
                    echo "<input type=submit name=alinearVisitante value=Alinear>";
                    echo "</td>";
                    echo "</tr>";
                    echo "</form>";
                    echo "</table>";
                } else {
                    echo "<table>";
                    echo "<tr>";
                    echo "<th colspan=3>{$nombreEquipoVisitante[0]['nombre_equipo']}</th>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<th colspan=3>Alineación titular</th>";
                    echo "</tr>";

                    foreach ($equipoVisitante as $key) {
                        echo "<form action='actualizarAlineacion' method='post'>";
                        if ($key['titular'] == 1) {
                            echo "<tr>";

                            echo "<td>";
                            echo "<input type='hidden' name='jugadorTitular' value={$key['id_jugador']}>";
                            echo "<select name=jugadorSeleccionado onChange='this.form.submit();'>";
                            echo "<option value={$key['id_jugador']} selected disabled>{$key['nombre_jugador']}</option>";
                            foreach ($equipoVisitante as $suplente) {
                                if ($suplente['titular'] == 0) {
                                    echo "<option value={$suplente['id_jugador']}>{$suplente['nombre_jugador']}</option>";
                                }
                            }
                            echo "</select>";
                            echo "</td>";
                            echo "</form>";
                            echo "<form action='insertarGolVisitante' method='post'>";
                            echo "<input type='hidden' name='jugadorTitular' value={$key['id_jugador']}>";
                            echo "<input type='hidden' name='nombreEquipoVisitante' value={$nombreEquipoVisitante[0]['nombre_equipo']}>";
                            echo "<td>";
                            echo "<input type='number' name='minutosGol' min='0' max='90'  required>";
                            echo "</td>";

                            echo "<td>";
                            echo "<input type='submit' name='accion' value='gol'>";
                            echo "</td>";
                            echo "</form>";


                            echo "</tr>";
                        }
                    }

                    echo "<tr>";
                    echo "<th colspan=3>Suplentes</th>";
                    echo "</tr>";

                    foreach ($equipoVisitante as $key) {
                        if ($key['titular'] == 0) {
                            echo "<tr>";
                            echo "<td colspan=3>{$key['nombre_jugador']}</td>";
                            echo "</tr>";
                        }
                    }

                    echo "</table>";
                }

                ?>
            </div>
        </div>
        <table border="1px" id="resumen">
            <tr>
                <th>Minuto</th>
                <th>Goleador</th>
            </tr>
            <?php
            foreach ($secuenciaGoles as $key) {
                echo "<tr>";
                echo "<td>" . $key['minuto'] . "</td>";
                echo "<td>" . $key['nombre'] . "</td>";
                echo "</tr>";
            }


            ?>

        </table>



    </main>
</body>

</html>