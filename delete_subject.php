<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in.";
    exit;
}

$user_id = $_SESSION['user_id'];
$subject_id = intval($_POST['subject_id'] ?? 0);

if ($subject_id <= 0) {
    echo "Invalid subject ID.";
    exit;
}

$stmt = $conn->prepare("DELETE FROM user_subjects WHERE user_id = ? AND subject_id = ?");
$stmt->bind_param("ii", $user_id, $subject_id);

if ($stmt->execute()) {
    echo "Subject deletion success";
} else {
    echo "Subject deletion failed";
}
$stmt->close();
$conn->close();
?>
