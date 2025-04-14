<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Signup</title>
    <style>
header {
    margin-top: 0px;
    position: relative;
    top: 0;
    left: 0;
    width: 100%;
    padding: 0.01rem 9%;
    background-color: transparent;
    filter: drop-shadow(10px);
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 100;
}

.logo {
    font-size: 2rem;
    color: #b74b4b;
    font-weight: 800;
    cursor: pointer;
    transition: 0.5s ease;
}

.logo:hover {
    transform: scale(1.1);
}

nav a {
    font-size: 1.8rem;
    color: white;
    margin-left: 4rem;
    font-weight: 500;
    transition: 0.3s ease;
    border-bottom: 3px solid transparent;
}

nav a:hover,
nav a.active {
    color: #b74b4b;
    border-bottom: 3px solid #b74b4b;
}

@media (max-width: 995px) {
    nav {
        position: absolute;
        display: none;
        top: 0;
        right: 0;
        width: 40%;
        border-left: 5px solid #b74b4b;
        border-bottom: 5px solid #b74b4b;
        border-bottom-left-radius: 2rem;
        padding: 1rem solid;
        background-color: #161616;
        border-top: 0.1rem solid rgba(0, 0, 0, 0.1);
    }

    nav.active {
        display: block;
    }

    nav a {
        display: block;
        font-size: 2rem;
        margin: 3rem 0;
    }

    nav a:hover,
    nav a.active {
        padding: 1rem;
        border-radius: 0.5rem;
        border-bottom: 0.5rem solid #b74b4b;
    }
}
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
            margin: 8px 0;
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
<header>
    <a href="../GYM SHARK/home.html" class="logo"><strong>GYM SHARK</strong></a>
    
  </header>
<body>
    <div class="container" id="container">
        <div class="form-container sign-in-container">
            <form action="login.php" method="post">
                <h1>Sign In</h1>
                <p>Enter your credentials</p>
                <input type="text" name="username" placeholder="Username" required=""/>
                <input type="password" name="password" placeholder="Password" required=""/>
                
                <!-- Error message section -->
                <?php if (isset($_GET['error'])) { ?>
                    <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
                <?php } ?>

                <a href="forgot_password/forgot-password.php">Forgot your password?</a>
                <button>Sign In</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-right">
                    <h1>Hello!</h1>
                    <p>Haven't registered yet? Sign-up now!</p>
                    <button id="signUp" onclick="location.href='register.html'">Sign Up</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
