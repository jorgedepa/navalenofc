<?php
if (session()->has('nombreUsuario')) {
    $nombreUsuario = session()->get('nombreUsuario');
}
$dia1 = session()->get('dia1');
$dia2 = session()->get('dia2');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Administración</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">
    <link rel='stylesheet' type='text/css' media='screen' href='styles/responsable.css'>

</head>

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
    <aside>
        <ul>
            <a href="inicioResponsable">
                <li>Inicio</li>
            </a>
            <a href="empleados">
                <li>Empleados</li>
            </a>
        </ul>
    </aside>
    <main>
        <h1>ADMINISTRACIÓN</h1>
        <div id="rangoFechas">
            <form action="rangoFechas" method="post">
                <label for="fecha_inicio">Fecha de inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo $dia1; ?>" onChange='this.form.submit();'>
                <label for="fecha_fin">Fecha de Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo $dia2; ?>" onChange="this.form.submit();">

            </form>
        </div>
        <table>
            <tr>
                <th>Empleado</th>
                <th>Horas justificadas</th>
                <th>Horas sin justificar</th>
                <th>Errores</th>
                <th>Permisos</th>
            </tr>

            <?php
            foreach ($empleadosFecha as $key) {
                echo "<tr>";
                echo "<td><a href='crud?empleado=" . urlencode($key['id_usuario']) . "&dia1=" . urlencode($dia1) . "&dia2=" . urlencode($dia2) . "'>" . $key['nombre_usuario'] . "</a></td>";
                echo "<td>" . $key['horas_justificadas'] . "</td>";
                echo "<td>" . $key['horas_no_justificadas'] . "</td>";
                echo "<td>" . $key['errores'] . "</td>";
                echo "<td>" . $key['permisos'] . "</td>";
                echo "</tr>";
            }
            ?>
        </table>


    </main>
</body>

</html>