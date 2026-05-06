<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["email"]) || $_SESSION["rol"] !== "admin") {
    header("Location: login.php");
    exit;
}

$id_admin_actual = (int) $_SESSION["id_usuario"];

// Asegura columna activo en planes para activar/desactivar sin borrar.
$tiene_activo_planes = false;
$resColumna = $conexion->query("SHOW COLUMNS FROM planes LIKE 'activo'");
if ($resColumna && $resColumna->num_rows > 0) {
    $tiene_activo_planes = true;
} else {
    $conexion->query("ALTER TABLE planes ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1");
    $resColumna2 = $conexion->query("SHOW COLUMNS FROM planes LIKE 'activo'");
    $tiene_activo_planes = $resColumna2 && $resColumna2->num_rows > 0;
}

$total_usuarios = 0;
$total_entrenadores = 0;
$total_clases = 0;
$total_reservas = 0;
$usuarios = [];
$planes = [];
$clases_globales = [];
$error_datos = false;

$sqlTotales = "SELECT
                (SELECT COUNT(*) FROM usuarios WHERE rol = 'usuario') AS total_usuarios,
                (SELECT COUNT(*) FROM usuarios WHERE rol = 'entrenador') AS total_entrenadores,
                (SELECT COUNT(*) FROM clases) AS total_clases,
                (SELECT COUNT(*) FROM usuarios_clases) AS total_reservas";
$stmtTotales = $conexion->prepare($sqlTotales);

if ($stmtTotales) {
    $stmtTotales->execute();
    $resultadoTotales = $stmtTotales->get_result();
    $filaTotales = $resultadoTotales ? $resultadoTotales->fetch_assoc() : null;
    $stmtTotales->close();

    if ($filaTotales) {
        $total_usuarios = (int) ($filaTotales["total_usuarios"] ?? 0);
        $total_entrenadores = (int) ($filaTotales["total_entrenadores"] ?? 0);
        $total_clases = (int) ($filaTotales["total_clases"] ?? 0);
        $total_reservas = (int) ($filaTotales["total_reservas"] ?? 0);
    }
} else {
    $error_datos = true;
}

$sqlUsuarios = "SELECT id_usuario, nombre, email, rol, activo
                FROM usuarios
                ORDER BY id_usuario ASC";
$stmtUsuarios = $conexion->prepare($sqlUsuarios);

if ($stmtUsuarios) {
    $stmtUsuarios->execute();
    $resultadoUsuarios = $stmtUsuarios->get_result();

    while ($fila = $resultadoUsuarios->fetch_assoc()) {
        $usuarios[] = $fila;
    }

    $stmtUsuarios->close();
} else {
    $error_datos = true;
}

if ($tiene_activo_planes) {
    $sqlPlanes = "SELECT id_plan, nombre, precio, tipo, duracion_dias, usos, activo
                  FROM planes
                  ORDER BY id_plan ASC";
    $stmtPlanes = $conexion->prepare($sqlPlanes);

    if ($stmtPlanes) {
        $stmtPlanes->execute();
        $resultadoPlanes = $stmtPlanes->get_result();

        while ($fila = $resultadoPlanes->fetch_assoc()) {
            $planes[] = $fila;
        }

        $stmtPlanes->close();
    } else {
        $error_datos = true;
    }
} else {
    $error_datos = true;
}

$sqlClasesGlobales = "SELECT c.id_clase,
                             c.nombre,
                             c.descripcion,
                             c.fecha,
                             c.capacidad,
                             CONCAT(COALESCE(u.nombre, ''), ' ', COALESCE(u.apellidos, '')) AS entrenador_nombre,
                             COUNT(uc.id_usuario_clase) AS total_apuntados
                      FROM clases c
                      LEFT JOIN usuarios u ON c.id_entrenador = u.id_usuario
                      LEFT JOIN usuarios_clases uc ON c.id_clase = uc.id_clase
                      GROUP BY c.id_clase, c.nombre, c.descripcion, c.fecha, c.capacidad, u.nombre, u.apellidos
                      ORDER BY c.fecha ASC";
$stmtClasesGlobales = $conexion->prepare($sqlClasesGlobales);

if ($stmtClasesGlobales) {
    $stmtClasesGlobales->execute();
    $resultadoClasesGlobales = $stmtClasesGlobales->get_result();

    while ($fila = $resultadoClasesGlobales->fetch_assoc()) {
        $clases_globales[] = $fila;
    }

    $stmtClasesGlobales->close();
} else {
    $error_datos = true;
}

$mensaje_exito = "";
$mensaje_error = "";

if (isset($_GET["ok"])) {
    if ($_GET["ok"] === "actualizado" || $_GET["ok"] === "actualizado_usuario") {
        $mensaje_exito = "Usuario actualizado correctamente.";
    } elseif ($_GET["ok"] === "creado_plan") {
        $mensaje_exito = "Plan creado correctamente.";
    } elseif ($_GET["ok"] === "actualizado_plan") {
        $mensaje_exito = "Plan actualizado correctamente.";
    } elseif ($_GET["ok"] === "admin_clase_actualizada") {
        $mensaje_exito = "Clase actualizada correctamente.";
    } elseif ($_GET["ok"] === "admin_clase_eliminada") {
        $mensaje_exito = "Clase eliminada correctamente.";
    }
}

if (isset($_GET["error"])) {
    $tipo_error = $_GET["error"];

    if ($tipo_error === "id") {
        $mensaje_error = "El usuario no es válido.";
    } elseif ($tipo_error === "rol") {
        $mensaje_error = "El rol no es válido.";
    } elseif ($tipo_error === "activo") {
        $mensaje_error = "El estado activo no es válido.";
    } elseif ($tipo_error === "autoproteccion") {
        $mensaje_error = "No puedes desactivarte ni quitarte el rol de admin.";
    } elseif ($tipo_error === "no_existe") {
        $mensaje_error = "El usuario no existe.";
    } elseif ($tipo_error === "plan_nombre") {
        $mensaje_error = "El nombre del plan es obligatorio.";
    } elseif ($tipo_error === "plan_precio") {
        $mensaje_error = "El precio del plan no es válido.";
    } elseif ($tipo_error === "plan_tipo") {
        $mensaje_error = "El tipo de plan no es válido.";
    } elseif ($tipo_error === "plan_duracion") {
        $mensaje_error = "La duración del plan debe ser mayor que 0 para suscripción.";
    } elseif ($tipo_error === "plan_usos") {
        $mensaje_error = "Los usos del plan deben ser mayores que 0 para bono.";
    } elseif ($tipo_error === "plan_id") {
        $mensaje_error = "El plan no es válido.";
    } elseif ($tipo_error === "plan_no_existe") {
        $mensaje_error = "El plan no existe.";
    } elseif ($tipo_error === "plan_activo_columna") {
        $mensaje_error = "No se pudo preparar la columna activo en planes.";
    } elseif ($tipo_error === "admin_clase_id") {
        $mensaje_error = "La clase no es válida.";
    } elseif ($tipo_error === "admin_clase_nombre") {
        $mensaje_error = "El nombre de la clase es obligatorio.";
    } elseif ($tipo_error === "admin_clase_fecha") {
        $mensaje_error = "La fecha de la clase es obligatoria.";
    } elseif ($tipo_error === "admin_clase_capacidad") {
        $mensaje_error = "La capacidad de la clase debe ser mayor que 0.";
    } elseif ($tipo_error === "admin_clase_no_existe") {
        $mensaje_error = "La clase no existe.";
    } else {
        $mensaje_error = "No se pudo completar la operación.";
    }
}

// Base preparada para futuras ampliaciones del panel admin.
$admin_modulos_futuros = [
    "gestion_clases_completa" => true,
    "gestion_planes_completa" => true,
    "dashboard_avanzado" => false,
    "graficos" => false,
    "estadisticas" => false,
    "logs" => false,
    "imagenes_planes" => false,
    "promociones" => false,
    "descuentos" => false,
    "pagos_online" => false,
    "suscripciones_automaticas" => false,
    "cancelar_clases_sin_borrar" => false,
    "mover_clases" => false,
    "sustituir_entrenador" => false,
    "clases_recurrentes" => false,
    "estadisticas_por_clase" => false
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - WhiteGym</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<header id="cabecera-admin">
    <h1>Panel de Administración</h1>
    <div id="usuario-admin">
        <span><?php echo htmlspecialchars($_SESSION["email"]); ?></span>
        <a href="../app/controllers/logout.php" id="btn-cerrar-sesion">Cerrar sesión</a>
    </div>
</header>

<main id="contenido-admin">

    <?php if ($mensaje_exito !== ""): ?>
        <p><?php echo htmlspecialchars($mensaje_exito); ?></p>
    <?php endif; ?>

    <?php if ($mensaje_error !== ""): ?>
        <p><?php echo htmlspecialchars($mensaje_error); ?></p>
    <?php endif; ?>

    <?php if ($error_datos): ?>
        <p>No se pudieron cargar todos los datos del panel.</p>
    <?php endif; ?>

    <section id="seccion-resumen">
        <h2>Resumen</h2>
        <p><strong>Total usuarios:</strong> <?php echo $total_usuarios; ?></p>
        <p><strong>Total entrenadores:</strong> <?php echo $total_entrenadores; ?></p>
        <p><strong>Total clases:</strong> <?php echo $total_clases; ?></p>
        <p><strong>Total reservas:</strong> <?php echo $total_reservas; ?></p>
    </section>

    <section id="seccion-usuarios">
        <h2>Usuarios</h2>

        <?php if (empty($usuarios)): ?>
            <p>No hay usuarios registrados.</p>
        <?php else: ?>
            <table id="tabla-usuarios">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Activo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario["nombre"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($usuario["email"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($usuario["rol"] ?? ""); ?></td>
                            <td><?php echo ((int) ($usuario["activo"] ?? 0)) === 1 ? "Sí" : "No"; ?></td>
                            <td>
                                <form action="../app/controllers/gestionar_usuario.php" method="POST">
                                    <input type="hidden" name="id_usuario" value="<?php echo (int) ($usuario["id_usuario"] ?? 0); ?>">

                                    <label>
                                        Rol
                                        <select name="rol">
                                            <option value="usuario" <?php echo ($usuario["rol"] === "usuario") ? "selected" : ""; ?>>usuario</option>
                                            <option value="entrenador" <?php echo ($usuario["rol"] === "entrenador") ? "selected" : ""; ?>>entrenador</option>
                                            <option value="admin" <?php echo ($usuario["rol"] === "admin") ? "selected" : ""; ?>>admin</option>
                                        </select>
                                    </label>

                                    <label>
                                        Activo
                                        <select name="activo">
                                            <option value="1" <?php echo ((int) $usuario["activo"] === 1) ? "selected" : ""; ?>>Sí</option>
                                            <option value="0" <?php echo ((int) $usuario["activo"] === 0) ? "selected" : ""; ?>>No</option>
                                        </select>
                                    </label>

                                    <button type="submit">Actualizar</button>
                                </form>

                                <?php if ((int) $usuario["id_usuario"] === $id_admin_actual): ?>
                                    <small>Tu usuario admin actual</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <section id="seccion-planes">
        <h2>Gestión de planes</h2>

        <h3>Crear plan</h3>
        <form action="../app/controllers/gestionar_plan.php" method="POST">
            <input type="hidden" name="accion" value="crear">

            <label>
                Nombre
                <input type="text" name="nombre" required>
            </label>

            <label>
                Precio
                <input type="number" name="precio" min="0" step="0.01" required>
            </label>

            <label>
                Tipo
                <select name="tipo" required>
                    <option value="suscripcion">suscripcion</option>
                    <option value="bono">bono</option>
                </select>
            </label>

            <label>
                Duración (días)
                <input type="number" name="duracion_dias" min="0" step="1" value="0" required>
            </label>

            <label>
                Usos
                <input type="number" name="usos" min="0" step="1" value="0" required>
            </label>

            <button type="submit">Crear plan</button>
        </form>

        <h3>Planes existentes</h3>
        <?php if (empty($planes)): ?>
            <p>No hay planes registrados.</p>
        <?php else: ?>
            <table id="tabla-planes">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Tipo</th>
                        <th>Duración</th>
                        <th>Usos</th>
                        <th>Activo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($planes as $plan): ?>
                        <tr>
                            <form action="../app/controllers/gestionar_plan.php" method="POST">
                                <td>
                                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($plan["nombre"] ?? ""); ?>" required>
                                </td>
                                <td>
                                    <input type="number" name="precio" min="0" step="0.01" value="<?php echo htmlspecialchars((string) ($plan["precio"] ?? "0")); ?>" required>
                                </td>
                                <td>
                                    <select name="tipo" required>
                                        <option value="suscripcion" <?php echo (($plan["tipo"] ?? "") === "suscripcion") ? "selected" : ""; ?>>suscripcion</option>
                                        <option value="bono" <?php echo (($plan["tipo"] ?? "") === "bono") ? "selected" : ""; ?>>bono</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="duracion_dias" min="0" step="1" value="<?php echo (int) ($plan["duracion_dias"] ?? 0); ?>" required>
                                </td>
                                <td>
                                    <input type="number" name="usos" min="0" step="1" value="<?php echo (int) ($plan["usos"] ?? 0); ?>" required>
                                </td>
                                <td>
                                    <select name="activo" required>
                                        <option value="1" <?php echo ((int) ($plan["activo"] ?? 1) === 1) ? "selected" : ""; ?>>Sí</option>
                                        <option value="0" <?php echo ((int) ($plan["activo"] ?? 1) === 0) ? "selected" : ""; ?>>No</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="hidden" name="accion" value="editar">
                                    <input type="hidden" name="id_plan" value="<?php echo (int) ($plan["id_plan"] ?? 0); ?>">
                                    <button type="submit">Actualizar plan</button>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <section id="seccion-clases-global-admin">
        <h2>Gestión global de clases</h2>

        <?php if (empty($clases_globales)): ?>
            <p>No hay clases registradas.</p>
        <?php else: ?>
            <table id="tabla-clases-globales">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>Capacidad</th>
                        <th>Entrenador asignado</th>
                        <th>Usuarios apuntados</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clases_globales as $clase): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($clase["nombre"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($clase["descripcion"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($clase["fecha"] ?? ""); ?></td>
                            <td><?php echo (int) ($clase["capacidad"] ?? 0); ?></td>
                            <td><?php echo htmlspecialchars(trim((string) ($clase["entrenador_nombre"] ?? "")) !== "" ? $clase["entrenador_nombre"] : "Sin asignar"); ?></td>
                            <td><?php echo (int) ($clase["total_apuntados"] ?? 0); ?></td>
                            <td>
                                <a href="admin_editar_clase.php?id_clase=<?php echo (int) ($clase["id_clase"] ?? 0); ?>">Editar</a>
                                <form action="../app/controllers/admin_eliminar_clase.php" method="POST">
                                    <input type="hidden" name="id_clase" value="<?php echo (int) ($clase["id_clase"] ?? 0); ?>">
                                    <button type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

</main>

</body>
</html>
