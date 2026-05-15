<?php // tabla con el listado de usuarios ?>
<!-- bloque principal de contenido -->
<section class="card">
    <?php if (empty($usuarios)): ?>
        <div class="empty-state">No hay usuarios para los filtros seleccionados.</div>
    <?php else: ?>
<!-- contenedor de la tabla -->
        <div class="table-wrap">
<!-- tabla de datos -->
            <table class="admin-users-table">
<!-- cabecera de la tabla -->
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Accion</th>
                    </tr>
                </thead>
<!-- contenido de la tabla -->
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <?php
                        $es_mi_cuenta = (int) $usuario["id_usuario"] === $id_admin_actual;
                        $estado = ((int) ($usuario["activo"] ?? 0)) === 1 ? "Activo" : "Inactivo";
                        ?>
                        <tr>
                            <td>
                                <div class="user-identity">
                                    <strong><?php echo htmlspecialchars(trim(($usuario["nombre"] ?? "") . " " . ($usuario["apellidos"] ?? ""))); ?></strong>
                                    <span><?php echo htmlspecialchars($usuario["email"] ?? ""); ?></span>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars(ucfirst((string) ($usuario["rol"] ?? ""))); ?></td>
                            <td>
                                <span class="status-pill <?php echo $estado === "Activo" ? "status-ok" : "status-muted"; ?>">
                                    <?php echo $estado; ?>
                                </span>
                            </td>
                            <td>
                                <button
                                    type="button"
                                    class="btn btn-secondary js-open-user-modal"
                                    data-id="<?php echo (int) ($usuario["id_usuario"] ?? 0); ?>"
                                    data-nombre="<?php echo htmlspecialchars($usuario["nombre"] ?? "", ENT_QUOTES, 'UTF-8'); ?>"
                                    data-apellidos="<?php echo htmlspecialchars($usuario["apellidos"] ?? "", ENT_QUOTES, 'UTF-8'); ?>"
                                    data-email="<?php echo htmlspecialchars($usuario["email"] ?? "", ENT_QUOTES, 'UTF-8'); ?>"
                                    data-telefono="<?php echo htmlspecialchars($usuario["telefono"] ?? "", ENT_QUOTES, 'UTF-8'); ?>"
                                    data-sexo="<?php echo htmlspecialchars($usuario["sexo"] ?? "", ENT_QUOTES, 'UTF-8'); ?>"
                                    data-ciudad="<?php echo htmlspecialchars($usuario["ciudad"] ?? "", ENT_QUOTES, 'UTF-8'); ?>"
                                    data-fecha-nacimiento="<?php echo htmlspecialchars($usuario["fecha_nacimiento"] ?? "", ENT_QUOTES, 'UTF-8'); ?>"
                                    data-fecha-registro="<?php echo htmlspecialchars($usuario["fecha_registro"] ?? "", ENT_QUOTES, 'UTF-8'); ?>"
                                    data-plan-activo="<?php echo htmlspecialchars($usuario["plan_activo"] ?? "Sin plan", ENT_QUOTES, 'UTF-8'); ?>"
                                    data-rol="<?php echo htmlspecialchars($usuario["rol"] ?? "", ENT_QUOTES, 'UTF-8'); ?>"
                                    data-activo="<?php echo (int) ($usuario["activo"] ?? 0); ?>"
                                >
                                    Editar
                                </button>
                                <?php if ($es_mi_cuenta): ?>
                                    <small class="admin-note">Tu cuenta</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

