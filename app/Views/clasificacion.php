<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/clasificacion.css">
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/footer.css">
    <title>Clasificación</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">
</head>

<body>
    <?php include "header.php"; ?>
    <h1>Clasificación</h1>
    <table>
        <thead>
            <tr>
                <th>Posición</th>
                <th></th>
                <th>Equipo</th>
                <th>Puntos</th>
                <th>PJ</th>
                <th>PG</th>
                <th>PE</th>
                <th>PP</th>
                <th>GF</th>
                <th>GC</th>
                <th>DG</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $pos = 1;
            foreach ($clasificacion as $equipo) {
                echo "<tr>";
                echo "<td>" . $pos . "</td>";
                echo "<td><img src=images/equipos/" . $equipo['equipo_id'] . ".png></td>";
                echo "<td>" . $equipo['nombre'] . "</td>";
                echo "<td>" . $equipo['puntos'] . "</td>";
                echo "<td>" . $equipo['PJ'] . "</td>";
                echo "<td>" . $equipo['PG'] . "</td>";
                echo "<td>" . $equipo['PE'] . "</td>";
                echo "<td>" . $equipo['PP'] . "</td>";
                echo "<td>" . $equipo['GF'] . "</td>";
                echo "<td>" . $equipo['GC'] . "</td>";
                echo "<td>" . $equipo['DG'] . "</td>";
                echo "</tr>";
                $pos++;
            }
            ?>
        </tbody>
    </table>
    <?php include "footer.php"; ?>
</body>

</html>