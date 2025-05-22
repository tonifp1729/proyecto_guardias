<!-- LISTADO DE USUARIOS ------------------------------------------------------------------------------------------------->
<?php
    if (!defined('ACCESO_PERMITIDO') || !isset($_SESSION['nombre'])) {
        header('Location: ../../index.php?accion=inicio');
        exit;
    }
?>
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
<script src="js/validarUsuario.js"></script>