<!-- filepath: d:\xampp\htdocs\ChatProject\index.php -->
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      overflow: hidden;
    }
    .icon {
      position: absolute;
      animation: float 10s infinite;
      opacity: 0.5;
    }
    @keyframes float {
      0% { transform: translateY(0) rotate(0deg); }
      50% { transform: translateY(-20px) rotate(180deg); }
      100% { transform: translateY(0) rotate(360deg); }
    }
  </style>
</head>
<body class="bg-gradient-to-r from-blue-500 to-purple-500 h-screen flex items-center justify-center relative">
  <!-- Animated Background Icons -->
  <div class="icon bg-white rounded-full w-16 h-16" style="top: 10%; left: 20%;"></div>
  <div class="icon bg-white rounded-full w-12 h-12" style="top: 30%; left: 50%;"></div>
  <div class="icon bg-white rounded-full w-20 h-20" style="top: 60%; left: 70%;"></div>
  <div class="icon bg-white rounded-full w-14 h-14" style="top: 80%; left: 30%;"></div>

  <!-- Login Form -->
  <div class="bg-white p-8 rounded-lg shadow-lg w-96">
    <h1 class="text-2xl font-bold text-center mb-6">Login</h1>
    <p id="error-msg" class="text-red-500 text-center mb-4 font-bold hidden"></p>
    <p class="text-center text-gray-600 mb-4">Welcome back! Please login to your account.</p>
    <form id="login-form">
      <div class="mb-4">
        <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
        <input type="email" id="email" required
          class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>
      <div class="mb-6">
        <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
        <input type="password" id="password" required
          class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>
      <button type="submit"
        class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-300">
        Login
      </button>
    </form>
    <p class="text-center text-gray-600 mt-4">
      Don't have an account? 
      <a href="register.php" class="text-blue-500 hover:underline">Signup</a>
    </p>
  </div>

  <!-- Supabase JS -->
  <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js"></script>
  <script>
    const supabase = supabase.createClient(
      'https://ibcyncamhxkztzrlyesz.supabase.co',
      'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImliY3luY2FtaHhrenR6cmx5ZXN6Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDc2NjE1OTAsImV4cCI6MjA2MzIzNzU5MH0.NrIBKzY1U__Ro0rOFcPqa2ih_FqR3xvSfSwSRnQFk-A'
    );

    document.getElementById('login-form').addEventListener('submit', async function(e) {
      e.preventDefault();
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;

      const { data, error } = await supabase.auth.signInWithPassword({ email, password });

      if (error) {
        document.getElementById('error-msg').textContent = error.message;
        document.getElementById('error-msg').classList.remove('hidden');
      } else {
        // Save user info in localStorage or redirect
        localStorage.setItem("user", JSON.stringify(data));
        window.location.href = "ui.php";
      }
    });
  </script>
</body>
</html>
