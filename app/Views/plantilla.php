<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/plantilla.css">
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/footer.css">
    <title>Plantilla</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">
</head>

<body>
    <?php include "header.php"; ?>

    <h2>Plantilla</h2>
    <div id="contenedorPlantilla">
        <?php foreach ($plantilla as $key) { ?>
            <?php echo "<a href='jugador?id=" . urlencode($key['id_jugador']) .  "'>"; ?>
            <div class="imagenJugador">
                <?php echo "<img src=images/jugadores/" . $key['id_jugador'] . ".png>" ?>
            </div>
            <div class="nombre">
                <h4><?php echo $key['nombre'] ?></h4>
            </div>

            </a>
        <?php } ?>
    </div>

    <?php include "footer.php"; ?>
</body>

</html>