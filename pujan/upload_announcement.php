<?php
session_start();
include 'config.php';  // Your database connection script

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input to prevent SQL injection
    $course_name = htmlspecialchars($_POST['course_name']);
    $description = htmlspecialchars($_POST['description']);
    $start_date = htmlspecialchars($_POST['start_date']);

    // Insert announcement into the database
    try {
        $query = "INSERT INTO announcements (course_name, description, start_date, created_at) 
                  VALUES (?, ?, ?, NOW())";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$course_name, $description, $start_date]);

        // Check if the row was successfully inserted
        if ($stmt->rowCount() > 0) {
            // Return a JSON response to update the UI dynamically
            echo json_encode([
                'success' => true,
                'course_name' => $course_name,
                'description' => $description,
                'start_date' => $start_date
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to post announcement']);
        }
    } catch (PDOException $e) {
        // Return JSON error message
        echo json_encode(['success' => false, 'message' => 'Error posting announcement: ' . $e->getMessage()]);
    }
} else {
    // Return an error message if the request method is not POST
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}