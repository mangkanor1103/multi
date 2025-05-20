<?php
session_start();
require 'config.php'; // Include database connection

$loginError = false;

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = $conn->real_escape_string($_POST['username']);
        $password = $_POST['password'];
        
        // Query to check if user exists
        $sql = "SELECT * FROM admin WHERE username = '$username'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            
            // Verify password (using password_verify if passwords are hashed)
            if (password_verify($password, $admin['password']) || $password === $admin['password']) {
                // Set session variables
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                
                // Redirect to admin dashboard
                header("Location: home.php");
                exit();
            } else {
                $loginError = true;
            }
        } else {
            $loginError = true;
        }
    } else {
        $loginError = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | MultiLingual</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f9ff;
            background-image: 
                radial-gradient(at 80% 10%, rgba(59, 130, 246, 0.1) 0px, transparent 50%),
                radial-gradient(at 20% 90%, rgba(16, 185, 129, 0.1) 0px, transparent 50%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-indigo-600 to-blue-500 text-white py-3 px-6 shadow-lg">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <i class="fas fa-language text-2xl"></i>
                <h1 class="text-xl font-bold">MultiLingual</h1>
            </div>
            <div class="hidden md:flex space-x-6">
                <a href="index.php" class="hover:text-blue-100 font-medium">Home</a>
                <a href="test.php" class="hover:text-blue-100 font-medium">Mangyan Alphabet</a>
                <a href="about.php" class="hover:text-blue-100 font-medium">About</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md">
            <!-- Login Form -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-blue-500 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-lock mr-2"></i>
                        Admin Login
                    </h2>
                </div>
                
                <form method="POST" action="login.php" class="p-6">
                    <?php if($loginError): ?>
                    <div class="bg-red-50 border border-red-200 text-red-600 rounded-lg p-4 mb-6 flex items-start">
                        <i class="fas fa-exclamation-circle mt-1 mr-3"></i>
                        <p>Invalid username or password. Please try again.</p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-4">
                        <label for="username" class="block text-gray-700 font-medium mb-2">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" name="username" id="username" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-key text-gray-400"></i>
                            </div>
                            <input type="password" name="password" id="password" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button type="button" id="togglePassword" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Sign In
                    </button>
                    
                    <div class="mt-6 text-center">
                        <a href="index.php" class="text-indigo-600 hover:text-indigo-800 text-sm">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Return to Main Site
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-gray-300 py-4 px-4">
        <div class="max-w-6xl mx-auto text-center">
            <p class="text-xs">&copy; 2025 MultiLingual Translator. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>