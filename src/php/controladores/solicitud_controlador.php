<!-- AUTOR: LEANDRO JOSÉ PANIAGUA BALBUENA Y ANTONIO MANUEL FIGUEROA PINILLA -->
<?php
    require_once 'C:/Users/Antonio/WorkSpace/Xampp/htdocs/espacio-proyectos/proyecto_guardias/src/config/path.php';
    require_once RUTA_MODELOS . 'curso.php';
    require_once RUTA_MODELOS . 'solicitud.php';

    class Solicitud_controlador {

        private $solicitud;
        private $curso;

        public function __construct() {
            $this->solicitud = new Solicitud();
            $this->curso = new Curso();
        }

        /**
         * Para disponer del identificador adecuado para las vistas del administrador.
         */
        function generarIdentificadorParaAdministrador($fecha, $num) {

            $fechaFormateada = date("Ymd", strtotime($fecha));
            $numFormateado = str_pad($num, 2, "0", STR_PAD_LEFT);

            $identificadorSolicitud = $_SESSION['id'] . "_" . $fechaFormateada . "_" . $numFormateado;

            return $identificadorSolicitud;
        }

        /**
         * Para disponer del identificador adecuado para vistas comunes.
         */
        function generarIdentificador($fecha, $num) {

            $fechaFormateada = date("Ymd", strtotime($fecha));
            $numFormateado = str_pad($num, 2, "0", STR_PAD_LEFT);

            $identificadorSolicitud = $fechaFormateada . "_" . $numFormateado;

            return $identificadorSolicitud;
        }

        /**
         * Vamos a retornar las solicitudes del usuario interesado en ver el estado de las propias presentadas.
         * Lo hacemos a partir de los datos guardados en la variable de sesión: 
         * id -> El usuario interesado en ver sus solicitudes
         * idCursoActivo -> El curso activo actualmente
         * 
         * Al usuario común solo se le mostrarán las solicitudes presentadas en el curso actual. En caso de que
         * no haya uno activo, no se mostrará ninguna acción a realizar en la aplicación.
         * 
         * @return
         */
        public function obtenerSolicitudesPropias() {
            if (!isset($_SESSION['id']) || !isset($_SESSION['idCursoActivo'])) {
                session_start();
            }

            $idUsuario = $_SESSION['id'];
            $idCursoActivo = $_SESSION['idCursoActivo'];

            $solicitudes = $this->solicitud->obtenerSolicitudesPorUsuarioYCurso($idUsuario, $idCursoActivo);

            foreach ($solicitudes as &$solicitud) {
                $solicitud['identificador'] = $this->generarIdentificador($solicitud['fecha_presentacion'], $solicitud['num']);
            }

            return ['vista' => 'listarsolicitudes', 'cursoActivo' => $idCursoActivo, 'solicitudes' => $solicitudes];
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

        /**
         * Carga los datos de la solicitud que un usuario desea modificar.
         */
        public function cargarModificarSolicitud() {
            $idUsuario = $_GET['id'] ?? null;
            $fechaPresentacion = $_GET['fecha'] ?? null;
            $num = $_GET['num'] ?? null;

            if (!$idUsuario || !$fechaPresentacion || !$num) {
                return [
                    'vista' => 'error',
                    'error' => 'Faltan datos para modificar la solicitud.'
                ];
            }

            $solicitud = $this->solicitud->obtenerSolicitud($idUsuario, $fechaPresentacion, $num);
            if (!$solicitud) {
                return [
                    'vista' => 'error',
                    'error' => 'No se ha encontrado la solicitud especificada.'
                ];
            }

            $horasSeleccionadas = $this->solicitud->obtenerHorasDeSolicitud($idUsuario, $fechaPresentacion, $num);
            $motivos = $this->solicitud->obtenerMotivos();
            $archivos = $this->solicitud->obtenerArchivosDeSolicitud($idUsuario, $fechaPresentacion, $num);

            // Clasificamos los archivos por tipo de ruta
            $justificantes = array_filter($archivos, fn($archivo) => $archivo['ruta_archivo'] === 'justificantes');
            $materiales = array_filter($archivos, fn($archivo) => $archivo['ruta_archivo'] === 'materiales');

            return ['vista' => 'formmodsolicitud', 'solicitud' => $solicitud, 'horasSeleccionadas' => $horasSeleccionadas, 'motivos' => $motivos, 'justificantes' => $justificantes, 'materiales' => $materiales];
        }

        /**
         * Realiza la eliminación de un archivo en el servidor.
         * Se ejecuta en el momento en que se hace una modificación de una solicitud que requiere de la eliminación de un archivo.
         */
        private function eliminarArchivoFisico($archivo) {
            $ruta = RUTA_PROYECTO . 'src/subidas/' . $archivo['ruta_archivo'] . '/' . $archivo['nombre_generado'] . '.' . $archivo['tipo_archivo'];

            if (file_exists($ruta)) {
                unlink($ruta);
            }
        }

        /**
         * 
         */
        public function modificarSolicitud() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                $idUsuario = intval($_POST['id_usuario'] ?? 0);
                $fechaPresentacion = $_POST['fecha_presentacion'] ?? null;
                $numSolicitud = intval($_POST['num'] ?? 0);
                $motivo = intval($_POST['motivo'] ?? 0);
                $descripcion = $_POST['descripcion'] ?? '';
                $comentario = $_POST['comentario'] ?? '';

                // Actualizar datos básicos de la solicitud
                $resultado = $this->solicitud->actualizarSolicitud($idUsuario, $fechaPresentacion, $numSolicitud, $motivo, $descripcion, $comentario);

                // Eliminar archivos marcados
                if (!empty($_POST['archivos_a_eliminar'])) {
                    foreach ($_POST['archivos_a_eliminar'] as $idArchivo => $valor) {
                        if ($valor == 1 && isset($_POST['archivos_info'][$idArchivo])) {
                            $archivoInfo = $_POST['archivos_info'][$idArchivo];

                            $datosArchivo = [
                                'ruta_archivo' => $archivoInfo['ruta'],
                                'nombre_generado' => $archivoInfo['nombre'],
                                'tipo_archivo' => $archivoInfo['tipo']
                            ];

                            $this->eliminarArchivoFisico($datosArchivo);
                            $this->solicitud->eliminarArchivo($idArchivo);
                        }
                    }
                }

                // Subir archivos nuevos (si llegan)
                if ((!empty($_FILES['justificantes']) && $_FILES['justificantes']['error'][0] !== UPLOAD_ERR_NO_FILE) ||
                    (!empty($_FILES['materiales']) && $_FILES['materiales']['error'][0] !== UPLOAD_ERR_NO_FILE)) {
                    
                    $archivosSubidos = $this->subirArchivosSolicitud($_FILES);

                    //Insertamos archivos, si los hay
                    foreach ($archivosSubidos['justificantes'] as $infoArchivo) {
                        $extension = pathinfo($infoArchivo['nombreOriginal'], PATHINFO_EXTENSION);
                        $nombreOriginal = pathinfo($infoArchivo['nombreOriginal'], PATHINFO_FILENAME);
                        $nombreGenerado = pathinfo($infoArchivo['nombreGenerado'], PATHINFO_FILENAME);
                        $tipoArchivo = 'justificantes';
                        $rutaRelativa = RUTA_PROYECTO.'src/subidas/justificantes/';
                        $this->solicitud->insertarArchivo($idUsuario, $fechaPresentacion, $numSolicitud, $nombreOriginal, $nombreGenerado, $extension, $tipoArchivo, $rutaRelativa);
                    }

                    foreach ($archivosSubidos['materiales'] as $infoArchivo) {
                        $extension = pathinfo($infoArchivo['nombreOriginal'], PATHINFO_EXTENSION);
                        $nombreOriginal = pathinfo($infoArchivo['nombreOriginal'], PATHINFO_FILENAME);
                        $nombreGenerado = pathinfo($infoArchivo['nombreGenerado'], PATHINFO_FILENAME);
                        $tipoArchivo = 'materiales';
                        $rutaRelativa = RUTA_PROYECTO.'src/subidas/material/';
                        $this->solicitud->insertarArchivo($idUsuario, $fechaPresentacion, $numSolicitud, $nombreOriginal, $nombreGenerado, $extension, $tipoArchivo, $rutaRelativa);
                    }
                }

                return 'avisoexito';

            } else {
                return 'saludo';
            }
        }

        /**
         * Necesitamos generar nombres únicos para los archivos que se van a introducir con cada solicitud.
         * 1. Extrae el nombre inicial.
         * 2. Extrae la extensión.
         * 3. Codificamos el nombre base por medio de un hash básico.
         * 4. Combinamos el nuevo nombre que hemos generado con la extensión.
         * @param
         * @return
         */
        function generarNombreUnico($archivo) {
            $nombreBase = pathinfo($archivo, PATHINFO_FILENAME);
            $extension = pathinfo($archivo, PATHINFO_EXTENSION);
            $nombreCodificado = uniqid().'_'.hash('sha256', $nombreBase);
            return $nombreCodificado.'.'.$extension;
        }

        private function manejarSubidaArchivo($archivo, $subdirectorio) {
            $rutaBase = RUTA_PROYECTO . 'src/subidas/' . $subdirectorio . '/';

            if (!file_exists($rutaBase)) {
                mkdir($rutaBase, 0777, true);
            }

            $nombreGenerado = $this->generarNombreUnico($archivo['name']);
            $rutaArchivoFinal = $rutaBase . $nombreGenerado;

            if (move_uploaded_file($archivo['tmp_name'], $rutaArchivoFinal)) {
                $tipoArchivo = pathinfo($archivo['name'], PATHINFO_EXTENSION);
                return [
                    'nombreGenerado' => $nombreGenerado,
                    'nombreOriginal' => $archivo['name'],
                    'tipoArchivo' => $tipoArchivo,
                    'rutaRelativa' => $subdirectorio . '/' . $nombreGenerado
                ];
            }

            return false;
        }

        /**
         * Procesa y guarda los archivos subidos en las rutas correspondientes.
         * 
         * @param array $archivos El array $_FILES con los campos 'justificantes' y/o 'materiales'.
         * @return array Un array con los nombres de los archivos subidos por cada tipo.
         */
        public function subirArchivosSolicitud($archivos) {
            $datos = ['justificantes' => [], 'materiales' => []];

            // Procesamos justificantes
            if (isset($archivos['justificantes']) && is_array($archivos['justificantes']['name'])) {
                foreach ($archivos['justificantes']['name'] as $index => $nombreOriginal) {
                    if ($archivos['justificantes']['error'][$index] === UPLOAD_ERR_OK) {
                        $file = [
                            'name' => $archivos['justificantes']['name'][$index],
                            'tmp_name' => $archivos['justificantes']['tmp_name'][$index],
                            'type' => $archivos['justificantes']['type'][$index],
                            'error' => $archivos['justificantes']['error'][$index],
                            'size' => $archivos['justificantes']['size'][$index],
                        ];
                        $infoArchivo = $this->manejarSubidaArchivo($file, 'justificantes');
                        if ($infoArchivo !== false) {
                            $datos['justificantes'][] = $infoArchivo;
                        }
                    }
                }
            }

            // Procesamos materiales
            if (isset($archivos['materiales']) && is_array($archivos['materiales']['name'])) {
                foreach ($archivos['materiales']['name'] as $index => $nombreOriginal) {
                    if ($archivos['materiales']['error'][$index] === UPLOAD_ERR_OK) {
                        $file = [
                            'name' => $archivos['materiales']['name'][$index],
                            'tmp_name' => $archivos['materiales']['tmp_name'][$index],
                            'type' => $archivos['materiales']['type'][$index],
                            'error' => $archivos['materiales']['error'][$index],
                            'size' => $archivos['materiales']['size'][$index],
                        ];
                        $infoArchivo = $this->manejarSubidaArchivo($file, 'materiales');
                        if ($infoArchivo !== false) {
                            $datos['materiales'][] = $infoArchivo;
                        }
                    }
                }
            }

            return $datos;
        }

        /**
         * Procesa el formulario de creación de una nueva solicitud.
         */
        public function crearSolicitud() {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['id'], $_POST['fecha_inicio_ausencia'], $_POST['fecha_fin_ausencia'], $_POST['id_motivo'])) {
                $motivos = $this->solicitud->obtenerMotivos();
                return ['vista' => 'formnuevasolicitud', 'error' => 'faltan-campos', 'motivos' => $motivos];
            }

            $idUsuario = intval($_SESSION['id']); 
            $fechaInicio = $_POST['fecha_inicio_ausencia'];
            $fechaFin = $_POST['fecha_fin_ausencia'];
            $idMotivo = intval($_POST['id_motivo']);
            $descripcion = $_POST['descripcion_solicitud'] ?? null;
            $comentario = $_POST['comentario_material'] ?? null;
            $horas = isset($_POST['horas']) ? $_POST['horas'] : [];

            if (!isset($_SESSION['idCursoActivo'])) {
                $motivos = $this->solicitud->obtenerMotivos();
                return ['vista' => 'formnuevasolicitud', 'error' => 'no-hay-curso', 'motivos' => $motivos];
            }

            $idCurso = $_SESSION['idCursoActivo'];

            // Obtenemos datos curso activo
            $cursoActivo = $this->curso->obtenerCurso($idCurso);
            $fechaFinCurso = $cursoActivo['fechaFinalizacion'];

            //Validamos fechas contra fin de curso activo
            $timestampInicio = strtotime($fechaInicio);
            $timestampFin = strtotime($fechaFin);
            $timestampFinCurso = strtotime($fechaFinCurso);
            $timestampManana = strtotime('tomorrow');

            if ($timestampInicio > $timestampFinCurso || $timestampFin > $timestampFinCurso) {
                $motivos = $this->solicitud->obtenerMotivos();
                return ['vista' => 'formnuevasolicitud', 'error' => 'fechas-fuera-curso', 'motivos' => $motivos];
            }

            // No fechas anteriores a mañana
            if ($timestampInicio < $timestampManana || $timestampFin < $timestampManana) {
                $motivos = $this->solicitud->obtenerMotivos();
                return ['vista' => 'formnuevasolicitud', 'error' => 'fechas-anteriores-manana', 'motivos' => $motivos];
            }

            // Validar solapamientos con otras solicitudes del usuario
            if ($this->solicitud->existeSolapamiento($idUsuario, $fechaInicio, $fechaFin)) {
                $motivos = $this->solicitud->obtenerMotivos();
                return ['vista' => 'formnuevasolicitud', 'error' => 'solapamiento-fechas', 'motivos' => $motivos];
            }

            $estado = 'p';

            //Insertamos la solicitud principal
            $resultado = $this->solicitud->insertarSolicitud($idUsuario, $idCurso, $idMotivo, $fechaInicio, $fechaFin, $estado, $descripcion, $comentario);

            if (!$resultado) {
                return ['vista' => 'formnuevasolicitud', 'error' => 'fallo-insercion'];
            }

            $numSolicitud = $resultado['num'];
            $fechaPresentacion = $resultado['fecha_presentacion'];

            //Insertamos horas, si las hay
            if (!empty($horas)) {
                $this->solicitud->insertarHoras($idUsuario, $fechaPresentacion, $numSolicitud, $horas);
            }

            $archivosSubidos = $this->subirArchivosSolicitud($_FILES);

            //Insertamos archivos, si los hay
            foreach ($archivosSubidos['justificantes'] as $infoArchivo) {
                $extension = pathinfo($infoArchivo['nombreOriginal'], PATHINFO_EXTENSION);
                $nombreOriginal = pathinfo($infoArchivo['nombreOriginal'], PATHINFO_FILENAME);
                $nombreGenerado = pathinfo($infoArchivo['nombreGenerado'], PATHINFO_FILENAME);
                $tipoArchivo = 'justificantes';
                $rutaRelativa = RUTA_PROYECTO.'src/subidas/justificantes/';
                $this->solicitud->insertarArchivo($idUsuario, $fechaPresentacion, $numSolicitud, $nombreOriginal, $nombreGenerado, $extension, $tipoArchivo, $rutaRelativa);
            }

            foreach ($archivosSubidos['materiales'] as $infoArchivo) {
                $extension = pathinfo($infoArchivo['nombreOriginal'], PATHINFO_EXTENSION);
                $nombreOriginal = pathinfo($infoArchivo['nombreOriginal'], PATHINFO_FILENAME);
                $nombreGenerado = pathinfo($infoArchivo['nombreGenerado'], PATHINFO_FILENAME);
                $tipoArchivo = 'materiales';
                $rutaRelativa = RUTA_PROYECTO.'src/subidas/material/';
                $this->solicitud->insertarArchivo($idUsuario, $fechaPresentacion, $numSolicitud, $nombreOriginal, $nombreGenerado, $extension, $tipoArchivo, $rutaRelativa);
            }

            return 'avisoexito';
        }

        /**
         * Elimina una solicitud de la base de datos.
         *
         * @return string - Devuelve la vista 'avisoexito' si la eliminación fue realizada con éxito.
         */
        public function borrarSolicitud() {
            $idSolicitud = $_GET['id'];

            $this->curso->eliminarSolicitud($idSolicitud);

            return 'avisoexito';
        }

    }