<header>
    <div id="logo">
        <img alt="" src="images/logo.png">
        <a href="<?= base_url('/') ?>">
            <h2>Navaleno Fútbol Club</h2>
        </a>
    </div>
    <?php
    if (session()->has('nombreUsuario')) {
        $nombreUsuario = session()->get('nombreUsuario');
    }
    if (session()->has('usuario')) {
        $usuario = session()->get('usuario');
    }

    if (isset($nombreUsuario)) {

        echo "<div id=inicioSesion>";
        echo "<span>" . $nombreUsuario . "</span>";
        echo "<a href=cerrar>";
        echo "<button>Cerrar Sesión</button>";
        echo "</a>";
        echo "</div>";
    } else {
        echo "<div id=inicioSesion>";
        echo "<a href=login>";
        echo "<button>Iniciar Sesión</button>";
        echo "</a>";
        echo "</div>";
    }
    ?>
</header>
<nav>
    <ul>
        <a href="noticias">
            <li>Noticias</li>
        </a>
        <a href="partidos">
            <li>Partidos</li>
        </a>
        <a href="clasificacion">
            <li>Clasificación</li>
        </a>
        <a href="plantilla">
            <li>Plantilla</li>
        </a>
        <a href="video">
            <li>Navaleno FC TV</li>
        </a>

    </ul>
</nav>