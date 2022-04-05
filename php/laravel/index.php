<?php
$host = "db";
$user = "user";
$pass = "123abc";
$base = "wp";

$mysqli = new mysqli($host, $user, $pass, $base);
if ($mysqli->connect_error) {
    die('Erreur de connexion (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
$mysqli->query('SET NAMES UTF8');
echo "test";
