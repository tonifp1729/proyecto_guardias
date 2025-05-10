<?php 

    require_once 'C:\Users\Antonio\WorkSpace\Xampp\htdocs\espacio-proyectos\proyfg\src\config\config.php';

    class Conexiondb {
        private $host;
        private $user;
        private $pass;
        private $db;
        public $conexion;
        
        /*
          * Damos valor a las variables con las constantes guardadas en configuración y con estas se realiza la conexión.
         **/
        public function __construct() {		

            $this->host = constant('DB_HOST');
            $this->user = constant('DB_USER');
            $this->pass = constant('DB_PASSWORD');
            $this->db = constant('DB_NAME');

            $this->conexion = new mysqli($this->host, $this->user, $this->pass, $this->db);

        }
    }
    
?>