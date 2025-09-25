<?php
$host = 'localhost';
$db = 'imdb';
$user = 'root';
$pass = '';
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>
