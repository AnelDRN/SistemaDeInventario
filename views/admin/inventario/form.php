<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
    <a href="inventario.php" class="btn btn-secondary">Volver al Inventario</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <strong>Por favor, corrija los siguientes errores:</strong>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form action="inventario_form.php<?php echo $isEditMode ? '?id=' . htmlspecialchars($part->getId()) : ''; ?>" method="POST" enctype="multipart/form-data">
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de la Parte</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($part->getNombre() ?? ''); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4"><?php echo htmlspecialchars($part->getDescripcion() ?? ''); ?></textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <?php if ($isEditMode && $part->getThumbnailUrl()): ?>
                        <div class="mb-3 text-center">
                            <label class="form-label">Imagen Actual</label>
                            <img src="../<?php echo htmlspecialchars($part->getThumbnailUrl()); ?>" class="img-thumbnail" alt="Imagen actual">
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="imagen" class="form-label"><?php echo $isEditMode ? 'Reemplazar Imagen' : 'Imagen de la Parte'; ?></label>
                        <input class="form-control" type="file" id="imagen" name="imagen" <?php echo !$isEditMode ? 'required' : ''; ?>>
                        <div class="form-text">Formatos: JPG, PNG, GIF. Tamaño máx: 5MB.</div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tipo_parte" class="form-label">Tipo de Parte</label>
                        <input type="text" class="form-control" id="tipo_parte" name="tipo_parte" value="<?php echo htmlspecialchars($part->getTipoParte() ?? ''); ?>" placeholder="Ej: Puerta, Motor, Faro">
                    </div>
                </div>
                 <div class="col-md-6">
                    <div class="mb-3">
                        <label for="seccion_id" class="form-label">Sección en el Rastro</label>
                        <select class="form-select" id="seccion_id" name="seccion_id" required>
                            <option value="">-- Seleccione una sección --</option>
                            <?php foreach ($sections as $section): ?>
                                <option value="<?php echo $section->getId(); ?>" <?php echo ($part->getSeccionId() === $section->getId()) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($section->getNombre()); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label for="marca_auto" class="form-label">Marca del Auto</label>
                    <input type="text" class="form-control" id="marca_auto" name="marca_auto" value="<?php echo htmlspecialchars($part->getMarcaAuto() ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label for="modelo_auto" class="form-label">Modelo del Auto</label>
                    <input type="text" class="form-control" id="modelo_auto" name="modelo_auto" value="<?php echo htmlspecialchars($part->getModeloAuto() ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label for="año_auto" class="form-label">Año</label>
                    <input type="number" class="form-control" id="año_auto" name="año_auto" value="<?php echo htmlspecialchars($part->getAñoAuto() ?? ''); ?>" placeholder="Ej: 2015">
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <label for="precio" class="form-label">Precio de Venta</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" id="precio" name="precio" value="<?php echo htmlspecialchars($part->getPrecio() ?? '0.00'); ?>" step="0.01" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="cantidad_disponible" class="form-label">Cantidad Disponible</label>
                    <input type="number" class="form-control" id="cantidad_disponible" name="cantidad_disponible" value="<?php echo htmlspecialchars($part->getCantidadDisponible() ?? '0'); ?>" required>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="inventario.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
