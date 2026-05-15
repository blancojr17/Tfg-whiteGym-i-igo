<?php
// cierre de sesion
// inicio de sesion
session_start();
session_destroy();
// redireccion final
header("Location: ../../public/index.php");
exit;

