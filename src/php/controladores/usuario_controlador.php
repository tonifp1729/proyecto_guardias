<?php
    require_once 'C:/Users/Antonio/WorkSpace/Xampp/htdocs/espacio-proyectos/proyecto_guardias/src/config/path.php';
    require_once RUTA_MODELOS . 'usuario.php';
    require_once RUTA_CONFIGURACION . 'config.php';

    class Usuario_controlador {

        private $usuario;

        public function __construct() {
            $this->usuario = new Usuario();
        }

        public function inicioSesionGoogle() {

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            require_once RUTA_VENDOR_AUTOLOAD;

            $cliente = new Google_Client();
            $cliente->setClientId(CLIENT_ID);
            $cliente->setClientSecret(CLIENT_SECRET);
            $cliente->setRedirectUri(REDIRECT_URI);
            $cliente->addScope('email');
            $cliente->addScope('profile');

            if (!isset($_GET['code'])) {


                //Generamos la URL de autenticación de Google, forzando con el promt la selección de cuenta en todo momento (de lo contrario, una vez se ha autenticado el usuario, la salta siempre)
                $cliente->setPrompt('select_account');
                $authUrl = $cliente->createAuthUrl();
                //Redirigimos a la ventana de inicio de sesión de Google con una URL sanitizada
                header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
                
                exit;

            } else {

                //El usuario ha vuelto desde Google con el código de autorización
                $token = $cliente->fetchAccessTokenWithAuthCode($_GET['code']);

                $cliente->setAccessToken($token);

                $oauth = new Google_Service_Oauth2($cliente);
                $perfil = $oauth->userinfo->get();

                $correo = $perfil->email;
                $nombre = $perfil->givenName;
                $apellidos = $perfil->familyName;

                //Verificamos existe en la base de datos y lo insertamos si no está
                if (!$this->usuario->correoRegistrado($correo)) {
                    $this->usuario->insertarUsuario($correo, $nombre, $apellidos, 'C');
                }

                //Recuperamos los datos del usuario
                $datos = $this->usuario->obtenerDatosSesionUsuario($correo);

                //Guardamos los datos en la sesión
                $_SESSION['id'] = $datos['id'];
                $_SESSION['correo'] = $correo;
                $_SESSION['nombre'] = $nombre;
                $_SESSION['rol'] = $datos['rol'];

                //Redirigimos a la vista de inicio del usuario (saludo)
                return 'saludo'; 
                
                exit;
            }
        }

        public function obtenerUsuarios() {
            $usuarios = $this->usuario->listarUsuarios();
            return ['vista' => 'listarusuarios', 'usuarios' => $usuarios];
        }

        /**
         * Se encarga de retornar todos los datos que se deben usar para cargar la vista de modificación de usuarios
         */
        public function cargarModificarUsuario() {
            $idUsuario = $_GET['id'];

            $prepararUsuario = $this->usuario->obtenerUsuario($idUsuario);
            $roles = $this->usuario->obtenerTodosRoles();

            return ['vista' => 'formmodusuario', 'usuario' => $prepararUsuario, 'roles' => $roles];
        }

        public function modificarUsuario() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                $idUsuario = $_POST['idUsuario'];
                $correo = trim($_POST['correo']);
                $rol = $_POST['rol'];

                $usuarioActual = $this->usuario->obtenerUsuario($idUsuario);

                //Todos los campos son obligatorios
                if (empty($correo) || empty($rol)) {
                    $roles = $this->usuario->obtenerTodosRoles();
                    return ['vista' => 'formmodusuario', 'usuario' => $usuarioActual, 'roles' => $roles, 'error' => 'campos-obligatorios'];
                }

                //Validamos el dominio de correo electrónico
                if (!preg_match('/^[a-zA-Z0-9._%+-]+@fundacionloyola\.es$/', $correo)) {
                    $roles = $this->usuario->obtenerTodosRoles();
                    return ['vista' => 'formmodusuario', 'usuario' => $usuarioActual, 'roles' => $roles, 'error' => 'correo-no-valido'];
                }

                //Verificamos que no haya otro usuario con ese correo
                if ($this->usuario->correoExistente($correo, $idUsuario)) {
                    $roles = $this->usuario->obtenerTodosRoles();
                    return ['vista' => 'formmodusuario', 'usuario' => $usuarioActual, 'roles' => $roles, 'error' => 'correo-duplicado'];
                }

                //Ejecutamos la modificación
                $modificado = $this->usuario->modificarUsuario($idUsuario, $correo, $rol);

                if ($modificado) {
                    return 'avisoexito';
                } else {
                    $roles = $this->usuario->obtenerTodosRoles();
                    return ['vista' => 'formmodusuario', 'usuario' => $usuarioActual, 'roles' => $roles];
                }
            }
            $roles = $this->usuario->obtenerTodosRoles();
            return ['vista' => 'formmodusuario', 'usuario' => $usuarioActual, 'roles' => $roles];
        }

        /**
         * Elimina un usuario de la base de datos.
         *
         * Este método obtiene el ID del usuario desde la URL (`$_GET['id']`)
         *
         * @return string - Devuelve la acción 'listarusuarios' si el rol del usuario es de administrador y no se elimina o 'avisoexito' si la eliminación fue realizada con éxito.
         */
        public function borrarUsuario() {
            $idUsuario = $_GET['id'];

            $prepararUsuario = $this->usuario->obtenerUsuario($idUsuario);

            if ($prepararUsuario['id_Rol'] === 'A') {
                return 'listarusuarios';
            }

            $this->usuario->eliminarUsuario($idUsuario);

            return 'avisoexito';
        }

    }