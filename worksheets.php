<?php
session_start();
include 'config.php'; // Make sure this sets up $conn as mysqli connection

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get subject_id from GET, validate it
$subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;
if ($subject_id <= 0) {
    echo "Invalid subject.";
    exit;
}

// Get subject name for display
$stmt = $conn->prepare("SELECT name FROM subjects WHERE id = ?");
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "Subject not found.";
    exit;
}
$subject = $result->fetch_assoc();
$stmt->close();

// Define number of levels (previously worksheets)
$levels_count = 10;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Levels for <?php echo htmlspecialchars($subject['name']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #e0f7fa; }
        h1 { color: #0277bd; }
        .level-container { border: 1px solid #0277bd; border-radius: 8px; padding: 15px; margin-bottom: 20px; background: #b3e5fc; }
        .file { background: #81d4fa; padding: 10px; margin: 8px 0; border-radius: 5px; cursor: pointer; }
        .file:hover { background: #4fc3f7; color: white; }
    </style>
</head>
<body>

<h1>Levels for <?php echo htmlspecialchars($subject['name']); ?></h1>

<?php
for ($level = 1; $level <= $levels_count; $level++) {
    echo "<div class='level-container'>";
    echo "<h2>Level $level</h2>";
    
    echo "<div class='file'>Question Worksheet</div>";
    echo "<div class='file'>Answer Key</div>";
    echo "<div class='file'>Other File</div>";
    
    echo "</div>";
}
?>

</body>
</html>
