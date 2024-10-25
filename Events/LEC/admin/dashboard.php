<?php
session_start();
include('../includes/functions.php');
include('../includes/db.php');
checkLogin();

if ($_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

$result = $conn->query("SELECT * FROM events");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex flex-col min-h-screen">
        <header class="bg-blue-600 text-white p-4">
            <h1 class="text-2xl font-bold"><i class="fas fa-tachometer-alt mr-2"></i>Admin Dashboard</h1>
        </header>
        <nav class="bg-white shadow">
            <div class="container mx-auto p-4">
                <a href="manage_events.php" class="text-blue-600 hover:text-blue-800"><i class="fas fa-calendar-alt mr-1"></i>Manage Events</a> |
                <a href="manage_users.php" class="text-blue-600 hover:text-blue-800"><i class="fas fa-users mr-1"></i>Manage Users</a> |
                <a href="../logout.php" class="text-blue-600 hover:text-blue-800"><i class="fas fa-sign-out-alt mr-1"></i>Logout</a>
            </div>
        </nav>

        <main class="flex-grow container mx-auto p-6">
            <h2 class="text-xl font-semibold mb-4"><i class="fas fa-list mr-2"></i>Events List</h2>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left"><i class="fas fa-tag mr-1"></i>Event Name</th>
                            <th class="py-3 px-6 text-left"><i class="far fa-calendar mr-1"></i>Date</th>
                            <th class="py-3 px-6 text-left"><i class="fas fa-info-circle mr-1"></i>Description</th>
                            <th class="py-3 px-6 text-left"><i class="fas fa-users mr-1"></i>Max Participants</th>
                            <th class="py-3 px-6 text-left"><i class="fas fa-flag mr-1"></i>Status</th>
                            <th class="py-3 px-6 text-left"><i class="fas fa-cogs mr-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="py-3 px-6"><?php echo htmlspecialchars($row['date']); ?></td>
                            <td class="py-3 px-6"><?php echo htmlspecialchars($row['description']); ?></td>
                            <td class="py-3 px-6"><?php echo htmlspecialchars($row['max_participants']); ?></td>
                            <td class="py-3 px-6"><?php echo htmlspecialchars($row['status']); ?></td>
                            <td class="py-3 px-6">
                                <a href="edit_event.php?id=<?php echo $row['id']; ?>" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit mr-1"></i>Edit</a> |
                                <a href="delete_event.php?id=<?php echo $row['id']; ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure?')"><i class="fas fa-trash-alt mr-1"></i>Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <footer class="bg-blue-600 text-white text-center p-4">
            <p><i class="far fa-copyright mr-1"></i><?php echo date("Y"); ?> Warkop Project</p>
        </footer>
    </div>
</body>
</html>