<?php
$pagina_actual = basename($_SERVER["PHP_SELF"] ?? "");
?>
<aside class="sidebar sidebar-admin">
    <div class="sidebar-intro">
        <span class="sidebar-kicker">WhiteGym</span>
        <h3 class="sidebar-title">Area admin</h3>
        <p class="sidebar-copy">Control general de usuarios, planes y clases.</p>
    </div>
    <nav>
        <a href="admin.php" class="<?php echo $pagina_actual === "admin.php" ? "active" : ""; ?>">Dashboard</a>
        <a href="admin_usuarios.php" class="<?php echo $pagina_actual === "admin_usuarios.php" ? "active" : ""; ?>">Usuarios</a>
        <a href="admin_planes.php" class="<?php echo $pagina_actual === "admin_planes.php" ? "active" : ""; ?>">Planes</a>
        <a href="admin_clases.php" class="<?php echo in_array($pagina_actual, ["admin_clases.php", "admin_editar_clase.php"], true) ? "active" : ""; ?>">Clases globales</a>
    </nav>
</aside>
