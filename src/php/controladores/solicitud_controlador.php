<?php
    require_once '/home/proyectosevg/public_html/10/src/config/path.php';
    require_once RUTA_MODELOS . 'solicitud.php';
    require_once RUTA_MODELOS . 'curso.php';

    /**
     * Controlador encargado de la gestión de solicitudes en toda la aplicación. 
     * 
     * @author Leandro José Paniagua Balbuena
     * @author Antonio Manuel Figueroa Pinilla
     */
    class Solicitud_controlador {

        /**
         * Instancia del modelo Solicitud
         * @var Solicitud
         */
        private $solicitud;

        /**
         * Instancia del modelo Curso
         * @var Curso
         */
        private $curso;

        /**
         * Constructor de la clase. Se instancian los modelos Solicitud y Curso.
         */
        public function __construct() {
            $this->solicitud = new Solicitud();
            $this->curso = new Curso();
        }

        /**
         * Genera un identificador único de solicitud para las vistas del administrador.
         *
         * El identificador resultante tiene el formato: `idUsuario_yyyymmdd_nn`, donde:
         * - `idUsuario` es el identificador del usuario que presentó la solicitud.
         * - `yyyymmdd` es la fecha de presentación sin guiones.
         * - `nn` es el número de solicitud de ese día, con al menos dos dígitos (relleno con ceros a la izquierda si es necesario).
         *
         * Este identificador permite al administrador diferenciar solicitudes de distintos usuarios
         * de forma precisa y ordenada en las interfaces de gestión.
         *
         * @param int|string $idUsuario - ID del usuario que presentó la solicitud.
         * @param string $fecha - Fecha de presentación de la solicitud (formato reconocible por strtotime).
         * @param int $num - Número de la solicitud presentada en ese día por el usuario.
         * @return string - Identificador único con el formato `idUsuario_yyyymmdd_nn`.
         */
        function generarIdentificadorParaAdministrador($idUsuario ,$fecha, $num) {

            $fechaFormateada = date("Ymd", strtotime($fecha));
            $numFormateado = str_pad($num, 2, "0", STR_PAD_LEFT);

            $identificadorSolicitud = $idUsuario . "_" . $fechaFormateada . "_" . $numFormateado;

            return $identificadorSolicitud;
        }

        /**
         * Genera un identificador único para una solicitud basada en su fecha de presentación y número.
         *
         * El identificador resultante tiene el siguiente formato: `yyyymmdd_nn`, donde:
         * - `yyyymmdd` es la fecha de presentación sin guiones.
         * - `nn` es el número de la solicitud con al menos dos dígitos (relleno con ceros a la izquierda si es necesario).
         *
         * Este identificador es útil para mostrar solicitudes de forma clara y ordenada en las vistas de usuario.
         *
         * @param string $fecha - Fecha de presentación de la solicitud (en formato reconocible por strtotime).
         * @param int $num - Número de solicitud presentado ese día.
         * @return string - Identificador único con el formato `yyyymmdd_nn`.
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
         * @return array Array asociativo con los datos para cargar la vista 'listarsolicitudes'.
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
         * Obtiene y prepara las solicitudes del curso activo para su gestión,
         * ya sea por parte del administrador o del moderador.
         *
         * Este método realiza las siguientes acciones:
         * - Verifica que exista una sesión activa y que el rol sea válido ('A' o 'M').
         * - Si el usuario no tiene permiso, redirige al inicio.
         * - Recupera los datos del curso activo desde la sesión.
         * - Obtiene las solicitudes asociadas al curso.
         * - Si el usuario es moderador, filtra las solicitudes excluyendo las suyas propias.
         * - Genera un identificador único para cada solicitud.
         *
         * @return array Array asociativo con los datos para cargar la vista 'cursoactual'.
         */
        public function obtenerSolicitudesGestion() {
            if (!isset($_SESSION['idCursoActivo'])) {
                session_start();
            }

            if ($_SESSION['rol'] !== 'A' && $_SESSION['rol'] !== 'M') {
                header('Location: index.php?accion=inicio');
                exit;
            }

            $idCursoActivo = $_SESSION['idCursoActivo'];

            $datosCurso = $this->curso->obtenerCurso($idCursoActivo);
            $solicitudes = $this->solicitud->obtenerSolicitudesPorCurso($idCursoActivo);

            
            if ($_SESSION['rol'] === 'M') {
                $solicitudes = array_filter($solicitudes, function ($solicitud) {
                    return $solicitud['id_Usuario'] !== $_SESSION['id'];
                });
            }

            foreach ($solicitudes as &$solicitud) { 
                $solicitud['identificador'] = $this->generarIdentificadorParaAdministrador($solicitud['id_Usuario'], $solicitud['fecha_presentacion'], $solicitud['num']);
            }

            return ['vista' => 'cursoactual', 'curso' => $datosCurso, 'solicitudes' => $solicitudes];
        }

        /**
         * Obtiene las solicitudes asociadas a un curso específico para su gestión por parte del administrador.
         *
         * - Verifica que el usuario tenga una sesión activa y sea administrador. Si no es así, redirige al inicio.
         * - Recupera el curso correspondiente al ID recibido por GET y todas las solicitudes asociadas a ese curso.
         * - A cada solicitud se le añade un identificador único para su visualización por parte del administrador.
         *
         * @return array - Retorna un array asociativo con los datos para la carga de la vista del listado de solicitudes.
         */
        public function obtenerSolicitudesCurso() {

            if (!isset($_SESSION['id'])) {
                session_start();
            }

            if ($_SESSION['rol'] !== 'A') {
                header('Location: index.php?accion=inicio');
                exit;
            }

            $idCurso = $_GET['id'];

            $datosCurso = $this->curso->obtenerCurso($idCurso);
            $solicitudes = $this->solicitud->obtenerSolicitudesPorCurso($idCurso);

            foreach ($solicitudes as &$solicitud) { 
                $solicitud['identificador'] = $this->generarIdentificadorParaAdministrador($solicitud['id_Usuario'], $solicitud['fecha_presentacion'], $solicitud['num']);
            }

            return ['vista' => 'cargarcurso', 'curso' => $datosCurso, 'solicitudes' => $solicitudes];
        }

        /**
         * Carga los datos necesarios para mostrar la vista de gestión de una solicitud específica.
         *
         * Obtiene los parámetros de la solicitud mediante GET, incluyendo el ID del usuario, la fecha de presentación
         * y el número de solicitud. Luego recupera la información completa de la solicitud, incluyendo horas seleccionadas,
         * motivos y archivos relacionados, clasificados por tipo.
         *
         * @return array - Retorna un array asociativo con toda la información.
         */
        public function cargarGestionarSolicitud() {
            $idUsuario = $_GET['id'] ?? null;
            $fechaPresentacion = $_GET['fecha'] ?? null;
            $num = $_GET['num'] ?? null;

            $solicitud = $this->solicitud->obtenerSolicitud($idUsuario, $fechaPresentacion, $num);

            $horasSeleccionadas = $this->solicitud->obtenerHorasDeSolicitud($idUsuario, $fechaPresentacion, $num);
            $motivos = $this->solicitud->obtenerMotivos();
            $archivos = $this->solicitud->obtenerArchivosDeSolicitud($idUsuario, $fechaPresentacion, $num);

            // Clasificamos los archivos por tipo de ruta
            $justificantes = array_filter($archivos, fn($archivo) => $archivo['ruta_archivo'] === 'justificantes');
            $materiales = array_filter($archivos, fn($archivo) => $archivo['ruta_archivo'] === 'materiales');

            return ['vista' => 'gestionarsolicitud', 'solicitud' => $solicitud, 'horasSeleccionadas' => $horasSeleccionadas, 'motivos' => $motivos, 'justificantes' => $justificantes, 'materiales' => $materiales];
        }
        
        /**
         * Gestiona la actualización del estado de una solicitud específica.
         * 
         * Recupera los parámetros necesarios desde un GET, incluyendo el ID del usuario, la fecha de presentación, el número de solicitud y el nuevo estado. 
         * Luego, el modelo realiza la actualización del estado de la solicitud.
         * 
         * @return string - Devuelve la cadena 'avisoexito' para indicar que la operación fue exitosa.
         */
        public function gestionarEstado() {
            $idUsuario = $_GET['id'] ?? null;
            $fechaPresentacion = $_GET['fecha'] ?? null;
            $num = $_GET['num'] ?? null;
            $estado = $_GET['estado'] ?? null;

            $this->solicitud->actualizarEstado($idUsuario, $fechaPresentacion, $num, $estado);

            return 'avisoexito';
        }

        /**
         * Carga los datos necesarios para mostrar el formulario de nueva solicitud.
         * 
         * Verifica si existe un curso activo en la sesión. Si no hay curso activo, devuelve una cadena indicando dicho estado.
         * En caso contrario, recupera el ID del curso activo y los motivos disponibles desde el modelo de solicitud.
         * 
         * @return array|string - Devuelve un array asociativo con los datos necesarios para la vista 'formnuevasolicitud'
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
         * Carga los datos necesarios para mostrar una solicitud específica sin posibilidad de modificación.
         * 
         * Obtiene los parámetros necesarios desde la URL mediante GET: ID del usuario, fecha de presentación y número de solicitud.
         * Recupera la solicitud correspondiente, las horas seleccionadas, los motivos disponibles y los archivos asociados,
         * clasificando estos últimos por tipo (justificantes y materiales).
         *
         * @return array - Retorna un array asociativo con toda la información (vista, datos de solicitud, horas marcadas...).
         */
        public function cargarVerSolicitud() {
            $idUsuario = $_GET['id'] ?? null;
            $fechaPresentacion = $_GET['fecha'] ?? null;
            $num = $_GET['num'] ?? null;

            $solicitud = $this->solicitud->obtenerSolicitud($idUsuario, $fechaPresentacion, $num);

            $horasSeleccionadas = $this->solicitud->obtenerHorasDeSolicitud($idUsuario, $fechaPresentacion, $num);
            $motivos = $this->solicitud->obtenerMotivos();
            $archivos = $this->solicitud->obtenerArchivosDeSolicitud($idUsuario, $fechaPresentacion, $num);

            // Clasificamos los archivos por tipo de ruta
            $justificantes = array_filter($archivos, fn($archivo) => $archivo['ruta_archivo'] === 'justificantes');
            $materiales = array_filter($archivos, fn($archivo) => $archivo['ruta_archivo'] === 'materiales');

            return ['vista' => 'versolicitud', 'solicitud' => $solicitud, 'horasSeleccionadas' => $horasSeleccionadas, 'motivos' => $motivos, 'justificantes' => $justificantes, 'materiales' => $materiales];
        }

        /**
         * Carga los datos necesarios para mostrar el formulario de modificación de la que se desea modificar solicitud.
         * 
         * Extrae el ID del usuario, la fecha de presentación y el número de solicitud desde el GET, recupera la solicitud correspondiente, las horas seleccionadas,
         * los motivos disponibles y los archivos asociados, clasificando estos últimos por tipo.
         *
         * @return array - Retorna un array asociativo con otros array para cargar toda la información de la vista.
         */
        public function cargarModificarSolicitud() {
            $idUsuario = $_GET['id'] ?? null;
            $fechaPresentacion = $_GET['fecha'] ?? null;
            $num = $_GET['num'] ?? null;

            $solicitud = $this->solicitud->obtenerSolicitud($idUsuario, $fechaPresentacion, $num);

            $horasSeleccionadas = $this->solicitud->obtenerHorasDeSolicitud($idUsuario, $fechaPresentacion, $num);
            $motivos = $this->solicitud->obtenerMotivos();
            $archivos = $this->solicitud->obtenerArchivosDeSolicitud($idUsuario, $fechaPresentacion, $num);

            // Clasificamos los archivos por tipo de ruta
            $justificantes = array_filter($archivos, fn($archivo) => $archivo['ruta_archivo'] === 'justificantes');
            $materiales = array_filter($archivos, fn($archivo) => $archivo['ruta_archivo'] === 'materiales');

            return ['vista' => 'formmodsolicitud', 'solicitud' => $solicitud, 'horasSeleccionadas' => $horasSeleccionadas, 'motivos' => $motivos, 'justificantes' => $justificantes, 'materiales' => $materiales];
        }

        /**
         * Elimina físicamente un archivo del servidor.
         * Se ejecuta en el momento en que se hace una modificación de una solicitud que requiere de la eliminación de un archivo.
         * 
         * Este método se utiliza durante la modificación de una solicitud cuando se requiere
         * eliminar uno de los archivos previamente subidos por el usuario.
         *
         * @param array $archivo Array asociativo que debe contener: 'nombre_generado' (el nuevo nombre del archivo), 'ruta_archivo' (subdirectorio dentro de src/subidas) y 'tipo_archivo' (la extensión del archivo)
         */
        private function eliminarArchivoFisico($archivo) {
            $ruta = RUTA_PROYECTO . 'src/subidas/' . $archivo['ruta_archivo'] . '/' . $archivo['nombre_generado'] . '.' . $archivo['tipo_archivo'];

            if (file_exists($ruta)) {
                unlink($ruta);
            }
        }

        /**
         * Procesa una solicitud (POST). Se modifica una solicitud previamente planteada.
         * 
         * Este método realiza las siguientes acciones:
         * 1. Recoge los datos del formulario enviado por POST.
         * 2. Actualiza la solicitud en la base de datos.
         * 3. Elimina los archivos marcados por el usuario.
         * 4. Sube y registra nuevos archivos, si se proporcionan.
         *
         * Requiere que existan en $_POST:
         * - id_usuario
         * - fecha_presentacion
         * - num (número de solicitud)
         * - motivo
         * - descripcion
         * - comentario
         * - archivos_a_eliminar (opcional)
         * - archivos_info (opcional)
         *
         * Requiere que existan en $_FILES:
         * - justificantes (opcional)
         * - materiales (opcional)
         *
         * @return string Devuelve 'avisoexito' si la modificación se ha procesado correctamente o 'saludo' si no es una petición POST.
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
                            $datosArchivo = ['ruta_archivo' => $archivoInfo['ruta'], 'nombre_generado' => $archivoInfo['nombre'], 'tipo_archivo' => $archivoInfo['tipo']];

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
         * 
         * 1. Extrae el nombre inicial.
         * 2. Extrae la extensión.
         * 3. Codificamos el nombre base por medio de un hash básico.
         * 4. Combinamos el nuevo nombre que hemos generado con la extensión.
         * 
         * @param string $archivo Nombre original del archivo (por ejemplo, 'documento.pdf').
         * @return string Nombre único generado con extensión (por ejemplo, '662fb190b...a.pdf').
         */
        function generarNombreUnico($archivo) {
            $nombreBase = pathinfo($archivo, PATHINFO_FILENAME);
            $extension = pathinfo($archivo, PATHINFO_EXTENSION);
            $nombreCodificado = uniqid().'_'.hash('sha256', $nombreBase);
            return $nombreCodificado.'.'.$extension;
        }

        /**
         * Procesa y guarda un archivo subido en el subdirectorio correspondiente.
         *
         * - Crea el directorio si no existe.
         * - Genera un nombre único para evitar la duplicidad de los archivos subidos, así no hay posible sobrescritura o confusión con los archivos.
         * - Si la subida es exitosa, devuelve información relevante del archivo.
         *
         * @param array $archivo - Array con los datos del archivo subido (como en $_FILES).
         * @param string $subdirectorio - Nombre del subdirectorio donde se guardará el archivo (por ejemplo, 'justificantes' o 'materiales').
         * @return array|false - Array con información del archivo subido si tiene éxito, o false en caso de error.
         */
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
         * @param array $archivos - El array $_FILES con los campos 'justificantes' y/o 'materiales'.
         * @return array $datos - Un array con los nombres de los archivos subidos por cada tipo.
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
         *
         * Este método:
         * - Valída la existencia de los campos necesarios y el curso activo.
         * - Comprueba que las fechas estén dentro del curso y no sean anteriores a mañana.
         * - Rechaza si hay solapamientos con solicitudes anteriores del mismo usuario.
         * - Inserta la solicitud, las horas seleccionadas (si las hay) y los archivos adjuntos.
         *
         * @return array|string - Un array que devuelve la vista y el error en caso de algún problema o la vista de éxito si la solicitud se crea correctamente.
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
         * Elimina una solicitud de la base de datos, retirando los archivos asociados del servidor durante el proceso.
         * Así evitamos la existencia de archivos que no estén relacionados a una solicitud.
         * 
         * @return string - Devuelve la vista 'avisoexito' si la eliminación fue realizada con éxito.
         */
        public function eliminarSolicitud() {
            $idUsuario = $_GET['id'] ?? null;
            $fechaPresentacion = $_GET['fecha'] ?? null;
            $num = $_GET['num'] ?? null;

            //Obtener archivos relacionados
            $archivos = $this->solicitud->obtenerArchivosDeSolicitud($idUsuario, $fechaPresentacion, $num);

            //Eliminar físicamente los archivos del servidor
            foreach ($archivos as $archivo) {
                $this->eliminarArchivoFisico($archivo);
            }

            //Eliminar la solicitud
            $exito = $this->solicitud->eliminarSolicitud($idUsuario, $fechaPresentacion, $num);

            if ($exito) {
                return 'avisoexito';
            } else {
                return 'saludo';
            }
        }

    }