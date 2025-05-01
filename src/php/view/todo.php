<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestor de ausencias</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/estilo.css">
    </head>
    <body>
        <!-- VISTA DE INICIO DE SESIÓN -->
        <div id="login">
            <h1>¡Bienvenido!</h1>
            <p id="mensaje">Estás accediendo a una aplicación del profesorado de la <em><b>Escuela Virgen de Guadalupe</b></em>. Recuerda que debes acceder con el correo de la fundación otorgado por el centro.</p>
            <p id="mensaje">Por favor, <a href="#">pulsa aquí</a> e introduce tus credenciales para acceder con Google.</p>
        </div>
        <!-- PANELES LATERALES, SE MUESTRAN SEGÚN EL TIPO DE USUARIO QUE ACCEDE -->
        <div class="sidebar">
            <h2>Menú Administrador</h2>
            <ul>
                <li><a href="#">Listado de cursos</a></li>
                <li><a href="#">Solicitudes actuales</a></li>
            </ul>
        </div>
        <!-- VISTA INICIAL AL HABER INICIADO SESIÓN -->
        <div class="vista-inicial">
            <h1>¡Bienvenido al gestor de ausencias!</h1>
            <p>Este es tu panel de control. A tu izquierda puedes ver las opciones disponibles para ti.</p>
        </div>
        <!-- VISTAS DE ADMINISTRADOR: ALTA DE CURSO, LISTAR CURSOS, LISTADO DE SOLICITUDES DE UN CURSO, SIN CURSO EXISTENTE, MODIFICAR CURSO, LISTADO SOLICITUDES DEL CURSO -->
    </body>
</html>