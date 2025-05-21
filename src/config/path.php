<?php

    //RUTA LOCAL HASTA PATH: C:/Users/Antonio/WorkSpace/Xampp/htdocs/espacio-proyectos/proyecto_guardias/src/config/path.php
    //RUTA SERVIDOR HASTA PATH: https://10.proyectos.esvirgua.com/src/config/path.php

    //define('PATH_PROYECTO', '/home/proyectosevg/public_html/2daw29/');

    /* Rutas de acceso ficheros en servidor */
    define('PATH_PROYECTO', 'https://10.proyectos.esvirgua.com/');
    define('PATH_VENDOR', PATH_PROYECTO . 'vendor/autoload.php');
    define('PATH_CONFIGURACION', PATH_PROYECTO . 'src/config/');
    define('PATH_MODELOS', PATH_PROYECTO . 'src/php/model/');
    define('PATH_VISTAS', PATH_PROYECTO . 'src/php/view/');
    define('PATH_CONTROLADORES', PATH_PROYECTO . 'src/php/controller/');

    /* Rutas de acceso de los ficheros para pruebas locales */
    define('RUTA_PROYECTO', 'C:/Users/Antonio/WorkSpace/Xampp/htdocs/espacio-proyectos/proyecto_guardias/');
    define('RUTA_VENDOR_AUTOLOAD', RUTA_PROYECTO . 'vendor/autoload.php');
    define('RUTA_CONFIGURACION', RUTA_PROYECTO . 'src/config/');
    define('RUTA_MODELOS', RUTA_PROYECTO . 'src/php/modelos/');
    define('RUTA_VISTAS', RUTA_PROYECTO . 'src/php/vistas/');
    define('RUTA_CONTROLADORES', RUTA_PROYECTO . 'src/php/controladores/');
    define('RUTA_CLIENTE', RUTA_PROYECTO . 'js/');

?>