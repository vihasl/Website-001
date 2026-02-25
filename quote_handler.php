<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_type = $_POST['project_type'];
    $budget_range = $_POST['budget_range'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO quote_requests (project_type, budget_range, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $project_type, $budget_range, $description);

    if ($stmt->execute()) {
        echo "Quote request sent successfully! Redirecting...";
        header("Location: index.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>