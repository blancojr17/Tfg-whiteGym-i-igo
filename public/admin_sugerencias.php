<?php
// revision de sugerencias en admin
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

// recogida de parametros de la url
$estado_filtro = (string) ($_GET["estado"] ?? "todos");
$estados_validos = ["todos", "pendiente", "leido"];
// mensajes segun el resultado
$estado_filtro = in_array($estado_filtro, $estados_validos, true) ? $estado_filtro : "todos";
// recogida de parametros de la url
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
// mensajes segun el resultado
$error_datos = false;

// consulta sql
$sqlTotal = "SELECT COUNT(*) AS total FROM sugerencias" . $where_sql;
// preparacion de la consulta
$stmtTotal = $conexion->prepare($sqlTotal);
// comprobacion de la consulta
if ($stmtTotal) {
    if ($estado_filtro !== "todos") {
        $stmtTotal->bind_param("s", $estado_filtro);
    }
// ejecucion de la consulta
    $stmtTotal->execute();
    $resultadoTotal = $stmtTotal->get_result();
// lectura de resultados
    $filaTotal = $resultadoTotal ? $resultadoTotal->fetch_assoc() : null;
    $total_sugerencias = (int) ($filaTotal["total"] ?? 0);
    $stmtTotal->close();
} else {
// mensajes segun el resultado
    $error_datos = true;
}

$total_paginas = max(1, (int) ceil($total_sugerencias / $por_pagina));
if ($pagina > $total_paginas) {
    $pagina = $total_paginas;
    $offset = ($pagina - 1) * $por_pagina;
}

// consulta sql
$sqlSugerencias = "SELECT id_sugerencia, nombre, email, mensaje, fecha, estado
                   FROM sugerencias" . $where_sql . "
                   ORDER BY fecha DESC, id_sugerencia DESC
                   LIMIT ?, ?";
// preparacion de la consulta
$stmtSugerencias = $conexion->prepare($sqlSugerencias);
// comprobacion de la consulta
if ($stmtSugerencias) {
    if ($estado_filtro === "todos") {
        $stmtSugerencias->bind_param("ii", $offset, $por_pagina);
    } else {
        $stmtSugerencias->bind_param("sii", $estado_filtro, $offset, $por_pagina);
    }
// ejecucion de la consulta
    $stmtSugerencias->execute();
    $resultadoSugerencias = $stmtSugerencias->get_result();
// lectura de resultados
    while ($fila = $resultadoSugerencias->fetch_assoc()) {
        $sugerencias[] = $fila;
    }
    $stmtSugerencias->close();
} else {
// mensajes segun el resultado
    $error_datos = true;
}

$query_contexto = http_build_query([
    "estado" => $estado_filtro,
    "page" => $pagina
]);

// recogida de parametros de la url
$mensaje_exito = (isset($_GET["ok"]) && $_GET["ok"] === "actualizada") ? "Sugerencia actualizada correctamente." : "";
// recogida de parametros de la url
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

<!-- estructura principal del panel -->
<div class="dashboard-layout">
    <?php include __DIR__ . "/includes/sidebar_admin.php"; ?>

<!-- contenido principal -->
    <main class="dashboard-main">
        <div class="page-shell">
<!-- cabecera del contenido -->
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

<!-- bloque principal de contenido -->
            <section class="card">
<!-- formulario principal -->
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

<!-- bloque principal de contenido -->
            <section class="card">
                <?php if (empty($sugerencias)): ?>
                    <div class="empty-state">No hay sugerencias para los filtros seleccionados.</div>
                <?php else: ?>
<!-- contenedor de la tabla -->
                    <div class="table-wrap">
<!-- tabla de datos -->
                        <table>
<!-- cabecera de la tabla -->
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
<!-- contenido de la tabla -->
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
<!-- formulario principal -->
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

<!-- seccion de contenido -->
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

