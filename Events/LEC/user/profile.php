<?php
session_start();
include('../includes/functions.php');
include('../includes/db.php');
checkLogin();

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();

$registrations_query = "
    SELECT e.id, e.name, e.date, e.time, e.location, e.description, e.status
    FROM registrations r
    JOIN events e ON r.event_id = e.id
    WHERE r.user_id = ?
    ORDER BY e.date DESC";
$stmt = $conn->prepare($registrations_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$registrations_result = $stmt->get_result();

$total_events = $upcoming_events = $past_events = 0;

if ($registrations_result) {
    $total_events = $registrations_result->num_rows;
    
    while ($row = $registrations_result->fetch_assoc()) {
        if (strtotime($row['date']) > time()) {
            $upcoming_events++;
        } else {
            $past_events++;
        }
    }
    
    $registrations_result->data_seek(0);
} else {
    echo "Error: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile | Event Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen font-sans flex flex-col">
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
                <h2 class="text-3xl font-bold mb-6 text-gray-800">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <p class="flex items-center text-gray-600">
                            <i class="fas fa-envelope mr-2 text-blue-500"></i>
                            <span class="font-semibold mr-2">Email:</span> <?php echo htmlspecialchars($user['email']); ?>
                        </p>
                        <a href="edit_profile.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-full transition duration-300">
                            <i class="fas fa-user-edit mr-2"></i>Edit Profile
                        </a>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <h3 class="text-xl font-semibold mb-4 text-gray-800">Your Event Statistics</h3>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div class="bg-blue-100 p-3 rounded-lg">
                                <p class="text-2xl font-bold text-blue-600"><?php echo $total_events; ?></p>
                                <p class="text-sm text-gray-600">Total Events</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-lg">
                                <p class="text-2xl font-bold text-green-600"><?php echo $upcoming_events; ?></p>
                                <p class="text-sm text-gray-600">Upcoming</p>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-lg">
                                <p class="text-2xl font-bold text-yellow-600"><?php echo $past_events; ?></p>
                                <p class="text-sm text-gray-600">Past Events</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12">
            <h3 class="text-2xl font-bold mb-6 text-gray-800">Your Registered Events</h3>
            <?php if ($registrations_result && $registrations_result->num_rows > 0): ?>
                <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
                    <table class="min-w-full">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Event Name</th>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date & Time</th>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Location</th>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while($registration = $registrations_result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 px-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($registration['name']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo substr(htmlspecialchars($registration['description']), 0, 50) . '...'; ?></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="text-sm text-gray-900"><?php echo date('M d, Y', strtotime($registration['date'])); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo date('h:i A', strtotime($registration['time'])); ?></div>
                                </td>
                                <td class="py-4 px-4 text-sm text-gray- 500">
                                    <?php echo htmlspecialchars($registration['location']); ?>
                                </td>
                                <td class="py-4 px-4 text-sm">
                                    <?php if ($registration['status'] == 'open'): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Open
                                        </span>
                                    <?php elseif ($registration['status'] == 'closed'): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Closed
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            <?php echo htmlspecialchars($registration['status']); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4 text-sm text-gray-500">
                                    <div class="flex space-x-2">
                                        <?php if (strtotime($registration['date']) > time()): ?>
                                            <a href="cancel_registration.php?event_id=<?php echo $registration['id']; ?>" 
                                               class="text-red-600 hover:text-red-800 transition duration-300"
                                               onclick="return confirm('Are you sure you want to cancel this registration?');">
                                                Cancel
                                            </a>
                                        <?php endif; ?>
                                        <a href="event_details.php?id=<?php echo $registration['id']; ?>" 
                                           class="text-blue-600 hover:text-blue-800 transition duration-300">
                                            Details
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-600">You haven't registered for any events yet.</p>
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