<?php
if (session()->has('nombreUsuario')) {
    $nombreUsuario = session()->get('nombreUsuario');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control horario</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel='stylesheet' type='text/css' media='screen' href='styles/trabajador.css'>
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">
    <script src="js/funciones.js" defer></script>
</head>

<body>

    <body>
        <header>
            <div id="logo">
                <img alt="" src="images/logo.png">
                <a href="entrar">
                    <h2>Navaleno Fútbol Club</h2>
                </a>
            </div>
            <div id=inicioSesion>
                <span><?php echo $nombreUsuario ?></span>
                <a href=cerrar><button>Cerrar Sesión</button></a>
            </div>
        </header>

        <main>
            <div id="diasSemana">
                <span>LU</span> <span>MA</span> <span>MI</span>
                <span>JU</span><span>VI</span><span>SA</span> <span>DO</span>
            </div>
            <div id="reloj"></div>
            <div id="fecha">
                <?php echo strtoupper(date("d M Y")); ?>
            </div>

            <?php
            if ($queMostrar == "findesemana") {
                echo "<div class='resumen'>Es fin de semana, la jornada empieza el lunes</div>";
            } elseif ($queMostrar == "permiso") {
                echo "<div class='resumen'>Hoy tienes asignado un permiso, no debes estar en la empresa</div>";
            } elseif ($queMostrar == "BotonEntrada") { ?>
                <!-- Mostrar botón de entrada -->
                <div id="contenedorBotones">
                    <form action="fichaje" method="post">
                        <input type="submit" name="entradaFichaje" value="Fichar entrada">
                    </form>
                </div>
            <?php } elseif ($queMostrar == "BotonSalida") { ?>
                <!-- Mostrar botón de salida -->
                <div id="contenedorBotones">
                    <form action="fichaje" method="post">
                        <input type="submit" name="salidaFichaje" value="Fichar salida">
                    </form>
                </div>
            <?php } elseif ($queMostrar == "BotonInicioPausaBotonSalida") { ?>
                <!-- Mostrar botón de inicio de pausa y salida -->
                <div id="contenedorBotones">
                    <form action="fichaje" method="post">
                        <input type="submit" name="inicioPausa" value="Inicio de Pausa">
                        <input type="submit" name="salidaFichaje" value="Fichar salida">
                    </form>
                </div>
            <?php } elseif ($queMostrar == "BotonFinPausa") { ?>
                <!-- Mostrar botón de fin de pausa -->
                <div id="contenedorBotones">
                    <form action="fichaje" method="post">
                        <input type="submit" name="finPausa" value="Fin de pausa">
                    </form>
                </div>
            <?php } ?>
            <div class="resumen">
                <?php echo $horas; ?>
            </div>
        </main>
    </body>

</html>