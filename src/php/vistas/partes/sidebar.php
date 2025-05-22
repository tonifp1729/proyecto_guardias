<!-- SIDEBAR ------------------------------------------------------------------------------------------------->
<?php
    if (!defined('ACCESO_PERMITIDO') || !isset($_SESSION['nombre'])) {
        header('Location: ../../../index.php?accion=inicio');
        exit;
    }
?>
<div class="sidebar">
    <h2>Menú</h2>
    <!-- SE CARGARÁN DINÁMICAMENTE, MOSTRANDOSE DEPENDIENDO DEL TIPO DE USUARIO QUE ACCEDA -->
    <ul>
        <?php
            if (isset($_SESSION['rol'])) {
                switch ($_SESSION['rol']) {
                    case 'C': //USUARIO COMÚN
                        echo '<li><a a href="#">Solicitudes presentadas</a></li>';
                        echo '<li><a a href="#">Nueva solicitud</a></li>';
                        break;
                    case 'A': //USUARIO ADMINISTRADOR
                        echo '<li><a href="index.php?accion=cursoActual">Curso activo</a></li>';
                        echo '<li><a href="index.php?accion=nuevoCurso">Iniciar nuevo curso</a></li>';
                        echo '<li><a href="index.php?accion=listadoCursos">Registro de cursos</a></li>';
                        echo '<li><a href="index.php?accion=listadoUsuarios">Registro de usuarios</a></li>';
                        break;
                    case 'M': //USUARIO MODERADOR
                        echo '<li><a href="#">Moderar solicitudes</a></li>';
                        echo '<li><a href="#">Historial de moderación</a></li>';
                        break;
                }
            }
        ?>
    </ul>
    <div class="logout">
        <a href="index.php?accion=logout">Cerrar Sesión</a>
    </div>
</div>