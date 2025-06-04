<?php
    require_once 'db.php';

    /**
     * Clase Solicitud
     * 
     * Esta clase gestiona todas las operaciones relacionadas con las solicitudes de ausencia.
     * 
     * @author - Leandro José Paniagua Balbuena y Antonio Manuel Figueroa Pinilla
     */
    class Solicitud {
        private $conexion;

        /**
         * Constructor del modelo.
         * Establece la conexión con la base de datos utilizando la clase Conexiondb.
         */
        public function __construct() {
            $db = new Conexiondb();
            $this->conexion = $db->conexion;
        }

        /**
         * Obtiene todas las solicitudes activas (no finalizadas) de un curso específico y la información del usuario que la realizó
         * (nombre y apellidos)
         *
         * @param int $idCurso - ID del curso.
         * @return array $solicitudes - Lista de solicitudes asociadas al curso.
         */
        public function obtenerSolicitudesPorCurso($idCurso) {
            $sql = "SELECT s.id_Usuario, s.num, s.fecha_presentacion, s.fecha_inicio_ausencia, s.fecha_fin_ausencia, s.estado, s.descripcion_solicitud, s.comentario_material, m.nombre AS motivo, u.nombre AS nombre_usuario, u.apellidos AS apellidos_usuario FROM Solicitud s INNER JOIN Motivo m ON s.id_Motivo = m.id INNER JOIN Usuario u ON s.id_Usuario = u.id WHERE s.id_Curso = ? AND s.fecha_fin_ausencia > CURDATE() ORDER BY s.fecha_presentacion DESC";

            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param("i", $idCurso);
            $consulta->execute();
            $resultado = $consulta->get_result();
            $solicitudes = [];

            while ($solicitud = $resultado->fetch_assoc()) {
                $solicitudes[] = $solicitud;
            }

            $consulta->close();

            return $solicitudes;
        }

        /**
         * Actualiza el estado de una solicitud específica.
         *
         * @param int $idUsuario - ID del usuario que presentó la solicitud.
         * @param string $fechaPresentacion - Fecha de presentación de la solicitud (YYYY-MM-DD).
         * @param int $num - Número de la solicitud en ese día.
         * @param string $estado - Nuevo estado ('p', 'a', 'r', etc.).
         * @return bool - True si se actualizó correctamente, false si hubo error.
         */
        public function actualizarEstado($idUsuario, $fechaPresentacion, $num, $estado) {
            $sql = "UPDATE Solicitud SET estado = ? WHERE id_Usuario = ? AND fecha_presentacion = ? AND num = ?";
            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param('sisi', $estado, $idUsuario, $fechaPresentacion, $num);
            return $consulta->execute();
        }

        /**
         * Obtiene las solicitudes activas de un usuario durante un curso determinado.
         *
         * @param int $idUsuario - ID del usuario.
         * @param int $idCurso - ID del curso.
         * @return array $solicitudes - Lista de solicitudes del usuario en ese curso.
         */
        public function obtenerSolicitudesPorUsuarioYCurso($idUsuario, $idCurso) {
            $sql = "SELECT s.num, s.fecha_presentacion, s.fecha_inicio_ausencia, s.fecha_fin_ausencia, s.estado, s.descripcion_solicitud, s.comentario_material, m.nombre AS motivo FROM Solicitud s INNER JOIN Motivo m ON s.id_Motivo = m.id WHERE s.id_Usuario = ? AND s.id_Curso = ? AND s.fecha_fin_ausencia > CURDATE() ORDER BY s.fecha_presentacion DESC";
            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param("ii", $idUsuario, $idCurso);
            $consulta->execute();
            $resultado = $consulta->get_result();
            $solicitudes = [];

            while ($solicitud = $resultado->fetch_assoc()) {
                $solicitudes[] = $solicitud;
            }

            $consulta->close();

            return $solicitudes;
        }

        /**
         * Obtiene todos los motivos de ausencia disponibles para seleccionar en la solicitud.
         *
         * @return array $motivos - Lista de motivos disponibles.
         */
        public function obtenerMotivos() {
            $sql = "SELECT id, nombre FROM Motivo";
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute();
            $resultado = $consulta->get_result();
            
            $motivos = [];
            if ($resultado->num_rows > 0) {
                while ($motivo = $resultado->fetch_assoc()) {
                    $motivos[] = $motivo;
                }
            }
            
            return $motivos;
        }


        /**
         * Comprueba si existe solapamiento de fechas con otras solicitudes activas presentadas por el usuario.
         *
         * @param int $idUsuario - ID del usuario.
         * @param string $fechaInicio - Fecha de inicio de la nueva ausencia.
         * @param string $fechaFin - Fecha de fin de la nueva ausencia.
         * @return bool - True si hay solapamiento, false si no.
         */
        public function existeSolapamiento($idUsuario, $fechaInicio, $fechaFin) {
            $sql = "SELECT COUNT(*) as total FROM Solicitud WHERE id_Usuario = ? AND estado != 'r' AND ((fecha_inicio_ausencia <= ? AND fecha_fin_ausencia >= ?))";

            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param("iss", $idUsuario, $fechaFin, $fechaInicio);
            $consulta->execute();
            $resultado = $consulta->get_result()->fetch_assoc();
            return $resultado['total'] > 0;
        }

        /**
         * Inserta una nueva solicitud de ausencia, realizada por un usuario, en la base de datos.
         *
         * @param int $idUsuario - ID del usuario que presenta la solicitud.
         * @param int $idCurso - ID del curso al que pertenece.
         * @param int $idMotivo - ID del motivo seleccionado.
         * @param string $fechaInicio - Fecha de inicio de la ausencia.
         * @param string $fechaFin - Fecha de fin de la ausencia.
         * @param string $estado - Estado inicial de la solicitud.
         * @param string $descripcion - Descripción del motivo de la ausencia.
         * @param string $comentario - Comentario adicional sobre material, si aplica.
         * @return array|false - Array con 'num' y 'fecha_presentacion' si tuvo éxito, false si hubo error.
         */
        public function insertarSolicitud($idUsuario, $idCurso, $idMotivo, $fechaInicio, $fechaFin, $estado, $descripcion, $comentario) {
            $fechaPresentacion = date('Y-m-d');

            $sqlNum = "SELECT COALESCE(MAX(num), 0) + 1 AS siguiente FROM Solicitud WHERE id_Usuario = ? AND fecha_presentacion = ?";
            $consultaNum = $this->conexion->prepare($sqlNum);
            $consultaNum->bind_param("is", $idUsuario, $fechaPresentacion);
            $consultaNum->execute();
            $resultado = $consultaNum->get_result();
            $fila = $resultado->fetch_assoc();
            $numSolicitud = $fila['siguiente'];

            $sql = "INSERT INTO Solicitud (id_Usuario, fecha_presentacion, num, id_Curso, id_Motivo, fecha_inicio_ausencia, fecha_fin_ausencia, estado, descripcion_solicitud, comentario_material) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param(
                "isiiisssss",
                $idUsuario,
                $fechaPresentacion,
                $numSolicitud,
                $idCurso,
                $idMotivo,
                $fechaInicio,
                $fechaFin,
                $estado,
                $descripcion,
                $comentario
            );

            if ($consulta->execute()) {
                return [
                    'num' => $numSolicitud,
                    'fecha_presentacion' => $fechaPresentacion
                ];
            } else {
                return false;
            }
        }

        /**
         * Inserta las horas específicas de ausencia asociadas a una solicitud que se realiza en un día concreto.
         *
         * @param int $idUsuario - ID del usuario.
         * @param string $fechaPresentacion - Fecha de presentación de la solicitud.
         * @param int $numSolicitud - Número de la solicitud.
         * @param array $horas - Array de horas (enteros) que se ausentará.
         */
        public function insertarHoras($idUsuario, $fechaPresentacion, $numSolicitud, $horas) {
            $sql = "INSERT INTO Hora (id_Usuario_Solicitud, fecha_presentacion, num, num_hora) VALUES (?, ?, ?, ?)";
            $consulta = $this->conexion->prepare($sql);

            foreach ($horas as $hora) {
                $consulta->bind_param("isii", $idUsuario, $fechaPresentacion, $numSolicitud, $hora);
                $consulta->execute();
            }
        }
        
        /**
         * Guarda los datos de un archivo presentado en una solicitud de ausencia.
         *
         * @param int $idUsuario - ID del usuario.
         * @param string $fechaPresentacion - Fecha de presentación.
         * @param int $numSolicitud - Número de la solicitud.
         * @param string $nombreOriginal - Nombre original del archivo subido.
         * @param string $nombreGenerado - Nombre generado para almacenar el archivo.
         * @param string $extension - Extensión o tipo del archivo.
         * @param string $rutaRelativa - Ruta relativa de almacenamiento en el servidor.
         * @return bool - True si se insertó correctamente, false si falló.
         */
        public function insertarArchivo($idUsuario, $fechaPresentacion, $numSolicitud, $nombreOriginal, $nombreGenerado, $extension, $rutaRelativa) {
            $sql = "INSERT INTO Archivo (id_Usuario_Solicitud, fecha_presentacion, num, nombre_original, nombre_generado, tipo_archivo, ruta_archivo) VALUES (?, ?, ?, ?, ?, ?, ?)";

            $consulta = $this->conexion->prepare($sql);

            $consulta->bind_param(
                "isissss",
                $idUsuario,
                $fechaPresentacion,
                $numSolicitud,
                $nombreOriginal,
                $nombreGenerado,  
                $extension,
                $rutaRelativa
            );

            return $consulta->execute();
        }

        /**
         * Devuelve una solicitud concreta utilizando para esto su clave primaria compuesta.
         *
         * @param int $idUsuario - ID del usuario.
         * @param string $fechaPresentacion - Fecha de presentación.
         * @param int $num - Número de solicitud.
         * @return array|null - Datos de la solicitud o null si no existe.
         */
        public function obtenerSolicitud($idUsuario, $fechaPresentacion, $num) {
            $sql = "SELECT * FROM Solicitud WHERE id_Usuario = ? AND fecha_presentacion = ? AND num = ?";
            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param("isi", $idUsuario, $fechaPresentacion, $num);
            $consulta->execute();
            $resultado = $consulta->get_result();
            return $resultado->fetch_assoc();
        }

        /**
         * Obtiene la lista de horas en las que un usuario se ausenta según su solicitud.
         *
         * @param int $idUsuario - ID del usuario.
         * @param string $fechaPresentacion - Fecha de presentación de la solicitud.
         * @param int $num - Número de la solicitud.
         * @return array $horas - Lista de horas de ausencia (números de hora).
         */
        public function obtenerHorasDeSolicitud($idUsuario, $fechaPresentacion, $num) {
            $sql = "SELECT num_hora FROM Hora WHERE id_Usuario_Solicitud = ? AND fecha_presentacion = ? AND num = ?";
            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param("isi", $idUsuario, $fechaPresentacion, $num);
            $consulta->execute();
            $resultado = $consulta->get_result();
            
            $horas = [];
            while ($fila = $resultado->fetch_assoc()) {
                $horas[] = (int)$fila['num_hora'];
            }

            return $horas;
        }

        /**
         * Obtiene todos los archivos asociados a una solicitud.
         *
         * @param int $idUsuario - ID del usuario.
         * @param string $fechaPresentacion - Fecha de presentación.
         * @param int $num - Número de solicitud.
         * @return array $archivos - Lista de archivos relacionados.
         */
        public function obtenerArchivosDeSolicitud($idUsuario, $fechaPresentacion, $num) {
            $sql = "SELECT * FROM Archivo WHERE id_Usuario_Solicitud = ? AND fecha_presentacion = ? AND num = ?";
            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param("isi", $idUsuario, $fechaPresentacion, $num);
            $consulta->execute();
            $resultado = $consulta->get_result();

            $archivos = [];
            while ($fila = $resultado->fetch_assoc()) {
                $archivos[] = $fila;
            }

            return $archivos;
        }

        /**
         * Actualiza los datos de una solicitud ya existente.
         *
         * @param int $idUsuario - ID del usuario.
         * @param string $fechaPresentacion - Fecha de presentación de la solicitud.
         * @param int $numSolicitud - Número de la solicitud.
         * @param int $motivo - Nuevo motivo (ID).
         * @param string $descripcion - Nueva descripción.
         * @param string $comentario - Nuevo comentario adicional.
         * @return bool - True si la solicitud fue actualizada con éxito o false en caso contrario.
         */
        public function actualizarSolicitud($idUsuario, $fechaPresentacion, $numSolicitud, $motivo, $descripcion, $comentario) {
            $sql = "UPDATE Solicitud 
                    SET id_Motivo = ?, descripcion_solicitud = ?, comentario_material = ?
                    WHERE id_Usuario = ? AND fecha_presentacion = ? AND num = ?";
            
            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param("issisi", $motivo, $descripcion, $comentario, $idUsuario, $fechaPresentacion, $numSolicitud);
            
            return $consulta->execute();
        }

        /**
         * Elimina una solicitud de la base de datos usando su clave primaria.
         *
         * @param int $idUsuario - ID del usuario.
         * @param string $fechaPresentacion - Fecha de presentación.
         * @param int $numSolicitud - Número de solicitud.
         * @return bool - True si la solicitud fue eliminada correctamente o false si no.
         */
        public function eliminarSolicitud($idUsuario, $fechaPresentacion, $numSolicitud) {
            $sql = "DELETE FROM Solicitud WHERE id_Usuario = ? AND fecha_presentacion = ? AND num = ?";
            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param("isi", $idUsuario, $fechaPresentacion, $numSolicitud);
            $resultado = $consulta->execute();
            $consulta->close();

            return $resultado;
        }

    }