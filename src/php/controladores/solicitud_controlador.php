<!-- AUTOR: ANTONIO MANUEL FIGUEROA PINILLA -->
<?php
    require_once 'C:/Users/Antonio/WorkSpace/Xampp/htdocs/espacio-proyectos/proyecto_guardias/src/config/path.php';
    require_once RUTA_MODELOS . 'solicitud.php';

    class Solicitud_controlador {

        private $solicitud;

        public function __construct() {
            $this->solicitud = new Solicitud();
        }

        /**
         * Carga los datos necesarios para mostrar el formulario de nueva solicitud.
         */
        public function cargarDatosSolicitud() {
            if (!isset($_SESSION['idCursoActivo'])) {
                return 'noHayCurso';
            }

            $idCursoActivo = $_SESSION['idCursoActivo'];
            $motivos = $this->solicitud->obtenerMotivos();

            return ['vista' => 'formnuevasolicitud', 'cursoActivo' => $idCursoActivo, 'motivos' => $motivos];
        }

    }