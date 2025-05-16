<?php
    require_once 'C:/Users/Antonio/WorkSpace/Xampp/htdocs/espacio-proyectos/proyecto_guardias/src/config/path.php';
    require_once RUTA_MODELOS . 'curso.php';
    require_once RUTA_MODELOS . 'solicitud.php';

    class Curso_controlador {

        private $curso;

        public function __construct() {
            $this->curso = new Curso();
            $this->solicitud = new Solicitud();
        }

        /**
         * Este método se lanza en el momento en el que se carga la sidebar, asegurandonos de que siempre que acceda un usuario 
         * la aplicación se mantiene actualizada con el último curso activo.
         * 1. Finaliza los cursos obsoletos (si los hay).
         * 2. Comprobamos que no hay curso activo y, si no lo hay, buscamos los que puedan estar pendientes.
         * 3. Activa el curso que estaba pendiente de iniciarse.
         */
        public function verificarActivacionCurso() {

            $this->curso->finalizarCursos();

            $cursoActivo = $this->curso->cursoActivo();

            if (!$cursoActivo) {
                
                $hoy = date('Y-m-d');
                $cursoPendiente = $this->curso->buscarCursoPendiente($hoy);

                if ($cursoPendiente) {
                    $this->curso->activarCurso($cursoPendiente);
                }
            }
        }

        /**
         * Controlador para iniciar un nuevo curso académico.
         * Este método se encarga de gestionar la creación de un nuevo curso,
         * validando los siguientes aspectos:
         * 1. Que no haya solapamiento con cursos ya existentes.
         * 2. Que la fecha de inicio no sea anterior a la fecha actual.
         * 3. Que la fecha de inicio sea anterior a la de finalización.
         * 
         * Si las validaciones son correctas, calcula el año académico (en formato 'yy/yy') y llama al modelo para insertar el nuevo curso.
         * @return string - Vista que debe renderizarse: "formnuevocurso" si hay errores, coincidencias de fechas o si no se ha enviado POST y "avisoexito" si se crea correctamente.
         */
        public function iniciarCurso() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $anoAcademico = null;
                $fechaInicio = $_POST['fecha_inicio'];
                $fechaFinalizacion = $_POST['fecha_fin'];

                //Comprobamos si existe solapamiento de fechas con los cursos anteriores, si la hay volvemos a la pantalla de formulario
                if ($this->curso->existeCoincidencia($fechaInicio, $fechaFinalizacion)) {
                    return 'formnuevocurso';
                }

                //Definimos la fecha actual para realizar validación
                $hoy = strtotime(date('Y-m-d')); 

                //Si la fecha de hoy es mayor al inicio del curso volveremos al formulario
                if ($fechaInicio < $hoy) {
                    return 'formnuevocurso';
                }

                //Calcular el año académico
                $anoInicio = date('y', strtotime($fechaInicio)); //Año de la fecha de inicio, tomamos los últimos dígitos
                $anoFin = date('y', strtotime($fechaFinalizacion)); //Año de finalización del curso
                
                //En caso de que la fecha de inicio sea mayor o igual a la de fin del curso solo volvemos a cargar el formulario, evitando registrar el curso
                if ((int)$anoInicio >= (int)$anoFin) {
                    return 'formnuevocurso';
                }

                //Concatenamos los años para formar el año académico del nuevo curso
                $anoAcademico = $anoInicio.'/'.$anoFin;

                if (!empty($fechaInicio) && !empty($fechaFinalizacion)) {

                    $cursoActivoExiste = $this->curso->hayCursoActivo();
                    $estado = (!$cursoActivoExiste && $fechaInicio === $hoy) ? 'A' : 'P';
                    $this->curso->insertarCurso($fechaInicio, $fechaFinalizacion, $anoAcademico, $estado);

                    return 'avisoexito';

                } else {
                    return 'formnuevocurso';
                }
            }

            //En caso de que no se realice una llamada POST simplemente se recarga el formulario
            return 'formnuevocurso';
        }

        public function listarCursos() {
            $cursos = $this->curso->obtenerTodos();
            return $cursos;
        }

        public function mostrarCursoActual() {
            $cursoActivo = $this->curso->cursoActivo();

            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $listadoSolicitudes = [];

            if ($cursoActivo) {
                $idCurso = $cursoActivo['idCurso'];
                $listadoSolicitudes = $this->solicitud->obtenerPorCurso($idCurso);
            }

            return ['cursoActivo' => $cursoActivo,'solicitudes' => $listadoSolicitudes,'accion' => 'cursoActual'];
        }

    }