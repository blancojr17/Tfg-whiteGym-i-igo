<?php
// contratacion de planes del usuario
// inicio de sesion
session_start();
// carga de archivos necesarios
require_once __DIR__ . "/../../config/conexion.php";

// proteccion de acceso segun rol
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "usuario") {
// redireccion final
    header("Location: ../../public/login.php");
    exit;
}

// validacion del metodo recibido
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
// redireccion final
    header("Location: ../../public/planes.php");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
// recogida de datos del formulario
$id_plan = (int) ($_POST["id_plan"] ?? 0);

if ($id_plan <= 0) {
// redireccion final
    header("Location: ../../public/planes.php?error=datos");
    exit;
}

// consulta sql
$sqlPlan = "SELECT id_plan, nombre, tipo, precio, duracion_dias, usos, activo
            FROM planes
            WHERE id_plan = ? AND activo = 1
            LIMIT 1";
// preparacion de la consulta
$stmtPlan = $conexion->prepare($sqlPlan);

if (!$stmtPlan) {
// redireccion final
    header("Location: ../../public/planes.php?error=1");
    exit;
}

$stmtPlan->bind_param("i", $id_plan);
// ejecucion de la consulta
$stmtPlan->execute();
$resultadoPlan = $stmtPlan->get_result();
// lectura de resultados
$plan = $resultadoPlan ? $resultadoPlan->fetch_assoc() : null;
$stmtPlan->close();

if (!$plan) {
// redireccion final
    header("Location: ../../public/planes.php?error=plan");
    exit;
}

$tipo = $plan["tipo"] ?? "";
$fecha_inicio = date("Y-m-d");

if ($tipo !== "suscripcion" && $tipo !== "bono") {
// redireccion final
    header("Location: ../../public/planes.php?error=tipo");
    exit;
}

$conexion->begin_transaction();

try {
// consulta sql
    $sqlPlanActivo = "SELECT up.id_usuario_plan
                      FROM usuarios_planes up
                      INNER JOIN planes p ON up.id_plan = p.id_plan
                      WHERE up.id_usuario = ?
                        AND up.fecha_fin >= CURDATE()
                        AND (
                            p.tipo = 'suscripcion'
                            OR (p.tipo = 'bono' AND up.usos_restantes > 0)
                        )";
// preparacion de la consulta
    $stmtPlanActivo = $conexion->prepare($sqlPlanActivo);

    if (!$stmtPlanActivo) {
        throw new Exception("plan_activo");
    }

    $stmtPlanActivo->bind_param("i", $id_usuario);
// ejecucion de la consulta
    $stmtPlanActivo->execute();
    $resultadoPlanActivo = $stmtPlanActivo->get_result();
    $planesActivos = [];

// lectura de resultados
    while ($fila = $resultadoPlanActivo->fetch_assoc()) {
        $planesActivos[] = (int) ($fila["id_usuario_plan"] ?? 0);
    }

    $stmtPlanActivo->close();

    if (!empty($planesActivos)) {
// consulta sql
        $sqlDesactivar = "UPDATE usuarios_planes
                          SET fecha_fin = DATE_SUB(CURDATE(), INTERVAL 1 DAY),
                              usos_restantes = 0
                          WHERE id_usuario_plan = ?";
// preparacion de la consulta
        $stmtDesactivar = $conexion->prepare($sqlDesactivar);

        if (!$stmtDesactivar) {
            throw new Exception("desactivar_plan");
        }

        foreach ($planesActivos as $id_usuario_plan) {
            $stmtDesactivar->bind_param("i", $id_usuario_plan);
            if (!$stmtDesactivar->execute()) {
                $stmtDesactivar->close();
                throw new Exception("desactivar_exec");
            }
        }

        $stmtDesactivar->close();
    }

    if ($tipo === "suscripcion") {
        $duracion_dias = (int) ($plan["duracion_dias"] ?? 0);

        if ($duracion_dias <= 0) {
            throw new Exception("duracion");
        }

        $fecha_fin = date("Y-m-d", strtotime("+" . $duracion_dias . " days"));
// consulta sql
        $sqlInsert = "INSERT INTO usuarios_planes (id_usuario, id_plan, fecha_inicio, fecha_fin, usos_restantes)
                      VALUES (?, ?, ?, ?, NULL)";
// preparacion de la consulta
        $stmtInsert = $conexion->prepare($sqlInsert);

        if (!$stmtInsert) {
            throw new Exception("insert_suscripcion");
        }

        $stmtInsert->bind_param("iiss", $id_usuario, $id_plan, $fecha_inicio, $fecha_fin);

        if (!$stmtInsert->execute()) {
            $stmtInsert->close();
            throw new Exception("insert_suscripcion_exec");
        }

        $stmtInsert->close();
    } else {
        $usos_plan = (int) ($plan["usos"] ?? 0);
        $duracion_dias = (int) ($plan["duracion_dias"] ?? 0);

        if ($usos_plan <= 0) {
            throw new Exception("usos");
        }

        if ($duracion_dias <= 0) {
            throw new Exception("duracion");
        }

        $fecha_fin = date("Y-m-d", strtotime("+" . $duracion_dias . " days"));
// consulta sql
        $sqlInsert = "INSERT INTO usuarios_planes (id_usuario, id_plan, fecha_inicio, fecha_fin, usos_restantes)
                      VALUES (?, ?, ?, ?, ?)";
// preparacion de la consulta
        $stmtInsert = $conexion->prepare($sqlInsert);

        if (!$stmtInsert) {
            throw new Exception("insert_bono");
        }

        $stmtInsert->bind_param("iissi", $id_usuario, $id_plan, $fecha_inicio, $fecha_fin, $usos_plan);

        if (!$stmtInsert->execute()) {
            $stmtInsert->close();
            throw new Exception("insert_bono_exec");
        }

        $stmtInsert->close();
    }

    $conexion->commit();
// redireccion final
    header("Location: ../../public/planes.php?ok=1");
    exit;
} catch (Exception $e) {
    $conexion->rollback();
// redireccion final
    header("Location: ../../public/planes.php?error=1");
    exit;
}

