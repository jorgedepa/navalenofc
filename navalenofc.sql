-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-01-2024 a las 22:36:39
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `navalenofc`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GestionErrores` ()   BEGIN
    DECLARE vFechaSalida DATETIME;
    DECLARE vFichajeID INT;
    DECLARE vNOW DATETIME;
    DECLARE para int default false;
    DECLARE c_cursor CURSOR FOR SELECT FechaSalida, FichajeID FROM fichaje WHERE FichajeID IN (SELECT MAX(FichajeID) FROM fichaje WHERE DAY(FechaEntrada) = DAY(NOW()) GROUP BY id_usuario);
    DECLARE CONTINUE handler FOR NOT FOUND SET para = true;

    
    OPEN c_cursor;
    bucle: LOOP
        FETCH c_cursor INTO  vFechaSalida, vFichajeID;
        IF para= true THEN 
			leave bucle;
		END IF;
        SET vNOW = NOW();
        IF vFechaSalida IS NULL THEN
            UPDATE fichaje SET FechaSalida = vNOW, TipoSalida = 3, Estado = "no justificado" WHERE FichajeID = vFichajeID;
        END IF;
    END LOOP bucle;
    CLOSE c_cursor;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GestionFinPausa` (IN `p_idUsuario` INT)   BEGIN
    DECLARE PausaInicio DATETIME;
    DECLARE DifMinutos INT;
    DECLARE Ahora DATETIME;

    -- Obtener el valor de NOW() al inicio del procedimiento
    SET Ahora = NOW();

    -- Obtener la última entrada para el empleado y fecha actual
    SELECT InicioPausa INTO PausaInicio
    FROM fichaje
    WHERE id_usuario = p_idUsuario AND DATE(FechaEntrada) = DATE(Ahora) AND InicioPausa IS NOT NULL
    ORDER BY FichajeID DESC
    LIMIT 1;

    -- Calcular la diferencia en minutos
    SET DifMinutos = TIMESTAMPDIFF(MINUTE, PausaInicio, Ahora);

    -- Verificar si han pasado más de 20 minutos
    IF DifMinutos > 20 THEN
        -- Actualizar FinPausa con InicioPausa + 20 minutos
        UPDATE fichaje
        SET FinPausa = DATE_ADD(PausaInicio, INTERVAL 20 MINUTE), FechaSalida = DATE_ADD(PausaInicio, INTERVAL 20 MINUTE), Estado = "justificado", TipoSalida = 1
        WHERE id_usuario = p_idUsuario
          AND DATE(FechaEntrada) = DATE(Ahora)
          AND InicioPausa IS NOT NULL
          AND FinPausa IS NULL;

        -- Insertar un nuevo registro
        INSERT INTO fichaje (id_usuario, FechaEntrada, FechaSalida, InicioPausa, FinPausa, TipoSalida, Estado)
        VALUES (p_idUsuario, DATE_ADD(PausaInicio, INTERVAL 20 MINUTE), Ahora, NULL, NULL, 2, 'no justificado');
        INSERT INTO fichaje (id_usuario, FechaEntrada, FechaSalida, InicioPausa, FinPausa, TipoSalida, Estado)
        VALUES (p_idUsuario, DATE_ADD(Ahora, INTERVAL 1 SECOND), NULL, NULL, NULL, NULL, NULL);
    ELSE
        -- Han pasado menos de 20 minutos, simplemente actualizar FinPausa
        UPDATE fichaje
        SET FinPausa = Ahora
        WHERE id_usuario = p_idUsuario
          AND DATE(FechaEntrada) = DATE(Ahora)
          AND InicioPausa IS NOT NULL
          AND FinPausa IS NULL;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `resultado` (IN `p_id_partido` INT)   BEGIN
    DECLARE goles_locales INT;
    
    DECLARE goles_visitantes INT;
    
    -- Obtener goles de cada equipo
    SELECT goles_equipo_local(p_id_partido) INTO goles_locales;
    SELECT goles_equipo_visitante(p_id_partido) INTO goles_visitantes;

    -- Determinar el resultado del partido
    IF goles_locales > goles_visitantes THEN
        -- Equipo local gana
        UPDATE enfrentamientos SET resultado = 1 WHERE id_partido = p_id_partido;

    ELSEIF goles_locales < goles_visitantes THEN
        -- Equipo visitante gana
        UPDATE enfrentamientos SET resultado = 2 WHERE id_partido = p_id_partido;
    ELSE
        -- Empate
		UPDATE enfrentamientos SET resultado = "X" WHERE id_partido = p_id_partido;    
    END IF;
END$$

--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `calcular_horas_justificadas` (`entrada_UsuarioID` INT, `dia1` DATE, `dia2` DATE) RETURNS VARCHAR(255) CHARSET utf8mb4 COLLATE utf8mb4_general_ci  BEGIN
    DECLARE horas_justificadas DECIMAL(10, 5);
    -- Variables para el cursor
    DECLARE done INT DEFAULT FALSE;
    DECLARE cur_fechaEntrada DATETIME;
    DECLARE cur_fechaSalida DATETIME;
    DECLARE cur_tipoSalida INT;
    DECLARE cur_estado VARCHAR(20);
    -- Declarar el cursor
    DECLARE cur CURSOR FOR
        SELECT FechaEntrada, FechaSalida, TipoSalida, Estado
        FROM fichaje
        WHERE id_usuario = entrada_UsuarioID
        AND DATE(FechaEntrada) BETWEEN dia1 AND dia2
        ORDER BY FechaEntrada;
    
    -- Manejo de errores
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    -- Inicializar variables
    SET horas_justificadas = 0;
    -- Abrir el cursor
    OPEN cur;
        -- Leer el primer registro
        FETCH cur INTO cur_fechaEntrada, cur_fechaSalida, cur_tipoSalida, cur_estado;
        -- Procesar los registros
        WHILE NOT done DO
                IF cur_tipoSalida = 1 THEN
                    SET horas_justificadas = horas_justificadas + TIMESTAMPDIFF(SECOND, cur_fechaEntrada, cur_fechaSalida) / 3600.0;
                END IF;
            -- Leer el siguiente registro
            FETCH cur INTO cur_fechaEntrada, cur_fechaSalida, cur_tipoSalida, cur_estado;
        END WHILE;
    
    -- Cerrar el cursor
    CLOSE cur;
    RETURN horas_justificadas;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `calcular_horas_no_justificadas` (`entrada_UsuarioID` INT, `dia1` DATE, `dia2` DATE) RETURNS VARCHAR(255) CHARSET utf8mb4 COLLATE utf8mb4_general_ci  BEGIN
    DECLARE horas_no_justificadas DECIMAL(10, 5);
    DECLARE resultado VARCHAR(255);
    -- Variables para el cursor
    DECLARE done INT DEFAULT FALSE;
    DECLARE cur_fechaEntrada DATETIME;
    DECLARE cur_fechaSalida DATETIME;
    DECLARE cur_tipoSalida INT;
    DECLARE cur_estado VARCHAR(20);
    -- Declarar el cursor
    DECLARE cur CURSOR FOR
        SELECT FechaEntrada, FechaSalida, TipoSalida, Estado
        FROM fichaje
        WHERE id_usuario = entrada_UsuarioID
        AND DATE(FechaEntrada) BETWEEN dia1 AND dia2
        ORDER BY FechaEntrada;
    
    -- Manejo de errores
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    -- Inicializar variables
    SET horas_no_justificadas = 0;
    -- Abrir el cursor
    OPEN cur;
        -- Leer el primer registro
        FETCH cur INTO cur_fechaEntrada, cur_fechaSalida, cur_tipoSalida, cur_estado;
        -- Procesar los registros
        WHILE NOT done DO
            -- Calcular las horas no justificadas
            IF cur_estado = 'no justificado' OR cur_tipoSalida > 3 THEN
                SET horas_no_justificadas = horas_no_justificadas + TIMESTAMPDIFF(SECOND, cur_fechaEntrada, cur_fechaSalida) / 3600.0;
            END IF;
            -- Leer el siguiente registro
            FETCH cur INTO cur_fechaEntrada, cur_fechaSalida, cur_tipoSalida, cur_estado;
        END WHILE;
    -- Cerrar el cursor
    CLOSE cur;
    RETURN horas_no_justificadas;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `calcular_horas_semana_justificadas` (`entrada_UsuarioID` INT) RETURNS VARCHAR(255) CHARSET utf8mb4 COLLATE utf8mb4_general_ci  BEGIN
    DECLARE semana_actual INT;
    DECLARE horas_justificadas DECIMAL(10, 2);
    -- Variables para el cursor
    DECLARE done INT DEFAULT FALSE;
    DECLARE cur_fechaEntrada DATETIME;
    DECLARE cur_fechaSalida DATETIME;
    DECLARE cur_tipoSalida INT;
    DECLARE cur_estado VARCHAR(20);
    -- Declarar el cursor
    DECLARE cur CURSOR FOR
        SELECT FechaEntrada, FechaSalida, TipoSalida, Estado
        FROM fichaje
        WHERE id_usuario = entrada_UsuarioID
        AND YEARWEEK(FechaEntrada) = YEARWEEK(NOW())
        ORDER BY FechaEntrada;
    
    -- Manejo de errores
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    -- Obtener el número de semana actual
    SET semana_actual = WEEK(NOW());
    -- Inicializar variables
    SET horas_justificadas = 0;
    -- Abrir el cursor
    OPEN cur;
        -- Leer el primer registro
        FETCH cur INTO cur_fechaEntrada, cur_fechaSalida, cur_tipoSalida, cur_estado;
        -- Procesar los registros
        WHILE NOT done DO
                IF cur_tipoSalida = 1 THEN
                    SET horas_justificadas = horas_justificadas + TIMESTAMPDIFF(SECOND, cur_fechaEntrada, cur_fechaSalida) / 3600.0;
                END IF;

                IF cur_tipoSalida IN (2, 3) THEN
                    -- Calcular el tiempo entre FechaEntrada y FechaSalida del registro con TipoSalida 2
                    SET horas_justificadas = horas_justificadas + TIMESTAMPDIFF(SECOND, cur_fechaEntrada, cur_fechaSalida) / 3600.0;
                    
                    -- Buscar el siguiente registro con TipoSalida 1
                    SELECT FechaEntrada, TipoSalida
                    INTO cur_fechaEntrada, cur_tipoSalida
                    FROM fichaje
                    WHERE id_usuario = entrada_UsuarioID
                    AND TipoSalida = 1
                    AND FechaEntrada > cur_fechaSalida
                    ORDER BY FechaEntrada
                    LIMIT 1;
                    
                    -- Calcular el tiempo trabajado efectivo
                    IF cur_fechaEntrada IS NOT NULL AND cur_tipoSalida = 1 THEN
                        SET horas_justificadas = horas_justificadas + TIMESTAMPDIFF(SECOND, cur_fechaSalida, cur_fechaEntrada) / 3600.0;
                    END IF;
                END IF;
            -- Leer el siguiente registro
            FETCH cur INTO cur_fechaEntrada, cur_fechaSalida, cur_tipoSalida, cur_estado;
        END WHILE;
    
    -- Cerrar el cursor
    CLOSE cur;
    RETURN horas_justificadas;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `calcular_horas_semana_no_justificadas` (`entrada_UsuarioID` INT) RETURNS VARCHAR(255) CHARSET utf8mb4 COLLATE utf8mb4_general_ci  BEGIN
    DECLARE semana_actual INT;
    DECLARE horas_no_justificadas DECIMAL(10, 2);
    DECLARE resultado VARCHAR(255);
    -- Variables para el cursor
    DECLARE done INT DEFAULT FALSE;
    DECLARE cur_fechaEntrada DATETIME;
    DECLARE cur_fechaSalida DATETIME;
    DECLARE cur_tipoSalida INT;
    DECLARE cur_estado VARCHAR(20);
    -- Declarar el cursor
    DECLARE cur CURSOR FOR
        SELECT FechaEntrada, FechaSalida, TipoSalida, Estado
        FROM fichaje
        WHERE id_usuario = entrada_UsuarioID
        AND YEARWEEK(FechaEntrada) = YEARWEEK(NOW())
        ORDER BY FechaEntrada;
    
    -- Manejo de errores
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    -- Obtener el número de semana actual
    SET semana_actual = WEEK(NOW());
    -- Inicializar variables
    SET horas_no_justificadas = 0;
    -- Abrir el cursor
    OPEN cur;
        -- Leer el primer registro
        FETCH cur INTO cur_fechaEntrada, cur_fechaSalida, cur_tipoSalida, cur_estado;
        -- Procesar los registros
        WHILE NOT done DO
            -- Calcular las horas no justificadas
            IF cur_estado = 'no justificado' OR cur_tipoSalida > 3 THEN
                SET horas_no_justificadas = horas_no_justificadas + TIMESTAMPDIFF(SECOND, cur_fechaEntrada, cur_fechaSalida) / 3600.0;
            END IF;
            -- Leer el siguiente registro
            FETCH cur INTO cur_fechaEntrada, cur_fechaSalida, cur_tipoSalida, cur_estado;
        END WHILE;
    -- Cerrar el cursor
    CLOSE cur;
    RETURN horas_no_justificadas;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `dime_nombre_equipo` (`p_id_equipo` INT) RETURNS VARCHAR(255) CHARSET utf8mb4 COLLATE utf8mb4_general_ci  BEGIN
    DECLARE nombre VARCHAR(255);

    SELECT nombre_equipo
    INTO nombre
    FROM equipos
    WHERE equipo_id = p_id_equipo;

    RETURN nombre;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `erroresFichajes` (`p_id_usuario` INT, `p_dia1` DATE, `p_dia2` DATE) RETURNS VARCHAR(255) CHARSET utf8mb4 COLLATE utf8mb4_general_ci  BEGIN
    DECLARE resultado VARCHAR(255);
    
    SELECT GROUP_CONCAT(CONCAT(DATE(fj.FechaEntrada), '→', ts.tipo) SEPARATOR ', ')
    INTO resultado
    FROM fichaje fj
    INNER JOIN tipossalidas ts ON fj.TipoSalida = ts.tipoID
    WHERE fj.id_usuario = p_id_usuario
    AND DATE(fj.FechaEntrada) BETWEEN p_dia1 AND p_dia2
    AND fj.TipoSalida IN (2, 3);

    RETURN resultado;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `goles_a_favor` (`f_id_equipo` INT) RETURNS INT(11)  BEGIN
    DECLARE resultado INT;
    SELECT COUNT(*) INTO resultado FROM goles WHERE id_equipo = f_id_equipo;
    RETURN resultado;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `goles_en_contra` (`f_id_equipo` INT) RETURNS INT(11)  BEGIN
    DECLARE resultado INT;
    SELECT COUNT(*) INTO resultado FROM enfrentamientos e INNER JOIN  goles g ON e.id_partido = g.id_partido WHERE (equipo_local_id = f_id_equipo OR equipo_visitante_id = f_id_equipo) AND g.id_equipo != 2;
    RETURN resultado;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `goles_equipo_local` (`idPartido` INT) RETURNS INT(11)  BEGIN
    DECLARE numGoles INT;
    
    SELECT COUNT(*) INTO numGoles
    FROM goles g
    INNER JOIN enfrentamientos e ON g.id_equipo = e.equipo_local_id
    WHERE g.id_partido = idPartido AND e.id_partido = idPartido;
    
    RETURN numGoles;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `goles_equipo_visitante` (`idPartido` INT) RETURNS INT(11)  BEGIN
    DECLARE numGoles INT;
    
    SELECT COUNT(*) INTO numGoles
    FROM goles g
    INNER JOIN enfrentamientos e ON g.id_equipo = e.equipo_visitante_id
    WHERE g.id_partido = idPartido AND e.id_partido = idPartido;
    
    RETURN numGoles;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `mostrarBotonEntrar` (`codEmpleado` INT(11)) RETURNS VARCHAR(50) CHARSET utf8mb4 COLLATE utf8mb4_general_ci  BEGIN
	DECLARE diaActual DATE;
 	DECLARE mes INT;
    DECLARE numEntrada INT;
    DECLARE ultimaSalida DATE;
    DECLARE numPausa INT;
    DECLARE estasPausa INT;
	SELECT DATE (CURRENT_DATE) INTO diaActual;
    SELECT COUNT(*) FROM fichaje WHERE id_usuario = codEmpleado AND DATE(FechaEntrada) = DATE(CURRENT_DATE) INTO numEntrada;
    SELECT FechaSalida FROM fichaje WHERE id_usuario = codEmpleado AND DATE(FechaEntrada) = DATE(CURRENT_DATE) ORDER BY FichajeID 	DESC LIMIT 1 INTO ultimaSalida;
    SELECT COUNT(*) FROM fichaje WHERE id_usuario = codEmpleado AND InicioPausa IS NOT NULL AND FinPausa IS NOT NULL AND DATE(FechaEntrada) = CURDATE() INTO numPausa;
    SELECT COUNT(*) FROM fichaje WHERE id_usuario = codEmpleado AND InicioPausa IS NOT NULL AND FinPausa IS NULL AND DATE(FechaEntrada) = CURDATE() INTO estasPausa;
	IF numEntrada = 0 OR (numEntrada > 0 AND ultimaSalida IS NOT NULL) THEN
        RETURN 'BotonEntrada';
    ELSEIF estasPausa = 1  THEN
        RETURN 'BotonFinPausa';
	ELSEIF numEntrada > 0 AND ultimaSalida IS NULL AND numPausa > 0 THEN
        RETURN 'BotonSalida';
	ELSEIF numEntrada > 0 AND ultimaSalida IS NULL AND numPausa = 0 THEN
        RETURN 'BotonInicioPausaBotonSalida';
    
	END IF;
	RETURN 'Error';
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `obtenerPermisos` (`p_id_usuario` INT, `p_dia1` DATE, `p_dia2` DATE) RETURNS VARCHAR(255) CHARSET utf8mb4 COLLATE utf8mb4_general_ci  BEGIN
    DECLARE resultado VARCHAR(255);

    SELECT GROUP_CONCAT(CONCAT(p.FechaInicio, " - ", p.FechaFin, "➝", tp.Descripcion) SEPARATOR ', ')
    INTO resultado
    FROM permisos p
    INNER JOIN tipospermisos tp ON p.TipoPermiso = tp.IdPermiso
    WHERE p.id_usuario = p_id_usuario
    AND (p.FechaInicio BETWEEN p_dia1 AND p_dia2 OR p.FechaFin BETWEEN p_dia1 AND p_dia2);

    RETURN resultado;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `partidos_empatados` (`f_id_equipo` INT) RETURNS INT(11)  BEGIN
    DECLARE total_empatados INT;

    SELECT COUNT(*) INTO total_empatados
    FROM enfrentamientos
    WHERE estado = 'finalizado'
        AND ((equipo_local_id = f_id_equipo AND resultado = "X")
            OR (equipo_visitante_id = f_id_equipo AND resultado = "X"));

    RETURN total_empatados;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `partidos_ganados` (`f_id_equipo` INT) RETURNS INT(11)  BEGIN
    DECLARE total_ganados INT;

    SELECT COUNT(*) INTO total_ganados
    FROM enfrentamientos
    WHERE estado = 'finalizado'
        AND ((equipo_local_id = f_id_equipo AND resultado = 1)
            OR (equipo_visitante_id = f_id_equipo AND resultado = 2));

    RETURN total_ganados;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `partidos_jugados` (`f_id_equipo` INT) RETURNS INT(11)  BEGIN
    DECLARE resultado INT;
    SELECT COUNT(*) INTO resultado FROM enfrentamientos WHERE estado = "finalizado" AND (equipo_local_id = f_id_equipo OR equipo_visitante_id = f_id_equipo);
    RETURN resultado;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `partidos_perdidos` (`f_id_equipo` INT) RETURNS INT(11)  BEGIN
    DECLARE total_perdidos INT;

    SELECT COUNT(*) INTO total_perdidos
    FROM enfrentamientos
    WHERE estado = 'finalizado'
        AND ((equipo_local_id = f_id_equipo AND resultado = 2)
            OR (equipo_visitante_id = f_id_equipo AND resultado = 1));

    RETURN total_perdidos;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `puntos` (`f_id_equipo` INT) RETURNS INT(11)  BEGIN
    DECLARE total_puntos INT;
    DECLARE ganados INT;
    DECLARE empatados INT;
    SET ganados = partidos_ganados(f_id_equipo) * 3;
    SET empatados = partidos_empatados(f_id_equipo);
    SET total_puntos = ganados + empatados;

    RETURN total_puntos;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alineacion`
--

CREATE TABLE `alineacion` (
  `id_partido` int(11) NOT NULL,
  `id_jugador` int(11) NOT NULL,
  `titular` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alineacion`
--

INSERT INTO `alineacion` (`id_partido`, `id_jugador`, `titular`) VALUES
(1, 1, 1),
(1, 2, 0),
(1, 3, 1),
(1, 4, 1),
(1, 5, 1),
(1, 6, 1),
(1, 7, 1),
(1, 8, 1),
(1, 9, 1),
(1, 10, 1),
(1, 11, 1),
(1, 12, 1),
(1, 13, 0),
(1, 14, 0),
(1, 15, 0),
(1, 16, 0),
(1, 17, 1),
(1, 18, 1),
(1, 19, 1),
(1, 20, 1),
(1, 21, 1),
(1, 22, 1),
(1, 23, 1),
(1, 24, 1),
(1, 25, 1),
(1, 26, 1),
(1, 27, 1),
(1, 28, 0),
(1, 29, 0),
(1, 30, 0),
(1, 31, 0),
(1, 32, 0),
(2, 1, 1),
(2, 2, 1),
(2, 3, 1),
(2, 4, 1),
(2, 5, 1),
(2, 6, 1),
(2, 7, 1),
(2, 8, 1),
(2, 9, 1),
(2, 10, 1),
(2, 11, 1),
(2, 12, 0),
(2, 13, 0),
(2, 14, 0),
(2, 15, 0),
(2, 16, 0),
(2, 17, 1),
(2, 18, 1),
(2, 19, 1),
(2, 20, 1),
(2, 21, 1),
(2, 22, 1),
(2, 23, 1),
(2, 24, 1),
(2, 25, 1),
(2, 26, 1),
(2, 27, 1),
(2, 28, 0),
(2, 29, 0),
(2, 30, 0),
(2, 31, 0),
(2, 32, 0),
(3, 1, 1),
(3, 2, 1),
(3, 3, 1),
(3, 4, 1),
(3, 5, 1),
(3, 6, 1),
(3, 7, 1),
(3, 8, 1),
(3, 9, 1),
(3, 10, 1),
(3, 11, 1),
(3, 12, 0),
(3, 13, 0),
(3, 14, 0),
(3, 15, 0),
(3, 16, 0),
(3, 33, 1),
(3, 34, 1),
(3, 35, 1),
(3, 36, 1),
(3, 37, 1),
(3, 38, 1),
(3, 39, 1),
(3, 40, 1),
(3, 41, 1),
(3, 42, 1),
(3, 43, 1),
(3, 44, 0),
(3, 45, 0),
(3, 46, 0),
(3, 47, 0),
(3, 48, 0),
(4, 1, 1),
(4, 2, 1),
(4, 3, 1),
(4, 4, 1),
(4, 5, 1),
(4, 6, 1),
(4, 7, 1),
(4, 8, 1),
(4, 9, 1),
(4, 10, 1),
(4, 11, 1),
(4, 12, 0),
(4, 13, 0),
(4, 14, 0),
(4, 15, 0),
(4, 16, 0),
(4, 33, 1),
(4, 34, 1),
(4, 35, 1),
(4, 36, 1),
(4, 37, 1),
(4, 38, 1),
(4, 39, 1),
(4, 40, 1),
(4, 41, 1),
(4, 42, 1),
(4, 43, 1),
(4, 44, 0),
(4, 45, 0),
(4, 46, 0),
(4, 47, 0),
(4, 48, 0),
(5, 1, 1),
(5, 2, 1),
(5, 3, 1),
(5, 4, 1),
(5, 5, 1),
(5, 6, 1),
(5, 7, 1),
(5, 8, 1),
(5, 9, 1),
(5, 10, 1),
(5, 11, 1),
(5, 12, 0),
(5, 13, 0),
(5, 14, 0),
(5, 15, 0),
(5, 16, 0),
(5, 49, 1),
(5, 50, 1),
(5, 51, 1),
(5, 52, 1),
(5, 53, 1),
(5, 54, 1),
(5, 55, 1),
(5, 56, 1),
(5, 57, 1),
(5, 58, 1),
(5, 59, 1),
(5, 60, 0),
(5, 61, 0),
(5, 62, 0),
(5, 63, 0),
(5, 64, 0),
(6, 1, 1),
(6, 2, 1),
(6, 3, 1),
(6, 4, 1),
(6, 5, 1),
(6, 6, 1),
(6, 7, 1),
(6, 8, 1),
(6, 9, 1),
(6, 10, 1),
(6, 11, 1),
(6, 12, 0),
(6, 13, 0),
(6, 14, 0),
(6, 15, 0),
(6, 16, 0),
(6, 49, 1),
(6, 50, 1),
(6, 51, 1),
(6, 52, 1),
(6, 53, 1),
(6, 54, 1),
(6, 55, 1),
(6, 56, 1),
(6, 57, 1),
(6, 58, 1),
(6, 59, 1),
(6, 60, 0),
(6, 61, 0),
(6, 62, 0),
(6, 63, 0),
(6, 64, 0),
(7, 1, 1),
(7, 2, 1),
(7, 3, 1),
(7, 4, 1),
(7, 5, 1),
(7, 6, 1),
(7, 7, 1),
(7, 8, 1),
(7, 9, 1),
(7, 10, 1),
(7, 11, 1),
(7, 12, 0),
(7, 13, 0),
(7, 14, 0),
(7, 15, 0),
(7, 16, 0),
(7, 65, 1),
(7, 66, 1),
(7, 67, 1),
(7, 68, 1),
(7, 69, 1),
(7, 70, 1),
(7, 71, 1),
(7, 72, 1),
(7, 73, 1),
(7, 74, 1),
(7, 75, 1),
(7, 76, 0),
(7, 77, 0),
(7, 78, 0),
(7, 79, 0),
(7, 80, 0),
(8, 1, 1),
(8, 2, 1),
(8, 3, 1),
(8, 4, 1),
(8, 5, 1),
(8, 6, 1),
(8, 7, 1),
(8, 8, 1),
(8, 9, 1),
(8, 10, 1),
(8, 11, 1),
(8, 12, 0),
(8, 13, 0),
(8, 14, 0),
(8, 15, 0),
(8, 16, 0),
(8, 65, 1),
(8, 66, 1),
(8, 67, 1),
(8, 68, 1),
(8, 69, 1),
(8, 70, 1),
(8, 71, 1),
(8, 72, 1),
(8, 73, 1),
(8, 74, 1),
(8, 75, 1),
(8, 76, 0),
(8, 77, 0),
(8, 78, 0),
(8, 79, 0),
(8, 80, 0),
(9, 1, 1),
(9, 2, 1),
(9, 3, 1),
(9, 4, 1),
(9, 5, 1),
(9, 6, 1),
(9, 7, 1),
(9, 8, 1),
(9, 9, 1),
(9, 10, 1),
(9, 11, 1),
(9, 12, 0),
(9, 13, 0),
(9, 14, 0),
(9, 15, 0),
(9, 16, 0),
(9, 81, 1),
(9, 82, 1),
(9, 83, 1),
(9, 84, 1),
(9, 85, 1),
(9, 86, 1),
(9, 87, 1),
(9, 88, 1),
(9, 89, 1),
(9, 90, 1),
(9, 91, 1),
(9, 92, 0),
(9, 93, 0),
(9, 94, 0),
(9, 95, 0),
(9, 96, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `enfrentamientos`
--

CREATE TABLE `enfrentamientos` (
  `id_partido` int(11) NOT NULL,
  `jornada` int(11) NOT NULL,
  `equipo_local_id` int(11) DEFAULT NULL,
  `equipo_visitante_id` int(11) DEFAULT NULL,
  `fecha_juego` datetime DEFAULT NULL,
  `estado` varchar(20) NOT NULL,
  `resultado` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `enfrentamientos`
--

INSERT INTO `enfrentamientos` (`id_partido`, `jornada`, `equipo_local_id`, `equipo_visitante_id`, `fecha_juego`, `estado`, `resultado`) VALUES
(1, 1, 1, 2, '2023-09-09 18:00:00', 'finalizado', '1'),
(2, 2, 2, 1, '2023-09-16 18:00:00', 'finalizado', '2'),
(3, 3, 1, 3, '2023-10-14 18:00:00', 'finalizado', '1'),
(4, 4, 3, 1, '2023-10-21 18:00:00', 'finalizado', '2'),
(5, 5, 1, 4, '2023-11-11 18:00:00', 'finalizado', '1'),
(6, 6, 4, 1, '2023-11-18 18:00:00', 'finalizado', 'X'),
(7, 7, 1, 5, '2023-12-16 18:00:00', 'finalizado', 'X'),
(8, 8, 5, 1, '2023-12-23 18:00:00', 'finalizado', 'X'),
(9, 9, 1, 6, '2024-01-13 18:00:00', 'finalizado', 'X'),
(10, 10, 6, 1, '2024-01-20 18:00:00', 'programado', ''),
(11, 11, 1, 7, '2024-02-10 18:00:00', 'programado', ''),
(12, 12, 7, 1, '2024-02-17 18:00:00', 'programado', ''),
(13, 13, 1, 8, '2024-03-09 18:00:00', 'programado', ''),
(14, 14, 8, 1, '2024-03-16 18:00:00', 'programado', ''),
(15, 15, 1, 9, '2024-04-13 18:00:00', 'programado', ''),
(16, 16, 9, 1, '2024-04-20 18:00:00', 'programado', ''),
(17, 17, 1, 10, '2024-05-11 18:00:00', 'programado', ''),
(18, 18, 10, 1, '2024-05-18 18:00:00', 'programado', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE `equipos` (
  `equipo_id` int(11) NOT NULL,
  `nombre_equipo` varchar(50) NOT NULL,
  `estadio` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`equipo_id`, `nombre_equipo`, `estadio`) VALUES
(1, 'Navaleno FC', 'Estadio Navaleno FC'),
(2, 'Equipo B', 'Estadio B'),
(3, 'Equipo C', 'Estadio C'),
(4, 'Equipo D', 'Estadio D'),
(5, 'Equipo E', 'Estadio E'),
(6, 'Equipo F', 'Estadio F'),
(7, 'Equipo G', 'Estadio G'),
(8, 'Equipo H', 'Estadio H'),
(9, 'Equipo I', 'Estadio I'),
(10, 'Equipo J', 'Estadio J');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fichaje`
--

CREATE TABLE `fichaje` (
  `FichajeID` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `FechaEntrada` datetime DEFAULT NULL,
  `FechaSalida` datetime DEFAULT NULL,
  `InicioPausa` datetime DEFAULT NULL,
  `FinPausa` datetime DEFAULT NULL,
  `TipoSalida` int(11) DEFAULT NULL,
  `Estado` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `goles`
--

CREATE TABLE `goles` (
  `id_gol` int(11) NOT NULL,
  `id_partido` int(11) NOT NULL,
  `id_jugador` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `minuto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `goles`
--

INSERT INTO `goles` (`id_gol`, `id_partido`, `id_jugador`, `id_equipo`, `minuto`) VALUES
(1, 1, 1, 1, 15),
(2, 1, 3, 1, 30),
(3, 1, 5, 1, 45),
(4, 1, 7, 1, 60),
(5, 1, 17, 2, 20),
(6, 1, 19, 2, 40),
(7, 1, 21, 2, 55),
(8, 2, 1, 1, 10),
(9, 2, 3, 1, 25),
(10, 2, 5, 1, 40),
(11, 2, 7, 1, 55),
(12, 2, 17, 2, 20),
(13, 2, 19, 2, 35),
(14, 2, 21, 2, 50),
(15, 3, 1, 1, 15),
(16, 3, 3, 1, 30),
(17, 3, 5, 1, 45),
(18, 3, 7, 1, 60),
(19, 3, 17, 3, 25),
(20, 3, 19, 3, 40),
(21, 3, 21, 3, 55),
(22, 4, 1, 1, 10),
(23, 4, 3, 1, 25),
(24, 4, 5, 1, 40),
(25, 4, 7, 1, 55),
(26, 4, 17, 3, 20),
(27, 4, 19, 3, 35),
(28, 4, 21, 3, 50),
(29, 5, 1, 1, 10),
(30, 5, 3, 1, 25),
(31, 5, 5, 1, 40),
(32, 5, 7, 1, 55),
(33, 5, 49, 4, 20),
(34, 5, 51, 4, 35),
(35, 5, 53, 4, 50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jornadas`
--

CREATE TABLE `jornadas` (
  `jornada` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `jornadas`
--

INSERT INTO `jornadas` (`jornada`) VALUES
(1),
(2),
(3),
(4),
(5),
(6),
(7),
(8),
(9),
(10),
(11),
(12),
(13),
(14),
(15),
(16),
(17),
(18);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugadores`
--

CREATE TABLE `jugadores` (
  `id_jugador` int(11) NOT NULL,
  `equipo_id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `jugadores`
--

INSERT INTO `jugadores` (`id_jugador`, `equipo_id`, `nombre`) VALUES
(1, 1, 'Luis Rodríguez'),
(2, 1, 'Ana Martínez'),
(3, 1, 'Juan Sánchez'),
(4, 1, 'María González'),
(5, 1, 'Carlos Pérez'),
(6, 1, 'Laura Hernández'),
(7, 1, 'Pedro Gómez'),
(8, 1, 'Isabel López'),
(9, 1, 'Miguel Fernández'),
(10, 1, 'Carmen Torres'),
(11, 1, 'Javier Ramírez'),
(12, 1, 'Elena Díaz'),
(13, 1, 'Francisco Ruiz'),
(14, 1, 'Lucía Vargas'),
(15, 1, 'Alejandro Jiménez'),
(16, 1, 'Sofía Ruiz'),
(17, 2, 'Andrés Rodríguez'),
(18, 2, 'Eva Martínez'),
(19, 2, 'José Sánchez'),
(20, 2, 'Ana García'),
(21, 2, 'David Pérez'),
(22, 2, 'Laura Fernández'),
(23, 2, 'Roberto Gómez'),
(24, 2, 'Sara López'),
(25, 2, 'Javier Fernández'),
(26, 2, 'Cristina Torres'),
(27, 2, 'Miguel Ramírez'),
(28, 2, 'Lucía Díaz'),
(29, 2, 'Juan Ruiz'),
(30, 2, 'Isabel Vargas'),
(31, 2, 'Pedro Jiménez'),
(32, 2, 'María Ruiz'),
(33, 3, 'Mario Rodríguez'),
(34, 3, 'Luisa Martínez'),
(35, 3, 'Pablo Sánchez'),
(36, 3, 'Carmen García'),
(37, 3, 'Andrea Pérez'),
(38, 3, 'Carlos Fernández'),
(39, 3, 'Elena Gómez'),
(40, 3, 'Daniel López'),
(41, 3, 'Sofía Fernández'),
(42, 3, 'José Torres'),
(43, 3, 'Marta Ramírez'),
(44, 3, 'Jorge Díaz'),
(45, 3, 'Laura Ruiz'),
(46, 3, 'Francisco Vargas'),
(47, 3, 'Isabel Jiménez'),
(48, 3, 'Alejandro Ruiz'),
(49, 4, 'Ricardo Rodríguez'),
(50, 4, 'Marina Martínez'),
(51, 4, 'Alberto Sánchez'),
(52, 4, 'Natalia García'),
(53, 4, 'Fernando Pérez'),
(54, 4, 'Ana Fernández'),
(55, 4, 'Víctor Gómez'),
(56, 4, 'Beatriz López'),
(57, 4, 'Diego Fernández'),
(58, 4, 'Eva Torres'),
(59, 4, 'Manuel Ramírez'),
(60, 4, 'Lourdes Díaz'),
(61, 4, 'Antonio Ruiz'),
(62, 4, 'Celia Vargas'),
(63, 4, 'Hugo Jiménez'),
(64, 4, 'Raquel Ruiz'),
(65, 5, 'Gabriel Rodríguez'),
(66, 5, 'Celia Martínez'),
(67, 5, 'Sergio Sánchez'),
(68, 5, 'Elena García'),
(69, 5, 'Jorge Pérez'),
(70, 5, 'Marta Fernández'),
(71, 5, 'Javier Gómez'),
(72, 5, 'Isabel López'),
(73, 5, 'Pablo Fernández'),
(74, 5, 'Ana Torres'),
(75, 5, 'Adrián Ramírez'),
(76, 5, 'Sara Díaz'),
(77, 5, 'Miguel Ruiz'),
(78, 5, 'Laura Vargas'),
(79, 5, 'Daniel Jiménez'),
(80, 5, 'María Ruiz'),
(81, 6, 'Iván Rodríguez'),
(82, 6, 'Alicia Martínez'),
(83, 6, 'Santiago Sánchez'),
(84, 6, 'Lucía García'),
(85, 6, 'Juan Pérez'),
(86, 6, 'Eva Fernández'),
(87, 6, 'Raúl Gómez'),
(88, 6, 'Natalia López'),
(89, 6, 'Andrés Fernández'),
(90, 6, 'Mónica Torres'),
(91, 6, 'Carlos Ramírez'),
(92, 6, 'Luisa Díaz'),
(93, 6, 'Óscar Ruiz'),
(94, 6, 'Marina Vargas'),
(95, 6, 'Héctor Jiménez'),
(96, 6, 'Carmen Ruiz'),
(97, 7, 'Rodrigo Rodríguez'),
(98, 7, 'Natalia Martínez'),
(99, 7, 'Miguel Sánchez'),
(100, 7, 'Cristina García'),
(101, 7, 'Ángel Pérez'),
(102, 7, 'Lorena Fernández'),
(103, 7, 'Joaquín Gómez'),
(104, 7, 'Silvia López'),
(105, 7, 'Fernando Fernández'),
(106, 7, 'Elena Torres'),
(107, 7, 'Diego Ramírez'),
(108, 7, 'Laura Díaz'),
(109, 7, 'José Ruiz'),
(110, 7, 'Sandra Vargas'),
(111, 7, 'Pablo Jiménez'),
(112, 7, 'Isabel Ruiz'),
(113, 8, 'Fernando Rodríguez'),
(114, 8, 'Sara Martínez'),
(115, 8, 'Juan Sánchez'),
(116, 8, 'Ana García'),
(117, 8, 'Alejandro Pérez'),
(118, 8, 'Eva Fernández'),
(119, 8, 'Carlos Gómez'),
(120, 8, 'Marta López'),
(121, 8, 'Daniel Fernández'),
(122, 8, 'Laura Torres'),
(123, 8, 'Javier Ramírez'),
(124, 8, 'Isabel Díaz'),
(125, 8, 'Luis Ruiz'),
(126, 8, 'Carmen Vargas'),
(127, 8, 'Miguel Jiménez'),
(128, 8, 'Natalia Ruiz'),
(129, 9, 'Javier Rodríguez'),
(130, 9, 'Sofía Martínez'),
(131, 9, 'Pedro Sánchez'),
(132, 9, 'Lucía García'),
(133, 9, 'Daniel Pérez'),
(134, 9, 'Eva Fernández'),
(135, 9, 'Miguel Gómez'),
(136, 9, 'Laura López'),
(137, 9, 'Carlos Fernández'),
(138, 9, 'Carmen Torres'),
(139, 9, 'Alejandro Ramírez'),
(140, 9, 'Isabel Díaz'),
(141, 9, 'José Ruiz'),
(142, 9, 'María Vargas'),
(143, 9, 'Antonio Jiménez'),
(144, 9, 'Ana Ruiz'),
(145, 10, 'Raúl Rodríguez'),
(146, 10, 'María Martínez'),
(147, 10, 'Andrés Sánchez'),
(148, 10, 'Laura García'),
(149, 10, 'Francisco Pérez'),
(150, 10, 'Sara Fernández'),
(151, 10, 'Javier Gómez'),
(152, 10, 'Natalia López'),
(153, 10, 'Alejandro Fernández'),
(154, 10, 'Carmen Torres'),
(155, 10, 'José Ramírez'),
(156, 10, 'Eva Díaz'),
(157, 10, 'Miguel Ruiz'),
(158, 10, 'Isabel Vargas'),
(159, 10, 'Antonio Jiménez'),
(160, 10, 'Lucía Ruiz');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticias`
--

CREATE TABLE `noticias` (
  `id` int(11) NOT NULL,
  `titular` varchar(255) NOT NULL,
  `subtitulo` varchar(255) DEFAULT NULL,
  `cuerpo` text NOT NULL,
  `fecha_publicacion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `noticias`
--

INSERT INTO `noticias` (`id`, `titular`, `subtitulo`, `cuerpo`, `fecha_publicacion`) VALUES
(1, 'Lorem ipsum dolor sit amet', 'Fusce ligula sem, suscipit eget dui vitae, dignissim malesuada odio.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam eget lacus et ligula iaculis viverra. Curabitur elementum varius ex. Nulla facilisi. Praesent sit amet posuere justo. Donec tempor leo eu erat venenatis, quis pretium lectus accumsan. Aliquam erat volutpat. Aliquam id sem nunc. Donec semper in ante vitae ultrices. Phasellus in porta tellus. Nulla velit dolor, mollis eu ligula auctor, aliquam tincidunt ex.\r\n\r\nMaecenas vel massa at mi dapibus eleifend non in nulla. Suspendisse magna orci, laoreet a ultrices quis, hendrerit varius urna. Sed sagittis arcu in mauris scelerisque vulputate. Sed sagittis mattis eros, eget hendrerit quam ultrices sed. Nunc at nulla tempor, aliquam velit quis, tempor libero. In id placerat metus. Praesent scelerisque semper justo a sollicitudin. In porta iaculis metus, vitae hendrerit diam tristique vitae. Integer quam nisi, faucibus id augue tincidunt, sagittis ornare libero. Cras placerat facilisis diam.\r\n\r\nNullam id laoreet mi. Suspendisse felis risus, porta eget sem et, posuere euismod felis. Vestibulum nec augue enim. Phasellus sollicitudin sit amet eros ut rutrum. Donec placerat elit nec tortor interdum malesuada. Maecenas efficitur orci id molestie interdum. Donec tempor sed diam ac viverra. Nulla facilisi. Proin mollis, nunc posuere venenatis dictum, mi eros egestas dui, et condimentum orci metus nec orci. Quisque dignissim felis vel porta rutrum. Fusce quis venenatis purus. Proin elit libero, scelerisque ac scelerisque ut, tincidunt id sem.\r\n\r\nDonec facilisis vitae nisi in rutrum. Curabitur lobortis libero sed dictum tincidunt. Quisque eget sem elit. Morbi quis sagittis ante. Suspendisse tortor nulla, tempor at iaculis non, sollicitudin sed ligula. Curabitur sed lectus eu augue suscipit viverra. Etiam scelerisque vestibulum accumsan. Nam pharetra est nulla, nec fringilla ex sagittis vitae. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Quisque ac felis augue. Curabitur quis velit eget velit iaculis molestie. Proin sollicitudin sem risus, vel convallis dolor consequat ac. Sed interdum molestie accumsan.\r\n\r\nMaecenas sed quam dapibus nisi varius bibendum eu at dui. Donec erat velit, volutpat eget rhoncus sed, commodo non magna. Duis ultricies mattis tellus, ac lobortis urna dapibus vel. Ut feugiat eu eros ut cursus. Praesent interdum eros id dictum dictum. Vestibulum magna quam, posuere eu diam sit amet, lobortis sodales augue. Maecenas quis mi non nunc interdum ornare. Mauris tempor mauris nisi, vitae pellentesque augue pulvinar et.\r\n\r\nMaecenas pretium nulla gravida, condimentum quam ac, lobortis lorem. Aliquam ante ipsum, fermentum sit amet maximus a, mattis sed velit. Proin maximus sit amet turpis nec efficitur. Ut erat nulla, aliquet ut quam sed, feugiat mattis nisl. Vestibulum convallis, lorem in convallis lacinia, arcu nibh dictum sem, id posuere tellus nibh sed ligula. Donec ut urna nec nisi imperdiet convallis non nec lacus. Integer dictum nulla nec lobortis pellentesque.\r\n\r\nIn ac neque arcu. Fusce porta sapien fringilla, maximus ex et, consequat massa. Curabitur congue justo non massa cursus ornare. Integer efficitur vehicula posuere. Nunc pulvinar, metus sit amet laoreet cursus, enim eros congue risus, a vulputate nisi diam id lacus. Nullam elementum dolor a lobortis malesuada. Integer ante est, facilisis eu felis nec, tristique venenatis enim. Aliquam blandit luctus orci nec gravida. Fusce dictum sit amet magna eu fermentum. Duis vel massa vel neque elementum ornare in et velit. Pellentesque dapibus eleifend vulputate. Curabitur id sagittis metus. Curabitur feugiat iaculis turpis, eu euismod mauris gravida ac. Nulla ut tortor lobortis, venenatis neque nec, suscipit purus.\r\n\r\nQuisque nisl felis, varius in augue at, elementum scelerisque enim. Nam a maximus urna, in sagittis lorem. In hac habitasse platea dictumst. Sed tristique nisi eget vestibulum varius. Curabitur nec turpis ex. Quisque eu semper tellus. Suspendisse augue augue, posuere ac facilisis nec, porta vulputate velit. Aliquam leo dui, condimentum at posuere ut, mattis et enim. Ut luctus consequat massa sed placerat. Praesent tellus nunc, dignissim eget tincidunt nec, tincidunt blandit risus. Suspendisse quis orci turpis. Quisque ut vehicula nunc, non convallis felis. In interdum vestibulum tellus, et mattis nunc eleifend in. Integer quam sem, posuere et imperdiet a, gravida et lectus.', '2023-01-15'),
(2, 'Lorem ipsum dolor sit amet', 'Fusce ligula sem, suscipit eget dui vitae, dignissim malesuada odio.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam eget lacus et ligula iaculis viverra. Curabitur elementum varius ex. Nulla facilisi. Praesent sit amet posuere justo. Donec tempor leo eu erat venenatis, quis pretium lectus accumsan. Aliquam erat volutpat. Aliquam id sem nunc. Donec semper in ante vitae ultrices. Phasellus in porta tellus. Nulla velit dolor, mollis eu ligula auctor, aliquam tincidunt ex.\r\n\r\nMaecenas vel massa at mi dapibus eleifend non in nulla. Suspendisse magna orci, laoreet a ultrices quis, hendrerit varius urna. Sed sagittis arcu in mauris scelerisque vulputate. Sed sagittis mattis eros, eget hendrerit quam ultrices sed. Nunc at nulla tempor, aliquam velit quis, tempor libero. In id placerat metus. Praesent scelerisque semper justo a sollicitudin. In porta iaculis metus, vitae hendrerit diam tristique vitae. Integer quam nisi, faucibus id augue tincidunt, sagittis ornare libero. Cras placerat facilisis diam.\r\n\r\nNullam id laoreet mi. Suspendisse felis risus, porta eget sem et, posuere euismod felis. Vestibulum nec augue enim. Phasellus sollicitudin sit amet eros ut rutrum. Donec placerat elit nec tortor interdum malesuada. Maecenas efficitur orci id molestie interdum. Donec tempor sed diam ac viverra. Nulla facilisi. Proin mollis, nunc posuere venenatis dictum, mi eros egestas dui, et condimentum orci metus nec orci. Quisque dignissim felis vel porta rutrum. Fusce quis venenatis purus. Proin elit libero, scelerisque ac scelerisque ut, tincidunt id sem.\r\n\r\nDonec facilisis vitae nisi in rutrum. Curabitur lobortis libero sed dictum tincidunt. Quisque eget sem elit. Morbi quis sagittis ante. Suspendisse tortor nulla, tempor at iaculis non, sollicitudin sed ligula. Curabitur sed lectus eu augue suscipit viverra. Etiam scelerisque vestibulum accumsan. Nam pharetra est nulla, nec fringilla ex sagittis vitae. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Quisque ac felis augue. Curabitur quis velit eget velit iaculis molestie. Proin sollicitudin sem risus, vel convallis dolor consequat ac. Sed interdum molestie accumsan.\r\n\r\nMaecenas sed quam dapibus nisi varius bibendum eu at dui. Donec erat velit, volutpat eget rhoncus sed, commodo non magna. Duis ultricies mattis tellus, ac lobortis urna dapibus vel. Ut feugiat eu eros ut cursus. Praesent interdum eros id dictum dictum. Vestibulum magna quam, posuere eu diam sit amet, lobortis sodales augue. Maecenas quis mi non nunc interdum ornare. Mauris tempor mauris nisi, vitae pellentesque augue pulvinar et.\r\n\r\nMaecenas pretium nulla gravida, condimentum quam ac, lobortis lorem. Aliquam ante ipsum, fermentum sit amet maximus a, mattis sed velit. Proin maximus sit amet turpis nec efficitur. Ut erat nulla, aliquet ut quam sed, feugiat mattis nisl. Vestibulum convallis, lorem in convallis lacinia, arcu nibh dictum sem, id posuere tellus nibh sed ligula. Donec ut urna nec nisi imperdiet convallis non nec lacus. Integer dictum nulla nec lobortis pellentesque.\r\n\r\nIn ac neque arcu. Fusce porta sapien fringilla, maximus ex et, consequat massa. Curabitur congue justo non massa cursus ornare. Integer efficitur vehicula posuere. Nunc pulvinar, metus sit amet laoreet cursus, enim eros congue risus, a vulputate nisi diam id lacus. Nullam elementum dolor a lobortis malesuada. Integer ante est, facilisis eu felis nec, tristique venenatis enim. Aliquam blandit luctus orci nec gravida. Fusce dictum sit amet magna eu fermentum. Duis vel massa vel neque elementum ornare in et velit. Pellentesque dapibus eleifend vulputate. Curabitur id sagittis metus. Curabitur feugiat iaculis turpis, eu euismod mauris gravida ac. Nulla ut tortor lobortis, venenatis neque nec, suscipit purus.\r\n\r\nQuisque nisl felis, varius in augue at, elementum scelerisque enim. Nam a maximus urna, in sagittis lorem. In hac habitasse platea dictumst. Sed tristique nisi eget vestibulum varius. Curabitur nec turpis ex. Quisque eu semper tellus. Suspendisse augue augue, posuere ac facilisis nec, porta vulputate velit. Aliquam leo dui, condimentum at posuere ut, mattis et enim. Ut luctus consequat massa sed placerat. Praesent tellus nunc, dignissim eget tincidunt nec, tincidunt blandit risus. Suspendisse quis orci turpis. Quisque ut vehicula nunc, non convallis felis. In interdum vestibulum tellus, et mattis nunc eleifend in. Integer quam sem, posuere et imperdiet a, gravida et lectus.', '2023-02-02'),
(3, 'Lorem ipsum dolor sit amet', 'Fusce ligula sem, suscipit eget dui vitae, dignissim malesuada odio.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam eget lacus et ligula iaculis viverra. Curabitur elementum varius ex. Nulla facilisi. Praesent sit amet posuere justo. Donec tempor leo eu erat venenatis, quis pretium lectus accumsan. Aliquam erat volutpat. Aliquam id sem nunc. Donec semper in ante vitae ultrices. Phasellus in porta tellus. Nulla velit dolor, mollis eu ligula auctor, aliquam tincidunt ex.\r\n\r\nMaecenas vel massa at mi dapibus eleifend non in nulla. Suspendisse magna orci, laoreet a ultrices quis, hendrerit varius urna. Sed sagittis arcu in mauris scelerisque vulputate. Sed sagittis mattis eros, eget hendrerit quam ultrices sed. Nunc at nulla tempor, aliquam velit quis, tempor libero. In id placerat metus. Praesent scelerisque semper justo a sollicitudin. In porta iaculis metus, vitae hendrerit diam tristique vitae. Integer quam nisi, faucibus id augue tincidunt, sagittis ornare libero. Cras placerat facilisis diam.\r\n\r\nNullam id laoreet mi. Suspendisse felis risus, porta eget sem et, posuere euismod felis. Vestibulum nec augue enim. Phasellus sollicitudin sit amet eros ut rutrum. Donec placerat elit nec tortor interdum malesuada. Maecenas efficitur orci id molestie interdum. Donec tempor sed diam ac viverra. Nulla facilisi. Proin mollis, nunc posuere venenatis dictum, mi eros egestas dui, et condimentum orci metus nec orci. Quisque dignissim felis vel porta rutrum. Fusce quis venenatis purus. Proin elit libero, scelerisque ac scelerisque ut, tincidunt id sem.\r\n\r\nDonec facilisis vitae nisi in rutrum. Curabitur lobortis libero sed dictum tincidunt. Quisque eget sem elit. Morbi quis sagittis ante. Suspendisse tortor nulla, tempor at iaculis non, sollicitudin sed ligula. Curabitur sed lectus eu augue suscipit viverra. Etiam scelerisque vestibulum accumsan. Nam pharetra est nulla, nec fringilla ex sagittis vitae. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Quisque ac felis augue. Curabitur quis velit eget velit iaculis molestie. Proin sollicitudin sem risus, vel convallis dolor consequat ac. Sed interdum molestie accumsan.\r\n\r\nMaecenas sed quam dapibus nisi varius bibendum eu at dui. Donec erat velit, volutpat eget rhoncus sed, commodo non magna. Duis ultricies mattis tellus, ac lobortis urna dapibus vel. Ut feugiat eu eros ut cursus. Praesent interdum eros id dictum dictum. Vestibulum magna quam, posuere eu diam sit amet, lobortis sodales augue. Maecenas quis mi non nunc interdum ornare. Mauris tempor mauris nisi, vitae pellentesque augue pulvinar et.\r\n\r\nMaecenas pretium nulla gravida, condimentum quam ac, lobortis lorem. Aliquam ante ipsum, fermentum sit amet maximus a, mattis sed velit. Proin maximus sit amet turpis nec efficitur. Ut erat nulla, aliquet ut quam sed, feugiat mattis nisl. Vestibulum convallis, lorem in convallis lacinia, arcu nibh dictum sem, id posuere tellus nibh sed ligula. Donec ut urna nec nisi imperdiet convallis non nec lacus. Integer dictum nulla nec lobortis pellentesque.\r\n\r\nIn ac neque arcu. Fusce porta sapien fringilla, maximus ex et, consequat massa. Curabitur congue justo non massa cursus ornare. Integer efficitur vehicula posuere. Nunc pulvinar, metus sit amet laoreet cursus, enim eros congue risus, a vulputate nisi diam id lacus. Nullam elementum dolor a lobortis malesuada. Integer ante est, facilisis eu felis nec, tristique venenatis enim. Aliquam blandit luctus orci nec gravida. Fusce dictum sit amet magna eu fermentum. Duis vel massa vel neque elementum ornare in et velit. Pellentesque dapibus eleifend vulputate. Curabitur id sagittis metus. Curabitur feugiat iaculis turpis, eu euismod mauris gravida ac. Nulla ut tortor lobortis, venenatis neque nec, suscipit purus.\r\n\r\nQuisque nisl felis, varius in augue at, elementum scelerisque enim. Nam a maximus urna, in sagittis lorem. In hac habitasse platea dictumst. Sed tristique nisi eget vestibulum varius. Curabitur nec turpis ex. Quisque eu semper tellus. Suspendisse augue augue, posuere ac facilisis nec, porta vulputate velit. Aliquam leo dui, condimentum at posuere ut, mattis et enim. Ut luctus consequat massa sed placerat. Praesent tellus nunc, dignissim eget tincidunt nec, tincidunt blandit risus. Suspendisse quis orci turpis. Quisque ut vehicula nunc, non convallis felis. In interdum vestibulum tellus, et mattis nunc eleifend in. Integer quam sem, posuere et imperdiet a, gravida et lectus.', '2023-03-10'),
(4, 'Lorem ipsum dolor sit amet', 'Fusce ligula sem, suscipit eget dui vitae, dignissim malesuada odio.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam eget lacus et ligula iaculis viverra. Curabitur elementum varius ex. Nulla facilisi. Praesent sit amet posuere justo. Donec tempor leo eu erat venenatis, quis pretium lectus accumsan. Aliquam erat volutpat. Aliquam id sem nunc. Donec semper in ante vitae ultrices. Phasellus in porta tellus. Nulla velit dolor, mollis eu ligula auctor, aliquam tincidunt ex.\r\n\r\nMaecenas vel massa at mi dapibus eleifend non in nulla. Suspendisse magna orci, laoreet a ultrices quis, hendrerit varius urna. Sed sagittis arcu in mauris scelerisque vulputate. Sed sagittis mattis eros, eget hendrerit quam ultrices sed. Nunc at nulla tempor, aliquam velit quis, tempor libero. In id placerat metus. Praesent scelerisque semper justo a sollicitudin. In porta iaculis metus, vitae hendrerit diam tristique vitae. Integer quam nisi, faucibus id augue tincidunt, sagittis ornare libero. Cras placerat facilisis diam.\r\n\r\nNullam id laoreet mi. Suspendisse felis risus, porta eget sem et, posuere euismod felis. Vestibulum nec augue enim. Phasellus sollicitudin sit amet eros ut rutrum. Donec placerat elit nec tortor interdum malesuada. Maecenas efficitur orci id molestie interdum. Donec tempor sed diam ac viverra. Nulla facilisi. Proin mollis, nunc posuere venenatis dictum, mi eros egestas dui, et condimentum orci metus nec orci. Quisque dignissim felis vel porta rutrum. Fusce quis venenatis purus. Proin elit libero, scelerisque ac scelerisque ut, tincidunt id sem.\r\n\r\nDonec facilisis vitae nisi in rutrum. Curabitur lobortis libero sed dictum tincidunt. Quisque eget sem elit. Morbi quis sagittis ante. Suspendisse tortor nulla, tempor at iaculis non, sollicitudin sed ligula. Curabitur sed lectus eu augue suscipit viverra. Etiam scelerisque vestibulum accumsan. Nam pharetra est nulla, nec fringilla ex sagittis vitae. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Quisque ac felis augue. Curabitur quis velit eget velit iaculis molestie. Proin sollicitudin sem risus, vel convallis dolor consequat ac. Sed interdum molestie accumsan.\r\n\r\nMaecenas sed quam dapibus nisi varius bibendum eu at dui. Donec erat velit, volutpat eget rhoncus sed, commodo non magna. Duis ultricies mattis tellus, ac lobortis urna dapibus vel. Ut feugiat eu eros ut cursus. Praesent interdum eros id dictum dictum. Vestibulum magna quam, posuere eu diam sit amet, lobortis sodales augue. Maecenas quis mi non nunc interdum ornare. Mauris tempor mauris nisi, vitae pellentesque augue pulvinar et.\r\n\r\nMaecenas pretium nulla gravida, condimentum quam ac, lobortis lorem. Aliquam ante ipsum, fermentum sit amet maximus a, mattis sed velit. Proin maximus sit amet turpis nec efficitur. Ut erat nulla, aliquet ut quam sed, feugiat mattis nisl. Vestibulum convallis, lorem in convallis lacinia, arcu nibh dictum sem, id posuere tellus nibh sed ligula. Donec ut urna nec nisi imperdiet convallis non nec lacus. Integer dictum nulla nec lobortis pellentesque.\r\n\r\nIn ac neque arcu. Fusce porta sapien fringilla, maximus ex et, consequat massa. Curabitur congue justo non massa cursus ornare. Integer efficitur vehicula posuere. Nunc pulvinar, metus sit amet laoreet cursus, enim eros congue risus, a vulputate nisi diam id lacus. Nullam elementum dolor a lobortis malesuada. Integer ante est, facilisis eu felis nec, tristique venenatis enim. Aliquam blandit luctus orci nec gravida. Fusce dictum sit amet magna eu fermentum. Duis vel massa vel neque elementum ornare in et velit. Pellentesque dapibus eleifend vulputate. Curabitur id sagittis metus. Curabitur feugiat iaculis turpis, eu euismod mauris gravida ac. Nulla ut tortor lobortis, venenatis neque nec, suscipit purus.\r\n\r\nQuisque nisl felis, varius in augue at, elementum scelerisque enim. Nam a maximus urna, in sagittis lorem. In hac habitasse platea dictumst. Sed tristique nisi eget vestibulum varius. Curabitur nec turpis ex. Quisque eu semper tellus. Suspendisse augue augue, posuere ac facilisis nec, porta vulputate velit. Aliquam leo dui, condimentum at posuere ut, mattis et enim. Ut luctus consequat massa sed placerat. Praesent tellus nunc, dignissim eget tincidunt nec, tincidunt blandit risus. Suspendisse quis orci turpis. Quisque ut vehicula nunc, non convallis felis. In interdum vestibulum tellus, et mattis nunc eleifend in. Integer quam sem, posuere et imperdiet a, gravida et lectus.', '2023-04-05'),
(5, 'Lorem ipsum dolor sit amet', 'Fusce ligula sem, suscipit eget dui vitae, dignissim malesuada odio.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam eget lacus et ligula iaculis viverra. Curabitur elementum varius ex. Nulla facilisi. Praesent sit amet posuere justo. Donec tempor leo eu erat venenatis, quis pretium lectus accumsan. Aliquam erat volutpat. Aliquam id sem nunc. Donec semper in ante vitae ultrices. Phasellus in porta tellus. Nulla velit dolor, mollis eu ligula auctor, aliquam tincidunt ex.\r\n\r\nMaecenas vel massa at mi dapibus eleifend non in nulla. Suspendisse magna orci, laoreet a ultrices quis, hendrerit varius urna. Sed sagittis arcu in mauris scelerisque vulputate. Sed sagittis mattis eros, eget hendrerit quam ultrices sed. Nunc at nulla tempor, aliquam velit quis, tempor libero. In id placerat metus. Praesent scelerisque semper justo a sollicitudin. In porta iaculis metus, vitae hendrerit diam tristique vitae. Integer quam nisi, faucibus id augue tincidunt, sagittis ornare libero. Cras placerat facilisis diam.\r\n\r\nNullam id laoreet mi. Suspendisse felis risus, porta eget sem et, posuere euismod felis. Vestibulum nec augue enim. Phasellus sollicitudin sit amet eros ut rutrum. Donec placerat elit nec tortor interdum malesuada. Maecenas efficitur orci id molestie interdum. Donec tempor sed diam ac viverra. Nulla facilisi. Proin mollis, nunc posuere venenatis dictum, mi eros egestas dui, et condimentum orci metus nec orci. Quisque dignissim felis vel porta rutrum. Fusce quis venenatis purus. Proin elit libero, scelerisque ac scelerisque ut, tincidunt id sem.\r\n\r\nDonec facilisis vitae nisi in rutrum. Curabitur lobortis libero sed dictum tincidunt. Quisque eget sem elit. Morbi quis sagittis ante. Suspendisse tortor nulla, tempor at iaculis non, sollicitudin sed ligula. Curabitur sed lectus eu augue suscipit viverra. Etiam scelerisque vestibulum accumsan. Nam pharetra est nulla, nec fringilla ex sagittis vitae. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Quisque ac felis augue. Curabitur quis velit eget velit iaculis molestie. Proin sollicitudin sem risus, vel convallis dolor consequat ac. Sed interdum molestie accumsan.\r\n\r\nMaecenas sed quam dapibus nisi varius bibendum eu at dui. Donec erat velit, volutpat eget rhoncus sed, commodo non magna. Duis ultricies mattis tellus, ac lobortis urna dapibus vel. Ut feugiat eu eros ut cursus. Praesent interdum eros id dictum dictum. Vestibulum magna quam, posuere eu diam sit amet, lobortis sodales augue. Maecenas quis mi non nunc interdum ornare. Mauris tempor mauris nisi, vitae pellentesque augue pulvinar et.\r\n\r\nMaecenas pretium nulla gravida, condimentum quam ac, lobortis lorem. Aliquam ante ipsum, fermentum sit amet maximus a, mattis sed velit. Proin maximus sit amet turpis nec efficitur. Ut erat nulla, aliquet ut quam sed, feugiat mattis nisl. Vestibulum convallis, lorem in convallis lacinia, arcu nibh dictum sem, id posuere tellus nibh sed ligula. Donec ut urna nec nisi imperdiet convallis non nec lacus. Integer dictum nulla nec lobortis pellentesque.\r\n\r\nIn ac neque arcu. Fusce porta sapien fringilla, maximus ex et, consequat massa. Curabitur congue justo non massa cursus ornare. Integer efficitur vehicula posuere. Nunc pulvinar, metus sit amet laoreet cursus, enim eros congue risus, a vulputate nisi diam id lacus. Nullam elementum dolor a lobortis malesuada. Integer ante est, facilisis eu felis nec, tristique venenatis enim. Aliquam blandit luctus orci nec gravida. Fusce dictum sit amet magna eu fermentum. Duis vel massa vel neque elementum ornare in et velit. Pellentesque dapibus eleifend vulputate. Curabitur id sagittis metus. Curabitur feugiat iaculis turpis, eu euismod mauris gravida ac. Nulla ut tortor lobortis, venenatis neque nec, suscipit purus.\r\n\r\nQuisque nisl felis, varius in augue at, elementum scelerisque enim. Nam a maximus urna, in sagittis lorem. In hac habitasse platea dictumst. Sed tristique nisi eget vestibulum varius. Curabitur nec turpis ex. Quisque eu semper tellus. Suspendisse augue augue, posuere ac facilisis nec, porta vulputate velit. Aliquam leo dui, condimentum at posuere ut, mattis et enim. Ut luctus consequat massa sed placerat. Praesent tellus nunc, dignissim eget tincidunt nec, tincidunt blandit risus. Suspendisse quis orci turpis. Quisque ut vehicula nunc, non convallis felis. In interdum vestibulum tellus, et mattis nunc eleifend in. Integer quam sem, posuere et imperdiet a, gravida et lectus.', '2023-04-12'),
(6, 'Lorem ipsum dolor sit amet', 'Fusce ligula sem, suscipit eget dui vitae, dignissim malesuada odio.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam eget lacus et ligula iaculis viverra. Curabitur elementum varius ex. Nulla facilisi. Praesent sit amet posuere justo. Donec tempor leo eu erat venenatis, quis pretium lectus accumsan. Aliquam erat volutpat. Aliquam id sem nunc. Donec semper in ante vitae ultrices. Phasellus in porta tellus. Nulla velit dolor, mollis eu ligula auctor, aliquam tincidunt ex.\r\n\r\nMaecenas vel massa at mi dapibus eleifend non in nulla. Suspendisse magna orci, laoreet a ultrices quis, hendrerit varius urna. Sed sagittis arcu in mauris scelerisque vulputate. Sed sagittis mattis eros, eget hendrerit quam ultrices sed. Nunc at nulla tempor, aliquam velit quis, tempor libero. In id placerat metus. Praesent scelerisque semper justo a sollicitudin. In porta iaculis metus, vitae hendrerit diam tristique vitae. Integer quam nisi, faucibus id augue tincidunt, sagittis ornare libero. Cras placerat facilisis diam.\r\n\r\nNullam id laoreet mi. Suspendisse felis risus, porta eget sem et, posuere euismod felis. Vestibulum nec augue enim. Phasellus sollicitudin sit amet eros ut rutrum. Donec placerat elit nec tortor interdum malesuada. Maecenas efficitur orci id molestie interdum. Donec tempor sed diam ac viverra. Nulla facilisi. Proin mollis, nunc posuere venenatis dictum, mi eros egestas dui, et condimentum orci metus nec orci. Quisque dignissim felis vel porta rutrum. Fusce quis venenatis purus. Proin elit libero, scelerisque ac scelerisque ut, tincidunt id sem.\r\n\r\nDonec facilisis vitae nisi in rutrum. Curabitur lobortis libero sed dictum tincidunt. Quisque eget sem elit. Morbi quis sagittis ante. Suspendisse tortor nulla, tempor at iaculis non, sollicitudin sed ligula. Curabitur sed lectus eu augue suscipit viverra. Etiam scelerisque vestibulum accumsan. Nam pharetra est nulla, nec fringilla ex sagittis vitae. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Quisque ac felis augue. Curabitur quis velit eget velit iaculis molestie. Proin sollicitudin sem risus, vel convallis dolor consequat ac. Sed interdum molestie accumsan.\r\n\r\nMaecenas sed quam dapibus nisi varius bibendum eu at dui. Donec erat velit, volutpat eget rhoncus sed, commodo non magna. Duis ultricies mattis tellus, ac lobortis urna dapibus vel. Ut feugiat eu eros ut cursus. Praesent interdum eros id dictum dictum. Vestibulum magna quam, posuere eu diam sit amet, lobortis sodales augue. Maecenas quis mi non nunc interdum ornare. Mauris tempor mauris nisi, vitae pellentesque augue pulvinar et.\r\n\r\nMaecenas pretium nulla gravida, condimentum quam ac, lobortis lorem. Aliquam ante ipsum, fermentum sit amet maximus a, mattis sed velit. Proin maximus sit amet turpis nec efficitur. Ut erat nulla, aliquet ut quam sed, feugiat mattis nisl. Vestibulum convallis, lorem in convallis lacinia, arcu nibh dictum sem, id posuere tellus nibh sed ligula. Donec ut urna nec nisi imperdiet convallis non nec lacus. Integer dictum nulla nec lobortis pellentesque.\r\n\r\nIn ac neque arcu. Fusce porta sapien fringilla, maximus ex et, consequat massa. Curabitur congue justo non massa cursus ornare. Integer efficitur vehicula posuere. Nunc pulvinar, metus sit amet laoreet cursus, enim eros congue risus, a vulputate nisi diam id lacus. Nullam elementum dolor a lobortis malesuada. Integer ante est, facilisis eu felis nec, tristique venenatis enim. Aliquam blandit luctus orci nec gravida. Fusce dictum sit amet magna eu fermentum. Duis vel massa vel neque elementum ornare in et velit. Pellentesque dapibus eleifend vulputate. Curabitur id sagittis metus. Curabitur feugiat iaculis turpis, eu euismod mauris gravida ac. Nulla ut tortor lobortis, venenatis neque nec, suscipit purus.\r\n\r\nQuisque nisl felis, varius in augue at, elementum scelerisque enim. Nam a maximus urna, in sagittis lorem. In hac habitasse platea dictumst. Sed tristique nisi eget vestibulum varius. Curabitur nec turpis ex. Quisque eu semper tellus. Suspendisse augue augue, posuere ac facilisis nec, porta vulputate velit. Aliquam leo dui, condimentum at posuere ut, mattis et enim. Ut luctus consequat massa sed placerat. Praesent tellus nunc, dignissim eget tincidunt nec, tincidunt blandit risus. Suspendisse quis orci turpis. Quisque ut vehicula nunc, non convallis felis. In interdum vestibulum tellus, et mattis nunc eleifend in. Integer quam sem, posuere et imperdiet a, gravida et lectus.', '2023-04-20'),
(7, 'Lorem ipsum dolor sit amet', 'Fusce ligula sem, suscipit eget dui vitae, dignissim malesuada odio.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam eget lacus et ligula iaculis viverra. Curabitur elementum varius ex. Nulla facilisi. Praesent sit amet posuere justo. Donec tempor leo eu erat venenatis, quis pretium lectus accumsan. Aliquam erat volutpat. Aliquam id sem nunc. Donec semper in ante vitae ultrices. Phasellus in porta tellus. Nulla velit dolor, mollis eu ligula auctor, aliquam tincidunt ex.\r\n\r\nMaecenas vel massa at mi dapibus eleifend non in nulla. Suspendisse magna orci, laoreet a ultrices quis, hendrerit varius urna. Sed sagittis arcu in mauris scelerisque vulputate. Sed sagittis mattis eros, eget hendrerit quam ultrices sed. Nunc at nulla tempor, aliquam velit quis, tempor libero. In id placerat metus. Praesent scelerisque semper justo a sollicitudin. In porta iaculis metus, vitae hendrerit diam tristique vitae. Integer quam nisi, faucibus id augue tincidunt, sagittis ornare libero. Cras placerat facilisis diam.\r\n\r\nNullam id laoreet mi. Suspendisse felis risus, porta eget sem et, posuere euismod felis. Vestibulum nec augue enim. Phasellus sollicitudin sit amet eros ut rutrum. Donec placerat elit nec tortor interdum malesuada. Maecenas efficitur orci id molestie interdum. Donec tempor sed diam ac viverra. Nulla facilisi. Proin mollis, nunc posuere venenatis dictum, mi eros egestas dui, et condimentum orci metus nec orci. Quisque dignissim felis vel porta rutrum. Fusce quis venenatis purus. Proin elit libero, scelerisque ac scelerisque ut, tincidunt id sem.\r\n\r\nDonec facilisis vitae nisi in rutrum. Curabitur lobortis libero sed dictum tincidunt. Quisque eget sem elit. Morbi quis sagittis ante. Suspendisse tortor nulla, tempor at iaculis non, sollicitudin sed ligula. Curabitur sed lectus eu augue suscipit viverra. Etiam scelerisque vestibulum accumsan. Nam pharetra est nulla, nec fringilla ex sagittis vitae. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Quisque ac felis augue. Curabitur quis velit eget velit iaculis molestie. Proin sollicitudin sem risus, vel convallis dolor consequat ac. Sed interdum molestie accumsan.\r\n\r\nMaecenas sed quam dapibus nisi varius bibendum eu at dui. Donec erat velit, volutpat eget rhoncus sed, commodo non magna. Duis ultricies mattis tellus, ac lobortis urna dapibus vel. Ut feugiat eu eros ut cursus. Praesent interdum eros id dictum dictum. Vestibulum magna quam, posuere eu diam sit amet, lobortis sodales augue. Maecenas quis mi non nunc interdum ornare. Mauris tempor mauris nisi, vitae pellentesque augue pulvinar et.\r\n\r\nMaecenas pretium nulla gravida, condimentum quam ac, lobortis lorem. Aliquam ante ipsum, fermentum sit amet maximus a, mattis sed velit. Proin maximus sit amet turpis nec efficitur. Ut erat nulla, aliquet ut quam sed, feugiat mattis nisl. Vestibulum convallis, lorem in convallis lacinia, arcu nibh dictum sem, id posuere tellus nibh sed ligula. Donec ut urna nec nisi imperdiet convallis non nec lacus. Integer dictum nulla nec lobortis pellentesque.\r\n\r\nIn ac neque arcu. Fusce porta sapien fringilla, maximus ex et, consequat massa. Curabitur congue justo non massa cursus ornare. Integer efficitur vehicula posuere. Nunc pulvinar, metus sit amet laoreet cursus, enim eros congue risus, a vulputate nisi diam id lacus. Nullam elementum dolor a lobortis malesuada. Integer ante est, facilisis eu felis nec, tristique venenatis enim. Aliquam blandit luctus orci nec gravida. Fusce dictum sit amet magna eu fermentum. Duis vel massa vel neque elementum ornare in et velit. Pellentesque dapibus eleifend vulputate. Curabitur id sagittis metus. Curabitur feugiat iaculis turpis, eu euismod mauris gravida ac. Nulla ut tortor lobortis, venenatis neque nec, suscipit purus.\r\n\r\nQuisque nisl felis, varius in augue at, elementum scelerisque enim. Nam a maximus urna, in sagittis lorem. In hac habitasse platea dictumst. Sed tristique nisi eget vestibulum varius. Curabitur nec turpis ex. Quisque eu semper tellus. Suspendisse augue augue, posuere ac facilisis nec, porta vulputate velit. Aliquam leo dui, condimentum at posuere ut, mattis et enim. Ut luctus consequat massa sed placerat. Praesent tellus nunc, dignissim eget tincidunt nec, tincidunt blandit risus. Suspendisse quis orci turpis. Quisque ut vehicula nunc, non convallis felis. In interdum vestibulum tellus, et mattis nunc eleifend in. Integer quam sem, posuere et imperdiet a, gravida et lectus.', '2023-04-25'),
(8, 'Lorem ipsum dolor sit amet', 'Fusce ligula sem, suscipit eget dui vitae, dignissim malesuada odio.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam eget lacus et ligula iaculis viverra. Curabitur elementum varius ex. Nulla facilisi. Praesent sit amet posuere justo. Donec tempor leo eu erat venenatis, quis pretium lectus accumsan. Aliquam erat volutpat. Aliquam id sem nunc. Donec semper in ante vitae ultrices. Phasellus in porta tellus. Nulla velit dolor, mollis eu ligula auctor, aliquam tincidunt ex.\r\n\r\nMaecenas vel massa at mi dapibus eleifend non in nulla. Suspendisse magna orci, laoreet a ultrices quis, hendrerit varius urna. Sed sagittis arcu in mauris scelerisque vulputate. Sed sagittis mattis eros, eget hendrerit quam ultrices sed. Nunc at nulla tempor, aliquam velit quis, tempor libero. In id placerat metus. Praesent scelerisque semper justo a sollicitudin. In porta iaculis metus, vitae hendrerit diam tristique vitae. Integer quam nisi, faucibus id augue tincidunt, sagittis ornare libero. Cras placerat facilisis diam.\r\n\r\nNullam id laoreet mi. Suspendisse felis risus, porta eget sem et, posuere euismod felis. Vestibulum nec augue enim. Phasellus sollicitudin sit amet eros ut rutrum. Donec placerat elit nec tortor interdum malesuada. Maecenas efficitur orci id molestie interdum. Donec tempor sed diam ac viverra. Nulla facilisi. Proin mollis, nunc posuere venenatis dictum, mi eros egestas dui, et condimentum orci metus nec orci. Quisque dignissim felis vel porta rutrum. Fusce quis venenatis purus. Proin elit libero, scelerisque ac scelerisque ut, tincidunt id sem.\r\n\r\nDonec facilisis vitae nisi in rutrum. Curabitur lobortis libero sed dictum tincidunt. Quisque eget sem elit. Morbi quis sagittis ante. Suspendisse tortor nulla, tempor at iaculis non, sollicitudin sed ligula. Curabitur sed lectus eu augue suscipit viverra. Etiam scelerisque vestibulum accumsan. Nam pharetra est nulla, nec fringilla ex sagittis vitae. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Quisque ac felis augue. Curabitur quis velit eget velit iaculis molestie. Proin sollicitudin sem risus, vel convallis dolor consequat ac. Sed interdum molestie accumsan.\r\n\r\nMaecenas sed quam dapibus nisi varius bibendum eu at dui. Donec erat velit, volutpat eget rhoncus sed, commodo non magna. Duis ultricies mattis tellus, ac lobortis urna dapibus vel. Ut feugiat eu eros ut cursus. Praesent interdum eros id dictum dictum. Vestibulum magna quam, posuere eu diam sit amet, lobortis sodales augue. Maecenas quis mi non nunc interdum ornare. Mauris tempor mauris nisi, vitae pellentesque augue pulvinar et.\r\n\r\nMaecenas pretium nulla gravida, condimentum quam ac, lobortis lorem. Aliquam ante ipsum, fermentum sit amet maximus a, mattis sed velit. Proin maximus sit amet turpis nec efficitur. Ut erat nulla, aliquet ut quam sed, feugiat mattis nisl. Vestibulum convallis, lorem in convallis lacinia, arcu nibh dictum sem, id posuere tellus nibh sed ligula. Donec ut urna nec nisi imperdiet convallis non nec lacus. Integer dictum nulla nec lobortis pellentesque.\r\n\r\nIn ac neque arcu. Fusce porta sapien fringilla, maximus ex et, consequat massa. Curabitur congue justo non massa cursus ornare. Integer efficitur vehicula posuere. Nunc pulvinar, metus sit amet laoreet cursus, enim eros congue risus, a vulputate nisi diam id lacus. Nullam elementum dolor a lobortis malesuada. Integer ante est, facilisis eu felis nec, tristique venenatis enim. Aliquam blandit luctus orci nec gravida. Fusce dictum sit amet magna eu fermentum. Duis vel massa vel neque elementum ornare in et velit. Pellentesque dapibus eleifend vulputate. Curabitur id sagittis metus. Curabitur feugiat iaculis turpis, eu euismod mauris gravida ac. Nulla ut tortor lobortis, venenatis neque nec, suscipit purus.\r\n\r\nQuisque nisl felis, varius in augue at, elementum scelerisque enim. Nam a maximus urna, in sagittis lorem. In hac habitasse platea dictumst. Sed tristique nisi eget vestibulum varius. Curabitur nec turpis ex. Quisque eu semper tellus. Suspendisse augue augue, posuere ac facilisis nec, porta vulputate velit. Aliquam leo dui, condimentum at posuere ut, mattis et enim. Ut luctus consequat massa sed placerat. Praesent tellus nunc, dignissim eget tincidunt nec, tincidunt blandit risus. Suspendisse quis orci turpis. Quisque ut vehicula nunc, non convallis felis. In interdum vestibulum tellus, et mattis nunc eleifend in. Integer quam sem, posuere et imperdiet a, gravida et lectus.', '2023-05-10'),
(9, 'Lorem ipsum dolor sit amet', 'Fusce ligula sem, suscipit eget dui vitae, dignissim malesuada odio.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam eget lacus et ligula iaculis viverra. Curabitur elementum varius ex. Nulla facilisi. Praesent sit amet posuere justo. Donec tempor leo eu erat venenatis, quis pretium lectus accumsan. Aliquam erat volutpat. Aliquam id sem nunc. Donec semper in ante vitae ultrices. Phasellus in porta tellus. Nulla velit dolor, mollis eu ligula auctor, aliquam tincidunt ex.\r\n\r\nMaecenas vel massa at mi dapibus eleifend non in nulla. Suspendisse magna orci, laoreet a ultrices quis, hendrerit varius urna. Sed sagittis arcu in mauris scelerisque vulputate. Sed sagittis mattis eros, eget hendrerit quam ultrices sed. Nunc at nulla tempor, aliquam velit quis, tempor libero. In id placerat metus. Praesent scelerisque semper justo a sollicitudin. In porta iaculis metus, vitae hendrerit diam tristique vitae. Integer quam nisi, faucibus id augue tincidunt, sagittis ornare libero. Cras placerat facilisis diam.\r\n\r\nNullam id laoreet mi. Suspendisse felis risus, porta eget sem et, posuere euismod felis. Vestibulum nec augue enim. Phasellus sollicitudin sit amet eros ut rutrum. Donec placerat elit nec tortor interdum malesuada. Maecenas efficitur orci id molestie interdum. Donec tempor sed diam ac viverra. Nulla facilisi. Proin mollis, nunc posuere venenatis dictum, mi eros egestas dui, et condimentum orci metus nec orci. Quisque dignissim felis vel porta rutrum. Fusce quis venenatis purus. Proin elit libero, scelerisque ac scelerisque ut, tincidunt id sem.\r\n\r\nDonec facilisis vitae nisi in rutrum. Curabitur lobortis libero sed dictum tincidunt. Quisque eget sem elit. Morbi quis sagittis ante. Suspendisse tortor nulla, tempor at iaculis non, sollicitudin sed ligula. Curabitur sed lectus eu augue suscipit viverra. Etiam scelerisque vestibulum accumsan. Nam pharetra est nulla, nec fringilla ex sagittis vitae. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Quisque ac felis augue. Curabitur quis velit eget velit iaculis molestie. Proin sollicitudin sem risus, vel convallis dolor consequat ac. Sed interdum molestie accumsan.\r\n\r\nMaecenas sed quam dapibus nisi varius bibendum eu at dui. Donec erat velit, volutpat eget rhoncus sed, commodo non magna. Duis ultricies mattis tellus, ac lobortis urna dapibus vel. Ut feugiat eu eros ut cursus. Praesent interdum eros id dictum dictum. Vestibulum magna quam, posuere eu diam sit amet, lobortis sodales augue. Maecenas quis mi non nunc interdum ornare. Mauris tempor mauris nisi, vitae pellentesque augue pulvinar et.\r\n\r\nMaecenas pretium nulla gravida, condimentum quam ac, lobortis lorem. Aliquam ante ipsum, fermentum sit amet maximus a, mattis sed velit. Proin maximus sit amet turpis nec efficitur. Ut erat nulla, aliquet ut quam sed, feugiat mattis nisl. Vestibulum convallis, lorem in convallis lacinia, arcu nibh dictum sem, id posuere tellus nibh sed ligula. Donec ut urna nec nisi imperdiet convallis non nec lacus. Integer dictum nulla nec lobortis pellentesque.\r\n\r\nIn ac neque arcu. Fusce porta sapien fringilla, maximus ex et, consequat massa. Curabitur congue justo non massa cursus ornare. Integer efficitur vehicula posuere. Nunc pulvinar, metus sit amet laoreet cursus, enim eros congue risus, a vulputate nisi diam id lacus. Nullam elementum dolor a lobortis malesuada. Integer ante est, facilisis eu felis nec, tristique venenatis enim. Aliquam blandit luctus orci nec gravida. Fusce dictum sit amet magna eu fermentum. Duis vel massa vel neque elementum ornare in et velit. Pellentesque dapibus eleifend vulputate. Curabitur id sagittis metus. Curabitur feugiat iaculis turpis, eu euismod mauris gravida ac. Nulla ut tortor lobortis, venenatis neque nec, suscipit purus.\r\n\r\nQuisque nisl felis, varius in augue at, elementum scelerisque enim. Nam a maximus urna, in sagittis lorem. In hac habitasse platea dictumst. Sed tristique nisi eget vestibulum varius. Curabitur nec turpis ex. Quisque eu semper tellus. Suspendisse augue augue, posuere ac facilisis nec, porta vulputate velit. Aliquam leo dui, condimentum at posuere ut, mattis et enim. Ut luctus consequat massa sed placerat. Praesent tellus nunc, dignissim eget tincidunt nec, tincidunt blandit risus. Suspendisse quis orci turpis. Quisque ut vehicula nunc, non convallis felis. In interdum vestibulum tellus, et mattis nunc eleifend in. Integer quam sem, posuere et imperdiet a, gravida et lectus.', '2023-05-15'),
(10, 'Prueba Funcional de publicación de una noticia', 'Prueba Funcional de publicación de una noticia', 'Prueba Funcional de publicación de una noticia', '2024-01-14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `DiaEspecialID` int(11) NOT NULL,
  `TipoPermiso` int(11) NOT NULL,
  `FechaInicio` date DEFAULT NULL,
  `FechaFin` date DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `comentario` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipospermisos`
--

CREATE TABLE `tipospermisos` (
  `IdPermiso` int(11) NOT NULL,
  `Descripcion` varchar(255) NOT NULL,
  `abreviatura` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipospermisos`
--

INSERT INTO `tipospermisos` (`IdPermiso`, `Descripcion`, `abreviatura`) VALUES
(1, 'Vacaciones', 'VA'),
(2, 'DLD', 'DL'),
(3, 'Matrimonio', 'MA'),
(4, 'Hospitalización', 'HO'),
(6, 'Nacimiento hijo', 'NA'),
(7, 'Preparación del parto', 'PA'),
(8, 'Lactancia', 'LA'),
(9, 'Mudanza', 'MU'),
(10, 'Sindicato', 'SI');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipossalidas`
--

CREATE TABLE `tipossalidas` (
  `tipoId` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `Abreviatura` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipossalidas`
--

INSERT INTO `tipossalidas` (`tipoId`, `tipo`, `Abreviatura`) VALUES
(1, 'Salida normal', 'SN'),
(2, 'No se fichó salida descanso', 'ERR-DES'),
(3, 'Falta fichar salida', 'ERR-SAL');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `correo_electronico` varchar(255) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) DEFAULT NULL,
  `rol` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `correo_electronico`, `nombre_usuario`, `contrasena`, `rol`) VALUES
(1, 'martalopez@gmail.com', 'martalopez', '1234', 'usuario'),
(2, 'alejandrorodriguez@gmail.com', 'alejandrorodriguez', '1234', 'trabajador'),
(4, 'sofiaramirez@gmail.com', 'sofiaramirez', '1234', 'responsable'),
(8, 'gabrielfernandez@gmail.com', 'gabrielfernandez', '1234', 'administrador');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alineacion`
--
ALTER TABLE `alineacion`
  ADD PRIMARY KEY (`id_partido`,`id_jugador`),
  ADD KEY `id_partido` (`id_partido`),
  ADD KEY `id_jugador` (`id_jugador`);

--
-- Indices de la tabla `enfrentamientos`
--
ALTER TABLE `enfrentamientos`
  ADD PRIMARY KEY (`id_partido`),
  ADD KEY `equipo_local_id` (`equipo_local_id`),
  ADD KEY `equipo_visitante_id` (`equipo_visitante_id`),
  ADD KEY `jornada` (`jornada`);

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`equipo_id`);

--
-- Indices de la tabla `fichaje`
--
ALTER TABLE `fichaje`
  ADD PRIMARY KEY (`FichajeID`),
  ADD KEY `TipoSalida` (`TipoSalida`),
  ADD KEY `id_usuario` (`id_usuario`) USING BTREE;

--
-- Indices de la tabla `goles`
--
ALTER TABLE `goles`
  ADD PRIMARY KEY (`id_gol`),
  ADD KEY `id_partido` (`id_partido`),
  ADD KEY `id_jugador` (`id_jugador`),
  ADD KEY `id_equipo` (`id_equipo`);

--
-- Indices de la tabla `jornadas`
--
ALTER TABLE `jornadas`
  ADD PRIMARY KEY (`jornada`);

--
-- Indices de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  ADD PRIMARY KEY (`id_jugador`,`equipo_id`),
  ADD KEY `equipo_id` (`equipo_id`);

--
-- Indices de la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`DiaEspecialID`),
  ADD KEY `EmpleadoID` (`id_usuario`),
  ADD KEY `TipoPermiso` (`TipoPermiso`);

--
-- Indices de la tabla `tipospermisos`
--
ALTER TABLE `tipospermisos`
  ADD PRIMARY KEY (`IdPermiso`);

--
-- Indices de la tabla `tipossalidas`
--
ALTER TABLE `tipossalidas`
  ADD PRIMARY KEY (`tipoId`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `enfrentamientos`
--
ALTER TABLE `enfrentamientos`
  MODIFY `id_partido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `equipo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `fichaje`
--
ALTER TABLE `fichaje`
  MODIFY `FichajeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT de la tabla `goles`
--
ALTER TABLE `goles`
  MODIFY `id_gol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `DiaEspecialID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT de la tabla `tipossalidas`
--
ALTER TABLE `tipossalidas`
  MODIFY `tipoId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alineacion`
--
ALTER TABLE `alineacion`
  ADD CONSTRAINT `alineacion_ibfk_1` FOREIGN KEY (`id_partido`) REFERENCES `enfrentamientos` (`id_partido`),
  ADD CONSTRAINT `alineacion_ibfk_2` FOREIGN KEY (`id_jugador`) REFERENCES `jugadores` (`id_jugador`);

--
-- Filtros para la tabla `enfrentamientos`
--
ALTER TABLE `enfrentamientos`
  ADD CONSTRAINT `enfrentamientos_ibfk_1` FOREIGN KEY (`equipo_local_id`) REFERENCES `equipos` (`equipo_id`),
  ADD CONSTRAINT `enfrentamientos_ibfk_2` FOREIGN KEY (`equipo_visitante_id`) REFERENCES `equipos` (`equipo_id`),
  ADD CONSTRAINT `enfrentamientos_ibfk_3` FOREIGN KEY (`jornada`) REFERENCES `jornadas` (`jornada`);

--
-- Filtros para la tabla `fichaje`
--
ALTER TABLE `fichaje`
  ADD CONSTRAINT `fichaje_ibfk_2` FOREIGN KEY (`TipoSalida`) REFERENCES `tipossalidas` (`tipoId`),
  ADD CONSTRAINT `fichaje_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `goles`
--
ALTER TABLE `goles`
  ADD CONSTRAINT `goles_ibfk_1` FOREIGN KEY (`id_partido`) REFERENCES `enfrentamientos` (`id_partido`),
  ADD CONSTRAINT `goles_ibfk_2` FOREIGN KEY (`id_jugador`) REFERENCES `jugadores` (`id_jugador`),
  ADD CONSTRAINT `goles_ibfk_3` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`equipo_id`);

--
-- Filtros para la tabla `jugadores`
--
ALTER TABLE `jugadores`
  ADD CONSTRAINT `jugadores_ibfk_1` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`equipo_id`);

--
-- Filtros para la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD CONSTRAINT `permisos_ibfk_2` FOREIGN KEY (`TipoPermiso`) REFERENCES `tipospermisos` (`IdPermiso`),
  ADD CONSTRAINT `permisos_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`root`@`localhost` EVENT `GestionErroresSalida` ON SCHEDULE EVERY 1 DAY STARTS '2023-11-13 22:00:01' ON COMPLETION NOT PRESERVE ENABLE DO CALL GestionErrores()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
