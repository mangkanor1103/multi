<?php
session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === 'admin' && $password === 'kian1103') {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
       body {
    font-family: Arial, sans-serif;
    background-color: #e0ffe0; /* Light background */
    color: #006600; /* Dark green text color */
    margin: 0;
    padding: 20px;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.container {
    width: 30%;
    margin: 80px auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 102, 0, 0.5);
    position: relative;
}

.input-container {
    position: relative;
    margin-bottom: 20px;
}

.input-container input {
    width: 100%; /* Full width */
    padding: 10px 40px; /* Adjust padding for better spacing */
    border: 1px solid #006600;
    border-radius: 5px;
    transition: all 0.3s ease-in-out;
    box-sizing: border-box; /* Include padding and border in width */
}

.input-container i {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #006600;
    transition: all 0.3s ease-in-out;
}

button {
    width: 100%; /* Full width */
    padding: 12px;
    border: none;
    border-radius: 5px;
    background: #006600;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
}

button:hover {
    background: #009900;
}

.back-button {
    display: inline-block;
    margin-top: 15px;
    text-decoration: none;
    color: #006600;
    font-size: 16px;
    transition: all 0.3s ease-in-out;
}

.back-button:hover {
    color: #009900;
    transform: translateX(-5px);
}

.error {
    color: red;
}
.moving-icons {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none; /* Prevent interaction with icons */
            overflow: hidden; /* Hide overflow */
            z-index: 0; /* Behind other content */
        }
        .moving-icons i {
            position: absolute;
            font-size: 50px; /* Size of icons */
            color: rgba(0, 128, 0, 0.5); /* Light green color */
            animation: move 10s linear infinite; /* Animation for moving icons */
        }
        @keyframes move {
            0% { transform: translateY(0); }
            100% { transform: translateY(-100vh); }
        }
    </style>
</head>
<body>
    <div class="moving-icons">
        <i class="fas fa-user" style="top: 10%; left: 10%; animation-delay: 0s;"></i>
        <i class="fas fa-lock" style="top: 20%; left: 30%; animation-delay: 2s;"></i>
        <i class="fas fa-key" style="top: 30%; left: 50%; animation-delay: 4s;"></i>
        <i class="fas fa-user-shield" style="top: 40%; left: 70%; animation-delay: 6s;"></i>
        <i class="fas fa-comments" style="top: 50%; left: 90%; animation-delay: 8s;"></i>
    </div>

    <div class="container">
        <h2>Admin Login</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <div class="input-container">
                <input type="text" name="username" placeholder="Username" required>
                <i class="fas fa-user"></i>
            </div>
            <div class="input-container">
                <input type="password" name="password" placeholder="Password" required>
                <i class="fas fa-lock"></i>
            </div>
            <button type="submit">Login</button>
        </form>
        <a href="index.php" class="back-button"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</body>
</html>