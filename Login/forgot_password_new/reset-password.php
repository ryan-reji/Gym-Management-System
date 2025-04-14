<?php

$token = $_GET["token"];

$token_hash = hash("sha256", $token);

$mysqli = require __DIR__ . "/database.php";

$sql = "SELECT username FROM users WHERE reset_token_hash = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();

$user = $result->fetch_assoc();

if ($user === null) {
    die("token not found");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("token has expired");
}

$username = $user["username"]; // Store the username

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;800&display=swap');
        * {
            box-sizing: border-box;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            font-family: 'Montserrat', sans-serif;
            height: 100vh;
            margin: 0;
            background-color: #0a0a0a;
            color: #fff;
        }
        .container {
            background-color: #1e1e1e;
            border-radius: 20px;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25), 0 10px 10px rgba(0, 0, 0, 0.22);
            width: 500px;
            min-height: 500px;
            border: 2px solid #fff;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        form {
            background-color: #1e1e1e;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 30px;
            width: 100%;
            text-align: center;
        }
        h1 {
            font-weight: bold;
            margin: 0;
            color: #f05454;
            font-size: 30px;
            margin-bottom: 20px;
        }
        input {
            background-color: #333;
            border: none;
            border-radius: 7px;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
            color: #fff;
        }
        input::placeholder {
            color: #ccc;
        }
        button {
            border-radius: 20px;
            border: 1px solid #f05454;
            background-color: #f05454;
            color: #FFFFFF;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in;
            cursor: pointer;
            margin-top: 20px;
        }
        button:hover {
            background-color: #ff7979;
        }
        button:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body>
    <div class="container">
        <form method="post" action="process-reset-password.php">
            <h1>Enter new password</h1>
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
            <input type="password" id="password" name="password" placeholder="New Password" required>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
            <button>Reset Password</button>
        </form>
    </div>
</body>
</html>
