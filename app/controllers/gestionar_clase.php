<?php
// gestion general de clases
// inicio de sesion
session_start();
// carga de archivos necesarios
require_once __DIR__ . "/../../config/conexion.php";

// recogida de datos del formulario
$origen = $_POST["origen"] ?? "clases.php";
$origen_permitido = in_array($origen, ["clases.php", "mis_clases.php"], true) ? $origen : "clases.php";

// proteccion de acceso segun rol
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "usuario") {
// redireccion final
    header("Location: ../../public/login.php");
    exit;
}

// validacion del metodo recibido
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
// redireccion final
    header("Location: ../../public/" . $origen_permitido . "?error=metodo");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
// recogida de datos del formulario
$id_clase = (int) ($_POST["id_clase"] ?? 0);
// recogida de datos del formulario
$accion = $_POST["accion"] ?? "";

if ($id_clase <= 0 || ($accion !== "apuntar" && $accion !== "desapuntar")) {
// redireccion final
    header("Location: ../../public/" . $origen_permitido . "?error=datos");
    exit;
}

$conexion->begin_transaction();

try {
// consulta sql
    $sqlClase = "SELECT capacidad FROM clases WHERE id_clase = ? LIMIT 1";
// preparacion de la consulta
    $stmtClase = $conexion->prepare($sqlClase);

    if (!$stmtClase) {
        throw new Exception("clase");
    }

    $stmtClase->bind_param("i", $id_clase);
// ejecucion de la consulta
    $stmtClase->execute();
    $resultadoClase = $stmtClase->get_result();
// lectura de resultados
    $clase = $resultadoClase ? $resultadoClase->fetch_assoc() : null;
    $stmtClase->close();

    if (!$clase) {
        $conexion->rollback();
// redireccion final
        header("Location: ../../public/" . $origen_permitido . "?error=clase");
        exit;
    }

// consulta sql
    $sqlYaApuntado = "SELECT id_usuario_clase
                      FROM usuarios_clases
                      WHERE id_usuario = ? AND id_clase = ?
                      LIMIT 1";
// preparacion de la consulta
    $stmtYaApuntado = $conexion->prepare($sqlYaApuntado);

    if (!$stmtYaApuntado) {
        throw new Exception("apuntado");
    }

    $stmtYaApuntado->bind_param("ii", $id_usuario, $id_clase);
// ejecucion de la consulta
    $stmtYaApuntado->execute();
    $resultadoYaApuntado = $stmtYaApuntado->get_result();
    $yaApuntado = $resultadoYaApuntado && $resultadoYaApuntado->num_rows > 0;
    $stmtYaApuntado->close();

    if ($accion === "apuntar") {
        if ($yaApuntado) {
            $conexion->rollback();
// redireccion final
            header("Location: ../../public/" . $origen_permitido . "?error=duplicado");
            exit;
        }

// consulta sql
        $sqlOcupadas = "SELECT COUNT(*) AS total
                        FROM usuarios_clases
                        WHERE id_clase = ?";
// preparacion de la consulta
        $stmtOcupadas = $conexion->prepare($sqlOcupadas);

        if (!$stmtOcupadas) {
            throw new Exception("ocupadas");
        }

        $stmtOcupadas->bind_param("i", $id_clase);
// ejecucion de la consulta
        $stmtOcupadas->execute();
        $resultadoOcupadas = $stmtOcupadas->get_result();
// lectura de resultados
        $filaOcupadas = $resultadoOcupadas ? $resultadoOcupadas->fetch_assoc() : ["total" => 0];
        $stmtOcupadas->close();

        $ocupadas = (int) ($filaOcupadas["total"] ?? 0);
        $capacidad = (int) ($clase["capacidad"] ?? 0);

        if ($ocupadas >= $capacidad) {
            $conexion->rollback();
// redireccion final
            header("Location: ../../public/" . $origen_permitido . "?error=llena");
            exit;
        }

// consulta sql
        $sqlInsert = "INSERT INTO usuarios_clases (id_usuario, id_clase, fecha_reserva)
                      VALUES (?, ?, NOW())";
// preparacion de la consulta
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
// redireccion final
        header("Location: ../../public/" . $origen_permitido . "?ok=apuntado");
        exit;
    }

    if (!$yaApuntado) {
        $conexion->rollback();
// redireccion final
        header("Location: ../../public/" . $origen_permitido . "?error=no_apuntado");
        exit;
    }

// consulta sql
    $sqlDelete = "DELETE FROM usuarios_clases
                  WHERE id_usuario = ? AND id_clase = ?
                  LIMIT 1";
// preparacion de la consulta
    $stmtDelete = $conexion->prepare($sqlDelete);

    if (!$stmtDelete) {
        throw new Exception("delete");
    }

    $stmtDelete->bind_param("ii", $id_usuario, $id_clase);

    if (!$stmtDelete->execute()) {
        throw new Exception("delete_exec");
    }

// comprobacion de la consulta
    if ($stmtDelete->affected_rows !== 1) {
        $stmtDelete->close();
        $conexion->rollback();
// redireccion final
        header("Location: ../../public/" . $origen_permitido . "?error=no_apuntado");
        exit;
    }

    $stmtDelete->close();

    $conexion->commit();
// redireccion final
    header("Location: ../../public/" . $origen_permitido . "?ok=desapuntado");
    exit;
} catch (Exception $e) {
    $conexion->rollback();
// redireccion final
    header("Location: ../../public/" . $origen_permitido . "?error=1");
    exit;
}

