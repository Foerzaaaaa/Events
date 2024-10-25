<?php
session_start();
include('../includes/functions.php');
include('../includes/db.php');
checkLogin();

if ($_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);
    $max_participants = intval($_POST['max_participants']);
    $status = 'open'; 

    if (empty($name) || empty($date) || empty($time) || empty($location) || empty($description) || $max_participants <= 0) {
        $errors[] = "All fields are required, and max participants must be greater than zero.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO events (name, date, time, location, description, max_participants, status) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssis', $name, $date, $time, $location, $description, $max_participants, $status);

        if ($stmt->execute()) {
            $success = "Event created successfully!";
        } else {
            $errors[] = "Failed to create event. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Event</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h2>Create New Event</h2>
    <a href="manage_events.php">Back to Event Management</a> | <a href="../logout.php">Logout</a>

    <?php if (!empty($success)) { echo "<p style='color:green;'>$success</p>"; } ?>
    <?php if (!empty($errors)) { foreach ($errors as $error) { echo "<p style='color:red;'>$error</p>"; } } ?>

    <form method="POST" action="create_event.php" enctype="multipart/form-data">
        <label>Event Name:</label><br>
        <input type="text" name="name" required value="<?php echo isset($name) ? $name : ''; ?>"><br><br>

        <label>Date:</label><br>
        <input type="date" name="date" required value="<?php echo isset($date) ? $date : ''; ?>"><br><br>

        <label>Time:</label><br>
        <input type="time" name="time" required value="<?php echo isset($time) ? $time : ''; ?>"><br><br>

        <label>Location:</label><br>
        <input type="text" name="location" required value="<?php echo isset($location) ? $location : ''; ?>"><br><br>

        <label>Description:</label><br>
        <textarea name="description" required><?php echo isset($description) ? $description : ''; ?></textarea><br><br>

        <label>Max Participants:</label><br>
        <input type="number" name="max_participants" required value="<?php echo isset($max_participants) ? $max_participants : ''; ?>"><br><br>

        <button type="submit">Create Event</button>
    </form>
</body>
</html>
