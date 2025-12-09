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
                                <td><a href="<?php echo BASE_URL; ?>/detalle.php?id=<?php echo $comment['parte_id']; ?>" target="_blank"><?php echo htmlspecialchars($comment['nombre_parte']); ?></a></td>
                                <td><?php echo htmlspecialchars($comment['nombre_usuario']); ?></td>
                                <td><?php echo htmlspecialchars($comment['texto_comentario']); ?></td>
                                <td>
                                    <span class="status-<?php echo htmlspecialchars($comment['estado']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($comment['estado'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($comment['estado'] !== 'aprobado'): ?>
                                        <form action="<?php echo BASE_URL; ?>/public/index.php?/admin/comentarios/action" method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="approve">
                                            <input type="hidden" name="id" value="<?php echo $comment['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-success">Aprobar</button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($comment['estado'] !== 'rechazado'): ?>
                                        <form action="<?php echo BASE_URL; ?>/public/index.php?/admin/comentarios/action" method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="reject">
                                            <input type="hidden" name="id" value="<?php echo $comment['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-secondary">Rechazar</button>
                                        </form>
                                    <?php endif; ?>
                                    <form action="<?php echo BASE_URL; ?>/public/index.php?/admin/comentarios/action" method="POST" onsubmit="return confirm('¿Está seguro de que desea eliminar este comentario permanentemente?');" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $comment['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Borrar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
