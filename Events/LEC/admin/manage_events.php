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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a90e2;
            --error-color: #e74c3c;
            --success-color: #2ecc71;
            --border-color: #e1e1e1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        html, body {
            height: 100%;
            overflow: hidden;
        }

        body {
            background-color: #f5f6fa;
            display: flex;
            flex-direction: column;
        }

        .container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            overflow-y: auto;
            max-height: calc(100vh - 2rem);
        }

        .page-title {
            color: #2c3e50;
            margin-bottom: 1rem;
            text-align: center;
            font-size: 1.8rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.25rem;
            color: #34495e;
            font-weight: 500;
        }

        input[type="text"],
        input[type="date"],
        input[type="time"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 0.9rem;
            transition: border-color 0.3s ease;
        }

        textarea {
            min-height: 80px;
            resize: vertical;
        }

        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #357abd;
        }

        .error-message, .success-message {
            padding: 0.5rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0.5rem;
            }

            .card {
                padding: 1rem;
            }

            .page-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1 class="page-title"><i class="fas fa-calendar-plus"></i> Create New Event</h1>
            <p>
                <a href="dashboard.php" class="btn btn-primary"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="../logout.php" class="btn btn-primary"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </p>

            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <div class="form-group">
                    <label for="name"><i class="fas fa-tag"></i> Event Name</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="date"><i class="far fa-calendar"></i> Date</label>
                    <input type="date" id="date" name="date" required>
                </div>

                <div class="form-group">
                    <label for="time"><i class="far fa-clock"></i> Time</label>
                    <input type="time" id="time" name="time" required>
                </div>

                <div class="form-group">
                    <label for="location"><i class="fas fa-map-marker-alt"></i> Location</label>
                    <input type="text" id="location" name="location" required>
                </div>

                <div class="form-group">
                    <label for="description"><i class="fas fa-info-circle"></i> Description</label>
                    <textarea id="description" name="description" required></textarea>
                </div>

                <div class="form-group">
                    <label for="max_participants"><i class="fas fa-users"></i> Max Participants</label>
                    <input type="number" id="max_participants" name="max_participants" required>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Create Event</button>
            </form>
        </div>
    </div>
</body>
</html>