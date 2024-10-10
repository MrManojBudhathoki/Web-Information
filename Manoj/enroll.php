<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];
    $user_id = $_SESSION['user_id'];

    try {
        $query = "INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$user_id, $course_id]);

        header("Location: dashboard.php#courses");
        exit;
    } catch (PDOException $e) {
        echo "Error enrolling in course: " . $e->getMessage();
        exit;
    }
} else {
    header("Location: dashboard.php#enroll");
    exit;
}
?>