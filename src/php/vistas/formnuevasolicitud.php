<!-- VISTA DEL FORMULARIO DE NUEVA SOLICITUD --------------------------------------------------------------------------------->
<?php
    if (!defined('ACCESO_PERMITIDO') || !isset($_SESSION['nombre'])) {
        header('Location: ../../index.php?accion=inicio');
        exit;
    }
?>
<div class="container">
    <h1>Nueva Solicitud de Ausencia</h1>
    <form action="index.php?accion=crearSolicitud" method="POST" class="form-solicitud" enctype="multipart/form-data">
        <div class="grupo-form">
            <label for="fecha_inicio_ausencia">Fecha de Inicio de Ausencia:</label>
            <input type="date" id="fecha_inicio_ausencia" name="fecha_inicio_ausencia" required>
        </div>

        <div class="grupo-form">
            <label for="fecha_fin_ausencia">Fecha de Fin de Ausencia:</label>
            <input type="date" id="fecha_fin_ausencia" name="fecha_fin_ausencia" required>
        </div>

        <div class="grupo-form" id="grupoHoras" style="display: none;">
            <h3>Selecciona Horas:</h3>
            <label><input type="checkbox" name="horas[]" value="1"> 1ª Hora</label>
            <label><input type="checkbox" name="horas[]" value="2"> 2ª Hora</label>
            <label><input type="checkbox" name="horas[]" value="3"> 3ª Hora</label>
            <label><input type="checkbox" name="horas[]" value="4"> 4ª Hora</label>
            <label><input type="checkbox" name="horas[]" value="5"> 5ª Hora</label>
            <label><input type="checkbox" name="horas[]" value="6"> 6ª Hora</label>
            <label><input type="checkbox" name="horas[]" value="7"> 7ª Hora</label>
        </div>

        <div class="grupo-form">
            <label for="id_motivo">Motivo de la ausencia:</label>
            <select id="id_motivo" name="id_motivo" required>
                <?php foreach ($motivos as $motivo): ?>
                    <option value="<?= htmlspecialchars($motivo['id']) ?>">
                        <?= htmlspecialchars($motivo['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grupo-form">
            <label for="descripcion_solicitud">Descripción (opcional):</label>
            <textarea id="descripcion_solicitud" name="descripcion_solicitud" rows="3"></textarea>
        </div>

        <div class="grupo-form">
            <label for="comentario_material">Comentario sobre el material (opcional):</label>
            <textarea id="comentario_material" name="comentario_material" rows="3"></textarea>
        </div>

        <div class="grupo-form">
            <label for="justificantes">Subir justificantes:</label>
            <input type="file" name="justificantes[]" id="justificantes" multiple accept=".pdf,.jpg,.jpeg,.png">
        </div>
        
        <div class="grupo-form">
            <label for="materiales">Subir materiales (si procede):</label>
            <input type="file" name="materiales[]" id="materiales" multiple accept=".pdf,.doc,.docx,.xls,.xlsx">
        </div>

        <button type="submit">Enviar Solicitud</button>
    </form>
    <?php if (isset($error)): ?>
        <div id="error-server" class="mensaje-error" data-error="<?= htmlspecialchars($error) ?>"></div>
    <?php endif; ?>
</div>
<script src="js/validarSolicitud.js"></script>