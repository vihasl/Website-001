<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $service = $_POST['service'];
    $details = $_POST['details'];

    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, service, details) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $service, $details);

    if ($stmt->execute()) {
        echo "Message sent successfully! Redirecting...";
        header("Location: index.php#contact");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>