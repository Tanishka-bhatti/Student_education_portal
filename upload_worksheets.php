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
            $filename = basename($_FILES[$fileKey]['name']);
            $targetPath = $uploadDir . $filename;

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
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e8f4fc;
            padding: 30px;
        }
        h1 {
            color: #0277bd;
            margin-bottom: 25px;
        }
        form {
            background: white;
            padding: 25px;
            border-radius: 15px;
            max-width: 480px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }
        label {
            margin-top: 15px;
            font-weight: 600;
            display: block;
        }
        select, input[type="file"] {
            margin-top: 6px;
            width: 100%;
            padding: 8px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 15px;
        }
        input[type="submit"] {
            margin-top: 25px;
            width: 100%;
            background-color: #3498db;
            color: white;
            border: none;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #2980b9;
        }
        .messages {
            margin-top: 20px;
            font-size: 15px;
        }
        .back-btn {
            margin-top: 30px;
            display: inline-block;
            background: #81d4fa;
            color: #0277bd;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .back-btn:hover {
            background: #4fc3f7;
            color: white;
        }
    </style>
</head>
<body>

<h1>Upload Worksheets</h1>

<form method="POST" enctype="multipart/form-data">
    <label for="subject_id">Select Subject:</label>
    <select name="subject_id" id="subject_id" required>
        <option value="">--Select Subject--</option>
        <?php foreach ($subjects as $sub): ?>
            <option value="<?= htmlspecialchars($sub['id']) ?>"><?= htmlspecialchars($sub['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="level">Select Level:</label>
    <select name="level" id="level" required>
        <option value="">--Select Level--</option>
        <?php for ($i = 1; $i <= 10; $i++): ?>
            <option value="<?= $i ?>">Level <?= $i ?></option>
        <?php endfor; ?>
    </select>

    <label for="question_worksheet">Upload Question Worksheet:</label>
    <input type="file" name="question_worksheet" id="question_worksheet" required>

    <label for="answer_key">Upload Answer Key:</label>
    <input type="file" name="answer_key" id="answer_key" required>

    <label for="other_file">Upload Other File:</label>
    <input type="file" name="other_file" id="other_file" required>

    <input type="submit" value="Upload Files">
</form>

<?php if (!empty($uploadMessages)): ?>
    <div class="messages">
        <?php foreach ($uploadMessages as $msg): ?>
            <p><?= $msg ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

</body>
</html>
