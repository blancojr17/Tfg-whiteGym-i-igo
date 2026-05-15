<?php
// seguimiento corporal del usuario
// inicio de sesion
session_start();
// carga de archivos necesarios
require_once __DIR__ . "/../config/conexion.php";
// carga de archivos necesarios
require_once __DIR__ . "/../app/helpers/fitness_helper.php";

// proteccion de acceso segun rol
if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["email"]) || $_SESSION["rol"] !== "usuario") {
// redireccion final
    header("Location: login.php");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
$actividad_opciones = fitness_activity_options();
$intensidades = fitness_intensity_options();
$objetivos = fitness_goal_options();

$datos = [
    "peso" => 0.0,
    "altura" => 0.0,
    "edad" => 0,
    "sexo" => "hombre",
    "actividad_semanal" => 3,
    "intensidad" => "media",
    "objetivo" => "mantener_peso",
    "calorias_recomendadas" => 0,
    "agua_litros" => 0.0,
    "proteinas" => 0.0,
    "carbohidratos" => 0.0,
    "grasas" => 0.0,
    "imc" => 0.0
];
$historial = [];
$fecha_ultima_medida = null;
// mensajes segun el resultado
$mensaje_ok = "";
// mensajes segun el resultado
$mensaje_error = "";

// preparacion de la consulta
$stmtUltima = $conexion->prepare("SELECT peso, altura, edad, sexo, actividad_semanal, intensidad, objetivo, calorias_recomendadas, agua_litros, proteinas, carbohidratos, grasas, imc, fecha_registro
    FROM medidas_usuario
    WHERE id_usuario = ?
    ORDER BY fecha_registro DESC, id_medida DESC
    LIMIT 1");
// comprobacion de la consulta
if ($stmtUltima) {
    $stmtUltima->bind_param("i", $id_usuario);
// ejecucion de la consulta
    $stmtUltima->execute();
    $resUltima = $stmtUltima->get_result();
// lectura de resultados
    $filaUltima = $resUltima ? $resUltima->fetch_assoc() : null;
    $stmtUltima->close();

    if ($filaUltima) {
        $datos = array_merge($datos, $filaUltima);
        $fecha_ultima_medida = $filaUltima["fecha_registro"] ?? null;
    }
}

// validacion del metodo recibido
if ($_SERVER["REQUEST_METHOD"] === "POST") {
// recogida de datos del formulario
    $datos["peso"] = (float) ($_POST["peso"] ?? 0);
// recogida de datos del formulario
    $datos["altura"] = (float) ($_POST["altura"] ?? 0);
// recogida de datos del formulario
    $datos["edad"] = (int) ($_POST["edad"] ?? 0);
// recogida de datos del formulario
    $datos["sexo"] = (string) ($_POST["sexo"] ?? "hombre");
// recogida de datos del formulario
    $datos["actividad_semanal"] = (int) ($_POST["actividad_semanal"] ?? 0);
// recogida de datos del formulario
    $datos["intensidad"] = (string) ($_POST["intensidad"] ?? "media");
// recogida de datos del formulario
    $datos["objetivo"] = (string) ($_POST["objetivo"] ?? "mantener_peso");

    $datos_validos = $datos["peso"] > 0
        && $datos["altura"] > 0
        && $datos["edad"] > 0
        && in_array($datos["sexo"], ["hombre", "mujer"], true)
        && in_array($datos["actividad_semanal"], $actividad_opciones, true)
        && array_key_exists($datos["intensidad"], $intensidades)
        && array_key_exists($datos["objetivo"], $objetivos);

    if (!$datos_validos) {
// mensajes segun el resultado
        $mensaje_error = "Revisa los datos introducidos antes de guardar tu seguimiento.";
    } else {
        $calculados = calculate_fitness($datos);
        $datos = array_merge($datos, $calculados);

// preparacion de la consulta
        $stmtInsert = $conexion->prepare("INSERT INTO medidas_usuario
            (id_usuario, peso, altura, edad, sexo, actividad_semanal, intensidad, objetivo, calorias_recomendadas, agua_litros, proteinas, carbohidratos, grasas, imc)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
// comprobacion de la consulta
        if ($stmtInsert) {
            $stmtInsert->bind_param(
                "iddisissiddddd",
                $id_usuario,
                $datos["peso"],
                $datos["altura"],
                $datos["edad"],
                $datos["sexo"],
                $datos["actividad_semanal"],
                $datos["intensidad"],
                $datos["objetivo"],
                $datos["calorias_recomendadas"],
                $datos["agua_litros"],
                $datos["proteinas"],
                $datos["carbohidratos"],
                $datos["grasas"],
                $datos["imc"]
            );

// comprobacion de la consulta
            if ($stmtInsert->execute()) {
// mensajes segun el resultado
                $mensaje_ok = "Seguimiento actualizado correctamente.";
                $fecha_ultima_medida = date("Y-m-d H:i:s");
            } else {
// mensajes segun el resultado
                $mensaje_error = "No se pudieron guardar tus datos. Revisa que hayas ejecutado el SQL de medidas.";
            }
            $stmtInsert->close();
        } else {
// mensajes segun el resultado
            $mensaje_error = "La estructura de medidas no esta actualizada. Ejecuta el SQL de /database.";
        }
    }
}

if ((float) $datos["peso"] > 0 && (float) $datos["altura"] > 0 && (int) $datos["edad"] > 0) {
    $datos = array_merge($datos, calculate_fitness($datos));
}

// preparacion de la consulta
$stmtHistorial = $conexion->prepare("SELECT fecha_registro, peso, imc, objetivo
    FROM medidas_usuario
    WHERE id_usuario = ?
    ORDER BY fecha_registro DESC, id_medida DESC
    LIMIT 8");
// comprobacion de la consulta
if ($stmtHistorial) {
    $stmtHistorial->bind_param("i", $id_usuario);
// ejecucion de la consulta
    $stmtHistorial->execute();
    $resHistorial = $stmtHistorial->get_result();
// lectura de resultados
    while ($fila = $resHistorial->fetch_assoc()) {
        $historial[] = $fila;
    }
    $stmtHistorial->close();
}

$titulo_objetivo = $objetivos[$datos["objetivo"]] ?? "Mantener peso";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi cuerpo - WhiteGym</title>
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/cliente.css">
</head>
<body>

<?php include __DIR__ . "/includes/topbar.php"; ?>

<!-- estructura principal del panel -->
<div class="dashboard-layout">
    <?php include __DIR__ . "/includes/sidebar_cliente.php"; ?>

<!-- contenido principal -->
    <main class="dashboard-main">
        <div class="page-shell">
<!-- cabecera del contenido -->
            <div class="page-header">
                <div>
                    <span class="eyebrow">Seguimiento fitness</span>
                    <h2>Mi cuerpo</h2>
                    <p>Guarda tu ultimo registro corporal, ajusta tu objetivo y consulta recomendaciones basicas de calorias, agua y macros.</p>
                </div>
                <?php if ($fecha_ultima_medida): ?>
                    <span class="badge badge-accent">Ultimo registro: <?php echo htmlspecialchars($fecha_ultima_medida); ?></span>
                <?php endif; ?>
            </div>

            <?php if ($mensaje_ok !== ""): ?>
                <p class="notice-ok"><?php echo htmlspecialchars($mensaje_ok); ?></p>
            <?php endif; ?>

            <?php if ($mensaje_error !== ""): ?>
                <p class="notice-error"><?php echo htmlspecialchars($mensaje_error); ?></p>
            <?php endif; ?>

<!-- bloques de resumen -->
            <section class="split-grid">
<!-- bloque principal de contenido -->
                <section class="card">
<!-- cabecera del bloque -->
                    <div class="panel-header">
                        <div>
                            <h3>Datos actuales</h3>
                            <p>El formulario se rellena con tu ultimo registro guardado.</p>
                        </div>
                    </div>

<!-- formulario principal -->
                    <form method="POST" class="form-grid two-columns">
                        <div class="field">
                            <label for="peso">Peso (kg)</label>
                            <input type="number" step="0.1" id="peso" name="peso" value="<?php echo $datos["peso"] > 0 ? htmlspecialchars((string) $datos["peso"]) : ""; ?>" required>
                        </div>

                        <div class="field">
                            <label for="altura">Altura (cm)</label>
                            <input type="number" step="0.1" id="altura" name="altura" value="<?php echo $datos["altura"] > 0 ? htmlspecialchars((string) $datos["altura"]) : ""; ?>" required>
                        </div>

                        <div class="field">
                            <label for="edad">Edad</label>
                            <input type="number" id="edad" name="edad" value="<?php echo $datos["edad"] > 0 ? (int) $datos["edad"] : ""; ?>" required>
                        </div>

                        <div class="field">
                            <label for="sexo">Sexo</label>
                            <select id="sexo" name="sexo">
                                <option value="hombre" <?php echo $datos["sexo"] === "hombre" ? "selected" : ""; ?>>Hombre</option>
                                <option value="mujer" <?php echo $datos["sexo"] === "mujer" ? "selected" : ""; ?>>Mujer</option>
                            </select>
                        </div>

                        <div class="field">
                            <label for="actividad_semanal">Dias de entrenamiento</label>
                            <select id="actividad_semanal" name="actividad_semanal">
                                <?php foreach ($actividad_opciones as $dia): ?>
                                    <option value="<?php echo $dia; ?>" <?php echo (int) $datos["actividad_semanal"] === $dia ? "selected" : ""; ?>><?php echo $dia; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="field">
                            <label for="intensidad">Intensidad</label>
                            <select id="intensidad" name="intensidad">
                                <?php foreach ($intensidades as $valor => $label): ?>
                                    <option value="<?php echo $valor; ?>" <?php echo $datos["intensidad"] === $valor ? "selected" : ""; ?>><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="field">
                            <label for="objetivo">Objetivo fitness</label>
                            <select id="objetivo" name="objetivo">
                                <?php foreach ($objetivos as $valor => $label): ?>
                                    <option value="<?php echo $valor; ?>" <?php echo $datos["objetivo"] === $valor ? "selected" : ""; ?>><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="inline-actions body-submit">
                            <button type="submit" class="btn btn-primary">Guardar seguimiento</button>
                        </div>
                    </form>
                </section>

<!-- bloque principal de contenido -->
                <section class="card">
<!-- cabecera del bloque -->
                    <div class="panel-header">
                        <div>
                            <h3>Objetivo activo</h3>
                            <p><?php echo htmlspecialchars($titulo_objetivo); ?></p>
                        </div>
                        <span class="status-pill status-accent"><?php echo htmlspecialchars($titulo_objetivo); ?></span>
                    </div>

                    <div class="stack">
                        <p><strong>IMC:</strong> <?php echo number_format((float) $datos["imc"], 2); ?></p>
                        <p><strong>Calorias recomendadas:</strong> <?php echo (int) $datos["calorias_recomendadas"]; ?> kcal</p>
                        <p><strong>Agua diaria:</strong> <?php echo number_format((float) $datos["agua_litros"], 2); ?> L</p>
                    </div>
                </section>
            </section>

<!-- bloque de estadisticas -->
            <section class="stats-grid body-results">
<!-- tarjeta de estadisticas -->
                <article class="card card-kpi">
                    <span class="eyebrow">Calorias</span>
                    <strong class="metric-value"><?php echo (int) $datos["calorias_recomendadas"]; ?></strong>
                    <span class="metric-caption">Recomendacion diaria para <?php echo strtolower(htmlspecialchars($titulo_objetivo)); ?>.</span>
                </article>
<!-- tarjeta de estadisticas -->
                <article class="card card-kpi">
                    <span class="eyebrow">Agua</span>
                    <strong class="metric-value"><?php echo number_format((float) $datos["agua_litros"], 2); ?> L</strong>
                    <span class="metric-caption">Cantidad diaria estimada segun tu actividad.</span>
                </article>
<!-- tarjeta de estadisticas -->
                <article class="card card-kpi">
                    <span class="eyebrow">Proteinas</span>
                    <strong class="metric-value"><?php echo number_format((float) $datos["proteinas"], 1); ?> g</strong>
                    <span class="metric-caption">Referencia diaria de proteina.</span>
                </article>
<!-- tarjeta de estadisticas -->
                <article class="card card-kpi">
                    <span class="eyebrow">Carbohidratos</span>
                    <strong class="metric-value"><?php echo number_format((float) $datos["carbohidratos"], 1); ?> g</strong>
                    <span class="metric-caption">Ajustados a tu objetivo actual.</span>
                </article>
<!-- tarjeta de estadisticas -->
                <article class="card card-kpi">
                    <span class="eyebrow">Grasas</span>
                    <strong class="metric-value"><?php echo number_format((float) $datos["grasas"], 1); ?> g</strong>
                    <span class="metric-caption">Cantidad diaria orientativa.</span>
                </article>
<!-- tarjeta de estadisticas -->
                <article class="card card-kpi">
                    <span class="eyebrow">IMC</span>
                    <strong class="metric-value"><?php echo number_format((float) $datos["imc"], 2); ?></strong>
                    <span class="metric-caption">Seguimiento corporal rapido desde tu panel.</span>
                </article>
            </section>

<!-- bloque principal de contenido -->
            <section class="card">
<!-- cabecera del bloque -->
                <div class="panel-header">
                    <div>
                        <h3>Historial reciente</h3>
                    </div>
                </div>

                <?php if (empty($historial)): ?>
                    <div class="empty-state">Aun no tienes registros guardados.</div>
                <?php else: ?>
<!-- contenedor de la tabla -->
                    <div class="table-wrap">
<!-- tabla de datos -->
                        <table>
<!-- cabecera de la tabla -->
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Peso</th>
                                    <th>IMC</th>
                                    <th>Objetivo</th>
                                </tr>
                            </thead>
<!-- contenido de la tabla -->
                            <tbody>
                                <?php foreach ($historial as $fila): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($fila["fecha_registro"] ?? ""); ?></td>
                                        <td><?php echo number_format((float) ($fila["peso"] ?? 0), 1); ?> kg</td>
                                        <td><?php echo number_format((float) ($fila["imc"] ?? 0), 2); ?></td>
                                        <td><?php echo htmlspecialchars($objetivos[$fila["objetivo"] ?? "mantener_peso"] ?? "Mantener peso"); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>
</div>

</body>
</html>

