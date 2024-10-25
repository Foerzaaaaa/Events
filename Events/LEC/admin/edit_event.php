<?php
session_start();

include('../includes/functions.php');
include('../includes/db.php');

checkLogin();

if ($_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_events.php');
    exit();
}

$event_id = intval($_GET['id']);
$errors = [];
$success = "";

$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param('i', $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    header('Location: manage_events.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = !empty($_POST['name']) ? trim($_POST['name']) : $event['name'];
    $date = !empty($_POST['date']) ? $_POST['date'] : $event['date'];
    $time = !empty($_POST['time']) ? $_POST['time'] : $event['time'];
    $location = !empty($_POST['location']) ? trim($_POST['location']) : $event['location'];
    $description = !empty($_POST['description']) ? trim($_POST['description']) : $event['description'];
    $max_participants = !empty($_POST['max_participants']) ? intval($_POST['max_participants']) : $event['max_participants'];
    $status = !empty($_POST['status']) ? trim($_POST['status']) : $event['status'];

    if (empty($name) || empty($date) || empty($time) || empty($location) || empty($description) || $max_participants <= 0) {
        $errors[] = "All fields are required, and max participants must be greater than zero.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE events SET name = ?, date = ?, time = ?, location = ?, description = ?, max_participants = ?, status = ? WHERE id = ?");
        $stmt->bind_param('sssssisi', $name, $date, $time, $location, $description, $max_participants, $status, $event_id);

        if ($stmt->execute()) {
            $success = "Event updated successfully!";
            $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
            $stmt->bind_param('i', $event_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $event = $result->fetch_assoc();
        } else {
            $errors[] = "Failed to update the event. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
    <header class="bg-blue-600 text-white p-4">
        <div class="container mx-auto">
            <h1 class="text-2xl font-bold">
                <i class="fas fa-calendar-alt mr-2"></i>Event Management System
            </h1>
        </div>
    </header>

    <main class="container mx-auto flex-grow p-4">
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold mb-4">
                <i class="fas fa-edit mr-2"></i>Edit Event
            </h2>
            <div class="flex space-x-4 mb-6">
                <a href="dashboard.php" class="text-blue-600 hover:underline">
                    <i class="fas fa-tachometer-alt mr-1"></i>Back to Dashboard
                </a>
                <a href="../logout.php" class="text-red-600 hover:underline">
                    <i class="fas fa-sign-out-alt mr-1"></i>Logout
                </a>
            </div>

            <?php if (!empty($success)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>
                        <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <?php foreach ($errors as $error): ?>
                        <p>
                            <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                        </p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="edit_event.php?id=<?php echo $event_id; ?>" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-tag mr-1"></i>Event Name:
                    </label>
                    <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($event['name']); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>

                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">
                        <i class="far fa-calendar mr-1"></i>Date:
                    </label>
                    <input type="date" id="date" name="date" required value="<?php echo htmlspecialchars($event['date']); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>

                <div>
                    <label for="time" class="block text-sm font-medium text-gray-700">
                        <i class="far fa-clock mr-1"></i>Time:
                    </label>
                    <input type="time" id="time" name="time" required value="<?php echo htmlspecialchars($event['time']); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-map-marker-alt mr-1"></i>Location:
                    </label>
                    <input type="text" id="location" name="location" required value="<?php echo htmlspecialchars($event['location']); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-info-circle mr-1"></i>Description:
                    </label>
                    <textarea id="description" name="description" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"><?php echo htmlspecialchars($event['description']); ?></textarea>
                </div>

                <div>
                    <label for="max_participants" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-users mr-1"></i>Max Participants:
                    </label>
                    <input type="number" id="max_participants" name="max_participants" required value="<?php echo htmlspecialchars($event['max_participants']); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-toggle-on mr-1"></i>Status:
                    </label>
                    <select id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="open" <?php if ($event['status'] == 'open') echo 'selected'; ?>>Open</option>
                        <option value="closed" <?php if ($event['status'] == 'closed') echo 'selected'; ?>>Closed</option>
                        <option value="canceled" <?php if ($event['status'] == 'canceled') echo 'selected'; ?>>Canceled</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>Update Event
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-blue-600 text-white p-4 mt-8">
        <div class="container mx-auto text-center">
            <p>&copy; <?php echo date("Y"); ?> Warkop Project</p>
        </div>
    </footer>
</body>

</html>
