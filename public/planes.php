<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "usuario") {
    header("Location: login.php");
    exit;
}

$planes = [];
$error_planes = false;

$sql = "SELECT * FROM planes WHERE activo = 1";
$resultado = $conexion->query($sql);

if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $planes[] = $fila;
    }
} else {
    $error_planes = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planes - WhiteGym</title>
    <link rel="stylesheet" href="assets/css/cliente.css">
</head>
<body>

<header id="cabecera-cliente">
    <h1>WhiteGym</h1>
    <div id="info-cliente">
        <span><?php echo htmlspecialchars($_SESSION["nombre"]); ?></span>
        <a href="cliente.php">Volver</a>
        <a href="../app/controllers/logout.php" id="btn-cerrar-sesion">Cerrar sesión</a>
    </div>
</header>

<main id="contenido-cliente">
    <h2>Planes disponibles</h2>

    <?php if (isset($_GET["ok"])): ?>
        <p>Plan contratado correctamente.</p>
    <?php endif; ?>

    <?php if (isset($_GET["error"]) && $_GET["error"] === "datos"): ?>
        <p>No se ha recibido un plan válido.</p>
    <?php elseif (isset($_GET["error"]) && $_GET["error"] === "plan"): ?>
        <p>El plan no existe.</p>
    <?php elseif (isset($_GET["error"]) && $_GET["error"] === "tipo"): ?>
        <p>El tipo de plan no es válido.</p>
    <?php elseif (isset($_GET["error"]) && $_GET["error"] === "duracion"): ?>
        <p>La suscripción no tiene una duración válida.</p>
    <?php elseif (isset($_GET["error"]) && $_GET["error"] === "usos"): ?>
        <p>El bono no tiene usos configurados.</p>
    <?php elseif (isset($_GET["error"])): ?>
        <p>No se ha podido completar la operación.</p>
    <?php endif; ?>

    <?php if ($error_planes): ?>
        <p>No se han podido cargar los planes.</p>
    <?php elseif (empty($planes)): ?>
        <p>No hay planes disponibles.</p>
    <?php else: ?>
        <?php foreach ($planes as $plan): ?>
            <article>
                <h3><?php echo htmlspecialchars($plan["nombre"] ?? "Sin nombre"); ?></h3>
                <p>Precio: <?php echo htmlspecialchars((string)($plan["precio"] ?? "0")); ?> €</p>
                <p>Tipo: <?php echo htmlspecialchars($plan["tipo"] ?? ""); ?></p>

                <form action="../app/controllers/contratar_plan.php" method="post">
                    <input type="hidden" name="id_plan" value="<?php echo (int)($plan["id_plan"] ?? 0); ?>">
                    <button type="submit">Contratar</button>
                </form>
            </article>
            <hr>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

</body>
</html>
