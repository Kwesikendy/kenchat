<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js"></script>
</head>
<body class="bg-gradient-to-r from-green-400 to-blue-500 h-screen flex items-center justify-center">

  <div class="bg-white p-8 rounded-lg shadow-lg w-96">
    <h1 class="text-2xl font-bold text-center mb-6">Register</h1>
    
    <form id="register-form">
      <div class="mb-4">
        <label class="block mb-1">Name</label>
        <input type="text" id="name" class="w-full px-3 py-2 border rounded" required />
      </div>
      <div class="mb-4">
        <label class="block mb-1">Email</label>
        <input type="email" id="email" class="w-full px-3 py-2 border rounded" required />
      </div>
      <div class="mb-6">
        <label class="block mb-1">Password</label>
        <input type="password" id="password" class="w-full px-3 py-2 border rounded" required />
      </div>
      <button type="submit" class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600">
        Sign Up
      </button>
    </form>
    <p class="text-center mt-4">
      Already have an account? <a href="index.php" class="text-blue-500">Login</a>
    </p>
  </div>

  <script>
    const supabase = supabase.createClient(
      'https://ibcyncamhxkztzrlyesz.supabase.co',
      'public-anon-key' // use the anon/public key, not service role key
    );

    const form = document.getElementById('register-form');
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;
      const name = document.getElementById('name').value;

      const { user, error } = await supabase.auth.signUp({
        email,
        password,
        options: {
          data: { name }
        }
      });

      if (error) {
        alert('Error: ' + error.message);
      } else {
        alert('Registration successful! Please check your email for verification.');
        window.location.href = 'index.php';
      }
    });
  </script>
</body>
</html>
