<?php
session_start();

$_SESSION['temp_enum_duration'] = $_POST['enum_duration'];
$_SESSION['temp_duration_months'] = $_POST['duration_months'];
$_SESSION['temp_amount'] = $_POST['amount'];

echo json_encode(['status' => 'success']);
?>