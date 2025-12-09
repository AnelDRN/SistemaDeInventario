<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Rastro'); ?> - Sistema de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>/public/index.php">Rastro de Partes</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNavbar" aria-controls="publicNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="publicNavbar">
            <ul class="navbar-nav ms-auto">
                <?php $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/public/index.php?/cart">
                        <i class="bi bi-cart"></i> Carrito
                        <?php if ($cart_count > 0): ?>
                            <span class="badge bg-primary rounded-pill"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <?php if ($_SESSION['role_id'] === 1): ?>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/public/index.php?/admin/dashboard">Admin Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/public/index.php?/logout">Cerrar Sesión</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/public/index.php?/register">Registrarse</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/public/index.php?/login">Iniciar Sesión</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container mt-5">
    <?php \App\Helpers\FlashMessage::displayAllMessages(); ?>
