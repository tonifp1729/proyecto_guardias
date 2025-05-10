<!-- SIDEBAR ------------------------------------------------------------------------------------------------->
<div class="sidebar">
    <h2>Menú</h2>
    <!-- SE CARGARÁN DINÁMICAMENTE, MOSTRANDOSE DEPENDIENDO DEL TIPO DE USUARIO QUE ACCEDA -->
    <ul>
        <?php
            if (isset($_SESSION['rol']) && (isset($_SESSION['cursoActivo']) || $_SESSION['rol'] === 'A')) {
                $rol = $_SESSION['rol'];
                switch ($rol) {
                    case 'C': // USUARIO COMÚN
                        echo '<li><a href="index.php?controlador=Leaverequests&action=iramilistado">Solicitudes presentadas</a></li>';
                        echo '<li><a href="index.php?controlador=Leaverequests&action=irsolicitud">Nueva solicitud</a></li>';
                        break;
                    case 'A': // USUARIO ADMINISTRADOR
                        echo '<li><a href="index.php?controlador=User&action=mostrarListadoUsuarios">Listado de profesores</a></li>';
                        echo '<li><a href="index.php?controlador=Course&action=mostrarCursoActual">Curso actual</a></li>';
                        if (isset($_SESSION['cursoActivo'])) {
                            echo '<li><a href="index.php?controlador=Leaverequests&action=">Ver mis solicitudes</a></li>';
                            echo '<li><a href="index.php?controlador=Leaverequests&action=irsolicitud">Hacer solicitud</a></li>';
                        }
                        break;
                    case 'M': // USUARIO MODERADOR
                        echo '<li><a href="solicitudes_moderar.php">Moderar solicitudes</a></li>';
                        echo '<li><a href="historial_moderacion.php">Historial de moderación</a></li>';
                        break;
                }
            }
        ?>
    </ul>
    <div class="logout">
        <a>Cerrar Sesión</a>
    </div>
</div>
<script src="js/sesion.js"></script>