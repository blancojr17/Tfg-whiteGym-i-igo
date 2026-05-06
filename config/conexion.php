<?php
$host = "localhost";
$usuario_bd = "root";
$password_bd = "";
$bd = "gym_tfg";
$puerto = 3306;

$conexion = new mysqli($host, $usuario_bd, $password_bd, $bd, $puerto);

if ($conexion->connect_error) {
    die("Error de conexion: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>
