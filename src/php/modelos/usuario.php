<?php

    require_once 'db.php';


    
    /**
     * Clase Usuario
     *
     * Modelo encargado de gestionar todas las operaciones relacionadas con los usuarios:
     * inserciones, consultas, actualizaciones y eliminaciones en la base de datos.
     * Utiliza la clase Conexiondb para interactuar con la base de datos.
     * 
     * @author - Antonio Manuel Figueroa Pinilla
     */
    class Usuario {
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
         * Insertar un nuevo usuario en la base de datos.
         *
         * @param string $correo - Correo electrónico del usuario
         * @param string $nombre - Nombre del usuario
         * @param string $apellidos - Apellidos del usuario
         * @param string $rol - Rol asignado al usuario (por ejemplo, 'U' para usuario común)
         * 
         */
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

        /**
         * Verifica si el correo ya está registrado en la base de datos.
         * Esta comprobación se lanza trás la autenticación.
         * 
         * @param string $correo Correo electrónico a verificar
         * @return bool True si el correo ya está registrado, false en caso contrario
         */
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
         * Obtiene los datos necesarios para la sesión del usuario.
         *
         * @param string $correo - Correo del usuario.
         * @return array|null - Array con 'id', 'nombre' e 'id_Rol' si existe, o null si no.
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
         * Obtenemos todos los roles que pueden ser asignados a un profesor y que han sido definidos para el sistema.
         *
         * @return array $roles - Lista de roles como arrays asociativos con claves 'id' y 'nombre'
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
         * Lista todos los usuarios, excepto el del director.
         * Se trata de un usuario especial de administrador.
         * 
         * @return array $usuarios - Listado de usuarios con sus 'id', 'nombre' y 'apellidos'
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
         * Obtiene los datos de un usuario por su ID.
         *
         * @param int $idUsuario - ID del usuario
         * @return array|null $usuario - Datos del usuario como array asociativo, o null si no existe
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
         * Comprueba si un correo ya está asignado a otro usuario distinto al actual.
         *
         * @param string $correo - Correo a comprobar
         * @param int $idActual - ID del usuario actual
         * @return bool - True si el correo ya existe en otro usuario, false en caso contrario
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
         * Modifica los datos del usuario indicado según el ID.
         *
         * @param int - $idUsuario ID del usuario a modificar
         * @param string - $correo Nuevo correo del usuario
         * @param string - $rol Nuevo rol del usuario
         * @return bool - True si la modificación tuvo éxito, false en caso contrario
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

        /**
         * Elimina un usuario por su ID.
         *
         * @param int $idUsuario - ID del usuario a eliminar
         * @return bool - True si se eliminó al menos un registro, false en caso contrario
         */
        public function eliminarUsuario($idUsuario) {
            $sql = "DELETE FROM Usuario WHERE id = ?";
            $consulta = $this->conexion->prepare($sql);
            $consulta->bind_param("i", $idUsuario);
            $consulta->execute();

            return $consulta->affected_rows > 0;
        }

    }