<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['subject_id'])) {
    die("Subject not selected.");
}

$subject_id = $_GET['subject_id'];

// Fetch subject name
$stmt = $conn->prepare("SELECT name FROM subjects WHERE id = ?");
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$stmt->bind_result($subject_name);
$stmt->fetch();
$stmt->close();

?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($subject_name); ?> - Notes</title>
</head>
<body>
    <h1>Notes for <?php echo htmlspecialchars($subject_name); ?></h1>
    <p>📝 Here you can display subject-specific notes fetched from a notes table later or hardcode for now.</p>
    <a href="dashboard.php">⬅ Back to Dashboard</a>
</body>
</html>
