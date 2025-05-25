<!-- AUTOR: ANTONIO MANUEL FIGUEROA PINILLA -->
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
                    $cursoActivo = $this->curso->cursoActivo();
                }
            }

            if ($cursoActivo) {
                $_SESSION['idCursoActivo'] = $cursoActivo['idCurso'];
            } else {
                unset($_SESSION['idCursoActivo']);
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
                    return ['vista' => 'formnuevocurso', 'error' => 'fecha-solapada'];
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

        /**
         * Modifica los datos de un curso existente si cumple con las condiciones de validación.
         *
         * Este método se activa al recibir una petición POST. Realiza múltiples validaciones:
         * - Que el curso exista.
         * - Que su estado sea 'A' (Activo) o 'P' (Pendiente).
         * - Que las nuevas fechas no estén en el pasado.
         * - Que la fecha de inicio sea anterior a la de fin.
         * - Que no haya solapamiento de fechas con otros cursos (excepto consigo mismo).
         * - Que no existan solicitudes anteriores a la nueva fecha de fin, si el curso está activo.
         *
         * Si alguna validación falla, devuelve la vista del formulario con el curso original.
         * Si todo es correcto, actualiza el curso en la base de datos y retorna 'avisoexito'.
         *
         * @return array|string - Devuelve un array con la vista y los datos del curso si hay error o el nombre de la vista 'avisoexito' si la modificación fue exitosa.
         */
        public function modificarCurso() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $idCurso = $_POST['idCurso'];
                $nuevaInicio = $_POST['fecha_inicio'];
                $nuevaFin = $_POST['fecha_fin'];
                $hoy = date('Y-m-d');

                $cursoActual = $this->curso->obtenerCurso($idCurso);

                //Validamos que el curso exista
                if (!$cursoActual) {
                    return ['vista' => 'formmodcurso', 'curso' => null];
                }

                $estado = $cursoActual['estado'];

                //Validamos que el estado sea modificable
                if (!in_array($estado, ['A', 'P'])) {
                    return ['vista' => 'formmodcurso', 'curso' => $cursoActual];
                }

                //Validamos que las fechas no estén en el pasado
                if ($nuevaInicio < $hoy || $nuevaFin < $hoy) {
                    return ['vista' => 'formmodcurso', 'curso' => $cursoActual];
                }

                //Validamos que la fecha de inicio sea anterior a la de fin
                if (strtotime($nuevaInicio) >= strtotime($nuevaFin)) {
                    return ['vista' => 'formmodcurso', 'curso' => $cursoActual];
                }

                //Validamos que no haya solapamiento con otros cursos (excluyendo el actual)
                if ($this->curso->existeSolapamiento($nuevaInicio, $nuevaFin, $idCurso)) {
                    return ['vista' => 'formmodcurso', 'curso' => $cursoActual, 'error' => 'fecha-solapada'];
                }

                //Comprobamos que el curso no tenga solicitudes antes de la nueva fecha de fin
                if ($estado === 'A' && $this->curso->haySolicitudesAntesDe($idCurso, $nuevaFin)) {
                    return ['vista' => 'formmodcurso', 'curso' => $cursoActual, 'error' => 'hay-solicitudes'];
                }

                $anoInicio = date('y', strtotime($nuevaInicio));
                $anoFin = date('y', strtotime($nuevaFin));
                $anoAcademico = $anoInicio . '/' . $anoFin;

                //Comprobamos si el estado debe de ser modificado
                if ($cursoActual['fechaInicio'] !== $nuevaInicio || $cursoActual['fechaFinalizacion'] !== $nuevaFin) {

                    if ($nuevaFin <= $hoy) {
                        $estado = 'F';
                    } elseif ($nuevaInicio > $hoy) {
                        $estado = 'P';
                    } else {
                        $estado = 'A';
                    }

                    $this->curso->modificarCurso($idCurso, $nuevaInicio, $nuevaFin, $anoAcademico, $estado);
                    return 'avisoexito';
                } else {
                    return ['vista' => 'formmodcurso', 'curso' => $cursoActual, 'error' => 'no-cambios'];
                }
            }

            return ['vista' => 'formmodcurso', 'curso' => $cursoActual];
        }

        /**
         * Elimina un curso de la base de datos si no está en estado 'A' (Activo).
         *
         * Este método obtiene el ID del curso desde la URL (`$_GET['id']`), recupera sus datos,
         * y verifica si su estado es 'A'. Si el curso está activo, no se elimina y se redirige 
         * al listado de cursos. Si no está activo, procede a eliminarlo de la base de datos.
         *
         * @return string - Devuelve la acción 'listadoCursos' si el curso está activo y no se elimina o 'avisoexito' si la eliminación fue realizada con éxito.
         */
        public function borrarCurso() {
            $idCurso = $_GET['id'];

            $prepararCurso = $this->curso->obtenerCurso($idCurso);

            if ($prepararCurso['estado'] === 'A') {
                return 'listadoCursos';
            }

            $this->curso->eliminarCurso($idCurso);

            return 'avisoexito';
        }

        /**
         * Obtiene todos los cursos y los ordena para su presentación en la vista.
         * Los cursos se ordenan primero por estado con el siguiente orden de prioridad:
         * - 'A' (Activo)
         * - 'P' (Pendiente)
         * - 'F' (Finalizado)
         * 
         * Dentro de cada grupo de estado, los cursos, son ordenanos por año académico de forma descendente
         * (por ejemplo, '24-25' aparece antes que '23-24').
         * @return array - Retorna un array asociativo con: 'accion' (el nombre de la vista a cargar) | 'cursos' (el array de cursos ya ordenado)
         */
        public function obtenerCursos() {
            $cursos = $this->curso->listarCursos();

            //Ordenamos los cursos por estado y año académico, utilizamos esta función que nos permite establecer el orden del array de manera personalizada
            usort($cursos, function ($a, $b) {

                //Definimos el orden de prioridad de los estados
                $ordenEstado = ['A' => 1, 'P' => 2, 'F' => 3];

                //Establecemos el valor númerico de los estados de los cursos que se comparan (2 a 2)
                $estadoA = $ordenEstado[$a['estado']] ?? 4;
                $estadoB = $ordenEstado[$b['estado']] ?? 4;

                if ($estadoA === $estadoB) {
                    //Comparamos por año académico para disponerlos en orden decreciente
                    return strcmp($b['anio_academico'], $a['anio_academico']);
                }

                return $estadoA - $estadoB;
            });

            return ['vista' => 'listarcursos', 'cursos' => $cursos];
        }

        /**
         * Recupera la información de un curso para su visualización o modificación.
         *
         * Si no se proporciona un ID como argumento, intenta obtenerlo desde $_GET['id'].
         * Si no hay un ID válido o si el curso no existe o está finalizado ('F') redirige al listado de cursos. 
         * Si el curso existe y es modificable se retorna la vista con el formulario de modificación y los datos del curso.
         *
         * @param int|null - $idCurso ID del curso a obtener. Si es null, se intenta recuperar de $_GET.
         * @return array - Devuelve un array asociativo con la clave 'vista' y, si corresponde,los datos del curso bajo la clave 'curso'.
         */
        public function obtenerCurso($idCurso = null) {
            if ($idCurso === null && isset($_GET['id'])) {
                $idCurso = $_GET['id'];
            }

            if ($idCurso === null) {
                return ['vista' => 'listadoCursos'];
            }

            $prepararCurso = $this->curso->obtenerCurso($idCurso);

            if (!$prepararCurso || $prepararCurso['estado'] === 'F') {
                return ['vista' => 'listadoCursos'];
            }

            return ['vista' => 'formmodcurso', 'curso' => $prepararCurso];
        }

    }