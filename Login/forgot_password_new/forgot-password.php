<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Signup</title>
    <style>
        /* Your existing styles */
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
            position: relative;
            overflow: hidden;
            width: 1000px;
            max-width: 100%;
            min-height: 600px;
            border: 2px solid #fff;
        }
        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }
        .sign-in-container {
            left: 0;
            width: 50%;
            z-index: 2;
        }
        form {
            background-color: #1e1e1e;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 50px;
            height: 100%;
            text-align: center;
        }
        h1 {
            font-weight: bold;
            margin: 0;
            color: #f05454;
        }
        p {
            font-size: 14px;
            font-weight: 100;
            line-height: 20px;
            letter-spacing: 0.5px;
            margin: 20px 0 30px;
            color: #aaa;
        }
        input {
            background-color: #333;
            border: none;
            border-radius: 7px;
            padding: 12px 15px;
            margin: 1px 0;
            width: 100%;
            color: #fff;
        }
        input::placeholder {
            color: #ccc;
        }
        a {
            color: #f05454;
            font-size: 14px;
            text-decoration: none;
            margin: 15px 0;
        }
        a:hover {
            text-decoration: underline;
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
        }
        button:hover {
            background-color: #ff7979;
        }
        button:active {
            transform: scale(0.95);
        }
        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
        }
        .overlay {
            background: linear-gradient(to right, #1e1e1e, #333);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: 0 0;
            color: #FFFFFF;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }
        .overlay-panel {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 40px;
            text-align: center;
            top: 0;
            height: 100%;
            width: 50%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }
        .overlay-right {
            right: 0;
            transform: translateX(0);
        }
        .overlay-panel h1 {
            font-weight: bold;
            color: #f05454;
        }
        .overlay-panel p {
            color: #ddd;
        }
        .overlay-panel button {
            background-color: transparent;
            border-color: #f05454;
        }
    </style>
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-in-container">
            <form action="send-password-reset.php" method="post">
                <h1>Forgot Password</h1>
                <p>Enter Email and Username</p>
                <input type="text" placeholder="Username" required name="username"/><br>
                <input type="email" placeholder="Email" required name="email"/><br>
                <button>Send Link</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-right">
                    <h1>Forgot Password?</h1>
                    <p>Don't worry! Enter your registered email and username to get a reset link.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
