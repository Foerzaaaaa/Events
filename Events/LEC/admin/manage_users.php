<?php
session_start();
include('../includes/functions.php');
include('../includes/db.php');
checkLogin();

if ($_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

if (isset($_GET['delete_user'])) {
    $user_id = intval($_GET['delete_user']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    if ($stmt->execute()) {
        $success = "User  deleted successfully!";
    } else {
        $error = "Failed to delete user.";
    }
}

$result = $conn->query("SELECT * FROM users WHERE role = 'user'");

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <header class="bg-blue-600 text-white p-4">
        <div class="container mx-auto">
            <h1 class="text-2xl font-bold"><i class="fas fa-users-cog mr-2"></i>Event Management System</h1>
        </div>
    </header>

    <main class="container mx-auto flex-grow p-4">
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold mb-4"><i class="fas fa-user-shield mr-2"></i>Manage Users</h2>
            <div class="flex space-x-4 mb-6">
                <a href="dashboard.php" class="text-blue-600 hover:underline"><i class="fas fa-arrow-left mr-1"></i>Back to Dashboard</a>
                <a href="../logout.php" class="text-red-600 hover:underline"><i class="fas fa-sign-out-alt mr-1"></i>Logout</a>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p><i class="fas fa-check-circle mr-2"></i><?php echo $success; ?></p>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p><i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-xl font-semibold mb-4"><i class="fas fa-user-friends mr-2"></i>Registered Users</h3>
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-4 py-2 text-left"><i class="fas fa-user mr-1"></i>Name</th>
                            <th class="px-4 py-2 text-left"><i class="fas fa-envelope mr-1"></i>Email</th>
                            <th class="px-4 py-2 text-left"><i class="fas fa-cogs mr-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="border-b">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="px-4 py-2">
                                <a href="user_events.php?id=<?php echo $row['id']; ?>" class="text-blue-600 hover:underline mr-2">
                                    <i class="fas fa-calendar-alt mr-1"></i>View Events
                                </a>
                                <a href="manage_users.php?delete_user=<?php echo $row['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this user?');" 
                                   class="text-red-600 hover:underline">
                                    <i class="fas fa-trash-alt mr-1"></i>Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer class="bg-blue-600 text-white p-4 mt-8">
        <div class="container mx-auto text-center">
            <p><i class="far fa-copyright mr-1"></i><?php echo date("Y"); ?> Warkop Project</p>
        </div>
    </footer>
</body>
</html>