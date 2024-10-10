<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Initialize search term
$search_term = isset($_GET['search_term']) ? trim($_GET['search_term']) : '';

// Initialize arrays for search results
$search_enrolled_courses = [];
$search_available_courses = [];
$search_upcoming_courses = [];

// Fetch enrolled courses for the logged-in user
try {
    $query = "SELECT c.* FROM courses c 
              JOIN enrollments e ON c.id = e.course_id 
              WHERE e.user_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $enrolled_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching enrolled courses: " . $e->getMessage();
    exit;
}

// If search term is provided, filter enrolled courses
if (!empty($search_term)) {
    foreach ($enrolled_courses as $course) {
        if (stripos($course['course_name'], $search_term) !== false) {
            $search_enrolled_courses[] = $course;
        }
    }
}

// Fetch available courses for enrollment
try {
    $query = "SELECT * FROM courses 
              WHERE id NOT IN (SELECT course_id FROM enrollments WHERE user_id = ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $available_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching available courses: " . $e->getMessage();
    exit;
}

// If search term is provided, filter available courses
if (!empty($search_term)) {
    foreach ($available_courses as $course) {
        if (stripos($course['course_name'], $search_term) !== false) {
            $search_available_courses[] = $course;
        }
    }
}

// Fetch upcoming courses
try {
    $query = "SELECT * FROM courses WHERE start_date > NOW()";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $upcoming_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching upcoming courses: " . $e->getMessage();
    exit;
}

// If search term is provided, filter upcoming courses
if (!empty($search_term)) {
    foreach ($upcoming_courses as $course) {
        if (stripos($course['course_name'], $search_term) !== false) {
            $search_upcoming_courses[] = $course;
        }
    }
}

// Display Enrolled Courses section
echo "<h2>Your Enrolled Courses</h2>";
if (!empty($search_enrolled_courses)) {
    foreach ($search_enrolled_courses as $course) {
        echo "<div class='course'>";
        echo "<h3>" . htmlspecialchars($course['course_name']) . "</h3>";
        echo "</div>";
    }
} else {
    echo "<p>No enrolled courses matching your search term.</p>";
}

// Display Available Courses section
echo "<h2>Available Courses</h2>";
if (!empty($search_available_courses)) {
    foreach ($search_available_courses as $course) {
        echo "<div class='course'>";
        echo "<h3>" . htmlspecialchars($course['course_name']) . "</h3>";
        echo "</div>";
    }
} else {
    echo "<p>No available courses matching your search term.</p>";
}

// Display Upcoming Courses section
echo "<h2>Upcoming Courses</h2>";
if (!empty($search_upcoming_courses)) {
    foreach ($search_upcoming_courses as $course) {
        echo "<div class='course'>";
        echo "<h3>" . htmlspecialchars($course['course_name']) . "</h3>";
        echo "</div>";
    }
} else {
    echo "<p>No upcoming courses matching your search term.</p>";
}
?>
