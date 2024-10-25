<?php
session_start();
include('../includes/functions.php');
include('../includes/db.php');
checkLogin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: events.php');
    exit();
}

$event_id = intval($_GET['id']);
$errors = [];
$success = '';
$event = [];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT e.*, COUNT(r.id) as registered_count FROM events e LEFT JOIN registrations r ON e.id = r.event_id WHERE e.id = ? GROUP BY e.id");
$stmt->bind_param('i', $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    header('Location: events.php');
    exit();
}

$check_stmt = $conn->prepare("SELECT * FROM registrations WHERE user_id = ? AND event_id = ?");
$check_stmt->bind_param('ii', $user_id, $event_id);
$check_stmt->execute();
$is_registered = $check_stmt->get_result()->num_rows > 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($is_registered) {
        $errors[] = "You are already registered for this event.";
    } elseif ($event['registered_count'] >= $event['max_participants']) {
        $errors[] = "This event is already full.";
    } else {
        $register_stmt = $conn->prepare("INSERT INTO registrations (user_id, event_id) VALUES (?, ?)");
        $register_stmt->bind_param('ii', $user_id, $event_id);
        
        if ($register_stmt->execute()) {
            $success = "Registration successful!";
            $is_registered = true;
        } else {
            $errors[] = "Failed to register for the event. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details | EventHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body class="flex flex-col min-h-screen bg-gray-100 font-sans">
    <nav class="bg-gradient-to-r from-blue-600 to-blue-800 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="events.php" class="text-2xl font-bold">Event<span class="text-yellow-300">Hub</span></a>
            <div class="space-x-4">
                <a href="events.php" class="hover:text-yellow-300 transition duration-300">Events</a>
                <a href="../logout.php" class="hover:text-yellow-300 transition duration-300">Logout</a>
            </div>
        </div>
    </nav>

    <main class="flex-grow container mx-auto mt-8 px-4 mb-12">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="p-6 md:p-8">
                <h2 class="text-3xl font-bold mb-6 text-gray-800"><?php echo htmlspecialchars($event['name']); ?></h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <p class="flex items-center text-gray-600">
                            <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                            <span class="font-semibold mr-2">Date:</span> <?php echo date('F j, Y', strtotime($event['date'])); ?>
                        </p>
                        <p class="flex items-center text-gray-600">
                            <i class="fas fa-clock mr-2 text-blue-500"></i>
                            <span class="font-semibold mr-2">Time:</span> <?php echo date('g:i A', strtotime($event['time'])); ?>
                        </p>
                        <p class="flex items-center text-gray-600">
                            <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>
                            <span class="font-semibold mr-2">Location:</span> <?php echo htmlspecialchars($event['location']); ?>
                        </p>
                    </div>
                    <div class="space-y-4">
                        <p class="flex items-center text-gray-600">
                            <i class="fas fa-users mr-2 text-blue-500"></i>
                            <span class="font-semibold mr-2">Participants:</span> 
                            <?php echo $event['registered_count']; ?> / <?php echo $event['max_participants']; ?>
                        </p>
                        <p class="flex items-center text-gray-600">
                            <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                            <span class="font-semibold mr-2">Status:</span> 
                            <?php if ($event['status'] == 'open'): ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Open
                                </span>
                            <?php else: ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Closed
                                </span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-xl font-semibold mb-2 text-gray-800">Description</h3>
                    <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                </div>

                <?php if (!empty($success)): ?>
                    <div class="mt-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                        <p class="font-bold">Success</p>
                        <p><?php echo $success; ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="mt-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                        <p class="font-bold">Error</p>
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="mt-8">
                    <?php if ($is_registered): ?>
                        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                            <p class="font-bold">You're Registered!</p>
                            <p>You have successfully registered for this event.</p>
                        </ div>
                    <?php elseif ($event['status'] == 'closed'): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                            <p class="font-bold">Registration Closed</p>
                            <p>Registration for this event is closed.</p>
                        </div>
                    <?php elseif ($event['registered_count'] >= $event['max_participants']): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                            <p class="font-bold">Event Full</p>
                            <p>This event is already full. Please try another event.</p>
                        </div>
                    <?php else: ?>
                        <form method="POST" action="" class="flex justify-center">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300">
                                Register
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
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