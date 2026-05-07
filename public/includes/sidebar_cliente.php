<?php
$pagina_actual = basename($_SERVER["PHP_SELF"] ?? "");
?>
<aside class="sidebar sidebar-cliente">
    <div class="sidebar-intro">
        <span class="sidebar-kicker">WhiteGym</span>
        <h3 class="sidebar-title">Area cliente</h3>
        <p class="sidebar-copy">Tu actividad, tu plan y tu progreso en un solo panel.</p>
    </div>
    <nav>
        <a href="cliente.php" class="<?php echo $pagina_actual === "cliente.php" ? "active" : ""; ?>">Dashboard</a>
        <a href="mi_cuerpo.php" class="<?php echo $pagina_actual === "mi_cuerpo.php" ? "active" : ""; ?>">Mi cuerpo</a>
        <a href="planes.php" class="<?php echo $pagina_actual === "planes.php" ? "active" : ""; ?>">Mi plan</a>
        <a href="mis_clases.php" class="<?php echo $pagina_actual === "mis_clases.php" ? "active" : ""; ?>">Mis clases</a>
        <a href="clases.php" class="<?php echo $pagina_actual === "clases.php" ? "active" : ""; ?>">Reservar clases</a>
        <a href="asistencia.php" class="<?php echo $pagina_actual === "asistencia.php" ? "active" : ""; ?>">Asistencia</a>
    </nav>
</aside>
