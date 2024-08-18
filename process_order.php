<?php
// Database configuration
$servername = "localhost";
$username = "roomy_database";  // Replace with your database username
$password = "giogio";  // Replace with your database password
$dbname = "my_database";  // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate and sanitize input
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $pass = $_POST["password"];
    $confirm_pass = $_POST["confirm_password"];

    // Validate input
    if (empty($user) || empty($email) || empty($pass) || empty($confirm_pass)) {
        echo "Please fill in all fields.";
    } elseif ($pass !== $confirm_pass) {
        echo "Passwords do not match.";
    } else {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $user, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "Username or email already taken.";
        } else {
            // Hash the password
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $user, $email, $hashed_password);

            if ($stmt->execute()) {
                echo "Registration successful!";
            } else {
                echo "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>
