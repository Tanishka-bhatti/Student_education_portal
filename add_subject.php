<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$result = $conn->query("SELECT * FROM subjects");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = $_POST['subject_id'];

    $stmt = $conn->prepare("INSERT INTO user_subjects (user_id, subject_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $_SESSION['user_id'], $subject_id);
    $stmt->execute();
    $stmt->close();

    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add Subject</title>
    <style>
        body {
            background-color: #e8f4fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 40px;
            display: flex;
            justify-content: center;
        }
        .container {
            background: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.12);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        h1 {
            color: #0277bd;
            margin-bottom: 30px;
        }
        select {
            width: 100%;
            padding: 12px 15px;
            border-radius: 10px;
            border: 1.5px solid #ccc;
            font-size: 16px;
            margin-bottom: 25px;
            transition: border-color 0.3s ease;
        }
        select:focus {
            border-color: #3498db;
            outline: none;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 12px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }
        button:hover {
            background-color: #2980b9;
        }
        a.back-link {
            display: inline-block;
            margin-top: 25px;
            color: #0277bd;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        a.back-link:hover {
            color: #015f99;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Add Subject</h1>

    <form method="post">
        <select name="subject_id" required>
            <option value="">Select a subject</option>
            <?php while ($row = $result->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Add Subject</button>
    </form>

    <a href="dashboard.php" class="back-link">⬅ Back to Dashboard</a>
</div>

</body>
</html>
