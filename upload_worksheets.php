<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$subjects = [];
$result = $conn->query("SELECT id, name FROM subjects");
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

$uploadMessages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = intval($_POST['subject_id']);
    $level = intval($_POST['level']);

    $uploadDir = "uploads/subject_$subject_id/level_$level/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $files = ['question_worksheet', 'answer_key', 'other_file'];

    foreach ($files as $fileKey) {
        if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === 0) {
            $extension = pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION);
            $targetPath = $uploadDir . $fileKey . '.' . $extension;

            if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetPath)) {
                $uploadMessages[] = "<span style='color:green;'>$fileKey uploaded successfully!</span>";
            } else {
                $uploadMessages[] = "<span style='color:red;'>Failed to upload $fileKey.</span>";
            }
        } else {
            $uploadMessages[] = "<span style='color:orange;'>No file uploaded for $fileKey.</span>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Upload Worksheets</title>
    <style>
        body { font-family: sans-serif; background: #f0f0f0; padding: 20px; }
        form { background: #fff; padding: 20px; border-radius: 10px; max-width: 400px; margin: auto; }
        label, select, input[type="file"], input[type="submit"] { display: block; width: 100%; margin-bottom: 15px; }
        .messages p { margin: 5px 0; }
        a { text-decoration: none; color: #007BFF; }
    </style>
</head>
<body>

<h1>Upload Worksheets</h1>

<form method="POST" enctype="multipart/form-data">
    <label>Subject:</label>
    <select name="subject_id" required>
        <option value="">-- Select Subject --</option>
        <?php foreach ($subjects as $sub): ?>
            <option value="<?= $sub['id'] ?>"><?= htmlspecialchars($sub['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Level:</label>
    <select name="level" required>
        <option value="">-- Select Level --</option>
        <?php for ($i = 1; $i <= 10; $i++): ?>
            <option value="<?= $i ?>">Level <?= $i ?></option>
        <?php endfor; ?>
    </select>

    <label>Question Worksheet:</label>
    <input type="file" name="question_worksheet" required>

    <label>Answer Key:</label>
    <input type="file" name="answer_key" required>

    <label>Other File:</label>
    <input type="file" name="other_file" required>

    <input type="submit" value="Upload">
</form>

<?php if (!empty($uploadMessages)): ?>
<div class="messages">
    <?php foreach ($uploadMessages as $msg): ?>
        <p><?= $msg ?></p>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<a href="dashboard.php">← Back to Dashboard</a>

</body>
</html>
