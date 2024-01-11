<!DOCTYPE html>
<html lang="es">
<meta charset='utf-8'>
<meta http-equiv='X-UA-Compatible' content='IE=edge'>
<title>Partido</title>
<meta name='viewport' content='width=device-width, initial-scale=1'>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles/partido.css">
<link rel="stylesheet" href="styles/header.css">
<link rel="stylesheet" href="styles/footer.css">
<link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">
</head>

<body>
    <?php include "header.php"; ?>

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
    <div id="mapa">
        <div id="alineacionLocal">
            <table>
                <tr>
                    <th><?php echo $nombreEquipoLocal[0]['nombre_equipo']; ?></th>
                </tr>
                <tr>
                    <th>Alineación titular</th>
                </tr>
                <?php
                foreach ($equipoLocal as $key) {
                    if ($key['titular'] == 1) {
                        echo "<tr>";
                        echo "<td>" . $key['nombre_jugador'] . "</td>";
                        echo "</tr>";
                    }
                }
                ?>
                <tr>
                    <th>Suplentes</th>
                </tr>
                <?php
                foreach ($equipoLocal as $key) {
                    if ($key['titular'] == 0) {
                        echo "<tr>";
                        echo "<td>" . $key['nombre_jugador'] . "</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </table>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 500" width="800" height="500">
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

            <rect x="20" y="20" width="760" height="460" fill="none" stroke="#FFF" stroke-width="5" />

            <line x1="400" y1="20" x2="400" y2="480" stroke="#FFF" stroke-width="5" />

            <rect x="20" y="180" width="40" height="140" fill="none" stroke="#FFF" stroke-width="5" />

            <rect x="20" y="130" width="100" height="240" fill="none" stroke="#FFF" stroke-width="5" />

            <rect x="740" y="180" width="40" height="140" fill="none" stroke="#FFF" stroke-width="5" />

            <rect x="680" y="130" width="100" height="240" fill="none" stroke="#FFF" stroke-width="5" />

            <circle cx="400" cy="250" r="7" fill="#FFF" />
            <circle cx="400" cy="250" r="100" fill="none" stroke="#FFF" stroke-width="5" />

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
            <table>
                <tr>
                    <th><?php echo $nombreEquipoVisitante[0]['nombre_equipo'] ?></th>
                </tr>
                <tr>
                    <th>Alineación titular</th>
                </tr>
                <?php
                foreach ($equipoVisitante as $key) {
                    if ($key['titular'] == 1) {
                        echo "<tr>";
                        echo "<td>" . $key['nombre_jugador'] . "</td>";
                        echo "</tr>";
                    }
                }
                ?>
                <tr>
                    <th>Suplentes</th>
                </tr>
                <?php
                foreach ($equipoVisitante as $key) {
                    if ($key['titular'] == 0) {
                        echo "<tr>";
                        echo "<td>" . $key['nombre_jugador'] . "</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </table>
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
    <?php include "footer.php"; ?>

</body>

</body>

</html>