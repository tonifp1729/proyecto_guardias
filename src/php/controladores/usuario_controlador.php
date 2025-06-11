<?php
    require_once '/home/proyectosevg/public_html/10/src/config/path.php';
    require_once RUTA_MODELOS . 'usuario.php';
    require_once RUTA_CONFIGURACION . 'config.php';

    
    /**
     * Controlador encargado de la gestión de usuarios. 
     * Autenticación con Google, obtención, modificación y eliminación de usuarios.
     * 
     * @author Antonio Manuel Figueroa Pinilla
     */
    class Usuario_controlador {

        /**
         * Instancia del modelo Usuario para gestionar las operaciones relacionadas con los usuarios.
         * @var Usuario
         */
        private $usuario;

        /**
         * Constructor de la clase. Se instancia el modelo Usuario.
         */
        public function __construct() {
            $this->usuario = new Usuario();
        }

        /**
         * Inicia sesión del usuario mediante Google.
         * 
         * - Si no se ha iniciado sesión, se inicia.
         * - Si no hay código de autorización, se redirige al login de Google.
         * - Si el usuario regresa con el código, se obtiene su perfil.
         * - Si el correo no está registrado, se inserta.
         * - Se guardan los datos necesarios en la variable de sesión.
         * 
         * @return string - Retorna la vista 'saludo' si el proceso es exitoso.
         */
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

        /**
         * Obtiene el listado completo de usuarios desde el modelo.
         * 
         * @return array Retorna un array con la vista a renderizar y los datos de los usuarios.
         */
        public function obtenerUsuarios() {
            $usuarios = $this->usuario->listarUsuarios();
            return ['vista' => 'listarusuarios', 'usuarios' => $usuarios];
        }

        /**
         * Carga los datos necesarios para mostrar el formulario de modificación de un usuario concreto.
         * 
         * @return array - Retorna un array con la vista, los datos del usuario y la lista de roles.
         */
        public function cargarModificarUsuario() {
            $idUsuario = $_GET['id'];

            $prepararUsuario = $this->usuario->obtenerUsuario($idUsuario);
            $roles = $this->usuario->obtenerTodosRoles();

            return ['vista' => 'formmodusuario', 'usuario' => $prepararUsuario, 'roles' => $roles];
        }

        /**
         * Procesa la modificación de un usuario tras el envío del formulario.
         * 
         * - Verifica si se han enviado los datos por POST.
         * - Valida campos obligatorios.
         * - Comprueba formato del correo y que no esté duplicado.
         * - Ejecuta la actualización y retorna la vista correspondiente.
         * 
         * @return array|string - Devuelve la vista con errores o la confirmación de éxito.
         */
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
         * - Si el usuario tiene rol de administrador, no se elimina.
         * - Si es válido, se elimina y se muestra confirmación.
         * 
         * @return string - Vista que se mostrará tras la operación ('listarusuarios' o 'avisoexito').
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