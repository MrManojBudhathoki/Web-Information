<?php
session_start();
include 'config.php';  // Your database connection script

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the JSON input and decode it
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate and sanitize the input
    if (isset($input['id'])) {
        $announcement_id = intval($input['id']);

        // Prepare the delete query
        try {
            $query = "DELETE FROM announcements WHERE id = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$announcement_id]);

            // Check if the row was successfully deleted
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Announcement deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Announcement not found']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error deleting announcement: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>