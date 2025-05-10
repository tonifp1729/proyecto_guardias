<!-- VISTA CURSO ACTUAL --------------------------------------------------------------------------------->
<div class="container">
    <h1>Curso actual</h1>
    <div class="curso-info">
        <?php
            if (isset($datosVista['data']['cursoActivo']) && !empty($datosVista['data']['cursoActivo'])) {
                $curso = $datosVista['data']['cursoActivo'];
                echo "<h2>Curso activo: Año académico {$curso['anoAcademico']}</h2>";
                echo "<p><strong>Fecha de inicio:</strong> {$curso['fechaInicio']}</p>";
                echo "<p><strong>Fecha de finalización:</strong> {$curso['fechaFinalizacion']}</p>";
                echo '<a href="index.php?controlador=Course&action=irmodificacioncurso">¿Algún error en el curso que se ha establecido?</a>';
            } else {
                echo "<h2>No hay ningún curso activo en este momento.</h2>";
                echo '<a href="index.php?controlador=Course&action=irnuevocurso">Crear un nuevo curso</a>';
            }
        ?>
    </div>
</div>
