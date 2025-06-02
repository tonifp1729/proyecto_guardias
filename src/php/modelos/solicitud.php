<?php
    require_once 'db.php';

    class Solicitud {
        private $conexion;

        public function __construct() {
            $db = new Conexiondb();
            $this->conexion = $db->conexion;
        }

        /**
         * 
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
         * Modifica el valor del estado
         */
        public function actualizarEstado($idUsuario, $fechaPresentacion, $num, $estado) {
            $sql = "UPDATE Solicitud SET estado = ? WHERE id_Usuario = ? AND fecha_presentacion = ? AND num = ?";
            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param('sisi', $estado, $idUsuario, $fechaPresentacion, $num);
            return $consulta->execute();
        }

        /**
         * Obtenemos las solicitudes de un usuario en concreto durante un curso específico en orden decreciente por la fecha de presentación.
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
         * Con este método obtenemos los motivos que utilizará el usuario como justificación de la ausencia.
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
         * Comprobamos la existencia de solapamiento con entre las solicitudes presentadas
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
         * Inserta la nueva solicitud realizada por el usuario.
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
         * En los casos en que las ausencias sean con una fecha (inicio y fin) concreta, se señalarán las horas a en las que se ausentarán.
         * El método guarda las horas de ausencia de la solicitud.
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
         * Hace inserción en la base de datos de los archivos de la solicitud.
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
         * Devuelve una solicitud concreta a partir de su clave primaria.
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
         * 
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
         * 
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
         * Actualiza una solicitud ya existente.
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
         * 
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