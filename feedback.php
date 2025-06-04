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

// If form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedback = $_POST['feedback'];

    $stmt = $conn->prepare("INSERT INTO feedback (user_id, subject_id, feedback) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $_SESSION['user_id'], $subject_id, $feedback);
    $stmt->execute();
    $stmt->close();

    echo "<p>✅ Feedback submitted!</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($subject_name); ?> - Feedback</title>
</head>
<body>
    <h1>Feedback for <?php echo htmlspecialchars($subject_name); ?></h1>

    <form method="post">
        <textarea name="feedback" rows="5" cols="50" placeholder="Write your feedback here..." required></textarea><br>
        <button type="submit">Submit Feedback</button>
    </form>

    <a href="dashboard.php">⬅ Back to Dashboard</a>
</body>
</html>
