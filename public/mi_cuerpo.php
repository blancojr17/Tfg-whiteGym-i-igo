<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["email"]) || $_SESSION["rol"] !== "usuario") {
    header("Location: login.php");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
$actividad_opciones = range(1, 7);
$intensidades = ["baja" => "Baja", "media" => "Media", "alta" => "Alta"];
$objetivos = [
    "perder_grasa" => "Perder grasa",
    "mantener_peso" => "Mantener peso",
    "ganar_masa" => "Ganar masa muscular"
];

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
$mensaje_ok = "";
$mensaje_error = "";

function calcular_fitness(array $datos): array
{
    $peso = (float) $datos["peso"];
    $altura = (float) $datos["altura"];
    $edad = (int) $datos["edad"];
    $sexo = (string) $datos["sexo"];
    $actividad = (int) $datos["actividad_semanal"];
    $intensidad = (string) $datos["intensidad"];
    $objetivo = (string) $datos["objetivo"];

    $altura_m = $altura / 100;
    $imc = $peso / ($altura_m * $altura_m);
    $tmb = $sexo === "mujer"
        ? (10 * $peso) + (6.25 * $altura) - (5 * $edad) - 161
        : (10 * $peso) + (6.25 * $altura) - (5 * $edad) + 5;

    $factor_actividad = [
        1 => 1.20,
        2 => 1.28,
        3 => 1.36,
        4 => 1.44,
        5 => 1.53,
        6 => 1.62,
        7 => 1.70
    ];
    $factor_intensidad = [
        "baja" => 0.98,
        "media" => 1.00,
        "alta" => 1.08
    ];
    $ajuste_objetivo = [
        "perder_grasa" => -350,
        "mantener_peso" => 0,
        "ganar_masa" => 300
    ];
    $proteinas_por_kg = [
        "perder_grasa" => 2.0,
        "mantener_peso" => 1.8,
        "ganar_masa" => 2.0
    ];
    $grasas_por_kg = [
        "perder_grasa" => 0.8,
        "mantener_peso" => 0.9,
        "ganar_masa" => 1.0
    ];
    $agua_extra = [
        "baja" => 0.00,
        "media" => 0.20,
        "alta" => 0.40
    ];

    $mantenimiento = $tmb * ($factor_actividad[$actividad] ?? 1.2) * ($factor_intensidad[$intensidad] ?? 1);
    $calorias = (int) round($mantenimiento + ($ajuste_objetivo[$objetivo] ?? 0));
    $proteinas = round($peso * ($proteinas_por_kg[$objetivo] ?? 1.8), 1);
    $grasas = round($peso * ($grasas_por_kg[$objetivo] ?? 0.9), 1);
    $kcal_restantes = max($calorias - (($proteinas * 4) + ($grasas * 9)), 0);
    $carbohidratos = round($kcal_restantes / 4, 1);
    $agua_litros = round(($peso * 0.033) + ($actividad * 0.10) + ($agua_extra[$intensidad] ?? 0), 2);

    return [
        "imc" => round($imc, 2),
        "calorias_recomendadas" => $calorias,
        "agua_litros" => $agua_litros,
        "proteinas" => $proteinas,
        "carbohidratos" => $carbohidratos,
        "grasas" => $grasas
    ];
}

$stmtUltima = $conexion->prepare("SELECT peso, altura, edad, sexo, actividad_semanal, intensidad, objetivo, calorias_recomendadas, agua_litros, proteinas, carbohidratos, grasas, imc, fecha_registro
    FROM medidas_usuario
    WHERE id_usuario = ?
    ORDER BY fecha_registro DESC, id_medida DESC
    LIMIT 1");
if ($stmtUltima) {
    $stmtUltima->bind_param("i", $id_usuario);
    $stmtUltima->execute();
    $resUltima = $stmtUltima->get_result();
    $filaUltima = $resUltima ? $resUltima->fetch_assoc() : null;
    $stmtUltima->close();

    if ($filaUltima) {
        $datos = array_merge($datos, $filaUltima);
        $fecha_ultima_medida = $filaUltima["fecha_registro"] ?? null;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $datos["peso"] = (float) ($_POST["peso"] ?? 0);
    $datos["altura"] = (float) ($_POST["altura"] ?? 0);
    $datos["edad"] = (int) ($_POST["edad"] ?? 0);
    $datos["sexo"] = (string) ($_POST["sexo"] ?? "hombre");
    $datos["actividad_semanal"] = (int) ($_POST["actividad_semanal"] ?? 0);
    $datos["intensidad"] = (string) ($_POST["intensidad"] ?? "media");
    $datos["objetivo"] = (string) ($_POST["objetivo"] ?? "mantener_peso");

    $datos_validos = $datos["peso"] > 0
        && $datos["altura"] > 0
        && $datos["edad"] > 0
        && in_array($datos["sexo"], ["hombre", "mujer"], true)
        && in_array($datos["actividad_semanal"], $actividad_opciones, true)
        && array_key_exists($datos["intensidad"], $intensidades)
        && array_key_exists($datos["objetivo"], $objetivos);

    if (!$datos_validos) {
        $mensaje_error = "Revisa los datos introducidos antes de guardar tu seguimiento.";
    } else {
        $calculados = calcular_fitness($datos);
        $datos = array_merge($datos, $calculados);

        $stmtInsert = $conexion->prepare("INSERT INTO medidas_usuario
            (id_usuario, peso, altura, edad, sexo, actividad_semanal, intensidad, objetivo, calorias_recomendadas, agua_litros, proteinas, carbohidratos, grasas, imc)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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

            if ($stmtInsert->execute()) {
                $mensaje_ok = "Seguimiento actualizado correctamente.";
                $fecha_ultima_medida = date("Y-m-d H:i:s");
            } else {
                $mensaje_error = "No se pudieron guardar tus datos. Revisa que hayas ejecutado el SQL de medidas.";
            }
            $stmtInsert->close();
        } else {
            $mensaje_error = "La estructura de medidas no esta actualizada. Ejecuta el SQL de /database.";
        }
    }
}

if ((float) $datos["peso"] > 0 && (float) $datos["altura"] > 0 && (int) $datos["edad"] > 0) {
    $datos = array_merge($datos, calcular_fitness($datos));
}

$stmtHistorial = $conexion->prepare("SELECT fecha_registro, peso, imc, objetivo
    FROM medidas_usuario
    WHERE id_usuario = ?
    ORDER BY fecha_registro DESC, id_medida DESC
    LIMIT 8");
if ($stmtHistorial) {
    $stmtHistorial->bind_param("i", $id_usuario);
    $stmtHistorial->execute();
    $resHistorial = $stmtHistorial->get_result();
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

<div class="dashboard-layout">
    <?php include __DIR__ . "/includes/sidebar_cliente.php"; ?>

    <main class="dashboard-main">
        <div class="page-shell">
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

            <section class="split-grid">
                <section class="card">
                    <div class="panel-header">
                        <div>
                            <h3>Datos actuales</h3>
                            <p>El formulario se rellena con tu ultimo registro guardado.</p>
                        </div>
                    </div>

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

                <section class="card">
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

            <section class="stats-grid body-results">
                <article class="card card-kpi">
                    <span class="eyebrow">Calorias</span>
                    <strong class="metric-value"><?php echo (int) $datos["calorias_recomendadas"]; ?></strong>
                    <span class="metric-caption">Recomendacion diaria para <?php echo strtolower(htmlspecialchars($titulo_objetivo)); ?>.</span>
                </article>
                <article class="card card-kpi">
                    <span class="eyebrow">Agua</span>
                    <strong class="metric-value"><?php echo number_format((float) $datos["agua_litros"], 2); ?> L</strong>
                    <span class="metric-caption">Cantidad diaria estimada segun tu actividad.</span>
                </article>
                <article class="card card-kpi">
                    <span class="eyebrow">Proteinas</span>
                    <strong class="metric-value"><?php echo number_format((float) $datos["proteinas"], 1); ?> g</strong>
                    <span class="metric-caption">Referencia diaria de proteina.</span>
                </article>
                <article class="card card-kpi">
                    <span class="eyebrow">Carbohidratos</span>
                    <strong class="metric-value"><?php echo number_format((float) $datos["carbohidratos"], 1); ?> g</strong>
                    <span class="metric-caption">Ajustados a tu objetivo actual.</span>
                </article>
                <article class="card card-kpi">
                    <span class="eyebrow">Grasas</span>
                    <strong class="metric-value"><?php echo number_format((float) $datos["grasas"], 1); ?> g</strong>
                    <span class="metric-caption">Cantidad diaria orientativa.</span>
                </article>
                <article class="card card-kpi">
                    <span class="eyebrow">IMC</span>
                    <strong class="metric-value"><?php echo number_format((float) $datos["imc"], 2); ?></strong>
                    <span class="metric-caption">Seguimiento corporal rapido desde tu panel.</span>
                </article>
            </section>

            <section class="card">
                <div class="panel-header">
                    <div>
                        <h3>Historial reciente</h3>
                    </div>
                </div>

                <?php if (empty($historial)): ?>
                    <div class="empty-state">Aun no tienes registros guardados.</div>
                <?php else: ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Peso</th>
                                    <th>IMC</th>
                                    <th>Objetivo</th>
                                </tr>
                            </thead>
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
