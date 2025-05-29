<?php
if (!defined('ACCESO_PERMITIDO') || !isset($_SESSION['nombre'])) {
    header('Location: ../../index.php?accion=inicio');
    exit;
}
?>
<div class="container">
    <h1>Modificar Solicitud</h1>
    <form action="index.php?accion=modificarSolicitud" method="POST" class="form-solicitud" enctype="multipart/form-data">

        <!-- Campos ocultos necesarios -->
        <input type="hidden" name="idSolicitud" value="<?= htmlspecialchars($solicitud['id']) ?>">
        <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($solicitud['id_Usuario']) ?>">
        <input type="hidden" name="fecha_presentacion" value="<?= htmlspecialchars($solicitud['fecha_presentacion']) ?>">
        <input type="hidden" name="num" value="<?= htmlspecialchars($solicitud['num']) ?>">

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
            <h3>Selecciona Horas:</h3>
            <?php for ($i = 1; $i <= 7; $i++): ?>
                <label>
                    <input type="checkbox" value="<?= $i ?>" <?= in_array($i, $horasSeleccionadas) ? 'checked' : '' ?> disabled>
                    <?= $i ?>ª Hora
                </label>
            <?php endfor; ?>
        </div>

        <!-- Motivo -->
        <div class="grupo-form">
            <label for="id_motivo">Motivo de la ausencia:</label>
            <select id="id_motivo" name="motivo" required>
                <?php foreach ($motivos as $motivo): ?>
                    <option value="<?= htmlspecialchars($motivo['id']) ?>" <?= $motivo['id'] == $solicitud['id_Motivo'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($motivo['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Descripción -->
        <div class="grupo-form">
            <label for="descripcion">Descripción (opcional):</label>
            <textarea id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars($solicitud['descripcion_solicitud']) ?></textarea>
        </div>

        <!-- Comentario material -->
        <div class="grupo-form">
            <label for="comentario">Comentario sobre el material (opcional):</label>
            <textarea id="comentario" name="comentario" rows="3"><?= htmlspecialchars($solicitud['comentario_material']) ?></textarea>
        </div>

        <!-- Archivos actuales -->
        <?php if (!empty($archivos)): ?>
            <div class="grupo-form">
                <h3>Archivos actuales:</h3>
                <?php foreach ($archivos as $archivo): ?>
                    <div>
                        <label>
                            <input type="checkbox" name="archivos_a_eliminar[]" value="<?= $archivo['id'] ?>"> Eliminar
                        </label>
                        <a href="subidas/<?= htmlspecialchars($archivo['ruta_archivo']) ?>/<?= htmlspecialchars($archivo['nombre_generado']) ?>.<?= htmlspecialchars($archivo['tipo_archivo']) ?>" target="_blank"><?= htmlspecialchars($archivo['nombre_original']) ?></a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Nuevos archivos -->
        <div class="grupo-form">
            <label for="justificantes">Subir nuevos justificantes:</label>
            <input type="file" name="justificantes[]" id="justificantes" multiple accept=".pdf,.jpg,.jpeg,.png">
        </div>

        <div class="grupo-form">
            <label for="materiales">Subir nuevos materiales (si procede):</label>
            <input type="file" name="materiales[]" id="materiales" multiple accept=".pdf,.doc,.docx,.xls,.xlsx">
        </div>

        <button type="submit">Guardar Cambios</button>
    </form>

    <?php if (isset($error)): ?>
        <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
</div>
<script src="js/validarSolicitud.js"></script>