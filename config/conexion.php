<?php
// conexion con la base de datos
// datos principales de acceso
$host = "localhost";
$usuario_bd = "root";
$password_bd = "";
$bd = "gym_tfg";
$puerto = 3307;

// creacion de la conexion
$conexion = new mysqli($host, $usuario_bd, $password_bd, $bd, $puerto);

if ($conexion->connect_error) {
    die("Error de conexion: " . $conexion->connect_error);
}

// configuracion de caracteres
$conexion->set_charset("utf8mb4");
?>
