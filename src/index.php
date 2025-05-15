<?php

    require_once 'php/controladores/controlador.php';
    require_once 'php/controladores/usuario_controlador.php';
    require_once 'php/controladores/curso_controlador.php';

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
            $vista = Usuario_controlador::inicioSesionGoogle();
            $controlador->cargarVista($vista);

            break;

        case 'cursoActual':
            $cursoControlador = new Curso_controlador();
            $datos = $cursoControlador->mostrarCursoActual();
            $controlador->cargarVista($datos['accion'], $datos);
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
            $vista = $cursoControlador->iniciarCurso();
            $controlador->cargarVista($vista);
            break;

        case '':
            break;
                        
        case 'exito':

            $vista = 'avisoexito';
            $controlador->cargarVista($vista);

            break;
        
        default:
            echo "No te portes mal. Accede por medio del inicio de sesión y no trates de juguetear con la URL.";
            break;
    }