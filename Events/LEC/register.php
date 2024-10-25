<?php
session_start();
include('includes/db.php');
include('includes/functions.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = password_hash(sanitizeInput($_POST['password']), PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
    $stmt->bind_param('sss', $name, $email, $password);
    
    if ($stmt->execute()) {
        header('Location: login.php');
    } else {
        $error = "Registrasi gagal!";
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub - Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF6B6B',
                        secondary: '#4ECDC4',
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body class="h-full flex items-center justify-center font-sans bg-gray-100">
    <div class="max-w-md w-full mx-4" x-data="{ name: '', email: '', password: '', error: '<?php echo isset($error) ? $error : ''; ?>' }">
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
            <div class="px-8 py-12">
                <div class="text-center mb-8">
                    <h2 class="text-4xl font-bold text-gray-800 mb-2">Event<span class="text-primary">Hub</span></h2>
                    <p class="text-gray-600">Join Us and Start Creating Memories</p>
                </div>
                <form method="POST" action="register.php" class="space-y-6">
                    <div class="relative">
                        <input type="text" id="name" name="name" required x-model="name" placeholder="Full Name"
                               class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-primary transition duration-200 ease-in-out">
                        <svg class="absolute right-3 top-3 h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div class="relative">
                        <input type="email" id="email" name="email" required x-model="email" placeholder="Email"
                               class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-primary transition duration-200 ease-in-out">
                        <svg class="absolute right-3 top-3 h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    </div>
                    <div class="relative">
                        <input type="password" id="password" name="password" required x-model="password" placeholder="Password"
                               class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-primary transition duration-200 ease-in-out">
                        <svg class="absolute right-3 top-3 h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <div>
                        <button type="submit"
                                class="w-full py-3 px-4 bg-primary text-white font-semibold rounded-lg shadow-md hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-opacity-75 transition duration-200 ease-in-out transform hover:-translate-y-1">
                            Register
                        </button>
                    </div>
                </form>
                <div x-show="error" x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-90"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                    <p x-text="error"></p>
                </div>
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account? 
                        <a href="login.php" class="font-medium text-secondary hover:text-secondary-dark transition duration-150 ease-in-out">
                            Log in here
                        </a>
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-8 text-center">
        <p class="text-center mb-4">&copy; <?php echo date("Y"); ?> Warkop Project.</p>
        </div>
    </div>
</body>
</html>