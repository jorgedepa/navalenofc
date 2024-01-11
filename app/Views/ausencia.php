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
    <title>Administrar ausencia</title>
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
        <h1>Ausencias de
            <?php echo $nombreEmpleado ?>
        </h1>
        <table>
            <tr>
                <th>Tipo</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Comentario</th>
                <th>Comentario</th>
            </tr>
            <?php
            foreach ($ausenciasEmpleado as $key) {

                echo "<tr>";
                echo "<td>";
                echo "<form action='actualizarAusencia' method='post'>";
                echo "<input type='hidden' name='DiaEspecialID' value='" . $key['DiaEspecialID'] . "'>";
                echo "<select name=\"TipoAusencia\"onChange='this.form.submit();'>";
                foreach ($tipoPermisos as $clave) {
                    echo "<option value='" . $clave['IdPermiso'] . "'";
                    if ($clave['IdPermiso'] == $key['TipoPermiso']) {
                        echo " selected";
                    }
                    echo ">" . $clave['Descripcion'] . "</option>";
                }
                echo "</select>";
                echo "</form>";
                echo "</td>";


                echo "<td>";
                echo "<form action='actualizarAusencia' method='post'>";
                echo "<input type='hidden' name='DiaEspecialID' value='" . $key['DiaEspecialID'] . "'>";
                if ($key['FechaInicio'] !== null) {
                    echo "<input type='date' name='InicioPermiso' value='" . date('Y-m-d', strtotime($key['FechaInicio'])) . "' onChange='this.form.submit();'>";
                } else {
                    echo "<input type='date' name='InicioPermiso' value=''onChange='this.form.submit();'>";
                }
                echo "</form>";
                echo "</td>";


                echo "<td>";
                echo "<form action='actualizarAusencia' method='post'>";
                echo "<input type='hidden' name='DiaEspecialID' value='" . $key['DiaEspecialID'] . "'>";
                if ($key['FechaFin'] !== null) {
                    echo "<input type='date' name='FinPermiso' value='" . date('Y-m-d', strtotime($key['FechaFin'])) . "' onChange='this.form.submit();'>";
                } else {
                    echo "<input type='date' name='FinPermiso' value=''onChange='this.form.submit();'>";
                }
                echo "</form>";
                echo "</td>";



                echo "<td>";
                echo "<form action='actualizarAusencia' method='post'>";
                echo "<input type='hidden' name='DiaEspecialID' value='" . $key['DiaEspecialID'] . "'>";
                if ($key['comentario'] !== null) {
                    echo "<input type='text' name='Comentario' value='" . $key['comentario'] . "' onChange='this.form.submit();'>";
                } else {
                    echo "<input type='text' name='Comentario' placeholder='Inserte un comentario' onChange='this.form.submit();'>";
                }

                echo "</form>";
                echo "</td>";

                echo "<td>";
                echo "<form action='actualizarAusencia' method='post'>";
                echo "<input type='hidden' name='DiaEspecialID' value='" . $key['DiaEspecialID'] . "'>";
                echo "<button type='submit' name='borrarAusencia' onClick='this.form.submit();'>Borrar</button>";
                echo "</form>";
                echo "</td>";

                echo "</tr>";
            }
            ?>
        </table>
        <!-- Sección donde se inserta una nueva ausencia -->
        <h1>Insertar nueva ausencia</h1>
        <table>
            <tr>
                <th>Tipo</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Comentario</th>
                <th>Acción</th>
            </tr>
            <tr>
                <form action="actualizarAusencia" method="post">
                    <td>
                        <select name="TipoAusenciaI" required>
                        <option disabled selected value="">Selecciona una opción</option>
                            <?php
                            foreach ($tipoPermisos as $key) {
                                echo "<option value=" . $key['IdPermiso'] . ">" . $key['Descripcion'] . "</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td><input type='date' name='InicioPermisoI'></td>
                    <td><input type='date' name='FinPermisoI'></td>
                    <td><input type='text' name='ComentarioI' placeholder='Inserte un comentario'></td>
                    <td><input type="submit" value="Añadir" name="AnadirPermiso"></td>

                </form>
            </tr>
        </table>


    </main>
</body>

</html>