<!-- VISTA DEL FORMULARIO PARA MODIFICAR USUARIO --------------------------------------------------------------------------------->
<?php
    if (!defined('ACCESO_PERMITIDO') || !isset($_SESSION['nombre'])) {
        header('Location: ../../index.php?accion=inicio');
        exit;
    }
?>
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
<script src="js/validarUsuario.js"></script>