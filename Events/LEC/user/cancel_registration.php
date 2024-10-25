<?php
session_start();
include('../includes/functions.php');
include('../includes/db.php');
checkLogin();

if (!isset($_GET['event_id']) || !is_numeric($_GET['event_id'])) {
    header('Location: profile.php');
    exit();
}

$event_id = intval($_GET['event_id']);
$user_id = $_SESSION['user_id'];

$check_registration_query = "
    SELECT * FROM registrations 
    WHERE user_id = ? AND event_id = ?";
$stmt = $conn->prepare($check_registration_query);
$stmt->bind_param('ii', $user_id, $event_id);
$stmt->execute();
$registration_result = $stmt->get_result();

if ($registration_result->num_rows > 0) {
    $delete_registration_query = "
        DELETE FROM registrations 
        WHERE user_id = ? AND event_id = ?";
    $delete_stmt = $conn->prepare($delete_registration_query);
    $delete_stmt->bind_param('ii', $user_id, $event_id);

    if ($delete_stmt->execute()) {
        header('Location: profile.php?msg=cancel_success');
        exit();
    } else {
        header('Location: profile.php?msg=cancel_error');
        exit();
    }
} else {
    header('Location: profile.php?msg=no_registration');
    exit();
}
?>
