<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/noticias.css">
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/footer.css">
    <title>Noticias</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">
</head>

<body>
    <?php include "header.php"; ?>

    <div id="contenedorNoticias">
        <h1>Noticias</h1>
        <div class="grid-container">
            <?php foreach ($noticias as $key) { ?>
                <div class="card">
                    <div class="card-img">
                        <?php echo "<img src=images/noticias/" . $key['id'] . ".jpg>" ?>
                    </div>
                    <div class="card-content">
                        <h3><?php echo $key['titular'] ?></h3>
                        <p><?php echo $key['subtitulo'] ?></p>
                    </div>
                    <div class="card-button">
                        <?php echo "<a href='noticia?id=" . urlencode($key['id']) .  "'>Leer más...</a>"; ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php include "footer.php"; ?>
</body>

</html>