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
    <title>Administrar noticias</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">
    <link rel='stylesheet' type='text/css' media='screen' href='styles/administrarNoticia.css'>

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
        <h1>Publicar una noticia</h1>
        <form action="actualizarNoticia" method="post" enctype="multipart/form-data">
            <label for="titular">Titular:</label>
            <input type="text" id="titular" name="titular" required>

            <label for="subtitulo">Subtítulo:</label>
            <input type="text" id="subtitulo" name="subtitulo" required>

            <label for="cuerpo">Cuerpo:</label>
            <textarea id="cuerpo" name="cuerpo" rows="4" required></textarea>

            <label for="imagen">Imagen:</label>
            <input type="file" id="imagen" name="archivo" accept="image/*" required>

            <button type="submit" name='publicar'>Publicar Noticia</button>
        </form>
        <h1>Noticias publicadas</h1>
        <table>
            <tr>
                <th>Titular</th>
                <th>Subtítulo</th>
                <th>Fecha de publicación</th>
                <th></th>
            </tr>
            <?php foreach ($noticias as $key) {
                echo "<tr>";
                // Titular
                echo "<td>";
                echo $key['titular'];
                echo "</td>";
                // Titular
                echo "<td>";
                echo $key['subtitulo'];
                echo "</td>";
                // FechaPublicacion
                echo "<td>";
                echo date('d/m/Y', strtotime($key['fecha_publicacion']));
                echo "</td>";

                // Eliminar
                echo "<td>";
                echo "<form action='actualizarNoticia' method='post'>";
                echo "<input type='hidden' name='idNoticia' value='" . $key['id'] . "'>";
                echo "<button type='submit' name='borrar' onClick='this.form.submit();'>Borrar</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }


            ?>
        </table>


    </main>
</body>

</html>