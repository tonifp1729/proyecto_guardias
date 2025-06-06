<?php

    //RUTA LOCAL HASTA PATH: C:/Users/Antonio/WorkSpace/Xampp/htdocs/espacio-proyectos/proyecto_guardias/src/config/path.php
    //RUTA SERVIDOR HASTA PATH: https://10.proyectos.esvirgua.com/src/config/path.php

    //define('PATH_PROYECTO', '/home/proyectosevg/public_html/2daw29/');
    
    define('ENTORNO', 'S'); // 'L' para local, 'S' para servidor

    if (ENTORNO === 'S') {
        // Datos del servidor
        define('RUTA_PROYECTO', '/home/proyectosevg/public_html/10/');
        define('RUTA_VENDOR_AUTOLOAD', RUTA_PROYECTO . 'vendor/autoload.php');
        define('RUTA_CONFIGURACION', RUTA_PROYECTO . 'src/config/');
        define('RUTA_MODELOS', RUTA_PROYECTO . 'src/php/modelos/');
        define('RUTA_VISTAS', RUTA_PROYECTO . 'src/php/vistas/');
        define('RUTA_CONTROLADORES', RUTA_PROYECTO . 'src/php/controladores/');
        define('RUTA_CLIENTE', RUTA_PROYECTO . 'js/');
    } else {
        // Datos locales
        define('RUTA_PROYECTO', 'C:/Users/Antonio/WorkSpace/Xampp/htdocs/espacio-proyectos/proyecto_guardias/');
        define('RUTA_VENDOR_AUTOLOAD', RUTA_PROYECTO . 'vendor/autoload.php');
        define('RUTA_CONFIGURACION', RUTA_PROYECTO . 'src/config/');
        define('RUTA_MODELOS', RUTA_PROYECTO . 'src/php/modelos/');
        define('RUTA_VISTAS', RUTA_PROYECTO . 'src/php/vistas/');
        define('RUTA_CONTROLADORES', RUTA_PROYECTO . 'src/php/controladores/');
        define('RUTA_CLIENTE', RUTA_PROYECTO . 'js/');
    }

?>