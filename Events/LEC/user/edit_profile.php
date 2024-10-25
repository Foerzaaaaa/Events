<?php
session_start();
include('../includes/functions.php');
include('../includes/db.php');
checkLogin();

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($name) || empty($email)) {
        $errors[] = "Name and email are required.";
    }

    if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
        if (!password_verify($current_password, $user['password'])) {
            $errors[] = "Current password is incorrect.";
        }
        if ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match.";
        }
        if (strlen($new_password) < 3) {
            $errors[] = "New password must be at least 3 characters long.";
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param('ssi', $name, $email, $user_id);
        
        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
            $_SESSION['name'] = $name;

            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param('si', $hashed_password, $user_id);
                $stmt->execute();
                $success .= " Password updated successfully!";
            }
        } else {
            $errors[] = "Failed to update profile. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | EventHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
</head>

<body class="bg-gray-100 min-h-screen font-sans flex flex-col">
    <nav class="bg-gradient-to-r from-blue-600 to-blue-800 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="events.php" class="text-2xl font-bold">Event<span class="text-yellow-300">Hub</span></a>
            <div class="space-x-4">
                <a href="profile.php" class="hover:text-yellow-300 transition duration-300">Profile</a>
                <a href="../logout.php" class="hover:text-yellow-300 transition duration-300">Logout</a>
            </div>
        </div>
    </nav>

    <main class="flex-grow container mx-auto mt-8 px-4 mb-12">
        <div class="max-w-2xl mx-auto bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="p-6 md:p-8">
                <h2 class="text-3xl font-bold mb-6 text-gray-800">Edit Profile</h2>

                <?php if (!empty($success)): ?>
                    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                        <p class="font-bold">Success</p>
                        <p><?php echo $success; ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                        <p class="font-bold">Error</p>
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="edit_profile.php" class="space-y-6" x-data="{ showPasswordForm: false }">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($user['name']); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($user['email']); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <button type="button" @click="showPasswordForm = !showPasswordForm" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300">
                            <span x-text="showPasswordForm ? 'Hide Password Form' : 'Change Password'"></span>
                        </button>

                        <div x-show="showPasswordForm" class="mt-4 space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                                <input type="password" name="current_password" id="current_password" placeholder="Enter current password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>

                            <div>
                                <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                                <input type="password" name="new_password" id="new_password" placeholder="Enter new password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>

                            <div>
                                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="profile.php" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full transition duration-300">Update Profile</button>
                    </div>
                </form>
            </div>
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
