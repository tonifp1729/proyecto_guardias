<?php

    require_once '/home/proyectosevg/public_html/10/src/config/path.php';
 
    /**
     * Clase Controlador
     *
     * Se encarga de cargar las vistas de la aplicación, incluyendo sus componentes
     * (header, sidebar y footer) y los datos necesarios.
     * 
     * También se lanza desde aquí la verificación sobre el curso activo.
     * 
     * @author Antonio Manuel Figueroa Pinilla
     */
    class Controlador {
        
        /**
         * Carga una vista con los datos proporcionados y los componentes comunes para las mismas.
         * También verifica si el usuario tiene sesión iniciada o de lo contrario es redirigido a la vista de inicio.
         *
         * @param string $vista Nombre del archivo de vista (sin la extensión `.php`) a cargar.
         * @param array $datos Datos a extraer y pasar como variables a la vista.
         */
        public static function cargarVista($vista, $datos = []) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
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