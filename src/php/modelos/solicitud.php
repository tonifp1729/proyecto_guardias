<?php
    require_once 'db.php';

    class Solicitud {
        private $conexion;

        public function __construct() {
            $db = new Conexiondb();
            $this->conexion = $db->conexion;
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
         * Listamos las solicitudes realizadas por el usuario en el curso activo.
         */
        public function listarSolicitudesActivasUsuario() {
            $sql = "SELECT id, nombre, apellidos FROM Usuario WHERE correo != 'dirsecundaria.guadalupe@fundacionloyola.es'";

            $consulta = $this->conexion->prepare($sql);
            $consulta->execute();
            $resultado = $consulta->get_result();

            $usuarios = [];

            while ($usuario = $resultado->fetch_assoc()) {
                $usuarios[] = $usuario;
            }

            return $usuarios;
        }

        /**
         * Cargamos los archivos de una solicitud
         */
        public function listarArchivosSolicitud($idUsuario, $fechaInicioAusencia) {
            $sql = "";
            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param("is", $idUsuario, $fechaInicioAusencia);
            $consulta->execute();
            $resultado = $consulta->get_result();
        
            $archivos = [];
            if ($resultado->num_rows > 0) {
                while ($archivo = $resultado->fetch_assoc()) {
                    $archivos[] = $archivo;
                }
            }
        
            return $archivos;
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
        public function insertarArchivo($idUsuario, $fechaPresentacion, $numSolicitud, $nombreOriginal, $nombreGenerado, $tipoArchivo, $ruta) {
            $sql = "INSERT INTO Archivo (id_Usuario_Solicitud, fecha_presentacion, num, nombre_original, nombre_generado, tipo_archivo, ruta_archivo)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("issssss", $idUsuario, $fechaPresentacion, $numSolicitud, $nombreOriginal, $nombreGenerado, $tipoArchivo, $ruta);
            $stmt->execute();
        }
    }