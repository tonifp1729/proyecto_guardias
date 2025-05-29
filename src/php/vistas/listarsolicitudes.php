<!-- VISTA LISTADO DE SOLICITUDES --------------------------------------------------------------------------------->
<?php
    if (!defined('ACCESO_PERMITIDO') || !isset($_SESSION['nombre'])) {
        header('Location: ../../index.php?accion=inicio');
        exit;
    }
?>
<div id="listado-solicitudes">
    <h1>Listado de solicitudes de <?= $_SESSION['nombre'] ?></h1>
    <table>
        <thead>
            <tr>
                <th>Identificador</th>
                <th>Motivo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($solicitudes as $solicitud): ?>
                <tr>
                    <td><?= htmlspecialchars($solicitud['identificador']) ?></td>
                    <td><?= htmlspecialchars($solicitud['motivo']) ?></td>
                    <td>
                        <?php
                            $estado = strtolower($solicitud['estado']);
                            if ($estado === 'a') {
                                echo "🟢";
                            } elseif ($estado === 'p') {
                                echo "🟡";
                            } elseif ($estado === 'r') {
                                echo "🔴";
                            }
                        ?>
                    </td>
                    <td>
                        <?php if ($estado === 'a'): ?>
                            <a href="index.php?accion=mostrar_solicitud&id=<?= $_SESSION['idCursoActivo'] ?>">Mostrar</a>
                        <?php else: ?>
                            <a href="index.php?accion=modificar_solicitud&id=<?= $_SESSION['idCursoActivo'] ?>">Modificar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="js/validarSolicitud.js"></script>