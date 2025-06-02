<!-- VISTA CURSO ACTUAL --------------------------------------------------------------------------------->
<?php
    if (!defined('ACCESO_PERMITIDO') || !isset($_SESSION['nombre'])) {
        header('Location: ../../index.php?accion=inicio');
        exit;
    }
?>
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