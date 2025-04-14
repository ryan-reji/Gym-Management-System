<?php

$token = $_POST["token"];
$username = $_POST["username"];

$token_hash = hash("sha256", $token);

$mysqli = require __DIR__ . "/database.php";

$sql = "SELECT * FROM users
        WHERE reset_token_hash = ? AND username = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ss", $token_hash, $username);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user === null) {
    die("Invalid token or username.");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("Token has expired");
}

if (strlen($_POST["password"]) < 8) {
    die("Password must be at least 8 characters");
}

if (!preg_match("/[a-z]/i", $_POST["password"])) {
    die("Password must contain at least one letter");
}

if (!preg_match("/[0-9]/", $_POST["password"])) {
    die("Password must contain at least one number");
}

if ($_POST["password"] !== $_POST["password_confirmation"]) {
    die("Passwords must match");
}

$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

$sql = "UPDATE users
        SET password = ?,
            reset_token_hash = NULL,
            reset_token_expires_at = NULL
        WHERE username = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ss", $password_hash, $username);
$stmt->execute();

echo "Password updated. You can now log in.";
