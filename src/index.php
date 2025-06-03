<?php

    require_once 'php/controladores/controlador.php';
    require_once 'php/controladores/usuario_controlador.php';
    require_once 'php/controladores/curso_controlador.php';
    require_once 'php/controladores/solicitud_controlador.php';

    //Definimos esta constante en el index, ya que al cargar cada vista se realiza una comprobación sobre esta. En caso de que no se pase por el index se lanza una redirección hacia el inicio.
    DEFINE('ACCESO_PERMITIDO', true);

    $accion = $_GET['accion'] ?? 'inicio';

    $controlador = new Controlador();

    switch ($accion) {
        case 'inicio':
            $vista = 'inicio';
            $controlador->cargarVista($vista);
            
            break;

        case 'loginGoogle':

            $usuarioControlador = new Usuario_controlador();
            $vista = $usuarioControlador->inicioSesionGoogle();
            $controlador->cargarVista($vista);

            break;

        case 'logout':
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            session_unset();
            session_destroy();
            
            $vista = 'inicio';
            $controlador->cargarVista($vista);

            break;

        case 'nuevoCurso':

            $vista = 'formnuevocurso';
            $controlador->cargarVista($vista);

            break;

        case 'iniciarCurso':

            $cursoControlador = new Curso_controlador();
            $datos = $cursoControlador->iniciarCurso();
   
            if (is_array($datos)) {
                $controlador->cargarVista($datos['vista'], $datos);
            } else {
                $controlador->cargarVista($datos);
            }

            break;

        case 'listadoCursos':

            $cursoControlador = new Curso_controlador();
            $datos = $cursoControlador->obtenerCursos();
            $controlador->cargarVista($datos['vista'], $datos);

            break;

        case 'borrarCurso':

            $cursoControlador = new Curso_controlador();
            $vista = $cursoControlador->borrarCurso();
            $controlador->cargarVista($vista);

            break;

        case 'irModificarCurso':

            $cursoControlador = new Curso_controlador();
            $datos = $cursoControlador->obtenerCurso();
            $controlador->cargarVista($datos['vista'], $datos);

            break;

        case 'modificarCurso':
            
            $cursoControlador = new Curso_controlador();
            $datos = $cursoControlador->modificarCurso();

            if (is_array($datos)) {
                $cursoActual = $cursoControlador->obtenerCurso($datos['curso']['idCurso']);
                $datos = array_merge($cursoActual, $datos);
                $controlador->cargarVista($datos['vista'], $datos);
            } else {
                $controlador->cargarVista($datos);
            }

            break;

        case 'irModificarUsuario':

            $usuarioControlador = new  Usuario_controlador();
            $datos = $usuarioControlador->cargarModificarUsuario();
            $controlador->cargarVista($datos['vista'], $datos);

            break;

        case 'modificarUsuario':
            $usuarioControlador = new Usuario_controlador();
            $datos = $usuarioControlador->modificarUsuario();

            if (is_array($datos)) {
                $controlador->cargarVista($datos['vista'], $datos);
            } else {
                $controlador->cargarVista($datos);
            }

            break;

        case 'borrarUsuario':

            $usuarioControlador = new Usuario_controlador();
            $vista = $usuarioControlador->borrarUsuario();
            $controlador->cargarVista($vista);

            break;

        case 'listadoUsuarios':

            $usuarioControlador = new Usuario_controlador();
            $datos = $usuarioControlador->obtenerUsuarios();
            $controlador->cargarVista($datos['vista'], $datos);

            break;

        case 'nuevaSolicitud':
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $solicitudControlador = new Solicitud_controlador();
            $datos = $solicitudControlador->cargarDatosSolicitud();

            if (is_array($datos)) {
                $controlador->cargarVista($datos['vista'], $datos);
            } else {
                $controlador->cargarVista($datos);
            }
            
            break;

        case 'crearSolicitud':

            $solicitudControlador = new Solicitud_controlador();
            $datos = $solicitudControlador->crearSolicitud();
   
            if (is_array($datos)) {
                $controlador->cargarVista($datos['vista'], $datos);
            } else {
                $controlador->cargarVista($datos);
            }

            break;

        case 'listadoSolicitudesPropias':

            $solicitudControlador = new Solicitud_controlador();
            $datos = $solicitudControlador->obtenerSolicitudesPropias();
            $controlador->cargarVista($datos['vista'], $datos);

            break;

        case 'irModificarSolicitud':

            $solicitudControlador = new Solicitud_controlador();
            $datos = $solicitudControlador->cargarModificarSolicitud();
            $controlador->cargarVista($datos['vista'], $datos);

            break;

        case 'modificarSolicitud':
            $solicitudControlador = new Solicitud_controlador();
            $datos = $solicitudControlador->modificarSolicitud();

            if (is_array($datos)) {
                $controlador->cargarVista($datos['vista'], $datos);
            } else {
                $controlador->cargarVista($datos);
            }

            break;

        case 'irVerSolicitud':

            $solicitudControlador = new Solicitud_controlador();
            $datos = $solicitudControlador->cargarVerSolicitud();
            $controlador->cargarVista($datos['vista'], $datos);

            break;

        case 'borrarSolicitud':

            $solicitudControlador = new Solicitud_controlador();
            $vista = $solicitudControlador->eliminarSolicitud();
            $controlador->cargarVista($vista);

            break;

        case 'cursoActual':

            $solicitudControlador = new Solicitud_controlador();
            $datos = $solicitudControlador->obtenerSolicitudesGestion();

            if (is_array($datos)) {
                $controlador->cargarVista($datos['vista'], $datos);
            } else {
                $controlador->cargarVista($datos);
            }

            break;

        case 'verCurso':

            $solicitudControlador = new Solicitud_controlador();
            $datos = $solicitudControlador->obtenerSolicitudesCurso();

            if (is_array($datos)) {
                $controlador->cargarVista($datos['vista'], $datos);
            } else {
                $controlador->cargarVista($datos);
            }

            break;

        case 'irGestionarSolicitud':

            $solicitudControlador = new Solicitud_controlador();
            $datos = $solicitudControlador->cargarGestionarSolicitud();
            $controlador->cargarVista($datos['vista'], $datos);

            break;

        case 'gestionSolicitud':

            $solicitudControlador = new Solicitud_controlador();
            $vista = $solicitudControlador->gestionarEstado();
            $controlador->cargarVista($vista);

            break;

        case 'denegarAcceso':

            $vista = 'accesodenegadonocurso';
            $controlador->cargarVista($vista);

            break;

        case 'exito':

            $vista = 'avisoexito';
            $controlador->cargarVista($vista);

            break;
        
        default:
            echo "No te portes mal. Accede por medio del inicio de sesión y no trates de juguetear con la URL.";
            break;
    }