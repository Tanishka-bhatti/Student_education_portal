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

// Check if form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $conn->real_escape_string($_POST['role'] ?? '');
    $subjects = $_POST['subjects'] ?? [];  // array from multi-select

    if (!$name || !$email || !$password || !$role || empty($subjects)) {
        echo "Please fill all required fields and select at least one subject.";
        exit;
    }

    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert into users table
    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$passwordHash', '$role')";

    if ($conn->query($sql) === TRUE) {
        $user_id = $conn->insert_id;

        // Prepare statement for subject registrations
        $stmtSub = $conn->prepare("INSERT INTO user_subjects (user_id, subject_id) VALUES (?, ?)");

        if (!$stmtSub) {
            die("Prepare failed: " . $conn->error);
        }

        // Insert each selected subject
        foreach ($subjects as $sub) {
            $subject_id = (int)$sub; // cast to int for safety
            $stmtSub->bind_param("ii", $user_id, $subject_id);
            $stmtSub->execute();
        }

        echo "Registration successful!";
    } else {
        echo "Error during registration: " . $conn->error;
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>  