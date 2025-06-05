<?php 

    require_once '/home/proyectosevg/public_html/10/src/config/config.php';

    /**
     * Clase Conexiondb
     * 
     * Para realizar la conexión msqli con la base de datos.
     * Esta clase se emplea en todos los modelos con los que pretendemos conectar a la BD.
     * 
     * @author - Antonio Manuel Figueroa Pinilla
     */
    class Conexiondb {
        /**
         * @var string $host - Dirección del servidor de base de datos
         */
        private $host;

        /**
         * @var string $user - Nombre de usuario de la base de datos
         */
        private $user;

        /**
         * @var string $pass - Contraseña de acceso a la base de datos
         */
        private $pass;

        /**
         * @var string $db - Nombre de la base de datos
         */
        private $db;

        /**
         * @var mysqli $conexion - Objeto de conexión a la base de datos
         */
        public $conexion;
        
        /**
         * Constructor de la clase.
         * 
         * Inicializa los valores de conexión usando constantes definidas en el archivo de configuración
         * y crea una nueva instancia de conexión `mysqli`.
         */
        public function __construct() {		

            $this->host = constant('DB_HOST');
            $this->user = constant('DB_USER');
            $this->pass = constant('DB_PASSWORD');
            $this->db = constant('DB_NAME');

            $this->conexion = new mysqli($this->host, $this->user, $this->pass, $this->db);

        }
    }
    
?>