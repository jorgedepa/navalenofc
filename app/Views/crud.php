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
    <title>Modificar empleados</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">
    <link rel='stylesheet' type='text/css' media='screen' href='styles/crud.css'>

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
            <a href="ausencia">
                <li>Ausencias</li>
            </a>
        </ul>
    </aside>
    <main>
        <h1>MODIFICAR REGISTROS</h1>
        <div id="rangoFechas">
            <form action="rangoFechasCrud" method="post">
                <label for="fecha_inicio">Fecha de inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo $dia1; ?>" onChange='this.form.submit();'>
                <label for="fecha_fin">Fecha de Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo $dia2; ?>" onChange="this.form.submit();">

            </form>
        </div>
        <table>
            <tr>
                <th>FichajeID</th>
                <th>EmpleadoID</th>
                <th>FechaEntrada</th>
                <th>FechaSalida</th>
                <th>InicioPausa</th>
                <th>FinPausa</th>
                <th>TipoSalida</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
            <?php
            foreach ($fichajesDia as $key) {

                echo "<tr>";
                echo "<td>" . $key['FichajeID'] . "</td>";
                echo "<td>" . $key['id_usuario'] . "</td>";

                // Fecha entrada
                echo "<td>";
                echo "<form action='actualizar' method='post'>";
                echo "<input type='hidden' name='FichajeID' value='" . $key['FichajeID'] . "'>";
                if ($key['FechaEntrada'] !== null) {
                    echo "<input type='datetime-local' name='fechaEntrada' value='" . date('Y-m-d\TH:i:s', strtotime($key['FechaEntrada'])) . "' onChange='this.form.submit();' step='1'>";
                } else {
                    echo "<input type='datetime-local' name='fechaEntrada' value=''onChange='this.form.submit();' step='1'>";
                }
                echo "</form>";
                echo "</td>";

                // Fecha salida
                echo "<td>";
                echo "<form action='actualizar' method='post'>";
                echo "<input type='hidden' name='FichajeID' value='" . $key['FichajeID'] . "'>";
                if ($key['FechaSalida'] !== null) {
                    echo "<input type='datetime-local' name='fechaSalida' value='" . date('Y-m-d\TH:i:s', strtotime($key['FechaSalida'])) . "' onChange='this.form.submit();' step='1'>";
                } else {
                    echo "<input type='datetime-local' name='fechaSalida' value=''onChange='this.form.submit();' step='1'>";
                }
                echo "</form>";
                echo "</td>";

                // Inicio pausa
                echo "<td>";
                echo "<form action='actualizar' method='post'>";
                echo "<input type='hidden' name='FichajeID' value='" . $key['FichajeID'] . "'>";
                if ($key['InicioPausa'] !== null) {
                    echo "<input type='datetime-local' name='inicioPausa' value='" . date('Y-m-d\TH:i:s', strtotime($key['InicioPausa'])) . "' onChange='this.form.submit();' step='1'>";
                } else {
                    echo "<input type='datetime-local' name='inicioPausa' value=''onChange='this.form.submit();' step='1'>";
                }
                echo "</form>";
                echo "</td>";

                // Fin pausa
                echo "<td>";
                echo "<form action='actualizar' method='post'>";
                echo "<input type='hidden' name='FichajeID' value='" . $key['FichajeID'] . "'>";
                if ($key['FinPausa'] !== null) {
                    echo "<input type='datetime-local' name='finPausa' value='" . date('Y-m-d\TH:i:s', strtotime($key['FinPausa'])) . "' onChange='this.form.submit();' step='1'>";
                } else {
                    echo "<input type='datetime-local' name='finPausa' value=''onChange='this.form.submit();' step='1'>";
                }
                echo "</form>";
                echo "</td>";

                // Tipo salida
                echo "<td>";
                echo "<form action='actualizar' method='post'>";
                echo "<input type='hidden' name='FichajeID' value='" . $key['FichajeID'] . "'>";
                echo "<select name=\"salida\"onChange='this.form.submit();'>";
                if ($key['TipoSalida'] == 1) {
                    echo "<option value=1 selected>Salida normal</option>";
                    echo "<option value=2>No se fichó salida descanso</option>";
                    echo "<option value=3>Falta fichar salida</option>";
                } elseif ($key['TipoSalida'] == 2) {
                    echo "<option value=1>Salida normal</option>";
                    echo "<option value=2 selected>No se fichó salida descanso</option>";
                    echo "<option value=3>Falta fichar salida</option>";
                } elseif ($key['TipoSalida'] == 3) {
                    echo "<option value=1>Salida normal</option>";
                    echo "<option value=2>No se fichó salida descanso</option>";
                    echo "<option value=3 selected>Falta fichar salida</option>";
                } else {
                    echo "<option value='' disabled selected>Selecciona una opción</option>";
                    echo "<option value=1>Salida normal</option>";
                    echo "<option value=2>No se fichó salida descanso</option>";
                    echo "<option value=3>Falta fichar salida</option>";
                }
                echo "</select>";
                echo "</form>";
                echo "</td>";


                // Estado
                echo "<td>";
                echo "<form action='actualizar' method='post'>";
                echo "<input type='hidden' name='FichajeID' value='" . $key['FichajeID'] . "'>";
                echo "<select name=\"justificacion\"onChange='this.form.submit();'>";
                if ($key['Estado'] == "justificado") {
                    echo "<option value=justificado selected>justificado</option>";
                    echo "<option value='no justificado'>no justificado</option>";
                } elseif ($key['Estado'] == "no justificado") {
                    echo "<option value=justificado>justificado</option>";
                    echo "<option value='no justificado' selected>no justificado</option>";
                } else {
                    echo "<option value='' disabled selected>Selecciona una opción</option>";
                    echo "<option value=justificado>justificado</option>";
                    echo "<option value='no justificado'>no justificado</option>";
                }
                echo "</select>";
                echo "</form>";
                echo "</td>";

                // Borrar
                echo "<td>";
                echo "<form action='actualizar' method='post'>";
                echo "<input type='hidden' name='FichajeID' value='" . $key['FichajeID'] . "'>";
                echo "<button type='submit' name='borrar' onClick='this.form.submit();'>Borrar</button>";
                echo "</form>";
                echo "</td>";

                echo "</tr>";
            }


            ?>


        </table>
        <!-- Sección para insertar un nuevo registro -->
        <h1>INSERTAR REGISTRO</h1>
        <table>
            <tr>
                <th>FechaEntrada</th>
                <th>FechaSalida</th>
                <th>InicioPausa</th>
                <th>FinPausa</th>
                <th>TipoSalida</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
            <tr>
                <?php
                echo "<form action='actualizar' method='post'>";

                echo "<td>";
                echo "<input type='datetime-local' name='fechaEntradaI' step='1'>";
                echo "</td>";

                echo "<td>";
                echo "<input type='datetime-local' name='fechaSalidaI' step='1'>";
                echo "</td>";

                echo "<td>";
                echo "<input type='datetime-local' name='inicioPausaI' step='1'>";
                echo "</td>";

                echo "<td>";
                echo "<input type='datetime-local' name='finPausaI' step='1'>";
                echo "</td>";

                echo "<td>";
                echo "<select name=\"salidaI\">";
                echo "<option value='' disabled selected>Selecciona una opción</option>";
                echo "<option value=1>Salida normal</option>";
                echo "<option value=2>No se fichó salida descanso</option>";
                echo "<option value=3>Falta fichar salida</option>";
                echo "</select>";
                echo "</td>";

                echo "<td>";
                echo "<select name=\"justificacionI\">";
                echo "<option disabled selected>Selecciona una opción</option>";
                echo "<option value=justificado>justificado</option>";
                echo "<option value='no justificado'>no justificado</option>";

                echo "</select>";
                echo "</td>";


                echo "<td>";
                echo "<input type=submit name=enviarI value=Insertar>";
                echo "</td>";
                echo "</form>";


                ?>
            </tr>
        </table>

    </main>
</body>

</html>