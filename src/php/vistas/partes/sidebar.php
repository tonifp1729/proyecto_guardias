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
                        if (isset($_SESSION['idCursoActivo'])) {
                            echo '<li><a href="index.php?accion=listadoSolicitudesPropias">Solicitudes presentadas</a></li>';
                            echo '<li><a href="index.php?accion=nuevaSolicitud">Nueva solicitud</a></li>';
                        }
                        break;
                    case 'A': //USUARIO ADMINISTRADOR
                        if (isset($_SESSION['idCursoActivo'])) {
                            echo '<li><a href="index.php?accion=cursoActual">Curso activo</a></li>';
                        }
                        echo '<li><a href="index.php?accion=nuevoCurso">Iniciar nuevo curso</a></li>';
                        echo '<li><a href="index.php?accion=listadoCursos">Registro de cursos</a></li>';
                        echo '<li><a href="index.php?accion=listadoUsuarios">Listado de usuarios</a></li>';
                        break;
                    case 'M': //USUARIO MODERADOR
                        if (isset($_SESSION['idCursoActivo'])) {
                            echo '<li><a href="index.php?accion=listadoSolicitudesPropias">Solicitudes presentadas</a></li>';
                            echo '<li><a href="index.php?accion=nuevaSolicitud">Nueva solicitud</a></li>';
                            echo '<li><a href="index.php?accion=cursoActual">Curso activo</a></li>';
                        }

                        break;
                }
            }
        ?>
    </ul>
    <div class="logout">
        <a href="index.php?accion=logout">Cerrar Sesión</a>
    </div>
</div>