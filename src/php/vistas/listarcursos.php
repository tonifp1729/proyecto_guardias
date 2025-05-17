<!-- LISTADO DE CURSOS ------------------------------------------------------------------------------------------------->
<?php
    if (!defined('ACCESO_PERMITIDO') || !isset($_SESSION['nombre'])) {
        header('Location: ../../index.php?accion=inicio');
        exit;
    }
?>
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
                    <a href="index.php?accion=modificarCurso&id=<?php echo $curso['id']; ?>">Modificar curso</a>
                <?php elseif ($estado === 'P'): ?>
                    <a href="index.php?accion=modificarCurso&id=<?php echo $curso['id']; ?>">Modificar curso</a>
                    <a href="index.php?accion=borrarCurso&id=<?php echo $curso['id']; ?>">Borrar curso</a>
                <?php elseif ($estado === 'F'): ?>
                    <a href="index.php?accion=mostrarCurso&id=<?php echo $curso['id']; ?>">Mostrar curso</a>
                    <a href="index.php?accion=borrarCurso&id=<?php echo $curso['id']; ?>">Borrar curso</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay cursos registrados.</p>
    <?php endif; ?>
</div>