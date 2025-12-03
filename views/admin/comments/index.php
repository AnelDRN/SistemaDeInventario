<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo htmlspecialchars($pageTitle ?? 'Gestión'); ?></h1>
</div>

<div class="card">
    <div class="card-header">
        <span>Todos los Comentarios</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Parte Comentada</th>
                        <th>Usuario</th>
                        <th>Comentario</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($comments)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay comentarios para moderar.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <tr>
                                <td><a href="../detalle.php?id=<?php echo $comment['parte_id']; ?>" target="_blank"><?php echo htmlspecialchars($comment['nombre_parte']); ?></a></td>
                                <td><?php echo htmlspecialchars($comment['nombre_usuario']); ?></td>
                                <td><?php echo htmlspecialchars($comment['texto_comentario']); ?></td>
                                <td>
                                    <span class="status-<?php echo htmlspecialchars($comment['estado']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($comment['estado'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($comment['estado'] !== 'aprobado'): ?>
                                        <a href="comentario_accion.php?action=approve&id=<?php echo $comment['id']; ?>" class="btn btn-sm btn-success">Aprobar</a>
                                    <?php endif; ?>
                                    <?php if ($comment['estado'] !== 'rechazado'): ?>
                                        <a href="comentario_accion.php?action=reject&id=<?php echo $comment['id']; ?>" class="btn btn-sm btn-secondary">Rechazar</a>
                                    <?php endif; ?>
                                    <a href="comentario_accion.php?action=delete&id=<?php echo $comment['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este comentario permanentemente?');">Borrar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
