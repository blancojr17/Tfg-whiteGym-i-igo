<?php
session_start();
require_once __DIR__ . "/../../config/conexion.php";

$origen = $_POST["origen"] ?? "clases.php";
$origen_permitido = in_array($origen, ["clases.php", "mis_clases.php"], true) ? $origen : "clases.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "usuario") {
    header("Location: ../../public/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/" . $origen_permitido . "?error=metodo");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
$id_clase = (int) ($_POST["id_clase"] ?? 0);
$accion = $_POST["accion"] ?? "";

if ($id_clase <= 0 || ($accion !== "apuntar" && $accion !== "desapuntar")) {
    header("Location: ../../public/" . $origen_permitido . "?error=datos");
    exit;
}

$conexion->begin_transaction();

try {
    $sqlClase = "SELECT capacidad FROM clases WHERE id_clase = ? LIMIT 1";
    $stmtClase = $conexion->prepare($sqlClase);

    if (!$stmtClase) {
        throw new Exception("clase");
    }

    $stmtClase->bind_param("i", $id_clase);
    $stmtClase->execute();
    $resultadoClase = $stmtClase->get_result();
    $clase = $resultadoClase ? $resultadoClase->fetch_assoc() : null;
    $stmtClase->close();

    if (!$clase) {
        $conexion->rollback();
        header("Location: ../../public/" . $origen_permitido . "?error=clase");
        exit;
    }

    $sqlYaApuntado = "SELECT id_usuario_clase
                      FROM usuarios_clases
                      WHERE id_usuario = ? AND id_clase = ?
                      LIMIT 1";
    $stmtYaApuntado = $conexion->prepare($sqlYaApuntado);

    if (!$stmtYaApuntado) {
        throw new Exception("apuntado");
    }

    $stmtYaApuntado->bind_param("ii", $id_usuario, $id_clase);
    $stmtYaApuntado->execute();
    $resultadoYaApuntado = $stmtYaApuntado->get_result();
    $yaApuntado = $resultadoYaApuntado && $resultadoYaApuntado->num_rows > 0;
    $stmtYaApuntado->close();

    if ($accion === "apuntar") {
        if ($yaApuntado) {
            $conexion->rollback();
            header("Location: ../../public/" . $origen_permitido . "?error=duplicado");
            exit;
        }

        $sqlOcupadas = "SELECT COUNT(*) AS total
                        FROM usuarios_clases
                        WHERE id_clase = ?";
        $stmtOcupadas = $conexion->prepare($sqlOcupadas);

        if (!$stmtOcupadas) {
            throw new Exception("ocupadas");
        }

        $stmtOcupadas->bind_param("i", $id_clase);
        $stmtOcupadas->execute();
        $resultadoOcupadas = $stmtOcupadas->get_result();
        $filaOcupadas = $resultadoOcupadas ? $resultadoOcupadas->fetch_assoc() : ["total" => 0];
        $stmtOcupadas->close();

        $ocupadas = (int) ($filaOcupadas["total"] ?? 0);
        $capacidad = (int) ($clase["capacidad"] ?? 0);

        if ($ocupadas >= $capacidad) {
            $conexion->rollback();
            header("Location: ../../public/" . $origen_permitido . "?error=llena");
            exit;
        }

        $sqlInsert = "INSERT INTO usuarios_clases (id_usuario, id_clase, fecha_reserva)
                      VALUES (?, ?, NOW())";
        $stmtInsert = $conexion->prepare($sqlInsert);

        if (!$stmtInsert) {
            throw new Exception("insert");
        }

        $stmtInsert->bind_param("ii", $id_usuario, $id_clase);

        if (!$stmtInsert->execute()) {
            throw new Exception("insert_exec");
        }

        $stmtInsert->close();

        $conexion->commit();
        header("Location: ../../public/" . $origen_permitido . "?ok=apuntado");
        exit;
    }

    if (!$yaApuntado) {
        $conexion->rollback();
        header("Location: ../../public/" . $origen_permitido . "?error=no_apuntado");
        exit;
    }

    $sqlDelete = "DELETE FROM usuarios_clases
                  WHERE id_usuario = ? AND id_clase = ?
                  LIMIT 1";
    $stmtDelete = $conexion->prepare($sqlDelete);

    if (!$stmtDelete) {
        throw new Exception("delete");
    }

    $stmtDelete->bind_param("ii", $id_usuario, $id_clase);

    if (!$stmtDelete->execute()) {
        throw new Exception("delete_exec");
    }

    if ($stmtDelete->affected_rows !== 1) {
        $stmtDelete->close();
        $conexion->rollback();
        header("Location: ../../public/" . $origen_permitido . "?error=no_apuntado");
        exit;
    }

    $stmtDelete->close();

    $conexion->commit();
    header("Location: ../../public/" . $origen_permitido . "?ok=desapuntado");
    exit;
} catch (Exception $e) {
    $conexion->rollback();
    header("Location: ../../public/" . $origen_permitido . "?error=1");
    exit;
}
