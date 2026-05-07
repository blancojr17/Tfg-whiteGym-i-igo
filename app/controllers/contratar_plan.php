<?php
session_start();
require_once __DIR__ . "/../../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "usuario") {
    header("Location: ../../public/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/planes.php");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
$id_plan = (int) ($_POST["id_plan"] ?? 0);

if ($id_plan <= 0) {
    header("Location: ../../public/planes.php?error=datos");
    exit;
}

$sqlPlan = "SELECT id_plan, nombre, tipo, precio, duracion_dias, usos, activo
            FROM planes
            WHERE id_plan = ? AND activo = 1
            LIMIT 1";
$stmtPlan = $conexion->prepare($sqlPlan);

if (!$stmtPlan) {
    header("Location: ../../public/planes.php?error=1");
    exit;
}

$stmtPlan->bind_param("i", $id_plan);
$stmtPlan->execute();
$resultadoPlan = $stmtPlan->get_result();
$plan = $resultadoPlan ? $resultadoPlan->fetch_assoc() : null;
$stmtPlan->close();

if (!$plan) {
    header("Location: ../../public/planes.php?error=plan");
    exit;
}

$tipo = $plan["tipo"] ?? "";
$fecha_inicio = date("Y-m-d");

if ($tipo !== "suscripcion" && $tipo !== "bono") {
    header("Location: ../../public/planes.php?error=tipo");
    exit;
}

$conexion->begin_transaction();

try {
    $sqlPlanActivo = "SELECT up.id_usuario_plan
                      FROM usuarios_planes up
                      INNER JOIN planes p ON up.id_plan = p.id_plan
                      WHERE up.id_usuario = ?
                        AND up.fecha_fin >= CURDATE()
                        AND (
                            p.tipo = 'suscripcion'
                            OR (p.tipo = 'bono' AND up.usos_restantes > 0)
                        )";
    $stmtPlanActivo = $conexion->prepare($sqlPlanActivo);

    if (!$stmtPlanActivo) {
        throw new Exception("plan_activo");
    }

    $stmtPlanActivo->bind_param("i", $id_usuario);
    $stmtPlanActivo->execute();
    $resultadoPlanActivo = $stmtPlanActivo->get_result();
    $planesActivos = [];

    while ($fila = $resultadoPlanActivo->fetch_assoc()) {
        $planesActivos[] = (int) ($fila["id_usuario_plan"] ?? 0);
    }

    $stmtPlanActivo->close();

    if (!empty($planesActivos)) {
        $sqlDesactivar = "UPDATE usuarios_planes
                          SET fecha_fin = DATE_SUB(CURDATE(), INTERVAL 1 DAY),
                              usos_restantes = 0
                          WHERE id_usuario_plan = ?";
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
        $sqlInsert = "INSERT INTO usuarios_planes (id_usuario, id_plan, fecha_inicio, fecha_fin, usos_restantes)
                      VALUES (?, ?, ?, ?, NULL)";
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
        $sqlInsert = "INSERT INTO usuarios_planes (id_usuario, id_plan, fecha_inicio, fecha_fin, usos_restantes)
                      VALUES (?, ?, ?, ?, ?)";
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
    header("Location: ../../public/planes.php?ok=1");
    exit;
} catch (Exception $e) {
    $conexion->rollback();
    header("Location: ../../public/planes.php?error=1");
    exit;
}
