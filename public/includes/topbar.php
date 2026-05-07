<?php
$nombre_usuario = htmlspecialchars($_SESSION["nombre"] ?? "Usuario");
$rol_usuario = htmlspecialchars($_SESSION["rol"] ?? "");
?>
<header class="topbar">
    <a href="index.php" class="topbar-brand" aria-label="Ir al inicio de WhiteGym">
        <img src="assets/img/logo.png" alt="Logo WhiteGym" class="topbar-logo">
        <span class="topbar-brand-text">
            <span class="topbar-brand-title">WhiteGym</span>
            <span class="topbar-brand-subtitle">Panel interno</span>
        </span>
    </a>
    <div class="topbar-user">
        <span class="topbar-profile">
            <strong><?php echo $nombre_usuario; ?></strong>
            <span><?php echo ucfirst($rol_usuario); ?></span>
        </span>
        <a href="../app/controllers/logout.php" class="btn-logout">Cerrar sesion</a>
    </div>
</header>
