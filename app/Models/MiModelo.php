<?php

namespace App\Models;

use CodeIgniter\Model;

class MiModelo extends Model
{
    function comprueba($correo, $contrasena)
    {
        $orden = "SELECT id_usuario FROM usuarios WHERE correo_electronico='" . $correo . "' AND contrasena='" . $contrasena . "'";
        $resultado = $this->db->query($orden);
        $fila = $resultado->getRow();
        if (isset($fila)) {
            return $fila->id_usuario;
        } else return 0;
    }
    function crearUsuario($correo, $contrasena, $nombreUsuario)
    {
        $orden = "INSERT INTO usuarios (correo_electronico, nombre_usuario, contrasena, rol) VALUES ('$correo','$nombreUsuario', '$contrasena', 'usuario');";
        $this->db->query($orden);
    }

    function dimeNombreUsuario($idUsuario)
    {
        $orden = "SELECT nombre_usuario FROM usuarios WHERE id_usuario=" . $idUsuario . ";";
        $resultado = $this->db->query($orden);
        $fila = $resultado->getRow();
        if (isset($fila)) {
            return $fila->nombre_usuario;
        } else return 0;
    }
    // Devuelve el rol de quien se le pide (U/B)
    function queEs($usuario)
    {
        $orden = "SELECT rol FROM usuarios WHERE id_usuario = '" . $usuario . "';";
        $resultado = $this->db->query($orden);
        $fila = $resultado->getRow();
        return $fila->rol;
    }
    function dameNoticia($id)
    {
        $orden = "SELECT * FROM noticias WHERE id = " . $id . " ORDER BY fecha_publicacion DESC;";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    function dameNoticias()
    {
        $orden = "SELECT * FROM noticias ORDER BY fecha_publicacion DESC;";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }

    function dameTresNoticias()
    {
        $orden = "SELECT * FROM noticias ORDER BY fecha_publicacion DESC LIMIT 3;";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }

    function dameSeisNoticias()
    {
        $orden = "SELECT * FROM noticias ORDER BY fecha_publicacion DESC LIMIT 6 OFFSET 3;";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    function damePartidoAnterior()
    {
        $orden = "SELECT e.*, dime_nombre_equipo(e.equipo_local_id) AS nombre_local, dime_nombre_equipo(e.equipo_visitante_id) AS nombre_visitante, eq.estadio FROM enfrentamientos e INNER JOIN equipos eq ON e.equipo_local_id = eq.equipo_id WHERE DATE(e.fecha_juego) < DATE(NOW()) AND (e.equipo_local_id = 1 OR e.equipo_visitante_id = 1) ORDER BY e.fecha_juego DESC LIMIT 1;";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    function dameProximoPartido()
    {
        $orden = "SELECT e.*, dime_nombre_equipo(e.equipo_local_id) AS nombre_local, dime_nombre_equipo(e.equipo_visitante_id) AS nombre_visitante, eq.estadio FROM enfrentamientos e INNER JOIN equipos eq ON e.equipo_local_id = eq.equipo_id WHERE DATE(e.fecha_juego) >= DATE(NOW()) AND (e.equipo_local_id = 1 OR e.equipo_visitante_id = 1) ORDER BY e.fecha_juego ASC LIMIT 1;";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }

    function dameSiguientePartido()
    {
        $orden = "SELECT e.*, dime_nombre_equipo(e.equipo_local_id) AS nombre_local, dime_nombre_equipo(e.equipo_visitante_id) AS nombre_visitante, eq.estadio FROM enfrentamientos e INNER JOIN equipos eq ON e.equipo_local_id = eq.equipo_id WHERE DATE(e.fecha_juego) >= DATE(NOW()) AND (e.equipo_local_id = 1 OR e.equipo_visitante_id = 1) ORDER BY e.fecha_juego ASC LIMIT 1, 1;";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }

    function clasificacion()
    {
        $orden = "SELECT equipo_id, nombre_equipo AS nombre, puntos(equipo_id) AS puntos,partidos_jugados(equipo_id) AS PJ, partidos_ganados(equipo_id) AS PG, partidos_empatados(equipo_id) AS PE, partidos_perdidos(equipo_id) AS PP, goles_a_favor(equipo_id) AS GF, goles_en_contra(equipo_id) AS GC, goles_a_favor(equipo_id) - goles_en_contra(equipo_id) AS DG FROM equipos ORDER by puntos DESC;";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    function calendarioMes($mes)
    {
        $orden = "SET lc_time_names = 'es_ES';";
        $this->db->query($orden);
        $orden = "SELECT e.id_partido, e.jornada, e.equipo_local_id, dime_nombre_equipo(e.equipo_local_id) AS equipo_local, e.equipo_visitante_id, dime_nombre_equipo(e.equipo_visitante_id) AS equipo_visitante, DATE(e.fecha_juego) AS fecha,DATE_FORMAT(e.fecha_juego, '%W, %e %b, %H:%i h') AS fecha_juego, eq.estadio, e.estado, goles_equipo_local(e.id_partido) AS golesEquipoLocal, goles_equipo_visitante(e.id_partido) AS golesEquipoVisitante  FROM enfrentamientos e INNER JOIN equipos eq on e.equipo_local_id = eq.equipo_id WHERE MONTH(e.fecha_juego) = " . $mes .  " ORDER BY e.fecha_juego ASC;";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    function equipoLocal($idPartido)
    {
        $orden = "SELECT a.id_jugador, j.nombre AS nombre_jugador, a.titular FROM alineacion a INNER JOIN jugadores j ON a.id_jugador = j.id_jugador INNER JOIN enfrentamientos e ON a.id_partido = e.id_partido WHERE a.id_partido = " . $idPartido . " AND j.equipo_id = e.equipo_local_id;";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    function nombreEquipoLocal($idPartido)
    {
        $orden = "SELECT equipos.nombre_equipo, enfrentamientos.equipo_local_id FROM enfrentamientos INNER JOIN equipos ON enfrentamientos.equipo_local_id = equipos.equipo_id WHERE enfrentamientos.id_partido = " . $idPartido . ";";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    function dimeIdEquipoLocal($idPartido)
    {
        $orden = "SELECT equipo_local_id FROM enfrentamientos WHERE id_partido = " . $idPartido . ";";
        $resultado = $this->db->query($orden);
        $fila = $resultado->getRow();
        return $fila->equipo_local_id;
    }
    function equipoVisitante($idPartido)
    {
        $orden = "SELECT a.id_jugador, j.nombre AS nombre_jugador, a.titular FROM alineacion a INNER JOIN jugadores j ON a.id_jugador = j.id_jugador INNER JOIN enfrentamientos e ON a.id_partido = e.id_partido WHERE a.id_partido = " . $idPartido . " AND j.equipo_id = e.equipo_visitante_id;";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    function nombreEquipoVisitante($idPartido)
    {
        $orden = "SELECT equipos.nombre_equipo, enfrentamientos.equipo_visitante_id  FROM enfrentamientos INNER JOIN equipos ON enfrentamientos.equipo_visitante_id = equipos.equipo_id WHERE enfrentamientos.id_partido = " . $idPartido . ";";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    function dimeIdEquipoVisitante($idPartido)
    {
        $orden = "SELECT equipo_visitante_id FROM enfrentamientos WHERE id_partido = " . $idPartido . ";";
        $resultado = $this->db->query($orden);
        $fila = $resultado->getRow();
        return $fila->equipo_visitante_id;
    }

    function estadoPartido($idPartido)
    {
        $orden = "SELECT estado FROM enfrentamientos WHERE id_partido = " . $idPartido . ";";
        $resultado = $this->db->query($orden);
        $fila = $resultado->getRow();
        return $fila->estado;
    }
    function golesEquipoLocal($idPartido)
    {
        $orden = "SELECT COUNT(*) AS goles FROM goles g INNER JOIN enfrentamientos e ON g.id_equipo = e.equipo_local_id WHERE g.id_partido = " . $idPartido . " AND e.id_partido = " . $idPartido . ";";
        $resultado = $this->db->query($orden);
        $fila = $resultado->getRow();
        return $fila->goles;
    }
    function golesEquipoVisitante($idPartido)
    {
        $orden = "SELECT COUNT(*) AS goles FROM goles g INNER JOIN enfrentamientos e ON g.id_equipo = e.equipo_visitante_id WHERE g.id_partido = " . $idPartido . " AND e.id_partido = " . $idPartido . ";";
        $resultado = $this->db->query($orden);
        $fila = $resultado->getRow();
        return $fila->goles;
    }

    function secuenciaGoles($idPartido)
    {
        $orden = "SELECT CONCAT(g.minuto, \"'\") AS minuto, j.nombre FROM goles g INNER JOIN jugadores j ON g.id_jugador = j.id_jugador WHERE id_partido = " . $idPartido . " ORDER BY minuto ASC;";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    function plantilla()
    {
        $orden = "SELECT id_jugador, nombre FROM jugadores WHERE equipo_id = 1 ORDER BY nombre ASC;";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    function golesJugador($idJugador)
    {
        $orden = "SELECT COUNT(*) AS goles FROM goles WHERE id_jugador = " . $idJugador . ";";
        $resultado = $this->db->query($orden);
        $fila = $resultado->getRow();
        return $fila->goles;
    }
    function partidosJugadosJugador($idJugador)
    {
        $orden = "SELECT COUNT(*) AS partidos FROM alineacion WHERE id_jugador = " . $idJugador . " AND titular = 1;";
        $resultado = $this->db->query($orden);
        $fila = $resultado->getRow();
        return $fila->partidos;
    }
    function nombreJugador($idJugador)
    {
        $orden = "SELECT nombre FROM jugadores WHERE id_jugador = " . $idJugador . ";";
        $resultado = $this->db->query($orden);
        $fila = $resultado->getRow();
        return $fila->nombre;
    }

    // PARTE DE FICHAJES
    public function estaDePermiso($idUsuario)
    {
        $orden = "SELECT COUNT(*) AS permisos FROM permisos WHERE id_usuario = " . $idUsuario . " AND DATE(NOW()) BETWEEN FechaInicio AND FechaFin;";
        $this->db->query($orden);
        $resultado = $this->db->query($orden);
        $fila = $resultado->getRow();
        if ($fila->permisos > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function mostrarBotonEntrar($idUsuario)
    {
        $orden = "SELECT mostrarBotonEntrar(" . $idUsuario . ") AS boton;";
        $resultado = $this->db->query($orden);
        $fila = $resultado->getRow();
        return $fila->boton;
    }

    public function opcionesSalida()
    {
        $orden = "SELECT tipoId, tipo FROM tipossalidas;";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }

    public function verHoras($idUsuario)
    {
        $orden = "SELECT calcular_horas_semana_justificadas(" . $idUsuario . ") AS justificadas, calcular_horas_semana_no_justificadas(" . $idUsuario . ") AS no_justificadas;";
        $resultado = $this->db->query($orden);
        $fila = $resultado->getRow();
        return "Esta semana llevas " . $fila->justificadas . " horas justificadas y " . $fila->no_justificadas . " horas no justificadas";
    }

    public function insertarEntrada($idUsuario)
    {
        $orden = "INSERT INTO fichaje (FichajeID, id_usuario, FechaEntrada, FechaSalida, InicioPausa, FinPausa, TipoSalida, Estado) VALUES (NULL, " . $idUsuario . ", NOW(), NULL, NULL, NULL, NULL, NULL);";
        $this->db->query($orden);
    }
    public function insertarSalida($idUsuario)
    {
        $orden = "UPDATE fichaje SET FechaSalida = NOW(), TipoSalida = 1, Estado = 'justificado'" .
            " WHERE FichajeID = (SELECT FichajeID FROM fichaje WHERE id_usuario = " . $idUsuario . " AND DATE(FechaEntrada) = DATE(CURRENT_DATE) ORDER BY FechaEntrada DESC LIMIT 1);";
        $this->db->query($orden);
    }
    public function insertarInicioPausa($idUsuario)
    {
        $orden = "UPDATE fichaje SET InicioPausa = NOW()
        WHERE FichajeID = (SELECT FichajeID FROM fichaje WHERE id_usuario = " . $idUsuario . " AND DATE(FechaEntrada) = DATE(CURRENT_DATE) AND FechaSalida IS NULL AND FechaEntrada IS NOT NULL);";
        $this->db->query($orden);
    }
    public function insertarFinPausa($idUsuario)
    {
        $orden = "CALL GestionFinPausa(" . $idUsuario . ");";
        $this->db->query($orden);
    }

    // Parte de administracion de los empleados
    public function empleados()
    {
        $orden = "SELECT * FROM usuarios WHERE rol IN ('trabajador', 'responsable');";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    public function actualizarCorreo($idUsuario, $correo)
    {
        $orden = "UPDATE usuarios SET correo_electronico = '" . $correo . "' WHERE id_usuario = " . $idUsuario . ";";
        $this->db->query($orden);
    }
    public function actualizarUsuario($idUsuario, $usuario)
    {
        $orden = "UPDATE usuarios SET nombre_usuario = '" . $usuario . "' WHERE id_usuario = " . $idUsuario . ";";
        $this->db->query($orden);
    }
    public function actualizarContrasena($idUsuario, $contrasena)
    {
        $orden = "UPDATE usuarios SET contrasena = '" . $contrasena . "' WHERE id_usuario = " . $idUsuario . ";";
        $this->db->query($orden);
    }
    public function actualizarRol($idUsuario, $rol)
    {
        $orden = "UPDATE usuarios SET rol = '" . $rol . "' WHERE id_usuario = " . $idUsuario . ";";
        $this->db->query($orden);
    }
    public function borrarRegistroEmpleado($idUsuario)
    {
        $orden = "DELETE FROM usuarios WHERE id_usuario = " . $idUsuario . ";";
        $this->db->query($orden);
    }
    public function insertarRegistroEmpleado($correo, $usuario, $contrasena, $rol)
    {
        $orden = "INSERT INTO usuarios (correo_electronico, nombre_usuario, contrasena, rol) VALUES
        ('" . $correo . "', '" . $usuario . "','" . $contrasena . "','" . $rol . "')";
        $this->db->query($orden);
    }
    // Parte de administración de fichajes
    public function empleadosFecha($dia1, $dia2)
    {
        $orden = "SELECT u.id_usuario, u.nombre_usuario, calcular_horas_justificadas(u.id_usuario, '" . $dia1 . "', '" . $dia2 . "') AS horas_justificadas, calcular_horas_no_justificadas(u.id_usuario, '" . $dia1 . "', '" . $dia2 . "') AS horas_no_justificadas, erroresFichajes(u.id_usuario, '" . $dia1 . "', '" . $dia2 . "') AS errores, obtenerPermisos(u.id_usuario, '" . $dia1 . "', '" . $dia2 . "') AS permisos FROM usuarios u INNER JOIN fichaje f ON u.id_usuario = f.id_usuario WHERE u.rol = 'trabajador';";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    public function fichajesDia($idUsuario, $dia1, $dia2)
    {
        $orden = "SELECT * FROM fichaje WHERE id_usuario = " . $idUsuario . " AND DATE(FechaEntrada) BETWEEN '" . $dia1 . "' AND '" . $dia2 . "';";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    // Parte de crud
    public function actualizarFechaEntrada($fichajeId, $fechaEntrada)
    {
        $orden = "UPDATE fichaje SET FechaEntrada = '" . $fechaEntrada . "' WHERE FichajeID = " . $fichajeId . ";";
        $this->db->query($orden);
    }
    public function actualizarFechaSalida($fichajeId, $fechaSalida)
    {
        $orden = "UPDATE fichaje SET FechaSalida = '" . $fechaSalida . "' WHERE FichajeID = " . $fichajeId . ";";
        $this->db->query($orden);
    }
    public function actualizarInicioPausa($fichajeId, $inicioPausa)
    {
        $orden = "UPDATE fichaje SET InicioPausa = '" . $inicioPausa . "' WHERE FichajeID = " . $fichajeId . ";";
        $this->db->query($orden);
    }
    public function actualizarFinPausa($fichajeId, $finPausa)
    {
        $orden = "UPDATE fichaje SET FinPausa = '" . $finPausa . "' WHERE FichajeID = " . $fichajeId . ";";
        $this->db->query($orden);
    }
    public function actualizarTipoSalida($fichajeId, $salida)
    {
        $orden = "UPDATE fichaje SET TipoSalida = " . $salida . " WHERE FichajeID = " . $fichajeId . ";";
        $this->db->query($orden);
    }
    public function actualizarJustificacion($fichajeId, $justificacion)
    {
        $orden = "UPDATE fichaje SET Estado = '" . $justificacion . "' WHERE FichajeID = " . $fichajeId . ";";
        $this->db->query($orden);
    }
    public function borrarRegistro($fichajeId)
    {
        $orden = "DELETE FROM fichaje WHERE FichajeID = " . $fichajeId . ";";
        $this->db->query($orden);
    }
    public function insertarRegistro($empleado, $fechaEntrada, $fechaSalida, $inicioPausa, $finPausa, $salida, $justificacion)
    {
        $orden = "INSERT INTO fichaje (id_usuario, FechaEntrada, FechaSalida, InicioPausa, FinPausa, TipoSalida, Estado) VALUES
        (" . $empleado . ", " . $fechaEntrada . "," . $fechaSalida . "," . $inicioPausa . ", " . $finPausa . "," . $salida . "," . $justificacion . ")";
        $this->db->query($orden);
    }

    // Ausencia.php
    public function obtenerTipoPermisos()
    {
        $orden = "SELECT * FROM tipospermisos";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    function ausenciasEmpleado($idUsuario)
    {
        $orden = "SELECT * FROM permisos WHERE id_usuario = " . $idUsuario . ";";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    public function actualizarTipoAusencia($diaEspecialID, $tipoAusencia)
    {
        $orden = "UPDATE permisos SET TipoPermiso = '" . $tipoAusencia . "' WHERE DiaEspecialId = " . $diaEspecialID . ";";
        $this->db->query($orden);
    }

    public function actualizarInicioAusencia($diaEspecialID, $inicioPermiso)
    {
        $orden = "UPDATE permisos SET FechaInicio = '" . $inicioPermiso . "' WHERE DiaEspecialId = " . $diaEspecialID . ";";
        $this->db->query($orden);
    }

    public function actualizarFinAusencia($diaEspecialID, $finPermiso)
    {
        $orden = "UPDATE permisos SET FechaFin = '" . $finPermiso . "' WHERE DiaEspecialId = " . $diaEspecialID . ";";
        $this->db->query($orden);
    }

    public function actualizarComentario($diaEspecialID, $comentario)
    {
        $orden = "UPDATE permisos SET comentario = '" . $comentario . "' WHERE DiaEspecialId = " . $diaEspecialID . ";";
        $this->db->query($orden);
    }

    public function borrarAusencia($diaEspecialID)
    {
        $orden = "DELETE FROM permisos WHERE DiaEspecialID = " . $diaEspecialID;
        $this->db->query($orden);
    }
    public function insertarRegistroAusencia($TipoPermiso, $fechaEntrada, $fechaSalida, $idUsuario, $comentario)
    {
        $orden = "INSERT INTO permisos (TipoPermiso, FechaInicio, FechaFin, id_usuario, comentario) VALUES
    (" . $TipoPermiso . ", " . $fechaEntrada . "," . $fechaSalida . "," . $idUsuario . ", " . $comentario . ")";
        $this->db->query($orden);
    }

    // PARTE ADMINISTRACIÓN
    public function admin()
    {
        $orden = "SELECT * FROM usuarios WHERE rol = 'administrador'";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    public function usuarios()
    {
        $orden = "SELECT * FROM usuarios WHERE rol = 'usuario'";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }

    // NOTICIAS
    public function dimeNumeroNoticia()
    {
        $orden = "SELECT MAX(id) + 1 AS numero FROM noticias;";
        $resultado = $this->db->query($orden);
        $fila = $resultado->getRow();
        if (isset($fila)) {
            return $fila->numero;
        } else return 0;
    }
    public function insertarNoticia($id, $titular, $subtitulo, $cuerpo)
    {
        $orden = "INSERT INTO noticias (id ,titular, subtitulo, cuerpo, fecha_publicacion) VALUES ($id, '$titular','$subtitulo', '$cuerpo', DATE(NOW()));";
        $this->db->query($orden);
    }
    public function borrarNoticia($id)
    {
        $orden = "DELETE FROM noticias WHERE id = " . $id . ";";
        $this->db->query($orden);
    }
    public function dameEquipos()
    {
        $orden = "SELECT * FROM equipos";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    public function borrarEquipo($id)
    {
        $orden = "DELETE FROM equipos WHERE equipo_id = " . $id . ";";
        $this->db->query($orden);
    }
    public function insertarEquipo($nombre, $estadio)
    {
        $orden = "INSERT INTO equipos (nombre_equipo, estadio) VALUES ('$nombre', '$estadio');";
        $this->db->query($orden);
    }
    public function dimeInfoEquipo($id)
    {
        $orden = "SELECT * FROM equipos WHERE equipo_id = $id;";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    public function dimeJugadores($idEquipo)
    {
        $orden = "SELECT * FROM jugadores WHERE equipo_id = $idEquipo;";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    public function actualizarNombreEquipo($idEquipo, $nombre)
    {
        $orden = "UPDATE equipos SET nombre_equipo = '" . $nombre . "' WHERE equipo_id = " . $idEquipo . ";";
        $this->db->query($orden);
    }
    public function actualizarNombreEstadio($idEquipo, $nombre)
    {
        $orden = "UPDATE equipos SET estadio = '" . $nombre . "' WHERE equipo_id = " . $idEquipo . ";";
        $this->db->query($orden);
    }
    public function actualizarNombreJugador($idjugador, $idEquipo, $nombre)
    {
        $orden = "UPDATE jugadores SET nombre = '" . $nombre . "' WHERE id_jugador = " . $idjugador . " AND equipo_id =" . $idEquipo .  ";";
        $this->db->query($orden);
    }
    public function borrarJugador($id, $idEquipo)
    {
        $orden = "DELETE FROM jugadores WHERE id_jugador = " . $id . " AND equipo_id =" . $idEquipo .  ";";
        $this->db->query($orden);
    }
    public function insertarJugador($idEquipo, $nombre)
    {
        $orden = "SELECT MAX(id_jugador) + 1 AS num FROM jugadores;";
        $resultado = $this->db->query($orden);
        $fila = $resultado->getRow();
        $num = $fila->num;
        $orden = "INSERT INTO jugadores (id_jugador, equipo_id, nombre) VALUES ($num, $idEquipo, '$nombre');";
        $this->db->query($orden);
    }
    public function dimePartidos()
    {
        $orden = "SELECT id_partido, jornada, dime_nombre_equipo(equipo_local_id) AS equipo_local, dime_nombre_equipo(equipo_visitante_id) AS equipo_visitante, fecha_juego, estado, resultado FROM enfrentamientos ORDER BY jornada ASC;";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    public function dimePartido($idPartido)
    {
        $orden = "SELECT *, dime_nombre_equipo(equipo_local_id) AS equipo_local, dime_nombre_equipo(equipo_visitante_id) AS equipo_visitante FROM enfrentamientos WHERE id_partido =" . $idPartido  . ";";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    public function realizarCambio($idPartido, $jugadorTitular, $jugadorSuplente)
    {
        $orden = "UPDATE alineacion SET titular = 0 WHERE id_partido=" .  $idPartido . " AND id_jugador = " . $jugadorTitular .  ";";
        $this->db->query($orden);
        $orden = "UPDATE alineacion SET titular = 1 WHERE id_partido=" .  $idPartido . " AND id_jugador = " . $jugadorSuplente .  ";";
        $this->db->query($orden);
    }
    public function insertarAlineacion($idPartido, $idJugador)
    {
        $orden = "INSERT INTO alineacion (id_partido, id_jugador, titular) VALUES ($idPartido, $idJugador, 1)";
        $this->db->query($orden);
    }
    public function insertarAlineacionSuplentes($idPartido, $idEquipo, $jugadoresTitulares)
    {
        $cadena = "(" . implode(", ", $jugadoresTitulares) . ")";
        $orden = "SELECT id_jugador FROM jugadores WHERE equipo_id =" . $idEquipo . " AND id_jugador NOT IN " . $cadena . ";";
        $resultado = $this->db->query($orden);
        $jugadoresSupplentes = $resultado->getResultArray();
        foreach ($jugadoresSupplentes as $key) {
            $orden = "INSERT INTO alineacion (id_partido, id_jugador, titular) VALUES ($idPartido, " . $key['id_jugador'] . ", 0);";
            $this->db->query($orden);
        }
    }
    public function insertarGol($idPartido, $idJugador, $idEquipo, $minuto)
    {
        $orden = "INSERT INTO goles (id_partido, id_jugador, id_equipo, minuto) VALUES ($idPartido, $idJugador, $idEquipo, $minuto)";
        $this->db->query($orden);
    }
    public function finalizarPartido($idPartido)
    {
        $orden = "UPDATE enfrentamientos SET estado ='finalizado' WHERE id_partido = " . $idPartido .  ";";
        $this->db->query($orden);
        $orden = "CALL resultado(" . $idPartido . ");";
        $this->db->query($orden);
    }
    public function dimeJornadas()
    {
        $orden = "SELECT * FROM jornadas;";
        $resultado = $this->db->query($orden);
        return $resultado->getResultArray();
    }
    public function insertarPartido($jornada, $idEquipoLocal, $idEquipoVisitante, $fecha)
    {
        $orden = "INSERT INTO enfrentamientos (jornada, equipo_local_id, equipo_visitante_id, fecha_juego, estado) VALUES ($jornada, $idEquipoLocal, $idEquipoVisitante, '$fecha', 'programado')";
        $this->db->query($orden);
    }
}
