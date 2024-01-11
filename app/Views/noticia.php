<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/noticia.css">
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">
    <title><?php echo $noticia[0]['titular']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <?php include "header.php"; ?>

    <div id="contenedorNoticia">
        <h1><?php echo $noticia[0]['titular']; ?></h1>
        <div id="contenedorImagen">
            <?php echo "<img src=images/noticias/" . $noticia[0]['id'] . ".jpg>" ?>
        </div>
        <div id="fecha"><?php echo date('d/m/Y', strtotime($noticia[0]['fecha_publicacion'])); ?></div>
        <div id="subtitulo">
            <h3><?php echo $noticia[0]['subtitulo']; ?></h3>
        </div>
        <div id="cuerpo"><?php echo nl2br($noticia[0]['cuerpo']); ?></div>
    </div>
    <?php include "footer.php" ?>
</body>

</html>