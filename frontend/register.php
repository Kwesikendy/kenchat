<!DOCTYPE html>
<?php
include '../backend/config.php';
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes float {
            0% {
                transform: translateY(0) translateX(0);
            }
            50% {
                transform: translateY(-30px) translateX(20px);
            }
            100% {
                transform: translateY(0) translateX(0);
            }
        }
        .bubble {
            animation: float 6s ease-in-out infinite;
            border-radius: 50%;
            opacity: 0.7;
        }
    </style>
</head>
<body class="bg-gradient-to-r from-green-400 to-blue-500 h-screen flex items-center justify-center relative overflow-hidden">
    <!-- Moving bubbles background -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="bubble absolute top-10 left-10 bg-white w-16 h-16"></div>
        <div class="bubble absolute top-20 right-20 bg-white w-20 h-20"></div>
        <div class="bubble absolute bottom-10 left-20 bg-white w-24 h-24"></div>
        <div class="bubble absolute bottom-20 right-10 bg-white w-12 h-12"></div>
    </div>
    <!-- Form container -->
    <div class="bg-white p-8 rounded-lg shadow-lg w-96 relative z-10">
        <h1 class="text-2xl font-bold text-center mb-6">Register Now!!</h1>
        <?php 
        if(isset($_SESSION['error']))
        {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">';
            echo '<strong class="font-bold">Error:</strong> '.$_SESSION['error'];
            echo $_SESSION['error'];
            echo '</div>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="../backend/backend.php" method="POST">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium mb-2">Name</label>
                <input type="text" id="name" name="name" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                <input type="email" id="email" name="email" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <button name = 'register' type="submit"
                class="w-full bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition duration-300">
                Sign Up
            </button>
        </form>
        <p class="text-center text-gray-600 mt-4">
            Already have an account? 
            <a href=".../frontend/index.php" class="text-blue-500 hover:underline">Sign In</a>
        </p>
    </div>
</body>
</html>
