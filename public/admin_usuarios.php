<?php
// listado y gestion de usuarios
// inicio de sesion
session_start();
// carga de archivos necesarios
require_once __DIR__ . "/../config/conexion.php";

// proteccion de acceso segun rol
if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["email"]) || $_SESSION["rol"] !== "admin") {
// redireccion final
    header("Location: login.php");
    exit;
}

$id_admin_actual = (int) $_SESSION["id_usuario"];
// recogida de parametros de la url
$q = trim((string) ($_GET["q"] ?? ""));
// recogida de parametros de la url
$rol_filtro = (string) ($_GET["rol"] ?? "todos");
$roles_validos = ["todos", "usuario", "entrenador", "admin"];
$rol_filtro = in_array($rol_filtro, $roles_validos, true) ? $rol_filtro : "todos";
// recogida de parametros de la url
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
    $where[] = "(u.nombre LIKE ? OR u.apellidos LIKE ? OR u.email LIKE ?)";
    $types .= "sss";
    $like = "%" . $q . "%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if ($rol_filtro !== "todos") {
    $where[] = "u.rol = ?";
    $types .= "s";
    $params[] = $rol_filtro;
}

$where_sql = $where ? " WHERE " . implode(" AND ", $where) : "";
$total_usuarios = 0;
$usuarios = [];

// consulta sql
$sqlTotal = "SELECT COUNT(*) AS total FROM usuarios u" . $where_sql;
// preparacion de la consulta
$stmtTotal = $conexion->prepare($sqlTotal);
// comprobacion de la consulta
if ($stmtTotal) {
    bind_dynamic_params($stmtTotal, $types, $params);
// ejecucion de la consulta
    $stmtTotal->execute();
    $resultadoTotal = $stmtTotal->get_result();
// lectura de resultados
    $filaTotal = $resultadoTotal ? $resultadoTotal->fetch_assoc() : null;
    $total_usuarios = (int) ($filaTotal["total"] ?? 0);
    $stmtTotal->close();
}

$total_paginas = max(1, (int) ceil($total_usuarios / $por_pagina));
if ($pagina > $total_paginas) {
    $pagina = $total_paginas;
    $offset = ($pagina - 1) * $por_pagina;
}

// consulta sql
$sqlUsuarios = "SELECT u.id_usuario,
                       u.nombre,
                       u.apellidos,
                       u.email,
                       u.telefono,
                       u.sexo,
                       u.ciudad,
                       u.fecha_nacimiento,
                       u.fecha_registro,
                       u.rol,
                       u.activo,
                       (
                           SELECT p.nombre
                           FROM usuarios_planes up
                           INNER JOIN planes p ON up.id_plan = p.id_plan
                           WHERE up.id_usuario = u.id_usuario
                             AND up.fecha_fin >= CURDATE()
                             AND (
                                 p.tipo = 'suscripcion'
                                 OR (p.tipo = 'bono' AND up.usos_restantes > 0)
                             )
                           ORDER BY up.fecha_inicio DESC, up.id_usuario_plan DESC
                           LIMIT 1
                       ) AS plan_activo
                FROM usuarios u
                " . $where_sql . "
                ORDER BY u.id_usuario DESC
                LIMIT ?, ?";
// preparacion de la consulta
$stmtUsuarios = $conexion->prepare($sqlUsuarios);
// comprobacion de la consulta
if ($stmtUsuarios) {
    $types_data = $types . "ii";
    $params_data = $params;
    $params_data[] = $offset;
    $params_data[] = $por_pagina;
    bind_dynamic_params($stmtUsuarios, $types_data, $params_data);
// ejecucion de la consulta
    $stmtUsuarios->execute();
    $resultadoUsuarios = $stmtUsuarios->get_result();
// lectura de resultados
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

// recogida de parametros de la url
$mensaje_exito = (isset($_GET["ok"]) && $_GET["ok"] === "actualizado") ? "Usuario actualizado correctamente." : "";
// recogida de parametros de la url
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

<!-- estructura principal del panel -->
<div class="dashboard-layout">
    <?php include __DIR__ . "/includes/sidebar_admin.php"; ?>

<!-- contenido principal -->
    <main class="dashboard-main">
        <div class="page-shell">
<!-- cabecera del contenido -->
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

            <?php include __DIR__ . "/includes/admin/filtros_usuarios.php"; ?>

            <?php include __DIR__ . "/includes/admin/tabla_usuarios.php"; ?>

<!-- seccion de contenido -->
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

<?php include __DIR__ . "/includes/admin/modal_usuario.php"; ?>

<script>
const userModalBackdrop = document.getElementById("user-modal-backdrop");
const userModalTriggers = document.querySelectorAll(".js-open-user-modal");
const closeUserModal = document.getElementById("close-user-modal");

function cerrarModalUsuario() {
    userModalBackdrop.classList.remove("is-open");
}

userModalTriggers.forEach((button) => {
    button.addEventListener("click", () => {
        const nombre = button.dataset.nombre || "";
        const apellidos = button.dataset.apellidos || "";
        document.getElementById("modal-id-usuario").value = button.dataset.id || "";
        document.getElementById("modal-nombre-completo").textContent = (nombre + " " + apellidos).trim();
        document.getElementById("modal-nombre").value = nombre;
        document.getElementById("modal-apellidos").value = apellidos;
        document.getElementById("modal-email").textContent = button.dataset.email || "";
        document.getElementById("modal-email-input").value = button.dataset.email || "";
        document.getElementById("modal-telefono").value = button.dataset.telefono || "";
        document.getElementById("modal-sexo").value = button.dataset.sexo || "";
        document.getElementById("modal-ciudad").value = button.dataset.ciudad || "";
        document.getElementById("modal-fecha-nacimiento").value = button.dataset.fechaNacimiento || "";
        document.getElementById("modal-fecha-registro").textContent = button.dataset.fechaRegistro || "-";
        document.getElementById("modal-plan-activo").textContent = button.dataset.planActivo || "Sin plan";
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

