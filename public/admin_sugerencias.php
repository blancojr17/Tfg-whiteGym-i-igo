<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["email"]) || $_SESSION["rol"] !== "admin") {
    header("Location: login.php");
    exit;
}

$estado_filtro = (string) ($_GET["estado"] ?? "todos");
$estados_validos = ["todos", "pendiente", "leido"];
$estado_filtro = in_array($estado_filtro, $estados_validos, true) ? $estado_filtro : "todos";
$pagina = max(1, (int) ($_GET["page"] ?? 1));
$por_pagina = 10;
$offset = ($pagina - 1) * $por_pagina;

$where_sql = "";
$types = "";
$params = [];

if ($estado_filtro !== "todos") {
    $where_sql = " WHERE estado = ?";
    $types = "s";
    $params[] = $estado_filtro;
}

$total_sugerencias = 0;
$sugerencias = [];
$error_datos = false;

$sqlTotal = "SELECT COUNT(*) AS total FROM sugerencias" . $where_sql;
$stmtTotal = $conexion->prepare($sqlTotal);
if ($stmtTotal) {
    if ($estado_filtro !== "todos") {
        $stmtTotal->bind_param("s", $estado_filtro);
    }
    $stmtTotal->execute();
    $resultadoTotal = $stmtTotal->get_result();
    $filaTotal = $resultadoTotal ? $resultadoTotal->fetch_assoc() : null;
    $total_sugerencias = (int) ($filaTotal["total"] ?? 0);
    $stmtTotal->close();
} else {
    $error_datos = true;
}

$total_paginas = max(1, (int) ceil($total_sugerencias / $por_pagina));
if ($pagina > $total_paginas) {
    $pagina = $total_paginas;
    $offset = ($pagina - 1) * $por_pagina;
}

$sqlSugerencias = "SELECT id_sugerencia, nombre, email, mensaje, fecha, estado
                   FROM sugerencias" . $where_sql . "
                   ORDER BY fecha DESC, id_sugerencia DESC
                   LIMIT ?, ?";
$stmtSugerencias = $conexion->prepare($sqlSugerencias);
if ($stmtSugerencias) {
    if ($estado_filtro === "todos") {
        $stmtSugerencias->bind_param("ii", $offset, $por_pagina);
    } else {
        $stmtSugerencias->bind_param("sii", $estado_filtro, $offset, $por_pagina);
    }
    $stmtSugerencias->execute();
    $resultadoSugerencias = $stmtSugerencias->get_result();
    while ($fila = $resultadoSugerencias->fetch_assoc()) {
        $sugerencias[] = $fila;
    }
    $stmtSugerencias->close();
} else {
    $error_datos = true;
}

$query_contexto = http_build_query([
    "estado" => $estado_filtro,
    "page" => $pagina
]);

$mensaje_exito = (isset($_GET["ok"]) && $_GET["ok"] === "actualizada") ? "Sugerencia actualizada correctamente." : "";
$mensaje_error = isset($_GET["error"]) ? "No se pudo completar la operacion." : "";

function build_sugerencias_page_url(int $page, string $estado): string
{
    $params = ["page" => $page];
    if ($estado !== "todos") {
        $params["estado"] = $estado;
    }
    return "admin_sugerencias.php?" . http_build_query($params);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin sugerencias - WhiteGym</title>
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
                    <h2>Sugerencias</h2>
                </div>
            </div>

            <?php if ($mensaje_exito !== ""): ?>
                <p class="notice-ok"><?php echo htmlspecialchars($mensaje_exito); ?></p>
            <?php endif; ?>

            <?php if ($mensaje_error !== ""): ?>
                <p class="notice-error"><?php echo htmlspecialchars($mensaje_error); ?></p>
            <?php endif; ?>

            <?php if ($error_datos): ?>
                <p class="notice-error">No se pudieron cargar las sugerencias. Revisa si la tabla ya existe en la base de datos.</p>
            <?php endif; ?>

            <section class="card">
                <form method="GET" class="toolbar">
                    <div class="field">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado">
                            <option value="todos" <?php echo $estado_filtro === "todos" ? "selected" : ""; ?>>Todos</option>
                            <option value="pendiente" <?php echo $estado_filtro === "pendiente" ? "selected" : ""; ?>>Pendiente</option>
                            <option value="leido" <?php echo $estado_filtro === "leido" ? "selected" : ""; ?>>Leido</option>
                        </select>
                    </div>
                    <div class="inline-actions">
                        <button type="submit">Filtrar</button>
                        <a href="admin_sugerencias.php" class="btn btn-secondary">Limpiar</a>
                    </div>
                </form>
            </section>

            <section class="card">
                <?php if (empty($sugerencias)): ?>
                    <div class="empty-state">No hay sugerencias para los filtros seleccionados.</div>
                <?php else: ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Mensaje</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sugerencias as $sugerencia): ?>
                                    <?php $estado = (string) ($sugerencia["estado"] ?? "pendiente"); ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($sugerencia["nombre"] ?? ""); ?></td>
                                        <td><?php echo htmlspecialchars($sugerencia["email"] ?? ""); ?></td>
                                        <td class="suggestion-message"><?php echo nl2br(htmlspecialchars($sugerencia["mensaje"] ?? "")); ?></td>
                                        <td><?php echo htmlspecialchars($sugerencia["fecha"] ?? ""); ?></td>
                                        <td>
                                            <span class="status-pill <?php echo $estado === "pendiente" ? "status-accent" : "status-muted"; ?>">
                                                <?php echo htmlspecialchars(ucfirst($estado)); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($estado === "pendiente"): ?>
                                                <form action="../app/controllers/gestionar_sugerencia.php" method="POST" class="inline-actions">
                                                    <input type="hidden" name="accion" value="marcar_leido">
                                                    <input type="hidden" name="id_sugerencia" value="<?php echo (int) ($sugerencia["id_sugerencia"] ?? 0); ?>">
                                                    <input type="hidden" name="redirect_query" value="<?php echo htmlspecialchars($query_contexto, ENT_QUOTES, 'UTF-8'); ?>">
                                                    <button type="submit" class="btn btn-secondary">Marcar leido</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="muted">Revisada</span>
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
                <span class="muted">Mostrando <?php echo count($sugerencias); ?> de <?php echo $total_sugerencias; ?> sugerencias</span>
                <div class="pagination-links">
                    <?php if ($pagina > 1): ?>
                        <a href="<?php echo htmlspecialchars(build_sugerencias_page_url($pagina - 1, $estado_filtro)); ?>">Anterior</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <?php if ($i === $pagina): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars(build_sugerencias_page_url($i, $estado_filtro)); ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <?php if ($pagina < $total_paginas): ?>
                        <a href="<?php echo htmlspecialchars(build_sugerencias_page_url($pagina + 1, $estado_filtro)); ?>">Siguiente</a>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>
</div>

</body>
</html>
