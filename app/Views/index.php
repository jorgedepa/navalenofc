<?php
$fechaVariable = $proximoPartido[0]['fecha_juego'];

$fechaActual = new DateTime();
$fechaVariable = new DateTime($fechaVariable);

if ($fechaActual < $fechaVariable) {
    $diferencia = $fechaActual->diff($fechaVariable);
    $tiempoRestante = [
        'dias' => $diferencia->d,
        'horas' => $diferencia->h,
        'minutos' => $diferencia->i,
    ];
} elseif ($fechaActual->format('Y-m-d') == $fechaVariable->format('Y-m-d')) {
    $tiempoRestante = [
        'dias' => 0,
        'horas' => 0,
        'minutos' => 0,
    ];
}


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navaleno FC</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/index.css">
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">


</head>

<body>
    <?php
    include "header.php";
    ?>

    <main>
        <section id="galeria">

            <?php foreach ($noticias as $key) {
                echo '<article class="elemento-galeria" style="background-image: url(\'images/noticias/' . urlencode($key['id']) . '.jpg\');">';
            ?>
                <?php echo "<a href='noticia?id=" . urlencode($key['id']) .  "'>"; ?>
                <div class="leyenda-galeria">
                    <h1><?php echo $key['titular'] ?></h1>
                </div>
                </a>
                </article>
            <?php } ?>
        </section>
        <section id="proximoPartido">
            <div id="contenedorProximo">
                <div id="cuenta">
                    <h4>PRÓXIMO PARTIDO</h4>
                    <div class="horas">
                        <div class="numero"><?php echo $tiempoRestante['dias'] ?></div>
                        <div class="texto">DÍAS</div>
                    </div>
                    <div class="contenedorDosPuntos">
                        <div class="dosPuntos">:</div>
                    </div>
                    <div class="horas">
                        <div class="numero"><?php echo $tiempoRestante['horas'] ?></div>
                        <div class="texto">HORAS</div>
                    </div>
                    <div class="contenedorDosPuntos">
                        <div class="dosPuntos">:</div>
                    </div>
                    <div class="horas">
                        <div class="numero"><?php echo $tiempoRestante['minutos'] ?></div>
                        <div class="texto">MINUTOS</div>
                    </div>
                </div>
                <div id="contenedor-3-partidos">
                    <div class="partidos">
                        <div class="encabezado-partido">
                            <div class="dia-partido"><?php echo date("d", strtotime($partidoAnterior[0]['fecha_juego'])); ?></div>
                            <div class="container-dia-mes">
                                <div class="dia-semana"><?php echo date("l", strtotime($partidoAnterior[0]['fecha_juego'])); ?></div>
                                <div class="mes"><?php echo date("F", strtotime($partidoAnterior[0]['fecha_juego'])); ?></div>
                            </div>
                        </div>
                        <div class="main-partido">
                            <div class="equiposEstadio">
                                <div class="equipos"><?php echo $partidoAnterior[0]['nombre_local'] . " - " . $partidoAnterior[0]['nombre_visitante'] ?></div>
                                <div class="estadio"><?php echo $partidoAnterior[0]['estadio'] ?></div>
                            </div>
                            <div class="equipoLocal">
                                <img src=<?php echo "images/equipos/" . $partidoAnterior[0]['equipo_local_id'] . ".png" ?>>
                            </div>
                            <div class="equipoVisitante">
                                <img src=<?php echo "images/equipos/" . $partidoAnterior[0]['equipo_visitante_id'] . ".png" ?>>
                            </div>
                            <div class="infoPartido">
                                <div class="resultadoHora">
                                    <?php echo date("H:i", strtotime($partidoAnterior[0]['fecha_juego'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="partidos" style="flex: 3;">
                        <div class="encabezado-partido">
                            <div class="dia-partido"><?php echo date("d", strtotime($proximoPartido[0]['fecha_juego'])); ?></div>
                            <div class="container-dia-mes">
                                <div class="dia-semana"><?php echo date("l", strtotime($proximoPartido[0]['fecha_juego'])); ?></div>
                                <div class="mes"><?php echo date("F", strtotime($proximoPartido[0]['fecha_juego'])); ?></div>
                            </div>
                        </div>
                        <div class="main-partido">
                            <div class="equiposEstadio">
                                <div class="equipos"><?php echo $proximoPartido[0]['nombre_local'] . " - " . $proximoPartido[0]['nombre_visitante'] ?></div>
                                <div class="estadio"><?php echo $proximoPartido[0]['estadio'] ?></div>
                            </div>
                            <div class="equipoLocal">
                                <img src=<?php echo "images/equipos/" . $proximoPartido[0]['equipo_local_id'] . ".png" ?>>
                            </div>
                            <div class="equipoVisitante">
                                <img src=<?php echo "images/equipos/" . $proximoPartido[0]['equipo_visitante_id'] . ".png" ?>>
                            </div>
                            <div class="infoPartido">
                                <div class="resultadoHora">
                                    <?php echo date("H:i", strtotime($proximoPartido[0]['fecha_juego'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="partidos">
                        <div class="encabezado-partido">
                            <div class="dia-partido"><?php echo date("d", strtotime($siguientePartido[0]['fecha_juego'])); ?></div>
                            <div class="container-dia-mes">
                                <div class="dia-semana"><?php echo date("l", strtotime($siguientePartido[0]['fecha_juego'])); ?></div>
                                <div class="mes"><?php echo date("F", strtotime($siguientePartido[0]['fecha_juego'])); ?></div>
                            </div>
                        </div>
                        <div class="main-partido">
                            <div class="equiposEstadio">
                                <div class="equipos"><?php echo $siguientePartido[0]['nombre_local'] . " - " . $siguientePartido[0]['nombre_visitante'] ?></div>
                                <div class="estadio"><?php echo $siguientePartido[0]['estadio'] ?></div>
                            </div>
                            <div class="equipoLocal">
                                <img src=<?php echo "images/equipos/" . $siguientePartido[0]['equipo_local_id'] . ".png" ?>>
                            </div>
                            <div class="equipoVisitante">
                                <img src=<?php echo "images/equipos/" . $siguientePartido[0]['equipo_visitante_id'] . ".png" ?>>
                            </div>
                            <div class="infoPartido">
                                <div class="resultadoHora">
                                    <?php echo date("H:i", strtotime($siguientePartido[0]['fecha_juego'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

            </div>
        </section>
        <section id="notCla">
            <div id="noticias">
                <div id="tituloNoticias">
                    <h1>Noticias</h1>
                </div>
                <div id="contenedorNoticias">
                    <?php foreach ($seisNoticias as $key) { ?>
                        <div>
                            <?php
                            echo "<a href='noticia?id=" . urlencode($key['id']) .  "'>";
                            echo '<div class="noticiaImagen" style="background-image: url(\'images/noticias/' . urlencode($key['id']) . '.jpg\');">';
                            ?>
                        </div>
                        <figcaption class="tituloNoticia">
                            <h3><?php echo $key['titular'] ?></h3>
                        </figcaption>
                        </a>
                </div>
            <?php } ?>
            </div>

            </div>
            <div id="clasificacion">
                <table>
                    <thead>
                        <tr>
                            <th>Posición</th>
                            <th></th>
                            <th>Equipo</th>
                            <th>Puntos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $pos = 1;
                        foreach ($clasificacion as $equipo) {
                            echo "<tr>";
                            echo "<td>" . $pos . "</td>";
                            echo "<td><img src=images/equipos/" . $equipo['equipo_id'] . ".png></td>";
                            echo "<td>" . $equipo['nombre'] . "</td>";
                            echo "<td>" . $equipo['puntos'] . "</td>";
                            echo "</tr>";
                            $pos++;
                        }
                        ?>
                    </tbody>
                </table>

            </div>

        </section>
        <?php include "footer.php"; ?>
</body>

</html>