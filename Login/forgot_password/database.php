<?php
$mysqli = new mysqli('localhost', 'root', '', 'miniproject_db');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

return $mysqli;
?>