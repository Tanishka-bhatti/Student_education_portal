<?php
// DB connection variables
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "info_portal";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $conn->real_escape_string($_POST['role'] ?? '');
    $subjects = $_POST['subjects'] ?? [];  // array from multi-select

    if (!$name || !$email || !$password || !$role || empty($subjects)) {
        $error = "Please fill all required fields and select at least one subject.";
    } else {
        // Check for existing email
        $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $checkEmail->store_result();

        if ($checkEmail->num_rows > 0) {
            $error = "Email already registered. Please use a different email.";
        } else {
            // Hash the password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insert into users table
            $stmtUser = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmtUser->bind_param("ssss", $name, $email, $passwordHash, $role);

            if ($stmtUser->execute()) {
                $user_id = $stmtUser->insert_id;

                // Prepare subject insert statement
                $stmtSub = $conn->prepare("INSERT INTO user_subjects (user_id, subject_id) VALUES (?, ?)");

                foreach ($subjects as $sub) {
                    $subject_id = (int)$sub;
                    $stmtSub->bind_param("ii", $user_id, $subject_id);
                    $stmtSub->execute();
                }

                $stmtSub->close();
                $stmtUser->close();

                header("Location: login.php?message=registered");
                exit();
            } else {
                $error = "Error during registration: " . $conn->error;
            }
        }

        $checkEmail->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Registration - Info Portal</title>
  <style>
    body {
      background: linear-gradient(to right, #87ceeb, #b0e0e6);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #333;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .container {
      background: white;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      max-width: 500px;
      width: 100%;
    }
    h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #006994;
    }
    label {
      display: block;
      margin: 12px 0 6px;
      font-weight: 600;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"],
    select {
      width: 100%;
      padding: 10px 12px;
      border: 2px solid #87ceeb;
      border-radius: 6px;
      font-size: 1rem;
      transition: border-color 0.3s ease;
    }
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus,
    select:focus {
      border-color: #006994;
      outline: none;
    }
    select[multiple] {
      height: 120px;
    }
    button {
      margin-top: 25px;
      width: 100%;
      padding: 12px;
      font-size: 1.1rem;
      background-color: #006994;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }
    button:hover {
      background-color: #004f66;
    }
    .note {
      font-size: 0.85rem;
      color: #555;
      margin-top: 5px;
    }
    .error {
      background: #ffe0e0;
      padding: 10px;
      border: 1px solid #ff7777;
      color: #a70000;
      border-radius: 6px;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Register Account</h2>
    <?php if (!empty($error)): ?>
      <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="" method="POST">
      <label for="name">Full Name *</label>
      <input type="text" id="name" name="name" placeholder="Your full name" required />

      <label for="email">Email Address *</label>
      <input type="email" id="email" name="email" placeholder="example@mail.com" required />

      <label for="password">Password *</label>
      <input type="password" id="password" name="password" placeholder="Choose a strong password" required />

      <label for="role">Role *</label>
      <select id="role" name="role" required>
        <option value="" disabled selected>Select role</option>
        <option value="student">Student</option>
        <option value="teacher">Teacher</option>
        <option value="admin">Admin</option>
      </select>

      <label for="subjects">Select Subjects:</label><br>
      <select name="subjects[]" id="subjects" multiple required>
        <option value="1">Mathematics[1]</option>
        <option value="2">Physics[2]</option>
        <option value="3">Chemistry[3]</option>
        <option value="4">Biology[4]</option>
        <option value="5">Computer Science[5]</option>
      </select>

      <div class="note">Hold Ctrl (Cmd on Mac) to select multiple subjects</div>

      <button type="submit">Register</button>
    </form>
  </div>
</body>
</html>
