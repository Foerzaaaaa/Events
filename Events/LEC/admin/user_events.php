<?php
session_start();
include('../includes/functions.php');
include('../includes/db.php');
checkLogin();

if ($_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$user_id) {
    header('Location: manage_users.php');
    exit();
}

$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header('Location: manage_users.php');
    exit();
}

$stmt = $conn->prepare("
    SELECT e.* 
    FROM events e
    JOIN registrations r ON e.id = r.event_id
    WHERE r.user_id = ?
");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param('i', $user_id);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$events_result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Events</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <header class="bg-blue-600 text-white p-4">
        <div class="container mx-auto">
            <h1 class="text-2xl font-bold"><i class="fas fa-calendar-check mr-2"></i>Event Management System</h1>
        </div>
    </header>

    <main class="container mx-auto flex-grow p-4">
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold mb-4"><i class="fas fa-user-calendar mr-2"></i>Events for <?php echo htmlspecialchars($user['name']); ?></h2>
            <div class="flex space-x-4 mb-6">
                <a href="manage_users.php" class="text-blue-600 hover:underline"><i class="fas fa-users mr-1"></i>Back to Manage Users</a>
                <a href="dashboard.php" class="text-blue-600 hover:underline"><i class="fas fa-tachometer-alt mr-1"></i>Back to Dashboard</a>
                <a href="../logout.php" class="text-red-600 hover:underline"><i class="fas fa-sign-out-alt mr-1"></i>Logout</a>
            </div>
            
            <h3 class="text-xl font-semibold mb-2"><i class="fas fa-info-circle mr-2"></i>User Details</h3>
            <p><strong><i class="fas fa-user mr-1"></i>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong><i class="fas fa-envelope mr-1"></i>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-xl font-semibold mb-4"><i class="fas fa-list-alt mr-2"></i>User's Registered Events</h3>
            <?php if ($events_result->num_rows > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="px-4 py-2 text-left"><i class="fas fa-tag mr-1"></i>Event Name</th>
                                <th class="px-4 py-2 text-left"><i class="far fa-calendar mr-1"></i>Date</th>
                                <th class="px-4 py-2 text-left"><i class="far fa-clock mr-1"></i>Time</th>
                                <th class="px-4 py-2 text-left"><i class="fas fa-map-marker-alt mr-1"></i>Location</th>
                                <th class="px-4 py-2 text-left"><i class="fas fa-info-circle mr-1"></i>Description</th>
                                <th class="px-4 py-2 text-left"><i class="fas fa-flag mr-1"></i>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($event = $events_result->fetch_assoc()): ?>
                            <tr class="border-b">
                                <td class="px-4 py-2"><?php echo htmlspecialchars($event['name']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($event['date']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($event['time']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($event['location']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($event['description']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($event['status']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-600"><i class="fas fa-exclamation-circle mr-1"></i>This user has not registered for any events.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer class="bg-blue-600 text-white p-4 mt-8">
        <div class="container mx-auto text-center">
            <p><i class="far fa-copyright mr-1"></i><?php echo date("Y"); ?> Warkop Project</p>
        </div>
    </footer>
</body>
</html>