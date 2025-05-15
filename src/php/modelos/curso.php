<?php

    require_once 'db.php';

    class Curso {
        private $conexion;

        public function __construct() {
            //Creamos un objeto e inicializamos la conexión a la base de datos
            $db = new Conexiondb();
            $this->conexion = $db->conexion;
        }

        /*
        *  Devuelve la información de un curso específico basado en su ID.
        *  @param int $idCurso - ID del curso.
        **/
        public function mostrarCurso($idCurso) {
            $SQL = "SELECT * FROM Curso WHERE id = ?";
            $consulta = $this->conexion->prepare($SQL);
            $consulta->bind_param("i", $idCurso);
            $consulta->execute();
            $resultado = $consulta->get_result();

            $curso = $resultado->fetch_assoc();

            $consulta->close();
            return $curso;
        }

        /*
        *  Inserta un nuevo curso en la tabla Cursos.
        **/
        public function insertarCurso($fechaInicio, $fechaFin, $anoAcademico) {
            $SQL = "INSERT INTO Curso (fecha_inicio, fecha_fin, anio_academico) VALUES (?, ?, ?)";
            
            $consulta = $this->conexion->prepare($SQL);
            $consulta->bind_param("sss", $fechaInicio, $fechaFin, $anoAcademico);
            $consulta->execute();
            $consulta->close();
        }

        /**
         * Comprueba si hay un curso actualmente activo basado en las fechas de inicio y finalización y lo devuelve.
         * Retorna el año académico del curso activo o `null` si no hay un curso activo.
         * @return array|null - Array asociativo con los datos del curso que está activo actualmente.
         */
        public function cursoActivo() {
            $SQL = "SELECT id, fecha_inicio, fecha_fin, anio_academico FROM Curso WHERE CURDATE() BETWEEN fecha_inicio AND fecha_fin LIMIT 1";
        
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

    }