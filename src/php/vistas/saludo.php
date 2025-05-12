<!-- Mensaje de bienvenida -->
<?php
    if (!defined('ACCESO_PERMITIDO')) {
        header('Location: ../../index.php?accion=inicio');
        exit;
    }
?>
<div id="saludo">
    <h1>¡Bienvenid@, <?php echo $_SESSION['nombre']; ?>!</h1>
    <p>Este es tu panel de control. A tu izquierda puedes ver las opciones disponibles para ti.</p>
</div>