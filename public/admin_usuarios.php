<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["email"]) || $_SESSION["rol"] !== "admin") {
    header("Location: login.php");
    exit;
}

$id_admin_actual = (int) $_SESSION["id_usuario"];
$q = trim((string) ($_GET["q"] ?? ""));
$rol_filtro = (string) ($_GET["rol"] ?? "todos");
$roles_validos = ["todos", "usuario", "entrenador", "admin"];
$rol_filtro = in_array($rol_filtro, $roles_validos, true) ? $rol_filtro : "todos";
$pagina = max(1, (int) ($_GET["page"] ?? 1));
$por_pagina = 10;
$offset = ($pagina - 1) * $por_pagina;

function bind_dynamic_params($stmt, string $types, array $params): void
{
    if ($types === "" || empty($params)) {
        return;
    }

    $bind_values = [$types];
    foreach ($params as $key => $value) {
        $bind_values[] = &$params[$key];
    }

    call_user_func_array([$stmt, "bind_param"], $bind_values);
}

$where = [];
$types = "";
$params = [];

if ($q !== "") {
    $where[] = "(nombre LIKE ? OR email LIKE ?)";
    $types .= "ss";
    $like = "%" . $q . "%";
    $params[] = $like;
    $params[] = $like;
}

if ($rol_filtro !== "todos") {
    $where[] = "rol = ?";
    $types .= "s";
    $params[] = $rol_filtro;
}

$where_sql = $where ? " WHERE " . implode(" AND ", $where) : "";
$total_usuarios = 0;
$usuarios = [];

$sqlTotal = "SELECT COUNT(*) AS total FROM usuarios" . $where_sql;
$stmtTotal = $conexion->prepare($sqlTotal);
if ($stmtTotal) {
    bind_dynamic_params($stmtTotal, $types, $params);
    $stmtTotal->execute();
    $resultadoTotal = $stmtTotal->get_result();
    $filaTotal = $resultadoTotal ? $resultadoTotal->fetch_assoc() : null;
    $total_usuarios = (int) ($filaTotal["total"] ?? 0);
    $stmtTotal->close();
}

$total_paginas = max(1, (int) ceil($total_usuarios / $por_pagina));
if ($pagina > $total_paginas) {
    $pagina = $total_paginas;
    $offset = ($pagina - 1) * $por_pagina;
}

$sqlUsuarios = "SELECT id_usuario, nombre, email, rol, activo
                FROM usuarios" . $where_sql . "
                ORDER BY id_usuario DESC
                LIMIT ?, ?";
$stmtUsuarios = $conexion->prepare($sqlUsuarios);
if ($stmtUsuarios) {
    $types_data = $types . "ii";
    $params_data = $params;
    $params_data[] = $offset;
    $params_data[] = $por_pagina;
    bind_dynamic_params($stmtUsuarios, $types_data, $params_data);
    $stmtUsuarios->execute();
    $resultadoUsuarios = $stmtUsuarios->get_result();
    while ($fila = $resultadoUsuarios->fetch_assoc()) {
        $usuarios[] = $fila;
    }
    $stmtUsuarios->close();
}

$query_contexto = http_build_query([
    "q" => $q,
    "rol" => $rol_filtro,
    "page" => $pagina
]);

$mensaje_exito = (isset($_GET["ok"]) && $_GET["ok"] === "actualizado") ? "Usuario actualizado correctamente." : "";
$mensaje_error = isset($_GET["error"]) ? "No se pudo completar la operacion." : "";

function build_page_url(int $page, string $q, string $rol): string
{
    $params = ["page" => $page];
    if ($q !== "") {
        $params["q"] = $q;
    }
    if ($rol !== "todos") {
        $params["rol"] = $rol;
    }
    return "admin_usuarios.php?" . http_build_query($params);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin usuarios - WhiteGym</title>
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<?php include __DIR__ . "/includes/topbar.php"; ?>

<div class="dashboard-layout">
    <?php include __DIR__ . "/includes/sidebar_admin.php"; ?>

    <main class="dashboard-main">
        <div class="page-shell">
            <div class="page-header">
                <div>
                    <h2>Usuarios</h2>
                </div>
            </div>

            <?php if ($mensaje_exito !== ""): ?>
                <p class="notice-ok"><?php echo htmlspecialchars($mensaje_exito); ?></p>
            <?php endif; ?>

            <?php if ($mensaje_error !== ""): ?>
                <p class="notice-error"><?php echo htmlspecialchars($mensaje_error); ?></p>
            <?php endif; ?>

            <section class="card">
                <form method="GET" class="toolbar">
                    <div class="field">
                        <label for="q">Buscar</label>
                        <input type="text" id="q" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Nombre o email">
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

            <section class="card">
                <?php if (empty($usuarios)): ?>
                    <div class="empty-state">No hay usuarios para los filtros seleccionados.</div>
                <?php else: ?>
                    <div class="table-wrap">
                        <table class="admin-users-table">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <?php
                                    $es_mi_cuenta = (int) $usuario["id_usuario"] === $id_admin_actual;
                                    $estado = ((int) ($usuario["activo"] ?? 0)) === 1 ? "Activo" : "Inactivo";
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="user-identity">
                                                <strong><?php echo htmlspecialchars($usuario["nombre"] ?? ""); ?></strong>
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
                                                data-email="<?php echo htmlspecialchars($usuario["email"] ?? "", ENT_QUOTES, 'UTF-8'); ?>"
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

            <section class="pagination">
                <span class="muted">Mostrando <?php echo count($usuarios); ?> de <?php echo $total_usuarios; ?> usuarios</span>
                <div class="pagination-links">
                    <?php if ($pagina > 1): ?>
                        <a href="<?php echo htmlspecialchars(build_page_url($pagina - 1, $q, $rol_filtro)); ?>">Anterior</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <?php if ($i === $pagina): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars(build_page_url($i, $q, $rol_filtro)); ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <?php if ($pagina < $total_paginas): ?>
                        <a href="<?php echo htmlspecialchars(build_page_url($pagina + 1, $q, $rol_filtro)); ?>">Siguiente</a>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>
</div>

<div class="modal-backdrop" id="user-modal-backdrop">
    <div class="modal">
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
                    <strong id="modal-nombre"></strong>
                    <span id="modal-email"></span>
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

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" id="close-user-modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
const userModalBackdrop = document.getElementById("user-modal-backdrop");
const userModalTriggers = document.querySelectorAll(".js-open-user-modal");
const closeUserModal = document.getElementById("close-user-modal");

function cerrarModalUsuario() {
    userModalBackdrop.classList.remove("is-open");
}

userModalTriggers.forEach((button) => {
    button.addEventListener("click", () => {
        document.getElementById("modal-id-usuario").value = button.dataset.id || "";
        document.getElementById("modal-nombre").textContent = button.dataset.nombre || "";
        document.getElementById("modal-email").textContent = button.dataset.email || "";
        document.getElementById("modal-rol").value = button.dataset.rol || "usuario";
        document.getElementById("modal-activo").value = button.dataset.activo || "1";
        userModalBackdrop.classList.add("is-open");
    });
});

closeUserModal.addEventListener("click", cerrarModalUsuario);
userModalBackdrop.addEventListener("click", (event) => {
    if (event.target === userModalBackdrop) {
        cerrarModalUsuario();
    }
});

document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
        cerrarModalUsuario();
    }
});
</script>

</body>
</html>
