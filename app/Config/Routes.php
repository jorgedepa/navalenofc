<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Home::login');
$routes->get('/registro', 'Home::registro');
$routes->get('/noticias', 'Home::noticias');
$routes->get('/noticia', 'Home::noticia');
$routes->get('/partidos', 'Home::partidos');
$routes->get('/partido', 'Home::partido');
$routes->get('/clasificacion', 'Home::clasificacion');
$routes->get('/video', 'Home::video');
$routes->get('/cerrar', 'Home::cerrarSesion');
$routes->get('/plantilla', 'Home::plantilla');
$routes->get('/jugador', 'Home::jugador');

// Fichajes
$routes->get('inicioResponsable', 'Home::inicioResponsable');

// FORMULARIOS

// Navaleno FC
$routes->post('crearUsuario', 'Home::crearUsuario');
$routes->post('entrar', 'Home::entrar');
$routes->post('/filtro', 'Home::filtroPartidos');
// Fichajes
$routes->post('fichaje', 'Home::registroFichaje');
$routes->post('rangoFechas', 'Home::rangoFechas');
$routes->get('crud', 'Home::crud');
$routes->post('rangoFechasCrud', 'Home::rangoFechasCrud');
$routes->post('actualizar', 'Home::actualizar');
$routes->get('ausencia', 'Home::ausencia');
$routes->post('actualizarAusencia', 'Home::actualizarAusencia');
$routes->get('empleados', 'Home::empleados');
$routes->post('actualizarEmpleado', 'Home::actualizarEmpleado');

// ADMINISTRADOR
$routes->get('inicioAdmin', 'Home::inicioAdmin');
$routes->get('usuarios', 'Home::usuarios');
$routes->post('actualizarUsuario', 'Home::actualizarUsuario');
$routes->get('administrarNoticia', 'Home::administrarNoticia');
$routes->post('actualizarNoticia', 'Home::actualizarNoticia');
$routes->get('equipos', 'Home::equipos');
$routes->get('equipo', 'Home::equipo');
$routes->get('gestionarEquipo', 'Home::gestionarEquipo');
$routes->get('administrarEquipo', 'Home::administrarEquipo');
$routes->post('actualizarEquipo', 'Home::actualizarEquipo');
$routes->get('calendario', 'Home::calendario');
$routes->get('enfrentamiento', 'Home::enfrentamiento');
$routes->post('actualizarAlineacion', 'Home::actualizarAlineacion');
$routes->post('insertarAlineacion', 'Home::insertarAlineacion');
$routes->post('insertarGol', 'Home::insertarGol');
$routes->post('insertarGolVisitante', 'Home::insertarGolVisitante');
$routes->post('finalizarPartido', 'Home::finalizarPartido');
$routes->post('insertarPartido', 'Home::insertarPartido');








