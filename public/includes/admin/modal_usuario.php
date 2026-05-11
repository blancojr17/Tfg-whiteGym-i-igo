<div class="modal-backdrop" id="user-modal-backdrop">
    <div class="modal modal-user">
        <div class="modal-header">
            <div>
                <h3>Editar usuario</h3>
            </div>
        </div>

        <form action="../app/controllers/gestionar_usuario.php" method="POST" id="user-modal-form">
            <input type="hidden" name="id_usuario" id="modal-id-usuario">
            <input type="hidden" name="redirect_query" value="<?php echo htmlspecialchars($query_contexto, ENT_QUOTES, 'UTF-8'); ?>">

            <div class="modal-body">
                <div class="modal-meta">
                    <strong id="modal-nombre-completo"></strong>
                    <span id="modal-email"></span>
                </div>

                <div class="form-grid two-columns">
                    <div class="field">
                        <label for="modal-nombre">Nombre</label>
                        <input type="text" name="nombre" id="modal-nombre" required>
                    </div>

                    <div class="field">
                        <label for="modal-apellidos">Apellidos</label>
                        <input type="text" name="apellidos" id="modal-apellidos" required>
                    </div>

                    <div class="field">
                        <label for="modal-email-input">Email</label>
                        <input type="email" name="email" id="modal-email-input" required>
                    </div>

                    <div class="field">
                        <label for="modal-telefono">Telefono</label>
                        <input type="text" name="telefono" id="modal-telefono">
                    </div>

                    <div class="field">
                        <label for="modal-sexo">Sexo</label>
                        <select name="sexo" id="modal-sexo">
                            <option value="">Sin indicar</option>
                            <option value="hombre">Hombre</option>
                            <option value="mujer">Mujer</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="modal-ciudad">Ciudad</label>
                        <input type="text" name="ciudad" id="modal-ciudad">
                    </div>

                    <div class="field">
                        <label for="modal-fecha-nacimiento">Fecha nacimiento</label>
                        <input type="date" name="fecha_nacimiento" id="modal-fecha-nacimiento">
                    </div>

                    <div class="field">
                        <label for="modal-rol">Rol</label>
                        <select name="rol" id="modal-rol">
                            <option value="usuario">Usuario</option>
                            <option value="entrenador">Entrenador</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="modal-activo">Estado</label>
                        <select name="activo" id="modal-activo">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="modal-meta modal-user-summary">
                    <span><strong>Fecha registro:</strong> <span id="modal-fecha-registro"></span></span>
                    <span><strong>Plan activo:</strong> <span id="modal-plan-activo"></span></span>
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" id="close-user-modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
