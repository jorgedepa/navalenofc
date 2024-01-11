<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Navaleno FC TV</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/video.css">
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">

</head>

<body>
    <?php include "header.php"; ?>

    <div id="contenedorIframe">
        <?php
        if (isset($usuario)) { ?>
            <iframe src="https://player.twitch.tv/?channel=navalenofc&parent=localhost" frameborder="0" allowfullscreen="true" scrolling="no" height="378" width="620"></iframe>
        <?php } else { ?>
            <h1>Inicia sesión para visualizar Navaleno FC TV</h1>
        <?php } ?>
    </div>
    <?php include "footer.php" ?>

</body>

</html>