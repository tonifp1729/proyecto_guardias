<?php

    require_once 'C:/Users/Antonio/WorkSpace/Xampp/htdocs/espacio-proyectos/proyecto_guardias/src/config/path.php';
 
    class Controlador {

        public function verInicio() {
            //Cargar datos del modelo y mostrar la vista
            require_once RUTA_VISTAS.'inicio.php';
        }
        
        public static function cargarVista($vista, $datos = []) {
            extract($datos);
            require_once RUTA_VISTAS.$vista.'.php';
        }

    }