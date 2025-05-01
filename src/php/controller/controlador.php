<?php

    class Controlador {
        public function login() {
            include 'view/login.php';
        }

        public function callback() {
            // aquí procesas el login con Google
            // luego rediriges a la vista del usuario
            include 'view/panel_usuario.php';
        }

        public function panelUsuario() {
            // Mostrar contenido para usuario ya autenticado
            include 'view/panel_usuario.php';
        }
    }