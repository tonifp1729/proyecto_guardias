<?php

    require_once 'db.php';

    class Usuario {
        private $conexion;

        public function __construct() {
            $db = new Conexiondb();
            $this->conexion = $db->conexion;
        }

        public function insertarUsuario($correo, $nombre, $apellidos, $rol) {    
            //Consulta SQL para insertar al nuevo alumno en la bd y lo define como un usuario común de la aplicación al momento del registro
            $SQL = "INSERT INTO Usuario (correo, nombre, apellidos, fecha_ingreso, id_Rol) VALUES (?, ?, ?, ?, ?)";
            
            //Preparamos la consulta
            $consulta = $this->conexion->prepare($SQL);
            
            $fecha = date('Y-m-d H:i:s');

            //Vinculamos los parámetros a la consulta
            $consulta->bind_param("sssss", $correo, $nombre, $apellidos, $fecha, $rol);
            
            //Ejecutamos la consulta
            $consulta->execute();
            
            //Cerramos la consulta
            $consulta->close();
        }

        //Comprobamos si existe en la base de datos el correo con el que se ha realizado el inicio de sesión
        public function correoRegistrado($correo) {
            //Consulta SQL para obtener el id del usuario que tiene el correo introducido
            $SQL = "SELECT id FROM Usuario WHERE correo = ?";

            //Preparamos la consulta
            $consulta = $this->conexion->prepare($SQL);

            //Vinculamos el parámetro con la variable $correo
            $consulta->bind_param('s', $correo);

            //Ejecutamos la consulta
            $consulta->execute();

            //Obtenemos el resultado
            $resultado = $consulta->get_result();

            //Verificamos si se encontró una coincidencia en la BD
            if ($resultado->num_rows > 0) {
                return true; //El usuario con este correo ya existe
            } else {
                return false; //El usuario no existe
            }
        }

        /**
         * Es la consulta que realizamos una vez accedemos por el inicio de sesión, para llenar los datos de sesión que necesitaremos durante el uso de la aplicación.
         */
        public function obtenerDatosSesionUsuario($correo) {
            $SQL = "SELECT id, nombre, id_Rol FROM Usuario WHERE correo = ?";
            $consulta = $this->conexion->prepare($SQL);
            $consulta->bind_param('s', $correo);
            $consulta->execute();
            $resultado = $consulta->get_result();
        
            if ($fila = $resultado->fetch_assoc()) {
                return ['id' => $fila['id'], 'nombre' => $fila['nombre'], 'rol' => $fila['id_Rol']];
            } else {
                return null;
            }
        }

        /**
         * Obtenemos todos los roles que pueden ser asignados a un profesor.
         */
        public function obtenerTodosRoles() {
            $SQL = "SELECT id, nombre FROM Rol";
            $resultado = $this->conexion->query($SQL);
            
            $roles = [];
            while ($rol = $resultado->fetch_assoc()) {
                $roles[] = $rol;
            }
            return $roles;
        }

        /**
         * Obtenemos el listado de usuarios
         */
        public function listarUsuarios() {
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
         * Obtenemos los datos de un usuario según el id
         */
        public function obtenerUsuario($idUsuario) {
            $SQL = "SELECT * FROM Usuario WHERE id = ?";
            $consulta = $this->conexion->prepare($SQL);
            $consulta->bind_param("i", $idUsuario);
            $consulta->execute();
            $resultado = $consulta->get_result();
            $usuario = $resultado->fetch_assoc();
            $consulta->close();

            return $usuario;
        }

        /**
         * Comprueba si el correo introducido está ya presente en otro usuario
         */
        public function correoExistente($correo, $idActual) {
            $sql = "SELECT COUNT(*) FROM Usuario WHERE correo = ? AND id != ?";
            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param('si', $correo, $idActual);
            $consulta->execute();
            $consulta->bind_result($total);
            $consulta->fetch();
            $consulta->close();
            return $total > 0;
        }

        /** 
         * Consulta para realizar la modificación del usuario
         */ 
        public function modificarUsuario($idUsuario, $correo, $rol) {
            $sql = "UPDATE Usuario SET correo = ?, id_Rol = ? WHERE id = ?";
            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param('ssi', $correo, $rol, $idUsuario);
            $consulta->execute();

            $filasAfectadas = $consulta->affected_rows;

            $consulta->close();

            return $filasAfectadas > 0;
        }

        public function eliminarUsuario($idUsuario) {
            $sql = "DELETE FROM Usuario WHERE id = ?";
            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param("i", $idUsuario);
            $consulta->execute();

            return $consulta->affected_rows > 0;
        }

    }