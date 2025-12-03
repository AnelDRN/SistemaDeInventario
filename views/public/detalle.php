<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Sistema de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Rastro de Partes</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Admin Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <!-- Columna de la Imagen -->
            <div class="col-lg-6">
                <img src="<?php echo htmlspecialchars($part->getImagenUrl() ?? 'assets/img/placeholder_large.png'); ?>" class="img-fluid rounded shadow" alt="<?php echo htmlspecialchars($part->getNombre()); ?>">
            </div>

            <!-- Columna de Detalles -->
            <div class="col-lg-6">
                <h1 class="display-5"><?php echo htmlspecialchars($part->getNombre()); ?></h1>
                <p class="lead"><?php echo htmlspecialchars($part->getDescripcion() ?? 'Sin descripción.'); ?></p>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Auto:</strong> <?php echo htmlspecialchars($part->getMarcaAuto() . ' ' . $part->getModeloAuto()); ?></li>
                    <li class="list-group-item"><strong>Año:</strong> <?php echo htmlspecialchars($part->getAñoAuto() ?? 'N/A'); ?></li>
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
                
                <!-- Formulario para añadir comentario -->
                <div class="card mb-4">
                    <div class="card-body">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form action="detalle.php?id=<?php echo $part->getId(); ?>" method="POST">
                                <div class="mb-3">
                                    <label for="comment_text" class="form-label">Escribe tu comentario, <?php echo htmlspecialchars($_SESSION['username']); ?>:</label>
                                    <textarea class="form-control" id="comment_text" name="comment_text" rows="3" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Enviar Comentario</button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <a href="login.php">Inicia sesión</a> para dejar un comentario.
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
                <?php if (empty($comments)): ?>
                    <p>No hay comentarios para esta parte todavía. ¡Sé el primero en comentar!</p>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <p class="card-text"><?php echo htmlspecialchars($comment['texto_comentario']); ?></p>
                                <footer class="blockquote-footer">
                                    Por <cite title="Source Title"><?php echo htmlspecialchars($comment['nombre_usuario']); ?></cite> 
                                    en <?php echo date('d/m/Y', strtotime($comment['fecha_creacion'])); ?>
                                </footer>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>
