<?php
$pagina_actual = basename($_SERVER["PHP_SELF"] ?? "");
?>
<aside class="sidebar sidebar-entrenador">
    <div class="sidebar-intro">
        <span class="sidebar-kicker">WhiteGym</span>
        <h3 class="sidebar-title">Area entrenador</h3>
        <p class="sidebar-copy">Gestion sencilla de tus clases y asistentes.</p>
    </div>
    <nav>
        <a href="entrenador.php" class="<?php echo in_array($pagina_actual, ["entrenador.php", "editar_clase.php"], true) ? "active" : ""; ?>">Dashboard</a>
        <a href="entrenador.php#crear-clase">Crear clase</a>
        <a href="entrenador.php#mis-clases">Mis clases</a>
    </nav>
</aside>
