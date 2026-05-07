<?php
session_start();
require_once __DIR__ . "/../../config/conexion.php";

$origen = $_POST["origen"] ?? "cliente.php";
$origen_permitido = in_array($origen, ["cliente.php", "asistencia.php"], true) ? $origen : "cliente.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "usuario") {
    header("Location: ../../public/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/" . $origen_permitido . "?asistencia_error=metodo");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];

$conexion->begin_transaction();

try {
    $sqlPlan = "SELECT up.id_usuario_plan, up.usos_restantes, p.tipo
                FROM usuarios_planes up
                INNER JOIN planes p ON up.id_plan = p.id_plan
                WHERE up.id_usuario = ?
                  AND up.fecha_fin >= CURDATE()
                  AND (
                        p.tipo = 'suscripcion'
                        OR (p.tipo = 'bono' AND up.usos_restantes > 0)
                      )
               ORDER BY 
                    CASE 
                        WHEN p.tipo = 'bono' THEN 1
                        ELSE 2
                    END,
                    up.fecha_inicio DESC,
                    up.id_usuario_plan DESC
                LIMIT 1";

    $stmtPlan = $conexion->prepare($sqlPlan);

    if (!$stmtPlan) {
        throw new Exception("plan");
    }

    $stmtPlan->bind_param("i", $id_usuario);
    $stmtPlan->execute();
    $resultadoPlan = $stmtPlan->get_result();
    $planActivo = $resultadoPlan ? $resultadoPlan->fetch_assoc() : null;
    $stmtPlan->close();

    if (!$planActivo) {
        $conexion->rollback();
        header("Location: ../../public/" . $origen_permitido . "?asistencia_error=sin_plan");
        exit;
    }

    $sqlAsistenciaHoy = "SELECT id_asistencia
                         FROM asistencia
                         WHERE id_usuario = ? AND fecha = CURDATE()
                         LIMIT 1";
    $stmtAsistenciaHoy = $conexion->prepare($sqlAsistenciaHoy);

    if (!$stmtAsistenciaHoy) {
        throw new Exception("asistencia_hoy");
    }

    $stmtAsistenciaHoy->bind_param("i", $id_usuario);
    $stmtAsistenciaHoy->execute();
    $resultadoAsistenciaHoy = $stmtAsistenciaHoy->get_result();
    $yaRegistrada = $resultadoAsistenciaHoy && $resultadoAsistenciaHoy->num_rows > 0;
    $stmtAsistenciaHoy->close();

    if ($yaRegistrada) {
        $conexion->rollback();
        header("Location: ../../public/" . $origen_permitido . "?asistencia_error=duplicada");
        exit;
    }

    $sqlInsertAsistencia = "INSERT INTO asistencia (id_usuario, fecha) VALUES (?, CURDATE())";
    $stmtInsertAsistencia = $conexion->prepare($sqlInsertAsistencia);

    if (!$stmtInsertAsistencia) {
        throw new Exception("insert_asistencia");
    }

    $stmtInsertAsistencia->bind_param("i", $id_usuario);

    if (!$stmtInsertAsistencia->execute()) {
        throw new Exception("insert_asistencia_exec");
    }

 
    if ($planActivo["tipo"] === "bono") {
        $id_usuario_plan = (int) $planActivo["id_usuario_plan"];

        if ((int)$planActivo["usos_restantes"] <= 0) {
        $conexion->rollback();
        header("Location: ../../public/" . $origen_permitido . "?asistencia_error=bono");
        exit;
        }

        $sqlDescontarUso = "UPDATE usuarios_planes
                            SET usos_restantes = usos_restantes - 1
                            WHERE id_usuario_plan = ? AND usos_restantes > 0";
        $stmtDescontarUso = $conexion->prepare($sqlDescontarUso);

        if (!$stmtDescontarUso) {
            throw new Exception("descontar");
        }

        $stmtDescontarUso->bind_param("i", $id_usuario_plan);

        if (!$stmtDescontarUso->execute()) {
            throw new Exception("descontar_exec");
        }

        if ($stmtDescontarUso->affected_rows !== 1) {
            $stmtDescontarUso->close();
            $conexion->rollback();
            header("Location: ../../public/" . $origen_permitido . "?asistencia_error=bono");
            exit;
        }

        $stmtDescontarUso->close();
    }

    $conexion->commit();
    header("Location: ../../public/" . $origen_permitido . "?asistencia_ok=1");
    exit;
} catch (Exception $e) {
    $conexion->rollback();
    header("Location: ../../public/" . $origen_permitido . "?asistencia_error=1");
    exit;
}
