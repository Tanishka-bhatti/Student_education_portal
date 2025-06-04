<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Safely get user_name from session, fallback to 'User' if not set
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard - <?php echo htmlspecialchars($user_name); ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e8f4fc;
            padding: 40px;
            margin: 0;
            position: relative;
        }
        .logout-btn {
            position: absolute;
            top: 30px;
            right: 30px;
            background-color: #e74c3c;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            user-select: none;
        }
        .logout-btn:hover {
            background-color: #c0392b;
        }

        .welcome-message {
            font-size: 48px;
            font-weight: 700;
            color: #0277bd;
            margin-bottom: 40px;
            text-align: center;
        }
        .button-container {
            max-width: 600px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }
        .dashboard-button {
            background-color: #3498db;
            color: white;
            padding: 30px 40px;
            border-radius: 20px;
            text-align: center;
            cursor: pointer;
            flex: 1 1 250px;
            max-width: 250px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            transition: background-color 0.3s ease, transform 0.2s ease;
            user-select: none;
        }
        .dashboard-button:hover {
            background-color: #2980b9;
            transform: translateY(-5px);
        }
        .dashboard-button .icon {
            font-size: 60px;
            margin-bottom: 15px;
            display: block;
        }
        .dashboard-button span {
            display: block;
            font-size: 22px;
            font-weight: 600;
        }
        @media(max-width: 480px) {
            .welcome-message {
                font-size: 36px;
            }
            .dashboard-button {
                max-width: 100%;
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>

<a href="logout.php" class="logout-btn">Logout</a>

<div class="welcome-message">Welcome, <?php echo htmlspecialchars($user_name); ?>!</div>

<div class="button-container">
    <div class="dashboard-button" onclick="location.href='upload_worksheets.php'">
        <span class="icon">📤</span>
        <span>Upload Worksheet</span>
    </div>

    <div class="dashboard-button" onclick="location.href='add_subject.php'">
        <span class="icon">➕</span>
        <span>Add Subject</span>
    </div>

    <div class="dashboard-button" onclick="location.href='subjects.php'">
        <span class="icon">📚</span>
        <span>Subjects</span>
    </div>
</div>

</body>
</html>
