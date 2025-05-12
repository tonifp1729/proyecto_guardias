<?php

    require_once 'db.php';

    class Cursos {
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
        public function insertarCurso($fechaInicio, $fechaFinalizacion, $anoAcademico) {
            $SQL = "INSERT INTO Curso (fecha_inicio, fecha_fin, anio_academico) VALUES (?, ?, ?)";
            
            $consulta = $this->conexion->prepare($SQL);
            $consulta->bind_param("sss", $fechaInicio, $fechaFinalizacion, $anoAcademico);
            $consulta->execute();
            $consulta->close();
        }        

        /*
        * Comprueba si hay un curso actualmente activo basado en las fechas de inicio y finalización y lo devuelve.
        * Retorna el año académico del curso activo o `null` si no hay un curso activo.
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
        
            return $curso; //Retornamos un array con los datos del curso activo o null si no hay curso.
        }
    }