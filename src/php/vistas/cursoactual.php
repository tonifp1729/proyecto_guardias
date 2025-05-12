<!-- VISTA CURSO ACTUAL --------------------------------------------------------------------------------->
<?php
    if (!defined('ACCESO_PERMITIDO') || !isset($_SESSION['nombre'])) {
        header('Location: ../../index.php?accion=inicio');
        exit;
    }
?>
<div class="container">
    <h1>Curso actual</h1>
    <div class="curso-info">
        <?php

        ?>
    </div>
</div>
