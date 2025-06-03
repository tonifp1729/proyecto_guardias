<!-- VISTA DEL FORMULARIO PARA MODIFICAR CURSO --------------------------------------------------------------------------------->
<?php
    if (!defined('ACCESO_PERMITIDO') || !isset($_SESSION['nombre'])) {
        header('Location: ../../index.php?accion=inicio');
        exit;
    }
?>
<div class="container">
    <form action="index.php?accion=modificarCurso" method="POST" class="form-curso">
        <h1><?php echo htmlspecialchars($curso['anoAcademico']); ?></h1>
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
<script src="js/validarCurso.js"></script>