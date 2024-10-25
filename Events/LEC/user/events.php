<?php
session_start();
include('../includes/functions.php');
include('../includes/db.php');
checkLogin();

$result = $conn->query("SELECT * FROM events WHERE status = 'open'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Dashboard | EventHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#3B82F6',
                        'secondary': '#1E40AF',
                    }
                }
            }
        }
    </script>
</head>
<body class="flex flex-col min-h-screen bg-gray-50 font-sans">
    <div x-data="{ mobileMenu: false }" class="bg-gradient-to-r from-primary to-secondary text-white shadow-lg">
        <nav class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="text-2xl font-bold">Event<span class="text-yellow-300">Hub</span></div>
                <div class="hidden md:flex space-x-6">
                    <a href="profile.php" class="hover:text-yellow-300 transition duration-300">Profile</a>
                    <a href="../logout.php" class="hover:text-yellow-300 transition duration-300">Logout</a>
                </div>
                <button @click="mobileMenu = !mobileMenu" class="md:hidden focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
            <div x-show="mobileMenu" x-transition class="md:hidden mt-4 space-y-3">
                <a href="profile.php" class="block hover:text-yellow-300 transition duration-300">Profile</a>
                <a href="../logout.php" class="block hover:text-yellow-300 transition duration-300">Logout</a>
            </div>
        </nav>
    </div>

    <main class="flex-grow container mx-auto px-6 py-12">
        <h2 class="text-4xl font-extrabold text-gray-800 mb-8 text-center">Available Events</h2>
        
        <div class="grid gap-8 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                    <div class="p-6">
                        <h3 class="font-bold text-2xl mb-3 text-gray-800"><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p class="text-gray-600 mb-4 flex items-center">
                            <i class="far fa-calendar-alt mr-2 text-primary"></i>
                            <?php echo date('F j, Y', strtotime($row['date'])); ?>
                        </p>
                        <a href="event_detail.php?id=<?php echo $row['id']; ?>" 
                           class="inline-block bg-primary hover:bg-secondary text-white font-bold py-2 px-6 rounded-full transition duration-300 transform hover:scale-105">
                            View Details
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-16">
                    <i class="fas fa-calendar-times text-6xl text-gray-400 mb-6"></i>
                    <p class="text-2xl text-gray-600">No events available at the moment.</p>
                    <p class="mt-2 text-gray-500">Check back later for exciting new events!</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="bg-gray-800 text-white mt-auto">
    <div class="container mx-auto px-6 py-6">
        <div class="flex flex-col items-center">
            <p class="text-center mb-4">&copy; <?php echo date("Y"); ?> Warkop Project.</p>
        </div>
    </div>
</footer>
</body>
</html>