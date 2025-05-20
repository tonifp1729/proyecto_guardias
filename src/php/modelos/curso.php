<?php

    require_once 'db.php';

    class Curso {
        private $conexion;

        public function __construct() {
            //Creamos un objeto e inicializamos la conexión a la base de datos
            $db = new Conexiondb();
            $this->conexion = $db->conexion;
        }

        /**
         * Busca un curso pendiente (estado 'P') cuya fecha de inicio y fin abarquen la fecha proporcionada.
         * Este método se utiliza para verificar si existe un curso en estado pendiente que deba activarse,
         * considerando que la fecha actual esté dentro del rango de duración del curso.
         * @param string - $fecha Fecha en formato 'Y-m-d' que se compara con las fechas de inicio y fin del curso.
         * @return int|null - Devuelve el ID del curso si se encuentra uno coincidente, o null si no existe.
         */
        public function buscarCursoPendiente($fecha) {
            $sql = "SELECT id FROM Curso WHERE estado = 'P' AND ? BETWEEN fecha_inicio AND fecha_fin";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("s", $fecha);
            $stmt->execute();
            $stmt->bind_result($idCurso);
            $stmt->fetch();
            $stmt->close();

            return $idCurso ?: null;
        }

        /**
         * Hace el alta de un nuevo curso.
         */
        public function insertarCurso($fechaInicio, $fechaFin, $anoAcademico, $estado) {
            $SQL = "INSERT INTO Curso (fecha_inicio, fecha_fin, anio_academico, estado) VALUES (?, ?, ?, ?)";
            
            $consulta = $this->conexion->prepare($SQL);
            $consulta->bind_param("ssss", $fechaInicio, $fechaFin, $anoAcademico, $estado);
            $consulta->execute();
            $consulta->close();
        }

        /**
         * Devolverá los datos del curso activo (estado = 'A') en caso de que este exista, de lo contrario devuelve un nulo.
         * @return array|null - Array asociativo con los datos del curso o null.
         */
        public function cursoActivo() {
            $SQL = "SELECT id, fecha_inicio, fecha_fin, anio_academico FROM Curso WHERE estado = 'A' LIMIT 1";

            $consulta = $this->conexion->prepare($SQL);
            $consulta->execute();

            $curso = null;
            $consulta->bind_result($idCurso, $fechaInicio, $fechaFinalizacion, $anoAcademico);
            if ($consulta->fetch()) {
                $curso = [
                    'idCurso' => $idCurso,
                    'fechaInicio' => $fechaInicio,
                    'fechaFinalizacion' => $fechaFinalizacion,
                    'anoAcademico' => $anoAcademico
                ];
            }
            $consulta->close();

            return $curso;
        }

        /**
         * Devolverá los datos del curso según la id del curso.
         * @param idcurso - id de Curso del curso que se requiere.
         * @return array|null - Array asociativo con los datos del curso o null.
         */
        public function obtenerCurso($idCurso) {
            $sql = "SELECT * FROM Curso WHERE id = ?";

            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param("i", $idCurso);
            $consulta->execute();

            $curso = null;
            $consulta->bind_result($idCurso, $fechaInicio, $fechaFinalizacion, $anoAcademico, $estado);
            if ($consulta->fetch()) {
                $curso = [
                    'idCurso' => $idCurso,
                    'fechaInicio' => $fechaInicio,
                    'fechaFinalizacion' => $fechaFinalizacion,
                    'anoAcademico' => $anoAcademico,
                    'estado' => $estado
                ];
            }
            $consulta->close();

            return $curso;
        }

        /**
         * Devuelve el ID del curso activo si este existe o un nulo en caso de que no.
         * @return id|null - ID curso con estado 'A'.
         */
        public function hayCursoActivo() {
            $SQL = "SELECT id FROM Curso WHERE estado = 'A' LIMIT 1";
            $consulta = $this->conexion->prepare($SQL);
            $consulta->execute();
            $resultado = $consulta->get_result();

            $idCurso = null;
            if ($fila = $resultado->fetch_assoc()) {
                $idCurso = $fila['id'];
            }

            $consulta->close();
            return $idCurso;
        }

        /**
         * Cambia el estado de los cursos activos ('A') a finalizados ('F') si su fecha_fin ya pasó.
         */
        public function finalizarCursos() {
            $SQL = "UPDATE Curso SET estado = 'F' WHERE estado = 'A' AND fecha_fin < CURDATE()";
            $this->conexion->query($SQL);
        }

        /**
         * Actualiza el estado del curso a activo ('A').
         * @param idCurso - ID del curso que va a ser activado.
         */
        public function activarCurso($idCurso) {
            $sql = "UPDATE Curso SET estado = 'A' WHERE id = ?";

            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param("i", $idCurso);
            $consulta->execute();
            $consulta->close();
        }

        /**
         * Verifica que las fechas introducidas no coincidan con las de otros cursos, evitando que se solapen dos cursos distintos en el tiempo.
         * Comprobamos que la fecha de inicio o la de fin no se encuentren entre las fechas marcadas en los anteriores cursos
         * Después comrpobamos que las fechas de los antiguos cursos no se encuentren entre la de los nuevos 
         * @param string $fechaInicio - Fecha de inicio del nuevo curso.
         * @param string $fechaFin - Fecha de fin del nuevo curso.
         * @return bool - Devolverá true si hay solapamiento, false si no lo hay.
         */
        public function existeCoincidencia($fechaInicio, $fechaFin) {
            $SQL = "SELECT COUNT(*) FROM Curso WHERE (? BETWEEN fecha_inicio AND fecha_fin) OR (? BETWEEN fecha_inicio AND fecha_fin) OR (fecha_inicio BETWEEN ? AND ?) OR (fecha_fin BETWEEN ? AND ?)";

            $consulta = $this->conexion->prepare($SQL);
            $consulta->bind_param("ssssss", $fechaInicio, $fechaFin, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin);
            $consulta->execute();
            $consulta->bind_result($count);
            $consulta->fetch();
            $consulta->close();

            return $count > 0;
        }

        /**
         * Verifica si existen solapamientos de fechas con otros cursos (excluyendo el curso actual).
         *
         * Comprueba si las fechas de inicio o fin propuestas se solapan con las fechas de otros cursos,
         * ignorando el curso con el ID proporcionado.
         *
         * @param string - $fechaInicio Fecha de inicio propuesta.
         * @param string - $fechaFin Fecha de fin propuesta.
         * @param int - $idCurso ID del curso que se está modificando.
         * @return bool - Devuelve true si hay solapamientos con otros cursos, false si no.
         */
        public function existeSolapamiento($fechaInicio, $fechaFin, $idCurso) {
            $sql = "SELECT COUNT(*) FROM Curso WHERE id != ? AND ((? BETWEEN fecha_inicio AND fecha_fin) OR (? BETWEEN fecha_inicio AND fecha_fin) OR (fecha_inicio BETWEEN ? AND ?) OR (fecha_fin BETWEEN ? AND ?))";

            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param("sssssss", $idCurso, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin);
            $consulta->execute();
            $consulta->bind_result($count);
            $consulta->fetch();
            $consulta->close();

            return $count > 0;
        }


        /**
         * Obtiene todos los cursos registrados en la base de datos.
         * Devuelve un array con la información básica de cada curso, incluyendo su estado.
         * @return array Lista de cursos como arrays asociativos.
         */
        public function listarCursos() {
            $sql = "SELECT id, anio_academico, fecha_inicio, fecha_fin, estado FROM Curso";

            $consulta = $this->conexion->prepare($sql);
            $consulta->execute();
            $resultado = $consulta->get_result();

            $cursos = [];

            while ($fila = $resultado->fetch_assoc()) {
                $cursos[] = $fila;
            }

            return $cursos;
        }

        /**
         * Elimina un curso de la base de datos según su ID.
         * @param int $idCurso ID del curso a eliminar.
         * @return bool Devuelve true si el curso fue eliminado correctamente, false si no.
         */
        public function eliminarCurso($idCurso) {
            $sql = "DELETE FROM Curso WHERE id = ?";
            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param("i", $idCurso);
            $consulta->execute();

            return $consulta->affected_rows > 0;
        }

        /**
         * Verifica si existen solicitudes vinculadas a un curso con fecha de inicio anterior a una fecha dada.
         * Este método se utiliza para validar que un curso activo no tenga solicitudes afectadas por el cambio de fechas.
         * @param int $idCurso ID del curso que se está evaluando.
         * @param string $nuevaFin Nueva fecha de fin propuesta para el curso.
         * @return bool Devuelve true si hay solicitudes antes de la nueva fecha de fin, false si no.
         */
        public function haySolicitudesAntesDe($idCurso, $nuevaFin) {
            $sql = "SELECT COUNT(*) as total FROM Solicitud WHERE id_Curso = ? AND fecha_inicio_ausencia < ?";

            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param("is", $idCurso, $nuevaFin);
            $consulta->execute();
            $consulta->bind_result($cantidad);
            $consulta->fetch();
            $consulta->close();

            return $cantidad > 0;
        }

        /**
         * Actualiza los datos de un curso en la base de datos.
         * Modifica la fecha de inicio, fecha de fin, año académico y estado del curso con el ID proporcionado.
         * @param int - $idCurso ID del curso a modificar.
         * @param string - $fechaInicio Nueva fecha de inicio.
         * @param string - $fechaFin Nueva fecha de fin.
         * @param string - $anioAcademico Año académico en formato 'yy/yy'.
         * @param string - $estado Nuevo estado del curso ('A', 'F', 'P').
         * @return bool - Devuelve true si se actualizó al menos una fila, false si no hubo cambios.
         */
        public function modificarCurso($idCurso, $fechaInicio, $fechaFin, $anioAcademico, $estado) {
            $sql = "UPDATE Curso SET fecha_inicio = ?, fecha_fin = ?, anio_academico = ?, estado = ? WHERE id = ?";
            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param("ssssi", $fechaInicio, $fechaFin, $anioAcademico, $estado, $idCurso);
            $consulta->execute();

            return $consulta->affected_rows > 0;
        }

    }