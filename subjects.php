<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user subjects with their worksheets count or any info
$sql = "
    SELECT s.id, s.name 
    FROM user_subjects us
    JOIN subjects s ON us.subject_id = s.id
    WHERE us.user_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Your Subjects</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e8f4fc;
            padding: 30px;
        }
        h1 {
            color: #0277bd;
            text-align: center;
            margin-bottom: 30px;
        }
        .subject-list {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 20px 30px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.12);
        }
        ul {
            list-style: none;
            padding-left: 0;
        }
        li {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        li:last-child {
            border-bottom: none;
        }
        li:hover {
            background-color: #d0e7fc;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #0277bd;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h1>Your Subjects</h1>

<div class="subject-list">
    <ul>
        <?php if (count($subjects) === 0): ?>
            <li>No subjects added yet.</li>
        <?php else: ?>
            <?php foreach ($subjects as $sub): ?>
                <li onclick="location.href='worksheets.php?subject_id=<?php echo $sub['id']; ?>'">
                    <?php echo htmlspecialchars($sub['name']); ?>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

<a href="dashboard.php" class="back-link">⬅ Back to Dashboard</a>

</body>
</html>
