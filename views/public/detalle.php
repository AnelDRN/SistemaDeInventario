<div class="row">
    <!-- Columna de la Imagen -->
    <div class="col-lg-6">
        <img src="<?php echo BASE_URL; ?>/<?php echo htmlspecialchars($part->getImagenUrl() ?? 'assets/img/placeholder_large.png'); ?>" class="img-fluid rounded shadow" alt="<?php echo htmlspecialchars($part->getNombre()); ?>">
    </div>

    <!-- Columna de Detalles -->
    <div class="col-lg-6">
        <h1 class="display-5"><?php echo htmlspecialchars($part->getNombre()); ?></h1>
        <p class="lead"><?php echo htmlspecialchars($part->getDescripcion() ?? 'Sin descripción.'); ?></p>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>Auto:</strong> <?php echo htmlspecialchars($part->getMarcaAuto() . ' ' . $part->getModeloAuto()); ?></li>
            <li class="list-group-item"><strong>Año:</strong> <?php echo htmlspecialchars($part->getAnioAuto() ?? 'N/A'); ?></li>
            <li class="list-group-item"><strong>Tipo de Parte:</strong> <?php echo htmlspecialchars($part->getTipoParte() ?? 'N/A'); ?></li>
            <li class="list-group-item"><strong>Cantidad Disponible:</strong> <?php echo htmlspecialchars($part->getCantidadDisponible()); ?></li>
            <li class="list-group-item bg-light">
                <strong class="text-success h4">Precio: $<?php echo number_format($part->getPrecio(), 2); ?></strong>
            </li>
        </ul>
    </div>
</div>

<hr class="my-5">

<!-- Sección de Comentarios -->
<div class="row">
    <div class="col-md-8 offset-md-2">
        <h2>Comentarios</h2>
        
        <!-- Formulario para añadir comentario principal -->
        <div class="card mb-4">
            <div class="card-body">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="<?php echo BASE_URL; ?>/public/index.php?/part/comment" method="POST">
                        <input type="hidden" name="part_id" value="<?php echo $part->getId(); ?>">
                        <div class="mb-3">
                            <label for="comment_text" class="form-label">Escribe tu comentario, <?php echo htmlspecialchars($_SESSION['username']); ?>:</label>
                            <textarea class="form-control" id="comment_text" name="comment_text" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Enviar Comentario</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info">
                        <a href="<?php echo BASE_URL; ?>/public/index.php?/login">Inicia sesión</a> para dejar un comentario.
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger mt-3">
                        <?php foreach ($errors as $error): ?>
                            <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($successMessage): ?>
                     <div class="alert alert-success mt-3">
                        <?php echo htmlspecialchars($successMessage); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Lista de Comentarios -->
        <?php
        function displayThreadedComments($comments, $partId, $level = 0) {
            $marginLeft = $level * 40; // Aumentar el margen para cada nivel de anidación
        
            foreach ($comments as $comment) {
                echo '<div class="card mb-3" style="margin-left: ' . $marginLeft . 'px;">';
                echo '<div class="card-body">';
                echo '<p class="card-text">' . htmlspecialchars($comment['texto_comentario']) . '</p>';
                echo '<footer class="blockquote-footer d-flex justify-content-between align-items-center">';
                echo '<span>Por <cite>' . htmlspecialchars($comment['nombre_usuario']) . '</cite> en ' . date('d/m/Y', strtotime($comment['fecha_creacion'])) . '</span>';
                
                // Botones de Acción
                echo '<div>';
                if (isset($_SESSION['user_id'])) {
                    echo '<button class="btn btn-sm btn-outline-primary reply-btn" data-comment-id="' . $comment['id'] . '">Responder</button>';
                }
                if (isset($_SESSION['role_id']) && $_SESSION['role_id'] === 1) {
                    echo '<form action="' . BASE_URL . '/public/index.php?/admin/comment/delete" method="POST" onsubmit="return confirm(\'¿Está seguro de eliminar este comentario y todas sus respuestas?\');" style="display: inline; margin-left: 5px;">';
                    echo '<input type="hidden" name="comment_id" value="' . $comment['id'] . '">';
                    echo '<input type="hidden" name="part_id" value="' . $partId . '">';
                    echo '<button type="submit" class="btn btn-sm btn-outline-danger">Borrar</button>';
                    echo '</form>';
                }
                echo '</div>'; // Cierre de botones
                
                echo '</footer>';

                // Formulario de respuesta (oculto por defecto)
                if (isset($_SESSION['user_id'])) {
                    echo '<div class="reply-form-container mt-3" id="reply-form-' . $comment['id'] . '" style="display: none;">';
                    echo '<form action="' . BASE_URL . '/public/index.php?/part/comment" method="POST">';
                    echo '<input type="hidden" name="part_id" value="' . $partId . '">';
                    echo '<input type="hidden" name="parent_id" value="' . $comment['id'] . '">';
                    echo '<div class="mb-3">';
                    echo '<label for="reply_text_' . $comment['id'] . '" class="form-label">Tu respuesta:</label>';
                    echo '<textarea class="form-control" id="reply_text_' . $comment['id'] . '" name="comment_text" rows="2" required></textarea>';
                    echo '</div>';
                    echo '<button type="submit" class="btn btn-secondary btn-sm">Enviar Respuesta</button>';
                    echo '</form>';
                    echo '</div>';
                }

                echo '</div>'; // Cierre de card-body
                echo '</div>'; // Cierre de card

                if (!empty($comment['children'])) {
                    displayThreadedComments($comment['children'], $partId, $level + 1);
                }
            }
        }

        if (empty($comments)) {
            echo '<p>No hay comentarios para esta parte todavía. ¡Sé el primero en comentar!</p>';
        } else {
            displayThreadedComments($comments, $part->getId());
        }
        ?>
    </div>
</div>
