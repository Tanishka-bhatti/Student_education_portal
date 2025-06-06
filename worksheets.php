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
        h1 { color: #0277bd; margin-bottom: 25px; }
        .level-container { border: 1px solid #0277bd; border-radius: 8px; padding: 15px; margin-bottom: 20px; background: #b3e5fc; }
        .file-button {
            display: inline-block;
            margin-right: 12px;
            margin-bottom: 10px;
            padding: 10px 18px;
            background: #0277bd;
            color: white;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .file-button:hover {
            background: #004f7a;
            color: #ccefff;
        }
        .nofile {
            color: #a00;
            font-style: italic;
        }
    </style>
</head>
<body>

<h1>Levels for <?php echo htmlspecialchars($subject['name']); ?></h1>

<?php
for ($level = 1; $level <= $levels_count; $level++) {
    echo "<div class='level-container'>";
    echo "<h2>Level $level</h2>";
    
    $uploadDir = "uploads/subject_$subject_id/level_$level/";
    $files = ['question_worksheet', 'answer_key', 'other_file'];
    foreach ($files as $fileKey) {
        $filePaths = glob($uploadDir . $fileKey . '.*'); // any extension

        if (!empty($filePaths)) {
            foreach ($filePaths as $filePath) {
                $fileName = basename($filePath);
                // Use relative path for href
                echo "<a href='$filePath' target='_blank' class='file-button' rel='noopener noreferrer'>".ucwords(str_replace('_',' ',$fileKey))."</a>";
            }
        } else {
            echo "<div class='nofile'>" . ucwords(str_replace('_',' ',$fileKey)) . " not uploaded yet.</div>";
        }
    }
    
    echo "</div>";
}
?>

</body>
</html>
