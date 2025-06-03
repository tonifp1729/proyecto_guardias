<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de ausencias</title>
    <link rel="stylesheet" href="css/estilo.css">
    
</head>
    <body> 
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
        <!-- AQUÍ EMPIEZAN LAS VISTAS DE LA APLICACIÓN -->
        <!-- VISTA QUE SE MUESTRA CUANDO NO HAY UN CURSO ACTIVO PARA CUALQUIER USUARIO QUE NO SEA ADMINISTRADOR --------------------------------------------------------->
        <div class="container">
            <h1>¡Hola, docentes!</h1>
            <p>En este momento no hay ningún curso activo en la plataforma.</p>
            <p>Aprovechen este tiempo libre para descansar, preparar materiales o simplemente disfrutar de un merecido descanso. 😊</p>
            <p><--- --- ---Recuerda que el botón de cerrar sesión está donde siempre.</p>
        </div>

        <!-- VISTA QUE SE MUESTRA TRÁS REALIZAR DETERMINADAS OPERACIONES DE MANERA EXITOSA --------------------------------------------------------------------------------->
        <div class="aviso-correcto">
                <h2>¡Acción realizada correctamente!</h2>
                <p>La operación que intentaste realizar se completó con éxito.</p>
        </div>

        <!-- VISTA PARA REVISAR SOLICITUDES DE UN CURSO CUALQUIERA --------------------------------------------------------------------------------->
        <div class="container">
            <div class="curso-info">
                <h1>Curso actual</h1>
                <?php if (isset($curso) && $_SESSION['idCursoActivo']): ?>
                    <p><strong>Año académico:</strong> <?= htmlspecialchars($curso['anoAcademico']) ?></p>
                    <p><strong>Inicio:</strong> <?= htmlspecialchars($curso['fechaInicio']) ?></p>
                    <p><strong>Finalización:</strong> <?= htmlspecialchars($curso['fechaFinalizacion']) ?></p>
                <?php else: ?>
                    <p>No hay ningún curso activo actualmente.</p>
                <?php endif; ?>
            </div>
            <div id="listado-solicitudes-curso" class="listado">
                <h2>Solicitudes del curso</h2>
                <?php if (!empty($solicitudes)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Identificador</th>
                                <th>Docente</th>
                                <th>Motivo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitudes as $solicitud): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($solicitud['identificador']) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($solicitud['nombre_usuario'].' '.$solicitud['apellidos_usuario']) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($solicitud['motivo']) ?>
                                    </td>
                                    <td>
                                        <?php
                                            $estado = strtolower($solicitud['estado']);
                                            if ($estado === 'a') {
                                                echo "🟢";
                                            } elseif ($estado === 'p') {
                                                echo "🟡";
                                            } elseif ($estado === 'r') {
                                                echo "🔴";
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($estado === 'p'): ?>
                                            <a href="index.php?accion=irVerSolicitud&id=<?= $solicitud['id_Usuario'] ?>&fecha=<?= $solicitud['fecha_presentacion'] ?>&num=<?= $solicitud['num'] ?>">Mostrar</a>
                                            <a href="index.php?accion=borrarSolicitud&id=<?= $solicitud['id_Usuario'] ?>&fecha=<?= $solicitud['fecha_presentacion'] ?>&num=<?= $solicitud['num'] ?>" class="enlace-borrar">Borrar</a>
                                        <?php else: ?>
                                            <a href="index.php?accion=irVerSolicitud&id=<?= $solicitud['id_Usuario'] ?>&fecha=<?= $solicitud['fecha_presentacion'] ?>&num=<?= $solicitud['num'] ?>">Mostrar</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No hay solicitudes pendientes este curso.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- VISTA CURSO ACTUAL --------------------------------------------------------------------------------->
         <div class="container">
            <div class="curso-info">
                <h1>Curso actual</h1>
                <?php if (isset($curso) && $_SESSION['idCursoActivo']): ?>
                    <p><strong>Año académico:</strong> <?= htmlspecialchars($curso['anoAcademico']) ?></p>
                    <p><strong>Inicio:</strong> <?= htmlspecialchars($curso['fechaInicio']) ?></p>
                    <p><strong>Finalización:</strong> <?= htmlspecialchars($curso['fechaFinalizacion']) ?></p>
                <?php else: ?>
                    <p>No hay ningún curso activo actualmente.</p>
                <?php endif; ?>
            </div>
            <div id="listado-solicitudes-curso" class="listado">
                <h2>Solicitudes del curso</h2>
                <?php if (!empty($solicitudes)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Identificador</th>
                                <th>Docente</th>
                                <th>Motivo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitudes as $solicitud): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($solicitud['identificador']) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($solicitud['nombre_usuario'].' '.$solicitud['apellidos_usuario']) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($solicitud['motivo']) ?>
                                    </td>
                                    <td>
                                        <?php
                                            $estado = strtolower($solicitud['estado']);
                                            if ($estado === 'a') {
                                                echo "🟢";
                                            } elseif ($estado === 'p') {
                                                echo "🟡";
                                            } elseif ($estado === 'r') {
                                                echo "🔴";
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($estado === 'p'): ?>
                                            <a href="index.php?accion=irGestionarSolicitud&id=<?= $solicitud['id_Usuario'] ?>&fecha=<?= $solicitud['fecha_presentacion'] ?>&num=<?= $solicitud['num'] ?>">Mostrar</a>
                                        <?php else: ?>
                                            <a href="index.php?accion=irVerSolicitud&id=<?= $solicitud['id_Usuario'] ?>&fecha=<?= $solicitud['fecha_presentacion'] ?>&num=<?= $solicitud['num'] ?>">Mostrar</a>
                                            <a href="index.php?accion=borrarSolicitud&id=<?= $solicitud['id_Usuario'] ?>&fecha=<?= $solicitud['fecha_presentacion'] ?>&num=<?= $solicitud['num'] ?>" class="enlace-borrar">Borrar</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No hay solicitudes pendientes este curso.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- VISTA DEL FORMULARIO PARA MODIFICAR CURSO --------------------------------------------------------------------------------->
        <div class="container">
            <h1><?php echo htmlspecialchars($curso['anoAcademico']); ?></h1>
            <form action="index.php?accion=modificarCurso" method="POST" class="form-curso">
                <!-- Campo oculto con el id que necesitamos por el envío post -->
                <input type="hidden" name="idCurso" value="<?php echo htmlspecialchars($curso['idCurso']); ?>">
                <div class="form-group">
                    <label for="fecha_inicio">Fecha de Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo htmlspecialchars($curso['fechaInicio']); ?>"  data-inicio-original="<?php echo htmlspecialchars($curso['fechaInicio']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="fecha_fin">Fecha de Finalización:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($curso['fechaFinalizacion']); ?>" data-fin-original="<?php echo htmlspecialchars($curso['fechaFinalizacion']); ?>" required>
                </div>
                <button type="submit">Aceptar modificación</button>
            </form>
            <?php if (isset($error)): ?>
                <div id="error-server" class="mensaje-error" data-error="<?= htmlspecialchars($error) ?>"></div>
            <?php endif; ?>
        </div>

        <!-- VISTA DEL FORMULARIO DE MODICICACION DE UNA SOLICITUD ------------------------------------------------------------------------->
        <div class="container">
            <h1>Modificar Solicitud</h1>
            <form action="index.php?accion=modificarSolicitud" method="POST" class="form-solicitud" enctype="multipart/form-data">

                <!-- Campos ocultos necesarios -->
                <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($solicitud['id_Usuario']) ?>">
                <input type="hidden" name="fecha_presentacion" value="<?= htmlspecialchars($solicitud['fecha_presentacion']) ?>">
                <input type="hidden" name="num" value="<?= htmlspecialchars($solicitud['num']) ?>">

                <!-- Fechas no modificables -->
                <div class="grupo-form">
                    <label>Fecha de Inicio de Ausencia:</label>
                    <input type="date" value="<?= $solicitud['fecha_inicio_ausencia'] ?>" readonly>
                    <input type="hidden" name="fecha_inicio_ausencia" value="<?= $solicitud['fecha_inicio_ausencia'] ?>">
                </div>

                <div class="grupo-form">
                    <label>Fecha de Fin de Ausencia:</label>
                    <input type="date" value="<?= $solicitud['fecha_fin_ausencia'] ?>" readonly>
                    <input type="hidden" name="fecha_fin_ausencia" value="<?= $solicitud['fecha_fin_ausencia'] ?>">
                </div>

                <!-- Horas seleccionadas -->
                <div class="grupo-form" id="grupoHoras">
                    <h3>Selecciona Horas:</h3>
                    <?php for ($i = 1; $i <= 7; $i++): ?>
                        <?php if (in_array($i, $horasSeleccionadas)): ?>
                            <label>
                                <input type="checkbox" checked disabled>
                                <?= $i ?>ª Hora
                            </label>
                            <input type="hidden" name="horasSeleccionadas[]" value="<?= $i ?>">
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>

                <!-- Motivo -->
                <div class="grupo-form">
                    <label for="id_motivo">Motivo de la ausencia:</label>
                    <select id="id_motivo" name="motivo" required>
                        <?php foreach ($motivos as $motivo): ?>
                            <option value="<?= htmlspecialchars($motivo['id']) ?>" <?= $motivo['id'] == $solicitud['id_Motivo'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($motivo['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Descripción -->
                <div class="grupo-form">
                    <label for="descripcion">Descripción (opcional):</label>
                    <textarea id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars($solicitud['descripcion_solicitud']) ?></textarea>
                </div>

                <!-- Comentario material -->
                <div class="grupo-form">
                    <label for="comentario">Comentario sobre el material (opcional):</label>
                    <textarea id="comentario" name="comentario" rows="3"><?= htmlspecialchars($solicitud['comentario_material']) ?></textarea>
                </div>

                <!-- Archivos actuales -->
                <!-- Justificantes -->
                <?php if (!empty($justificantes)): ?>
                    <div class="grupo-form">
                        <h3>Justificantes:</h3>
                        <?php foreach ($justificantes as $archivo): ?>
                            <div>
                                <label>
                                    <input type="checkbox" name="archivos_a_eliminar[<?= $archivo['id'] ?>]" value="1"> Eliminar
                                </label>

                                <input type="hidden" name="archivos_info[<?= $archivo['id'] ?>][ruta]" value="<?= htmlspecialchars($archivo['ruta_archivo']) ?>">
                                <input type="hidden" name="archivos_info[<?= $archivo['id'] ?>][nombre]" value="<?= htmlspecialchars($archivo['nombre_generado']) ?>">
                                <input type="hidden" name="archivos_info[<?= $archivo['id'] ?>][tipo]" value="<?= htmlspecialchars($archivo['tipo_archivo']) ?>">

                                <a href="subidas/<?= htmlspecialchars($archivo['ruta_archivo']) ?>/<?= htmlspecialchars($archivo['nombre_generado']) ?>.<?= htmlspecialchars($archivo['tipo_archivo']) ?>" target="_blank">
                                    <?= htmlspecialchars($archivo['nombre_original']) ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Materiales -->
                <?php if (!empty($materiales)): ?>
                    <div class="grupo-form">
                        <h3>Materiales:</h3>
                        <?php foreach ($materiales as $archivo): ?>
                            <div>
                                <label>
                                    <input type="checkbox" name="archivos_a_eliminar[<?= $archivo['id'] ?>]" value="1"> Eliminar
                                </label>

                                <input type="hidden" name="archivos_info[<?= $archivo['id'] ?>][ruta]" value="<?= htmlspecialchars($archivo['ruta_archivo']) ?>">
                                <input type="hidden" name="archivos_info[<?= $archivo['id'] ?>][nombre]" value="<?= htmlspecialchars($archivo['nombre_generado']) ?>">
                                <input type="hidden" name="archivos_info[<?= $archivo['id'] ?>][tipo]" value="<?= htmlspecialchars($archivo['tipo_archivo']) ?>">

                                <a href="subidas/<?= htmlspecialchars($archivo['ruta_archivo']) ?>/<?= htmlspecialchars($archivo['nombre_generado']) ?>.<?= htmlspecialchars($archivo['tipo_archivo']) ?>" target="_blank">
                                    <?= htmlspecialchars($archivo['nombre_original']) ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Nuevos archivos -->
                <div class="grupo-form">
                    <label for="justificantes">Subir nuevos justificantes:</label>
                    <input type="file" name="justificantes[]" id="justificantes" multiple accept=".pdf,.jpg,.jpeg,.png">
                </div>

                <div class="grupo-form">
                    <label for="materiales">Subir nuevos materiales (si procede):</label>
                    <input type="file" name="materiales[]" id="materiales" multiple accept=".pdf,.doc,.docx,.xls,.xlsx">
                </div>

                <button type="submit">Guardar Cambios</button>
            </form>
            <?php if (isset($error)): ?>
                <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
        </div>

        <!-- VISTA DEL FORMULARIO PARA MODIFICAR USUARIO --------------------------------------------------------------------------------->
        <div class="container">
            <h1>Modificar Usuario</h1>
            <form action="index.php?accion=modificarUsuario" method="POST" class="form-usuario">
                <input type="hidden" name="idUsuario" value="<?php echo htmlspecialchars($usuario['id']); ?>">
                <div class="form-group">
                    <label for="correo">Correo electrónico:</label>
                    <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" data-correo-original="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="rol">Rol:</label>
                    <select id="rol" name="rol" data-rol-original="<?= htmlspecialchars($usuario['id_Rol']) ?>" required>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?= htmlspecialchars($rol['id']) ?>" <?= $usuario['id_Rol'] === $rol['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($rol['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit">Aceptar modificación</button>
            </form>
            <?php if (isset($error)): ?>
                <div id="error-server" class="mensaje-error" data-error="<?= htmlspecialchars($error) ?>"></div>
            <?php endif; ?>
        </div>

        <!-- VISTA DEL FORMULARIO DE NUEVA SOLICITUD --------------------------------------------------------------------------------->
        <div class="container">
            <h1>Nueva Solicitud de Ausencia</h1>
            <form action="index.php?accion=crearSolicitud" method="POST" class="form-solicitud" enctype="multipart/form-data">
                <div class="grupo-form">
                    <label for="fecha_inicio_ausencia">Fecha de Inicio de Ausencia:</label>
                    <input type="date" id="fecha_inicio_ausencia" name="fecha_inicio_ausencia" required>
                </div>

                <div class="grupo-form">
                    <label for="fecha_fin_ausencia">Fecha de Fin de Ausencia:</label>
                    <input type="date" id="fecha_fin_ausencia" name="fecha_fin_ausencia" required>
                </div>

                <div class="grupo-form" id="grupoHoras" style="display: none;">
                    <h3>Selecciona Horas:</h3>
                    <label><input type="checkbox" name="horas[]" value="1"> 1ª Hora</label>
                    <label><input type="checkbox" name="horas[]" value="2"> 2ª Hora</label>
                    <label><input type="checkbox" name="horas[]" value="3"> 3ª Hora</label>
                    <label><input type="checkbox" name="horas[]" value="4"> 4ª Hora</label>
                    <label><input type="checkbox" name="horas[]" value="5"> 5ª Hora</label>
                    <label><input type="checkbox" name="horas[]" value="6"> 6ª Hora</label>
                    <label><input type="checkbox" name="horas[]" value="7"> 7ª Hora</label>
                </div>

                <div class="grupo-form">
                    <label for="id_motivo">Motivo de la ausencia:</label>
                    <select id="id_motivo" name="id_motivo" required>
                        <?php foreach ($motivos as $motivo): ?>
                            <option value="<?= htmlspecialchars($motivo['id']) ?>">
                                <?= htmlspecialchars($motivo['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grupo-form">
                    <label for="descripcion_solicitud">Descripción (opcional):</label>
                    <textarea id="descripcion_solicitud" name="descripcion_solicitud" rows="3"></textarea>
                </div>

                <div class="grupo-form">
                    <label for="comentario_material">Comentario sobre el material (opcional):</label>
                    <textarea id="comentario_material" name="comentario_material" rows="3"></textarea>
                </div>

                <div class="grupo-form">
                    <label for="justificantes">Subir justificantes:</label>
                    <input type="file" name="justificantes[]" id="justificantes" multiple accept=".pdf,.jpg,.jpeg,.png">
                </div>
                
                <div class="grupo-form">
                    <label for="materiales">Subir materiales (si procede):</label>
                    <input type="file" name="materiales[]" id="materiales" multiple accept=".pdf,.doc,.docx,.xls,.xlsx">
                </div>

                <button type="submit">Enviar Solicitud</button>
            </form>
            <?php if (isset($error)): ?>
                <div id="error-server" class="mensaje-error" data-error="<?= htmlspecialchars($error) ?>"></div>
            <?php endif; ?>
        </div>

        <!-- VISTA DEL FORMULARIO PARA INICIAR UN NUEVO CURSO --------------------------------------------------------------------------------->
        <div class="container">
            <h1>Iniciar Nuevo Curso</h1>
            <form action="index.php?accion=iniciarCurso" method="POST" class="form-curso">
                <div class="form-group">
                    <label for="fecha_inicio">Fecha de Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                </div>
                <div class="form-group">
                    <label for="fecha_fin">Fecha de Finalización:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" required>
                </div>
                <button type="submit">Iniciar Curso</button>
            </form>
            <?php if (isset($error)): ?>
                <div id="error-server" class="mensaje-error" data-error="<?= htmlspecialchars($error) ?>"></div>
            <?php endif; ?>
        </div>

        <!-- VISTA CON LAS OPCIONES DE GESTION DE UNA SOLICITUD -->
        <?php
            if (!defined('ACCESO_PERMITIDO') || !isset($_SESSION['nombre'])) {
                header('Location: ../../index.php?accion=inicio');
                exit;
            }
        ?>
        <div class="container">
            <h1>Gestión de solicitud</h1>

            <!-- Fechas no modificables -->
            <div class="grupo-form">
                <label>Fecha de Inicio de Ausencia:</label>
                <input type="date" value="<?= $solicitud['fecha_inicio_ausencia'] ?>" disabled>
            </div>

            <div class="grupo-form">
                <label>Fecha de Fin de Ausencia:</label>
                <input type="date" value="<?= $solicitud['fecha_fin_ausencia'] ?>" disabled>
            </div>

            <!-- Horas seleccionadas -->
            <div class="grupo-form" id="grupoHoras">
                <h3>Horas seleccionadas:</h3>
                <?php for ($i = 1; $i <= 7; $i++): ?>
                    <?php if (in_array($i, $horasSeleccionadas)): ?>
                        <p><?= $i ?>ª Hora</p>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>

            <!-- Motivo -->
            <div class="grupo-form">
                <label>Motivo de la ausencia:</label>
                <input type="text" value="<?php
                    foreach ($motivos as $motivo) {
                        if ($motivo['id'] == $solicitud['id_Motivo']) {
                            echo htmlspecialchars($motivo['nombre']);
                            break;
                        }
                    }
                ?>" disabled>
            </div>

            <!-- Descripción -->
            <div class="grupo-form">
                <label>Descripción:</label>
                <textarea rows="3" disabled><?= htmlspecialchars($solicitud['descripcion_solicitud']) ?></textarea>
            </div>

            <!-- Comentario material -->
            <div class="grupo-form">
                <label>Comentario sobre el material:</label>
                <textarea rows="3" disabled><?= htmlspecialchars($solicitud['comentario_material']) ?></textarea>
            </div>

            <!-- Justificantes -->
            <?php if (!empty($justificantes)): ?>
                <div class="grupo-form">
                    <h3>Justificantes:</h3>
                    <ul>
                        <?php foreach ($justificantes as $archivo): ?>
                            <li>
                                <a href="subidas/<?= htmlspecialchars($archivo['ruta_archivo']) ?>/<?= htmlspecialchars($archivo['nombre_generado']) ?>.<?= htmlspecialchars($archivo['tipo_archivo']) ?>" target="_blank">
                                    <?= htmlspecialchars($archivo['nombre_original']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Materiales -->
            <?php if (!empty($materiales)): ?>
                <div class="grupo-form">
                    <h3>Materiales:</h3>
                    <ul>
                        <?php foreach ($materiales as $archivo): ?>
                            <li>
                                <a href="subidas/<?= htmlspecialchars($archivo['ruta_archivo']) ?>/<?= htmlspecialchars($archivo['nombre_generado']) ?>.<?= htmlspecialchars($archivo['tipo_archivo']) ?>" target="_blank">
                                    <?= htmlspecialchars($archivo['nombre_original']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <div class="gestion">
                <a href="index.php?accion=gestionSolicitud&id=<?= $solicitud['id_Usuario'] ?>&fecha=<?= $solicitud['fecha_presentacion'] ?>&num=<?= $solicitud['num'] ?>&estado=a">Aceptar</a>
                <a href="index.php?accion=gestionSolicitud&id=<?= $solicitud['id_Usuario'] ?>&fecha=<?= $solicitud['fecha_presentacion'] ?>&num=<?= $solicitud['num'] ?>&estado=r">Rechazar</a>
            </div>
        </div>

        <!-- VISTA DE INICIO DE SESIÓN -->
        <div id="login">
            <h1>¡Bienvenido!</h1>
            <p id="mensaje">Estás accediendo a una aplicación del profesorado de la <em><b>Escuela Virgen de Guadalupe</b></em>. Recuerda que debes acceder con el correo de la fundación otorgado por el centro.</p>
            <p id="mensaje">Por favor, <a href="index.php?accion=loginGoogle">pulsa aquí</a> e introduce tus credenciales para acceder con Google.</p>
        </div>

        <!-- LISTADO DE CURSOS ------------------------------------------------------------------------------------------------->
        <div id="listado-cursos">
            <?php if (!empty($cursos)): ?>
                <?php foreach ($cursos as $curso): ?>
                    <?php
                        $estado = $curso['estado'];
                        $clase = match ($estado) {
                            'A' => 'activo',
                            'P' => 'pendiente',
                            'F' => 'finalizado',
                            default => '',
                        };
                    ?>
                    <div class="<?php echo $clase; ?>">
                        <h2><?php echo htmlspecialchars($curso['anio_academico']); ?></h2>
                        <p>Inicio: <?php echo htmlspecialchars($curso['fecha_inicio']); ?></p>
                        <p>Fin: <?php echo htmlspecialchars($curso['fecha_fin']); ?></p>

                        <?php if ($estado === 'A'): ?>
                            <a href="index.php?accion=irModificarCurso&id=<?php echo $curso['id']; ?>">Modificar curso</a>
                        <?php elseif ($estado === 'P'): ?>
                            <a href="index.php?accion=irModificarCurso&id=<?php echo $curso['id']; ?>">Modificar curso</a>
                            <a href="index.php?accion=borrarCurso&id=<?php echo $curso['id']; ?>" class="btn-borrar-curso" data-anio="<?php echo htmlspecialchars($curso['anio_academico']); ?>">Borrar curso</a>
                        <?php elseif ($estado === 'F'): ?>
                            <a href="index.php?accion=verCurso&id=<?php echo $curso['id']; ?>">Mostrar curso</a>
                            <a href="index.php?accion=borrarCurso&id=<?php echo $curso['id']; ?>" class="btn-borrar-curso" data-anio="<?php echo htmlspecialchars($curso['anio_academico']); ?>">Borrar curso</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay cursos registrados.</p>
            <?php endif; ?>
        </div>

        <!-- VISTA LISTADO DE SOLICITUDES --------------------------------------------------------------------------------->
        <div id="listado-solicitudes">
            <h1>Listado de solicitudes de <?= $_SESSION['nombre'] ?></h1>
            <table>
                <thead>
                    <tr>
                        <th>Identificador</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($solicitudes as $solicitud): ?>
                        <tr>
                            <td><?= htmlspecialchars($solicitud['identificador']) ?></td>
                            <td><?= htmlspecialchars($solicitud['motivo']) ?></td>
                            <td>
                                <?php
                                    $estado = strtolower($solicitud['estado']);
                                    if ($estado === 'a') {
                                        echo "🟢";
                                    } elseif ($estado === 'p') {
                                        echo "🟡";
                                    } elseif ($estado === 'r') {
                                        echo "🔴";
                                    }
                                ?>
                            </td>
                            <td>
                                <?php if ($estado === 'a' || $estado === 'r'): ?>
                                    <a href="index.php?accion=irVerSolicitud&id=<?= $_SESSION['id'] ?>&fecha=<?= $solicitud['fecha_presentacion'] ?>&num=<?= $solicitud['num'] ?>">Mostrar</a>
                                <?php else: ?>
                                    <a href="index.php?accion=irVerSolicitud&id=<?= $_SESSION['id'] ?>&fecha=<?= $solicitud['fecha_presentacion'] ?>&num=<?= $solicitud['num'] ?>">Mostrar</a>
                                    <a href="index.php?accion=irModificarSolicitud&id=<?= $_SESSION['id'] ?>&fecha=<?= $solicitud['fecha_presentacion'] ?>&num=<?= $solicitud['num'] ?>">Modificar</a>
                                    <a href="index.php?accion=borrarSolicitud&id=<?= $_SESSION['id'] ?>&fecha=<?= $solicitud['fecha_presentacion'] ?>&num=<?= $solicitud['num'] ?>" class="enlace-borrar">Borrar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- LISTADO DE USUARIOS ------------------------------------------------------------------------------------------------->
        <div id="listado-usuarios" class="listado-general">
            <?php if (!empty($usuarios)): ?>
                <?php foreach ($usuarios as $usuario): ?>
                    <div class="usuario">
                        <h2><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']); ?></h2>
                        <a href="index.php?accion=irModificarUsuario&id=<?php echo $usuario['id']; ?>">Modificar usuario</a>
                        <a href="index.php?accion=borrarUsuario&id=<?php echo $usuario['id']; ?>" class="btn-borrar-usuario">Borrar usuario</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay usuarios registrados.</p>
            <?php endif; ?>
        </div>

        <!-- Mensaje de bienvenida -->
        <div id="saludo">
            <h1>¡Bienvenid@, <?php echo $_SESSION['nombre']; ?></h1>
            <p>Este es tu panel de control. A tu izquierda puedes ver las opciones disponibles para ti.</p>
        </div>
        
        <!-- VISTA PARA VER LOS DATOS DE UNA SOLICITUD CONCRETA -->
        <div class="container">
            <h1>Mostrando solicitud</h1>

            <!-- Fechas no modificables -->
            <div class="grupo-form">
                <label>Fecha de Inicio de Ausencia:</label>
                <input type="date" value="<?= $solicitud['fecha_inicio_ausencia'] ?>" disabled>
            </div>

            <div class="grupo-form">
                <label>Fecha de Fin de Ausencia:</label>
                <input type="date" value="<?= $solicitud['fecha_fin_ausencia'] ?>" disabled>
            </div>

            <!-- Horas seleccionadas -->
            <div class="grupo-form" id="grupoHoras">
                <h3>Horas seleccionadas:</h3>
                <?php for ($i = 1; $i <= 7; $i++): ?>
                    <?php if (in_array($i, $horasSeleccionadas)): ?>
                        <p><?= $i ?>ª Hora</p>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>

            <!-- Motivo -->
            <div class="grupo-form">
                <label>Motivo de la ausencia:</label>
                <input type="text" value="<?php
                    foreach ($motivos as $motivo) {
                        if ($motivo['id'] == $solicitud['id_Motivo']) {
                            echo htmlspecialchars($motivo['nombre']);
                            break;
                        }
                    }
                ?>" disabled>
            </div>

            <!-- Descripción -->
            <div class="grupo-form">
                <label>Descripción:</label>
                <textarea rows="3" disabled><?= htmlspecialchars($solicitud['descripcion_solicitud']) ?></textarea>
            </div>

            <!-- Comentario material -->
            <div class="grupo-form">
                <label>Comentario sobre el material:</label>
                <textarea rows="3" disabled><?= htmlspecialchars($solicitud['comentario_material']) ?></textarea>
            </div>

            <!-- Justificantes -->
            <?php if (!empty($justificantes)): ?>
                <div class="grupo-form">
                    <h3>Justificantes:</h3>
                    <ul>
                        <?php foreach ($justificantes as $archivo): ?>
                            <li>
                                <a href="subidas/<?= htmlspecialchars($archivo['ruta_archivo']) ?>/<?= htmlspecialchars($archivo['nombre_generado']) ?>.<?= htmlspecialchars($archivo['tipo_archivo']) ?>" target="_blank">
                                    <?= htmlspecialchars($archivo['nombre_original']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Materiales -->
            <?php if (!empty($materiales)): ?>
                <div class="grupo-form">
                    <h3>Materiales:</h3>
                    <ul>
                        <?php foreach ($materiales as $archivo): ?>
                            <li>
                                <a href="subidas/<?= htmlspecialchars($archivo['ruta_archivo']) ?>/<?= htmlspecialchars($archivo['nombre_generado']) ?>.<?= htmlspecialchars($archivo['tipo_archivo']) ?>" target="_blank">
                                    <?= htmlspecialchars($archivo['nombre_original']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

    </body>
    <!-- Antonio Manuel Figueroa Pinilla con la colaboración de Leandro José Paniagua Balbuena -->
</html>