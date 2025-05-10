<?php

require_once 'php/controladores/controlador.php';
require_once 'php/controladores/usuario_controlador.php';

session_start();

$accion = $_GET['accion'] ?? 'inicio';

$controlador = new Controlador();

switch ($accion) {
    case 'inicio':
        $controlador->verInicio();
        break;

    case 'loginGoogle':
        $vista = Usuario_controlador::inicioSesionGoogle();
        if ($vista) {
            $controlador->cargarVista($vista);
        }
        break;

    case 'saludo':
        $controlador->cargarVista('saludo');
        break;

    default:
        echo "Acción no reconocida.";
        break;
}