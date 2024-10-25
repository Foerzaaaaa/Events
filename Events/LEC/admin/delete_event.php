<?php
session_start();
include('../includes/functions.php');
include('../includes/db.php');
checkLogin();

if ($_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

if (isset($_GET['id'])) {
    $event_id = intval($_GET['id']);
    
    if ($event_id > 0) {
        $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $stmt->bind_param('i', $event_id);
        
        if ($stmt->execute()) {
            $_SESSION['delete_status'] = "success";
            $_SESSION['delete_message'] = "Event berhasil dihapus!";
        } else {
            $_SESSION['delete_status'] = "error";
            $_SESSION['delete_message'] = "Gagal menghapus event. Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['delete_status'] = "error";
        $_SESSION['delete_message'] = "ID event tidak valid.";
    }
} else {
    $_SESSION['delete_status'] = "error";
    $_SESSION['delete_message'] = "ID event tidak ditemukan.";
}

header('Location: dashboard.php');
exit();
?>