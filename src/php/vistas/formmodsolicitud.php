<?php
    if (!defined('ACCESO_PERMITIDO') || !isset($_SESSION['nombre'])) {
        header('Location: ../../index.php?accion=inicio');
        exit;
    }
?>
<div class="container">
    <h1>Modificar Solicitud</h1>

    <form action="index.php?accion=guardarModificacionSolicitud" method="POST" class="form-solicitud" enctype="multipart/form-data">
        <input type="hidden" name="idSolicitud" value="<?= $solicitud['idSolicitud'] ?>">

        <div class="grupo-form">
            <label for="fecha_inicio_ausencia">Fecha de Inicio:</label>
            <input type="date" id="fecha_inicio_ausencia" name="fecha_inicio_ausencia"
                   value="<?= htmlspecialchars($solicitud['fecha_inicio']) ?>" readonly>
        </div>

        <div class="grupo-form">
            <label for="fecha_fin_ausencia">Fecha de Fin:</label>
            <input type="date" id="fecha_fin_ausencia" name="fecha_fin_ausencia"
                   value="<?= htmlspecialchars($solicitud['fecha_fin']) ?>" readonly>
        </div>

        <div class="grupo-form">
            <label for="id_motivo">Motivo de la ausencia:</label>
            <select id="id_motivo" name="id_motivo" required>
                <?php foreach ($motivos as $motivo): ?>
                    <option value="<?= $motivo['id'] ?>" <?= $motivo['id'] == $solicitud['id_motivo'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($motivo['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grupo-form">
            <label for="descripcion_solicitud">Descripción:</label>
            <textarea id="descripcion_solicitud" name="descripcion_solicitud" rows="3"><?= htmlspecialchars($solicitud['descripcion']) ?></textarea>
        </div>

        <div class="grupo-form">
            <label for="comentario_material">Comentario sobre el material:</label>
            <textarea id="comentario_material" name="comentario_material" rows="3"><?= htmlspecialchars($solicitud['comentario_material']) ?></textarea>
        </div>

        <!-- Archivos existentes: Justificantes -->
        <div class="grupo-form">
            <label>Justificantes actuales:</label>
            <?php if (!empty($solicitud['justificantes'])): ?>
                <ul>
                    <?php foreach ($solicitud['justificantes'] as $archivo): ?>
                        <li>
                            <a href="<?= $archivo['ruta'] ?>" target="_blank"><?= basename($archivo['ruta']) ?></a>
                            <label>
                                <input type="checkbox" name="eliminar_justificantes[]" value="<?= $archivo['id'] ?>"> Eliminar
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No hay justificantes actuales.</p>
            <?php endif; ?>
        </div>

        <!-- Nuevos justificantes -->
        <div class="grupo-form">
            <label for="justificantes">Subir nuevos justificantes:</label>
            <input type="file" name="justificantes[]" id="justificantes" multiple accept=".pdf,.jpg,.jpeg,.png">
        </div>

        <!-- Archivos existentes: Materiales -->
        <div class="grupo-form">
            <label>Materiales actuales:</label>
            <?php if (!empty($solicitud['materiales'])): ?>
                <ul>
                    <?php foreach ($solicitud['materiales'] as $archivo): ?>
                        <li>
                            <a href="<?= $archivo['ruta'] ?>" target="_blank"><?= basename($archivo['ruta']) ?></a>
                            <label>
                                <input type="checkbox" name="eliminar_materiales[]" value="<?= $archivo['id'] ?>"> Eliminar
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No hay materiales actuales.</p>
            <?php endif; ?>
        </div>

        <!-- Nuevos materiales -->
        <div class="grupo-form">
            <label for="materiales">Subir nuevos materiales:</label>
            <input type="file" name="materiales[]" id="materiales" multiple accept=".pdf,.doc,.docx,.xls,.xlsx">
        </div>

        <button type="submit">Guardar Cambios</button>
    </form>

    <?php if (isset($error)): ?>
        <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
</div>