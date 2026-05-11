<section class="card">
    <form method="GET" class="toolbar">
        <div class="field">
            <label for="q">Buscar</label>
            <input type="text" id="q" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Nombre, apellidos o email">
        </div>
        <div class="field">
            <label for="rol">Rol</label>
            <select id="rol" name="rol">
                <option value="todos" <?php echo $rol_filtro === "todos" ? "selected" : ""; ?>>Todos</option>
                <option value="usuario" <?php echo $rol_filtro === "usuario" ? "selected" : ""; ?>>Usuario</option>
                <option value="entrenador" <?php echo $rol_filtro === "entrenador" ? "selected" : ""; ?>>Entrenador</option>
                <option value="admin" <?php echo $rol_filtro === "admin" ? "selected" : ""; ?>>Admin</option>
            </select>
        </div>
        <div class="inline-actions">
            <button type="submit">Filtrar</button>
            <a href="admin_usuarios.php" class="btn btn-secondary">Limpiar</a>
        </div>
    </form>
</section>
