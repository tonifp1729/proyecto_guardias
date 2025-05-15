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
        <?php if (isset($cursoActivo) && $cursoActivo): ?>
            <p><strong>Año académico:</strong> <?= htmlspecialchars($cursoActivo['anoAcademico']) ?></p>
            <p><strong>Inicio:</strong> <?= htmlspecialchars($cursoActivo['fechaInicio']) ?></p>
            <p><strong>Finalización:</strong> <?= htmlspecialchars($cursoActivo['fechaFinalizacion']) ?></p>
        <?php else: ?>
            <p>No hay ningún curso activo actualmente.</p>
        <?php endif; ?>
    </div>
    <div id="listado-solicitudes-curso" class="listado">
        <h2>Solicitudes del curso</h2>
        <?php if (!empty($solicitudes)): ?>
            <ul>
                <?php foreach ($solicitudes as $s): ?>
                    <li>
                        <strong>Solicitante:</strong> <?= htmlspecialchars($s['nombre']) ?> <?= htmlspecialchars($s['apellidos']) ?>
                        | <strong>Estado:</strong> <?= htmlspecialchars($s['estado']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No hay solicitudes pendientes este curso.</p>
        <?php endif; ?>
    </div>
</div>