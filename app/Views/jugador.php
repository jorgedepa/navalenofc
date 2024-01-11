<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/jugador.css">
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/footer.css">
    <title><?php echo $nombre ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">

</head>

<body>
    <?php include "header.php"; ?>

    <div id="contenedorJugador">
        <div id="imagen"><img src="images/jugadores/1.png" alt="Alba Redondo"></div>
        <div id="contenedorEstadisticas">
            <h1><?php echo $nombre ?></h1>
            <div id="estadisticas">
                <div>
                    <p><?php echo $partidos; ?></p>
                    <p>Partidos Jugados</p>
                </div>
                <div>
                    <p><?php echo $goles; ?></p>
                    <p>Goles</p>
                </div>
            </div>
        </div>
    </div>
    <?php include "footer.php"; ?>
</body>

</html>