<!-- VISTA PARA VER LOS DATOS DE UNA SOLICITUD CONCRETA -->
<?php
    if (!defined('ACCESO_PERMITIDO') || !isset($_SESSION['nombre'])) {
        header('Location: ../../index.php?accion=inicio');
        exit;
    }
?>
<div class="container">
    <div class="gestion-solicitud">
        <h1>Mostrando solicitud</h1>

        <!-- Fechas no modificables -->
        <div class="grupo-form">
            <label>Fecha de Inicio de Ausencia:</label>
            <input type="date" value="<?= $solicitud['fecha_inicio_ausencia'] ?>" disabled>
        </div>

        <div class="grupo-form">
            <label>Fecha de Fin de Ausencia:</label>
            <input type="date" value="<?= $solicitud['fecha_fin_ausencia'] ?>" disabled>
        </div>

        <!-- Horas seleccionadas -->
        <div class="grupo-form" id="grupoHoras">
            <h3>Horas seleccionadas:</h3>
            <?php for ($i = 1; $i <= 7; $i++): ?>
                <?php if (in_array($i, $horasSeleccionadas)): ?>
                    <p><?= $i ?>ª Hora</p>
                <?php endif; ?>
            <?php endfor; ?>
        </div>

        <!-- Motivo -->
        <div class="grupo-form">
            <label>Motivo de la ausencia:</label>
            <input type="text" value="<?php
                foreach ($motivos as $motivo) {
                    if ($motivo['id'] == $solicitud['id_Motivo']) {
                        echo htmlspecialchars($motivo['nombre']);
                        break;
                    }
                }
            ?>" disabled>
        </div>

        <!-- Justificantes -->
        <?php if (!empty($justificantes)): ?>
            <div class="grupo-form">
                <h3>Justificantes:</h3>
                <ul>
                    <?php foreach ($justificantes as $archivo): ?>
                        <li>
                            <a href="subidas/<?= htmlspecialchars($archivo['ruta_archivo']) ?>/<?= htmlspecialchars($archivo['nombre_generado']) ?>.<?= htmlspecialchars($archivo['tipo_archivo']) ?>" target="_blank">
                                <?= htmlspecialchars($archivo['nombre_original']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Descripción -->
        <div class="grupo-form">
            <label>Descripción:</label>
            <textarea rows="3" disabled><?= htmlspecialchars($solicitud['descripcion_solicitud']) ?></textarea>
        </div>

        <!-- Materiales -->
        <?php if (!empty($materiales)): ?>
            <div class="grupo-form">
                <h3>Materiales:</h3>
                <ul>
                    <?php foreach ($materiales as $archivo): ?>
                        <li>
                            <a href="subidas/<?= htmlspecialchars($archivo['ruta_archivo']) ?>/<?= htmlspecialchars($archivo['nombre_generado']) ?>.<?= htmlspecialchars($archivo['tipo_archivo']) ?>" target="_blank">
                                <?= htmlspecialchars($archivo['nombre_original']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Comentario material -->
        <div class="grupo-form">
            <label>Comentario sobre el material:</label>
            <textarea rows="3" disabled><?= htmlspecialchars($solicitud['comentario_material']) ?></textarea>
        </div>
    </div>
</div>