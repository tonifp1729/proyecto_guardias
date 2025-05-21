<!-- VISTA DEL FORMULARIO PARA INICIAR UN NUEVO CURSO --------------------------------------------------------------------------------->
<?php
    if (!defined('ACCESO_PERMITIDO') || !isset($_SESSION['nombre'])) {
        header('Location: ../../index.php?accion=inicio');
        exit;
    }
?>
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
<script src="js/validarCurso.js"></script>