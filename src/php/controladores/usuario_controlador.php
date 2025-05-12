<?php
    require_once 'C:/Users/Antonio/WorkSpace/Xampp/htdocs/espacio-proyectos/proyecto_guardias/src/config/path.php';
    require_once RUTA_MODELOS . 'usuario.php';
    require_once RUTA_CONFIGURACION . 'config.php';

    class Usuario_controlador {

        public static function inicioSesionGoogle() {

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
                $modeloUsuario = new Usuario();

                if (!$modeloUsuario->correoRegistrado($correo)) {
                    $modeloUsuario->insertarUsuario($correo, $nombre, $apellidos, 'C');
                }

                //Recuperamos los datos del usuario
                $datos = $modeloUsuario->obtenerDatosSesionUsuario($correo);

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
    }