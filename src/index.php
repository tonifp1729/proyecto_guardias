<?php

    require_once 'php/controladores/controlador.php';
    require_once 'php/controladores/usuario_controlador.php';

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
            if ($vista) {
                $controlador->cargarVista($vista);
            }

            break;

        case '':
            break;

        default:
            echo "No te portes mal. Accede por medio del inicio de sesión y no trates de juguetear con la URL.";
            break;
    }