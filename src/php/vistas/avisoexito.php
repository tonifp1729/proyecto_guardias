<!-- VISTA QUE SE MUESTRA TRÁS REALIZAR DETERMINADAS OPERACIONES DE MANERA EXITOSA --------------------------------------------------------------------------------->
<?php
    if (!defined('ACCESO_PERMITIDO') || !isset($_SESSION['nombre'])) {
        header('Location: ../../index.php?accion=inicio');
        exit;
    }
?>
<div class="aviso-correcto">
        <h2>¡Acción realizada correctamente!</h2>
        <p>La operación que intentaste realizar se completó con éxito.</p>
</div>