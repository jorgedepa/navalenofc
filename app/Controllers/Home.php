<?php

namespace App\Controllers;

// Para usar MiModelo
use App\models\MiModelo;

class Home extends BaseController
{
    public function index(): string
    {
        $modelo = new MiModelo();
        $datos['noticias'] = $modelo->dameTresNoticias();
        $datos['seisNoticias'] = $modelo->dameSeisNoticias();
        $datos['clasificacion'] = $modelo->clasificacion();
        $datos['partidoAnterior'] = $modelo->damePartidoAnterior();
        $datos['proximoPartido'] = $modelo->dameProximoPartido();
        $datos['siguientePartido'] = $modelo->dameSiguientePartido();


        return view('index', $datos);
    }
    public function login(): string
    {
        return view('login');
    }
    public function registro(): string
    {
        return view('registro');
    }
    public function noticias(): string
    {
        $modelo = new MiModelo();
        $noticias = $modelo->dameNoticias();
        $datos['noticias'] = $noticias;
        return view('noticias', $datos);
    }
    public function noticia(): string
    {
        $modelo = new MiModelo();
        $id = urldecode($_GET['id']);
        $noticia = $modelo->dameNoticia($id);
        $datos['noticia'] = $noticia;


        return view('noticia', $datos);
    }
    public function partidos(): string
    {
        $modelo = new MiModelo();
        $mes = date('m');
        session()->set('mesSeleccionado', $mes);
        $datos['calendario'] = $modelo->calendarioMes($mes);

        return view('partidos', $datos);
    }
    public function filtroPartidos(): string
    {
        $modelo = new MiModelo();
        $mes = $this->request->getPost('mes');
        session()->set('mesSeleccionado', $mes);

        $datos['calendario'] = $modelo->calendarioMes($mes);
        return view('partidos', $datos);
    }
    public function partido(): string
    {
        $modelo = new MiModelo();
        $idPartido = urldecode($_GET['id']);
        $datos['equipoLocal'] = $modelo->equipoLocal($idPartido);
        $datos['nombreEquipoLocal'] = $modelo->nombreEquipoLocal($idPartido);
        $datos['equipoVisitante'] = $modelo->equipoVisitante($idPartido);
        $datos['nombreEquipoVisitante'] = $modelo->nombreEquipoVisitante($idPartido);
        $datos['estado'] = $modelo->estadoPartido($idPartido);
        $datos['golesEquipoLocal'] = $modelo->golesEquipoLocal($idPartido);
        $datos['golesEquipoVisitante'] = $modelo->golesEquipoVisitante($idPartido);
        $datos['secuenciaGoles'] = $modelo->secuenciaGoles($idPartido);


        return view('partido', $datos);
    }
    public function clasificacion(): string
    {
        $modelo = new MiModelo();
        $datos['clasificacion'] = $modelo->clasificacion();
        return view('clasificacion', $datos);
    }
    public function video(): string
    {
        return view('video');
    }

    public function crearUsuario(): string
    {
        $nombreUsuario = $this->request->getPost('usuario');
        $correo = $this->request->getPost('correo');
        $contrasena = $this->request->getPost('contrasena');
        $modelo = new miModelo();
        $modelo->crearUsuario($correo, $contrasena, $nombreUsuario);
        return view('login');
    }
    // Metodo para cuando se desea entrar (formulario de login)
    public function entrar(): string
    {
        // Recojo datos del formulario de login
        $correo = $this->request->getPost('correo');
        $contrasena = $this->request->getPost('contrasena');

        // Le pregunto al modelo si existe esa cuenta
        $modelo = new MiModelo();
        $usuario = $modelo->comprueba($correo, $contrasena);

        // Si no existe, a la página de inicio
        if ($usuario == 0) {
            return view('login');
        } else {
            $tipoUsuario = $modelo->queEs($usuario);
            session()->set('usuario', $usuario);
            session()->set('nombreUsuario', $modelo->dimeNombreUsuario($usuario));

            switch ($tipoUsuario) {
                case "usuario":
                    return $this->index();

                case "administrador":
                    return view('admin');
                case "responsable":
                    $datos['dia1'] = date("Y-m-d");
                    $datos['dia2'] = date("Y-m-d");
                    $datos['empleadosFecha'] = $modelo->empleadosFecha($datos['dia1'], $datos['dia2']);
                    return view('responsable', $datos);

                case "trabajador":
                    $datos['opcionesSalida'] =  $modelo->opcionesSalida();
                    $datos['horas'] =  $modelo->verHoras($usuario);

                    if (date("w") == 0 || date("w") == 6) {
                        $datos['queMostrar'] = "findesemana";
                    } elseif ($modelo->estaDePermiso($usuario)) {
                        $datos['queMostrar'] = "permiso";
                    } else {
                        $datos['queMostrar'] = $modelo->mostrarBotonEntrar($usuario);
                    }

                    return view('trabajador', $datos);
            }
        }
    }


    public function cerrarSesion(): string
    {
        session()->remove('usuario');
        session()->remove('nombreUsuario');
        session()->destroy();
        return $this->index();
    }
    public function plantilla(): string
    {
        $modelo = new MiModelo();
        $datos['plantilla'] = $modelo->plantilla();
        return view('plantilla', $datos);
    }

    public function jugador(): string
    {
        $modelo = new MiModelo();
        $idJugador = urldecode($_GET['id']);
        $datos['nombre'] = $modelo->nombreJugador($idJugador);
        $datos['goles'] = $modelo->golesJugador($idJugador);
        $datos['partidos'] = $modelo->partidosJugadosJugador($idJugador);
        return view('jugador', $datos);
    }
    // Parte de fichajes
    public function registroFichaje(): string
    {
        $usuario = session()->get('usuario');
        $modelo = new MiModelo();
        if (!empty($_POST['entradaFichaje'])) {
            $modelo->insertarEntrada($usuario);
        } elseif (!empty($_POST['salidaFichaje'])) {
            $modelo->insertarSalida($usuario);
        } elseif (!empty($_POST['inicioPausa'])) {
            $modelo->insertarInicioPausa($usuario);
        } elseif (!empty($_POST['finPausa'])) {
            $modelo->insertarFinPausa($usuario);
        }

        $datos['opcionesSalida'] =  $modelo->opcionesSalida();
        $datos['horas'] =  $modelo->verHoras($usuario);

        if (date("w") == 0 || date("w") == 6) {
            $datos['queMostrar'] = "findesemana";
        } elseif ($modelo->estaDePermiso($usuario)) {
            $datos['queMostrar'] = "permiso";
        } else {
            $datos['queMostrar'] = $modelo->mostrarBotonEntrar($usuario);
        }

        return view('trabajador', $datos);
    }

    public function inicioResponsable(): string
    {
        $modelo = new MiModelo();
        $dia1 = date("Y-m-d");
        $dia2 = date("Y-m-d");
        session()->set('dia1', $dia1);
        session()->set('dia2', $dia2);

        $datos['empleadosFecha'] = $modelo->empleadosFecha($dia1, $dia2);
        return view('responsable', $datos);
    }
    public function empleados(): string
    {
        $modelo = new MiModelo();
        $datos['empleados'] = $modelo->empleados();
        return view('empleados', $datos);
    }

    public function actualizarEmpleado(): string
    {
        $modelo = new MiModelo();
        $idUsuario = $this->request->getPost('idUsuario');
        if (isset($_POST['correo'])) {
            $correo = $this->request->getPost('correo');
            $modelo->actualizarCorreo($idUsuario, $correo);
        } elseif (isset($_POST['usuario'])) {
            $usuario = $this->request->getPost('usuario');
            $modelo->actualizarUsuario($idUsuario, $usuario);
        } elseif (isset($_POST['contrasena'])) {
            $contrasena = $this->request->getPost('contrasena');
            $modelo->actualizarContrasena($idUsuario, $contrasena);
        } elseif (isset($_POST['rol'])) {
            $rol = $this->request->getPost('rol');
            $modelo->actualizarRol($idUsuario, $rol);
        } elseif (isset($_POST['borrar'])) {
            $modelo->borrarRegistroEmpleado($idUsuario);
        } elseif (isset($_POST['enviarI'])) {
            $correo = $this->request->getPost('correoI');
            $usuario = $this->request->getPost('usuarioI');
            $contrasena = $this->request->getPost('contrasenaI');
            $rol = $this->request->getPost('rolI');
            $modelo->insertarRegistroEmpleado($correo, $usuario, $contrasena, $rol);
        }


        $datos['empleados'] = $modelo->empleados();
        return view('empleados', $datos);
    }

    public function rangoFechas(): string
    {
        $modelo = new MiModelo();
        $dia1 = $this->request->getPost('fecha_inicio');
        $dia2 = $this->request->getPost('fecha_fin');
        session()->set('dia1', $dia1);
        session()->set('dia2', $dia2);
        $datos['empleadosFecha'] = $modelo->empleadosFecha($dia1, $dia2);
        return view('responsable', $datos);
    }
    public function crud(): string
    {
        $modelo = new MiModelo();
        $dia1 = urldecode($_GET['dia1']);
        $dia2 = urldecode($_GET['dia2']);

        session()->set('dia1', $dia1);
        session()->set('dia2', $dia2);
        session()->set('empleado', urldecode($_GET['empleado']));

        $datos['fichajesDia'] = $modelo->fichajesDia(urldecode($_GET['empleado']), $dia1, $dia2);
        return view('crud', $datos);
    }
    public function rangoFechasCrud(): string
    {
        $modelo = new MiModelo();

        $dia1 = $this->request->getPost('fecha_inicio');
        $dia2 = $this->request->getPost('fecha_fin');
        session()->set('dia1', $dia1);
        session()->set('dia2', $dia2);
        $datos['fichajesDia'] = $modelo->fichajesDia(session()->get('empleado'), $dia1, $dia2);
        return view('crud', $datos);
    }

    public function actualizar(): string
    {
        $modelo = new MiModelo();
        $dia1 = session()->get('dia1');
        $dia2 = session()->get('dia2');
        $fichajeId = $this->request->getPost('FichajeID');
        $empleado = session()->get('empleado');

        if (isset($_POST['fechaEntrada'])) {
            $fechaEntrada = $this->request->getPost('fechaEntrada');
            $modelo->actualizarFechaEntrada($fichajeId, $fechaEntrada);
        } elseif (isset($_POST['fechaSalida'])) {
            $fechaSalida = $this->request->getPost('fechaSalida');
            $modelo->actualizarFechaSalida($fichajeId,  $fechaSalida);
        } elseif (isset($_POST['inicioPausa'])) {
            $inicioPausa = $this->request->getPost('inicioPausa');
            $modelo->actualizarInicioPausa($fichajeId, $inicioPausa);
        } elseif (isset($_POST['finPausa'])) {
            $finPausa = $this->request->getPost('finPausa');
            $modelo->actualizarFinPausa($fichajeId, $finPausa);
        } elseif (isset($_POST['salida'])) {
            $salida = $this->request->getPost('salida');
            $modelo->actualizarTipoSalida($fichajeId, $salida);
        } elseif (isset($_POST['justificacion'])) {
            $justificacion = $this->request->getPost('justificacion');
            $modelo->actualizarJustificacion($fichajeId, $justificacion);
        } elseif (isset($_POST['borrar'])) {
            $modelo->borrarRegistro($fichajeId);
        } elseif (isset($_POST['enviarI'])) {
            $fechaEntrada = $this->request->getPost('fechaEntradaI');
            if ($fechaEntrada == "") {
                $fechaEntrada = "null";
            } else {
                $fechaEntrada = "'" . $fechaEntrada . "'";
            }
            $fechaSalida = $this->request->getPost('fechaSalidaI');;
            if ($fechaSalida == "") {
                $fechaSalida = "null";
            } else {
                $fechaSalida = "'" . $fechaSalida . "'";
            }
            $inicioPausa = $this->request->getPost('inicioPausaI');
            if ($inicioPausa == "") {
                $inicioPausa = "null";
            } else {
                $inicioPausa = "'" . $inicioPausa . "'";
            }
            $finPausa = $this->request->getPost('finPausaI');
            if ($finPausa == "") {
                $finPausa = "null";
            } else {
                $finPausa = "'" . $finPausa . "'";
            }

            $salida = $this->request->getPost('salidaI');
            if ($salida == "") {
                $salida = "null";
            } else {
                $salida = "'" . $salida . "'";
            }

            $justificacion = $this->request->getPost('justificacionI');
            if ($justificacion == "") {
                $justificacion = "null";
            } else {
                $justificacion = "'" . $justificacion . "'";
            }
            $modelo->insertarRegistro($empleado, $fechaEntrada, $fechaSalida, $inicioPausa, $finPausa, $salida, $justificacion);
        }
        $datos['fichajesDia'] = $modelo->fichajesDia($empleado, $dia1, $dia2);
        return view('crud', $datos);
    }

    public function ausencia(): string
    {
        $modelo = new MiModelo();
        $empleado = session()->get('empleado');
        $datos['tipoPermisos'] = $modelo->obtenerTipoPermisos();
        $datos['nombreEmpleado'] = $modelo->dimeNombreUsuario($empleado);
        $datos['ausenciasEmpleado'] = $modelo->ausenciasEmpleado($empleado);
        return view('ausencia', $datos);
    }

    public function actualizarAusencia(): string
    {
        $modelo = new MiModelo();
        $empleado = session()->get('empleado');
        $diaEspecialId = $this->request->getPost('DiaEspecialID');
        if (isset($_POST['TipoAusencia'])) {
            $tipoAusencia = $this->request->getPost('TipoAusencia');
            $modelo->actualizarTipoAusencia($diaEspecialId,  $tipoAusencia);
        } elseif (isset($_POST['InicioPermiso'])) {
            $inicioPermiso = $this->request->getPost('InicioPermiso');
            $modelo->actualizarInicioAusencia($diaEspecialId, $inicioPermiso);
        } elseif (isset($_POST['FinPermiso'])) {
            $finPermiso = $this->request->getPost('FinPermiso');
            $modelo->actualizarFinAusencia($diaEspecialId, $finPermiso);
        } elseif (isset($_POST['Comentario'])) {
            $comentario = $this->request->getPost('Comentario');
            $modelo->actualizarComentario($diaEspecialId, $comentario);
            header("Location:ausencia.php");
        } elseif (isset($_POST['borrarAusencia'])) {
            $modelo->borrarAusencia($diaEspecialId);
        }
        if (isset($_POST['AnadirPermiso'])) {

            $tipoPermiso = $this->request->getPost('TipoAusenciaI');
            if ($tipoPermiso == "") {
                $tipoPermiso = "null";
            }

            $fechaEntrada = $this->request->getPost('InicioPermisoI');
            if ($fechaEntrada == "") {
                $fechaEntrada = "null";
            } else {
                $fechaEntrada = "'" . $fechaEntrada . "'";
            }

            $fechaSalida = $this->request->getPost('FinPermisoI');
            if ($fechaSalida == "") {
                $fechaSalida = "null";
            } else {
                $fechaSalida = "'" . $fechaSalida . "'";
            }

            $comentario = $this->request->getPost('Comentario');
            if ($comentario == "") {
                $comentario = "null";
            } else {
                $comentario = "'" .  $comentario . "'";
            }

            $modelo->insertarRegistroAusencia($tipoPermiso, $fechaEntrada, $fechaSalida, $empleado, $comentario);
        }
        return $this->ausencia();
    }

    // PARTE ADMINISTRADOR
    public function InicioAdmin(): string
    {
        return view('admin');
    }
    public function usuarios(): string
    {
        $modelo = new MiModelo();
        $datos['administradores'] = $modelo->admin();
        $datos['usuarios'] = $modelo->usuarios();
        return view('usuarios', $datos);
    }
    public function actualizarUsuario(): string
    {
        $modelo = new MiModelo();
        $idUsuario = $this->request->getPost('idUsuario');
        if (isset($_POST['correo'])) {
            $correo = $this->request->getPost('correo');
            $modelo->actualizarCorreo($idUsuario, $correo);
        } elseif (isset($_POST['usuario'])) {
            $usuario = $this->request->getPost('usuario');
            $modelo->actualizarUsuario($idUsuario, $usuario);
        } elseif (isset($_POST['contrasena'])) {
            $contrasena = $this->request->getPost('contrasena');
            $modelo->actualizarContrasena($idUsuario, $contrasena);
        } elseif (isset($_POST['borrar'])) {
            $modelo->borrarRegistroEmpleado($idUsuario);
        } elseif (isset($_POST['enviarI'])) {
            $correo = $this->request->getPost('correoI');
            $usuario = $this->request->getPost('usuarioI');
            $contrasena = $this->request->getPost('contrasenaI');
            $rol = $this->request->getPost('rolI');
            $modelo->insertarRegistroEmpleado($correo, $usuario, $contrasena, $rol);
        }
        return $this->usuarios();
    }
    public function administrarNoticia(): string
    {
        $modelo = new MiModelo();
        $datos['noticias'] = $modelo->dameNoticias();

        return view('administrarNoticia', $datos);
    }

    public function actualizarNoticia(): string
    {
        $modelo = new MiModelo();
        if (isset($_POST['publicar'])) {
            $numNoticia = $modelo->dimeNumeroNoticia();
            copy($_FILES['archivo']['tmp_name'], "images/noticias/" . $numNoticia . ".jpg");

            $titular = $this->request->getPost('titular');
            $subtitulo = $this->request->getPost('subtitulo');
            $cuerpo = $this->request->getPost('cuerpo');
            $modelo->insertarNoticia($numNoticia, $titular, $subtitulo, $cuerpo);
        } elseif (isset($_POST['borrar'])) {
            $id = $this->request->getPost('idNoticia');
            $modelo->borrarNoticia($id);
            unlink("images/noticias/" . $id . ".jpg");
        }
        return $this->administrarNoticia();
    }

    public function equipos(): string
    {
        $modelo = new MiModelo();
        $datos['equipos'] = $modelo->dameEquipos();

        return view('equipos', $datos);
    }

    public function gestionarEquipo(): string
    {
        $modelo = new MiModelo();
        if (isset($_POST['enviarI'])) {
            $nombre = $this->request->getPost('nombreI');
            $estadio = $this->request->getPost('estadioI');
            $modelo->insertarEquipo($nombre, $estadio);
        } elseif (isset($_POST['borrar'])) {
            $id = $this->request->getPost('idEquipo');
            $modelo->borrarEquipo($id);
        }
        return $this->equipos();
    }

    public function equipo(): string
    {
        $modelo = new MiModelo();
        $datos['equipos'] = $modelo->dameEquipos();
        $idEquipo = urldecode($_GET['id']);
        return view('equipo', $datos);
    }
    public function administrarEquipo(): string
    {
        $modelo = new MiModelo();
        $idEquipo = urldecode($_GET['id']);
        $datos['equipos'] = $modelo->dimeInfoEquipo($idEquipo);
        $datos['jugadores'] = $modelo->dimeJugadores($idEquipo);


        return view('administrarEquipo', $datos);
    }
    public function actualizarEquipo(): string
    {
        $modelo = new MiModelo();
        $idEquipo = $this->request->getPost('idEquipo');

        if (isset($_POST['nombre'])) {
            $nombre = $this->request->getPost('nombre');
            $modelo->actualizarNombreEquipo($idEquipo, $nombre);
        } elseif (isset($_POST['estadio'])) {
            $nombre = $this->request->getPost('estadio');
            $modelo->actualizarNombreEstadio($idEquipo, $nombre);
        } elseif (isset($_POST['nombreJugador'])) {
            $idJugador = $this->request->getPost('idJugador');
            $nombre = $this->request->getPost('nombreJugador');
            $modelo->actualizarNombreJugador($idJugador, $idEquipo, $nombre);
        } elseif (isset($_POST['borrar'])) {
            $idJugador = $this->request->getPost('idJugador');
            $modelo->borrarJugador($idJugador, $idEquipo) . "";
        } elseif (isset($_POST['enviarI'])) {
            $nombreJugador = $this->request->getPost('nombreI');
            $modelo->insertarJugador($idEquipo, $nombreJugador);
        }
        $datos['equipos'] = $modelo->dimeInfoEquipo($idEquipo);
        $datos['jugadores'] = $modelo->dimeJugadores($idEquipo);

        return view('administrarEquipo', $datos);
    }
    public function calendario(): string
    {
        $modelo = new MiModelo();
        $datos['partidos'] = $modelo->dimePartidos();
        $datos['equipos'] = $modelo->dameEquipos();
        $datos['jornadas'] = $modelo->dimeJornadas();

        // return var_dump($datos['equipos']) . "";
        return view('calendario', $datos);
    }
    public function enfrentamiento(): string
    {
        $modelo = new MiModelo();
        $idPartido = urldecode($_GET['id']);
        session()->set('idPartido',  $idPartido);
        $datos['equipoLocal'] = $modelo->equipoLocal($idPartido);
        $datos['nombreEquipoLocal'] = $modelo->nombreEquipoLocal($idPartido);
        $datos['idLocal'] = $modelo->dimeIdEquipoLocal($idPartido);
        $datos['jugadoresLocal'] = $modelo->dimeJugadores($datos['idLocal']);


        $datos['equipoVisitante'] = $modelo->equipoVisitante($idPartido);
        $datos['nombreEquipoVisitante'] = $modelo->nombreEquipoVisitante($idPartido);
        $datos['idVisitante'] = $modelo->dimeIdEquipoVisitante($idPartido);
        $datos['jugadoresVisitante'] = $modelo->dimeJugadores($datos['idVisitante']);
        $datos['estado'] = $modelo->estadoPartido($idPartido);
        $datos['golesEquipoLocal'] = $modelo->golesEquipoLocal($idPartido);
        $datos['golesEquipoVisitante'] = $modelo->golesEquipoVisitante($idPartido);
        $datos['secuenciaGoles'] = $modelo->secuenciaGoles($idPartido);
        $datos['idPartido'] = $idPartido;
        return view('enfrentamiento', $datos);
    }
    public function actualizarAlineacion(): string
    {
        $modelo = new MiModelo();
        $idPartido = session()->get('idPartido');
        $jugadorTitular = $this->request->getPost('jugadorTitular');
        $jugadorSuplente = $this->request->getPost('jugadorSeleccionado');
        $modelo->realizarCambio($idPartido, $jugadorTitular, $jugadorSuplente);
        $datos['equipoLocal'] = $modelo->equipoLocal($idPartido);
        $datos['nombreEquipoLocal'] = $modelo->nombreEquipoLocal($idPartido);
        $datos['equipoVisitante'] = $modelo->equipoVisitante($idPartido);
        $datos['nombreEquipoVisitante'] = $modelo->nombreEquipoVisitante($idPartido);
        $datos['estado'] = $modelo->estadoPartido($idPartido);
        $datos['golesEquipoLocal'] = $modelo->golesEquipoLocal($idPartido);
        $datos['golesEquipoVisitante'] = $modelo->golesEquipoVisitante($idPartido);
        $datos['secuenciaGoles'] = $modelo->secuenciaGoles($idPartido);
        $datos['idPartido'] = $idPartido;
        return view('enfrentamiento', $datos);
    }
    public function insertarAlineacion(): string
    {
        $modelo = new MiModelo();
        $idPartido = session()->get('idPartido');
        $datos['idPartido'] = $idPartido;
        $datos['idLocal'] = $modelo->dimeIdEquipoLocal($idPartido);
        $datos['idVisitante'] = $modelo->dimeIdEquipoVisitante($idPartido);
        $datos['jugadoresVisitante'] = $modelo->dimeJugadores($datos['idVisitante']);
        $datos['jugadoresLocal'] = $modelo->dimeJugadores($datos['idLocal']);


        if (isset($_POST['alinearLocal'])) {
            if (isset($_POST["jugadores"]) && is_array($_POST["jugadores"])) {
                $modelo->insertarAlineacionSuplentes($idPartido, $datos['idLocal'], $_POST["jugadores"]);
                foreach ($_POST["jugadores"] as $key) {
                    $modelo->insertarAlineacion($idPartido, $key);
                }
            }
        } elseif (isset($_POST['alinearVisitante'])) {
            if (isset($_POST["jugadoresV"]) && is_array($_POST["jugadoresV"])) {
                $modelo->insertarAlineacionSuplentes($idPartido, $datos['idVisitante'], $_POST["jugadoresV"]);
                foreach ($_POST["jugadoresV"] as $key) {
                    $modelo->insertarAlineacion($idPartido, $key);
                }
            }
        }
        $datos['equipoLocal'] = $modelo->equipoLocal($idPartido);
        $datos['nombreEquipoLocal'] = $modelo->nombreEquipoLocal($idPartido);
        $datos['equipoVisitante'] = $modelo->equipoVisitante($idPartido);
        $datos['nombreEquipoVisitante'] = $modelo->nombreEquipoVisitante($idPartido);
        $datos['estado'] = $modelo->estadoPartido($idPartido);
        $datos['golesEquipoLocal'] = $modelo->golesEquipoLocal($idPartido);
        $datos['golesEquipoVisitante'] = $modelo->golesEquipoVisitante($idPartido);
        $datos['secuenciaGoles'] = $modelo->secuenciaGoles($idPartido);
        return view('enfrentamiento', $datos);
    }
    public function insertarGol(): string
    {
        $modelo = new MiModelo();
        $idPartido = session()->get('idPartido');
        $idJugador = $this->request->getPost('jugadorTitular');
        $minuto = $this->request->getPost('minutosGol');
        $idEquipoLocal = $modelo->dimeIdEquipoLocal($idPartido);
        $modelo->insertarGol($idPartido, $idJugador, $idEquipoLocal, $minuto);
        $datos['idLocal'] = $modelo->dimeIdEquipoLocal($idPartido);
        $datos['idVisitante'] = $modelo->dimeIdEquipoVisitante($idPartido);
        $datos['jugadoresVisitante'] = $modelo->dimeJugadores($datos['idVisitante']);
        $datos['jugadoresLocal'] = $modelo->dimeJugadores($datos['idLocal']);
        $datos['equipoLocal'] = $modelo->equipoLocal($idPartido);
        $datos['nombreEquipoLocal'] = $modelo->nombreEquipoLocal($idPartido);
        $datos['equipoVisitante'] = $modelo->equipoVisitante($idPartido);
        $datos['nombreEquipoVisitante'] = $modelo->nombreEquipoVisitante($idPartido);
        $datos['estado'] = $modelo->estadoPartido($idPartido);
        $datos['golesEquipoLocal'] = $modelo->golesEquipoLocal($idPartido);
        $datos['golesEquipoVisitante'] = $modelo->golesEquipoVisitante($idPartido);
        $datos['secuenciaGoles'] = $modelo->secuenciaGoles($idPartido);
        return view('enfrentamiento', $datos);
    }
    public function insertarGolVisitante(): string
    {
        $modelo = new MiModelo();
        $idPartido = session()->get('idPartido');
        $idJugador = $this->request->getPost('jugadorTitular');
        $minuto = $this->request->getPost('minutosGol');
        $idEquipoVisitante = $modelo->dimeIdEquipoVisitante($idPartido);
        $modelo->insertarGol($idPartido, $idJugador, $idEquipoVisitante, $minuto);
        $datos['idLocal'] = $modelo->dimeIdEquipoLocal($idPartido);
        $datos['idVisitante'] = $modelo->dimeIdEquipoVisitante($idPartido);
        $datos['jugadoresVisitante'] = $modelo->dimeJugadores($datos['idVisitante']);
        $datos['jugadoresLocal'] = $modelo->dimeJugadores($datos['idLocal']);
        $datos['equipoLocal'] = $modelo->equipoLocal($idPartido);
        $datos['nombreEquipoLocal'] = $modelo->nombreEquipoLocal($idPartido);
        $datos['equipoVisitante'] = $modelo->equipoVisitante($idPartido);
        $datos['nombreEquipoVisitante'] = $modelo->nombreEquipoVisitante($idPartido);
        $datos['estado'] = $modelo->estadoPartido($idPartido);
        $datos['golesEquipoLocal'] = $modelo->golesEquipoLocal($idPartido);
        $datos['golesEquipoVisitante'] = $modelo->golesEquipoVisitante($idPartido);
        $datos['secuenciaGoles'] = $modelo->secuenciaGoles($idPartido);
        return view('enfrentamiento', $datos);
    }
    public function finalizarPartido(): string
    {
        $modelo = new MiModelo();
        $idPartido = session()->get('idPartido');
        $modelo->finalizarPartido($idPartido);
        return $this->calendario();
    }
    public function insertarPartido(): string
    {
        $modelo = new MiModelo();
        $jornada = $this->request->getPost('jornada');
        $idEquipoLocal = $this->request->getPost('equipoLocal');
        $idEquipoVisitante = $this->request->getPost('equipoVisitante');
        $fecha = $this->request->getPost('fechaPartido');
        $modelo->insertarPartido($jornada, $idEquipoLocal, $idEquipoVisitante, $fecha);
        return $this->calendario();
    }
}
