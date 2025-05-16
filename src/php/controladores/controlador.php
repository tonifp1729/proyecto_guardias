<?php

    require_once 'C:/Users/Antonio/WorkSpace/Xampp/htdocs/espacio-proyectos/proyecto_guardias/src/config/path.php';
 
    class Controlador {
        
        public static function cargarVista($vista, $datos = []) {
            
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            //Si no ha pasado por la autenticación y la vista no es "inicio" se forzará la redirección a la vista inicio. Así evitamos que acceda por medio del uso de un parámetro por URL
            if (!isset($_SESSION['rol']) && $vista !== 'inicio') {
                header("Location: index.php?accion=inicio");
                exit;
            }

            extract($datos);

            //HEADER
            require_once RUTA_VISTAS . 'partes/header.php';

            //La sidebar se carga en caso de que se haya accedido a una vista que la requiera
            if ($vista !== 'inicio') {
                require_once RUTA_CONTROLADORES.'curso_controlador.php';
                $controladorCurso = new Curso_controlador();
                $controladorCurso->verificarActivacionCurso();
                require_once RUTA_VISTAS . 'partes/sidebar.php';
            }

            //VISTA
            require_once RUTA_VISTAS . $vista . '.php';

            //FOOTER
            require_once RUTA_VISTAS . 'partes/footer.php';
        }

    }