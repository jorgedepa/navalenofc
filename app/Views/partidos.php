<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/partidos.css">
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/footer.css">
    <title>Calendario</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">
</head>

<body>
    <?php include "header.php"; ?>

    <div id="contenedorNoticias">
        <h1>Calendario</h1>
        <div id="selectorMes">
            <form method="post" action="filtro">
                <?php
                $mes_actual = idate('m');
                $meses = array(9, 10, 11, 12, 1, 2, 3, 4, 5);
                $abreviaturas = array(
                    1 => 'Ene',
                    2 => 'Feb',
                    3 => 'Mar',
                    4 => 'Abr',
                    5 => 'May',
                    9 => 'Sep',
                    10 => 'Oct',
                    11 => 'Nov',
                    12 => 'Dic'
                );


                foreach ($meses as $mes) {
                    $checked = '';
                    if ($mes == session('mesSeleccionado')) {
                        $checked = 'checked';
                    }
                    $abreviatura = $abreviaturas[$mes];
                    echo "<input type='radio' id='$mes' name='mes' value='$mes' onChange='this.form.submit();' $checked>";
                    echo "<label for='$mes'>$abreviatura</label>";
                }

                ?>


            </form>

        </div>
        <div class="grid-container">
            <?php foreach ($calendario as $key) { ?>
                <div class="card">
                    <div class="resultado">
                        <?php echo "<div class=equipoLocal><img src=images/equipos/" . $key['equipo_local_id'] . ".png></div>"; ?>
                        <div class="resultadoPartido">
                            <?php
                            if ($key['estado'] == "finalizado" || $key['estado'] == "en juego") {
                                echo $key['golesEquipoLocal'] . " - " . $key['golesEquipoVisitante'];
                            } elseif ($key['estado'] == "programado") {
                                echo "- - -";
                            }
                            ?>
                        </div>
                        <?php echo "<div class=equipoVisitante><img src=images/equipos/" . $key['equipo_visitante_id'] . ".png></div>"; ?>
                        <?php echo "<div class=nombreLocal>" . $key['equipo_local'] . "</div>"; ?>
                        <div class="div5"> </div>
                        <?php echo "<div class=nombreVisitante>" . $key['equipo_visitante'] . "</div>"; ?>
                    </div>
                    <div class="info">
                        <h3>Liga provincial</h3>
                        <div class="fecha">
                            <div class="icono">
                                <svg _ngcontent-tba-c34="" focusable="false" class="rm-icon rm-icon--relative" width="16" height="16" aria-hidden="true">
                                    <use _ngcontent-tba-c34="" xlink:href="#calandar"></use>
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <defs>
                                            <clipPath id="a">
                                                <path d="M0 0h24v24H0z"></path>
                                            </clipPath>

                                            <clipPath id="a">
                                                <path d="M0 0h24v24H0z"></path>
                                            </clipPath>

                                            <clipPath id="a">
                                                <path d="M0 0h24v24H0z"></path>
                                            </clipPath>

                                            <clipPath id="a">
                                                <path d="M0 0h24v24H0z"></path>
                                            </clipPath>

                                            <clipPath id="a">
                                                <path d="M0 0h24v24H0z"></path>
                                            </clipPath>

                                            <clipPath id="a">
                                                <path d="M0 0h24v24H0z"></path>
                                            </clipPath>

                                            <clipPath id="a">
                                                <path d="M0 0h24v24H0z"></path>
                                            </clipPath>

                                            <clipPath id="a">
                                                <path d="M0 0h24v8.077H0z"></path>
                                            </clipPath>

                                            <clipPath id="a">
                                                <path d="M0 0h24v24H0z"></path>
                                            </clipPath>

                                            <clipPath id="a">
                                                <path transform="translate(.462 .04)" d="M0 0h24v24H0z"></path>
                                            </clipPath>
                                        </defs>
                                        <symbol id="calandar" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7 0a1 1 0 0 1 1 1v1h8V1a1 1 0 1 1 2 0v1h1a4 4 0 0 1 4 4v13a4 4 0 0 1-4 4H5a4 4 0 0 1-4-4V6a4 4 0 0 1 4-4h1V1a1 1 0 0 1 1-1ZM5 4a2 2 0 0 0-2 2v1h18V6a2 2 0 0 0-2-2H5Zm16 5H3v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9ZM6.5 13a1 1 0 0 1 1-1h2a1 1 0 1 1 0 2h-2a1 1 0 0 1-1-1Zm7 0a1 1 0 0 1 1-1h2a1 1 0 1 1 0 2h-2a1 1 0 0 1-1-1Zm-7 4a1 1 0 0 1 1-1h2a1 1 0 1 1 0 2h-2a1 1 0 0 1-1-1Zm7 0a1 1 0 0 1 1-1h2a1 1 0 1 1 0 2h-2a1 1 0 0 1-1-1Z">
                                            </path>
                                        </symbol>
                                    </svg>
                                </svg>
                            </div>
                            <?php echo "<div class=texto>" . ucfirst($key['fecha_juego']) . "</div>"; ?>
                        </div>
                        <div class="ubicacion">
                            <div class="icono">
                                <svg _ngcontent-tba-c34="" focusable="false" class="rm-icon rm-icon--relative" width="16" height="16" aria-hidden="true">
                                    <use _ngcontent-tba-c34="" xlink:href="#location"></use><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <defs>
                                            <clipPath id="a">
                                                <path d="M0 0h24v24H0z"></path>
                                            </clipPath>

                                            <clipPath id="a">
                                                <path d="M0 0h24v24H0z"></path>
                                            </clipPath>

                                            <clipPath id="a">
                                                <path d="M0 0h24v24H0z"></path>
                                            </clipPath>

                                            <clipPath id="a">
                                                <path d="M0 0h24v24H0z"></path>
                                            </clipPath>

                                            <clipPath id="a">
                                                <path d="M0 0h24v24H0z"></path>
                                            </clipPath>

                                            <clipPath id="a">
                                                <path d="M0 0h24v24H0z"></path>
                                            </clipPath>

                                            <clipPath id="a">
                                                <path d="M0 0h24v24H0z"></path>
                                            </clipPath>

                                            <clipPath id="a">
                                                <path d="M0 0h24v8.077H0z"></path>
                                            </clipPath>

                                            <clipPath id="a">
                                                <path d="M0 0h24v24H0z"></path>
                                            </clipPath>

                                            <clipPath id="a">
                                                <path transform="translate(.462 .04)" d="M0 0h24v24H0z"></path>
                                            </clipPath>
                                        </defs>
                                        <symbol id="location" viewBox="0 0 24 25">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2a9 9 0 0 0-9 9c0 .86.31 1.855.891 2.944.577 1.081 1.385 2.19 2.296 3.26 1.823 2.138 3.98 4.027 5.28 5.096.315.26.75.26 1.066 0 1.3-1.069 3.457-2.958 5.28-5.097.911-1.069 1.72-2.178 2.296-3.259.58-1.09.891-2.085.891-2.944a9 9 0 0 0-9-9ZM1 11C1 4.925 5.925 0 12 0s11 4.925 11 11c0 1.314-.464 2.643-1.126 3.885-.668 1.251-1.572 2.481-2.539 3.616-1.933 2.268-4.192 4.243-5.531 5.344a2.828 2.828 0 0 1-3.608 0c-1.339-1.101-3.598-3.076-5.531-5.344-.967-1.135-1.872-2.365-2.539-3.616C1.464 13.643 1 12.314 1 11Zm11-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-4 2a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z">
                                            </path>
                                        </symbol>
                                    </svg>
                                </svg>
                            </div>
                            <?php echo "<div class=texto>" . $key['estadio'] . "</div>"; ?>
                        </div>
                        <?php echo "<a href='partido?id=" . urlencode($key['id_partido']) .  "'>Leer más...</a>"; ?>

                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php include "footer.php"; ?>
</body>

</html>