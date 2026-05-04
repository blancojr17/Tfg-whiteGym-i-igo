<?php
$host = "localhost";
$usuario_bd = "root";
$password_bd = "";
$bd = "whitegym";
$puerto = 3306;

$conexion = new mysqli($host, $usuario_bd, $password_bd, $bd, $puerto);

if ($conexion->connect_error) {
    die("error de conexión");
}
?>
