<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

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

// Fetch upcoming courses
try {
    $query = "SELECT * FROM courses 
              WHERE start_date > NOW() 
              ORDER BY start_date ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $upcoming_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching upcoming courses: " . $e->getMessage();
    exit;
}

// Fetch upcoming announcements
try {
    $query = "SELECT * FROM announcements 
              WHERE start_date > NOW() 
              ORDER BY start_date ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $upcoming_announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching announcements: " . $e->getMessage();
    exit;
}
$search_results = [];
$search_source = '';

if (isset($_GET['search_term'])) {
    $search_term = '%' . $_GET['search_term'] . '%';

    // Search in enrolled courses
    try {
        $query = "SELECT c.* FROM courses c 
                  JOIN enrollments e ON c.id = e.course_id 
                  WHERE e.user_id = ? AND (c.course_name LIKE ? OR c.description LIKE ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$_SESSION['user_id'], $search_term, $search_term]);
        $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($search_results)) {
            $search_source = 'My Courses';
        }
    } catch (PDOException $e) {
        echo "Error searching in enrolled courses: " . $e->getMessage();
    }

    // If no results, search upcoming courses
    if (empty($search_results)) {
        try {
            $query = "SELECT * FROM courses 
                      WHERE (course_name LIKE ? OR description LIKE ?) AND start_date > NOW()";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$search_term, $search_term]);
            $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($search_results)) {
                $search_source = 'Upcoming Courses';
            }
        } catch (PDOException $e) {
            echo "Error searching in upcoming courses: " . $e->getMessage();
        }
    }

    // If still no results, search announcements
    if (empty($search_results)) {
        try {
            $query = "SELECT * FROM announcements 
                      WHERE (course_name LIKE ? OR description LIKE ?) AND start_date > NOW()";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$search_term, $search_term]);
            $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($search_results)) {
                $search_source = 'Upcoming Announcements';
            }
        } catch (PDOException $e) {
            echo "Error searching announcements: " . $e->getMessage();
        }
    }
}

// Mock data for the purpose of this example
$project_title = "ScholarNest Online Learning Platform";
$introduction = "This project aims to develop a collaborative online learning platform for students and instructors. The platform will allow users to manage courses, quizzes, and communication effectively.";
$objectives = [
    "Develop a user-friendly interface for both students and instructors.",
    "Implement secure authentication and enrollment processes.",
    "Create a robust backend for managing course content and user data."
];
$key_findings = "The platform successfully integrated course management, user authentication, and real-time communication features.";
$methodology = "The project followed Agile development methodology, with iterative sprints focusing on front-end development, back-end integration, and user testing.";
$conclusions = "The ScholarNest platform met the key objectives and provides a solid foundation for future enhancements.";
$recommendations = "Future iterations could include mobile application support and advanced analytics features for instructors.";
$scope_limitations = "The project scope was limited to desktop web applications, and time constraints prevented the inclusion of mobile compatibility.";
$contact_info = "For more information, please contact the development team at scholar@nest.com.";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScholarNest Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <img src="images/logo.png" alt="logo"><h2>ScholarNest</h2>
            <ul>
                <li><a href="#overview">Overview</a></li>
                <li><a href="#courses">My Courses</a></li>
                <li><a href="#enroll">Enroll in New Courses</a></li>
                <li><a href="#announcements">Announcements</a></li>
                <li><a href="#profile">Profile</a></li>
                <li><a href="#settings">Settings</a></li>
                <li><a href="#" id="logoutBtn">Logout</a></li>
    
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <header>
                <h1>Welcome to Your Dashboard, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                <div class="header-right">
                    <form method="GET" action="">
                        <input type="text" name="search_term" placeholder="Search...">
                        <button type="submit">Search</button>
                    </form>
                </div>
            </header>
            <section id="overview" class="content-section">
                <h2>Overview</h2>
                <div class="overview-grid">
                    <div class="overview-item">
                        <h3>Title</h3>
                        <p><?php echo htmlspecialchars($project_title); ?></p>
                    </div>
                    <div class="overview-item">
                        <h3>Introduction</h3>
                        <p><?php echo htmlspecialchars($introduction); ?></p>
                    </div>
                    <div class="overview-item">
                        <h3>Objectives</h3>
                        <ul>
                            <?php foreach ($objectives as $objective): ?>
                                <li><?php echo htmlspecialchars($objective); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="overview-item">
                        <h3>Key Findings</h3>
                        <p><?php echo htmlspecialchars($key_findings); ?></p>
                    </div>
                    <div class="overview-item">
                        <h3>Methodology</h3>
                        <p><?php echo htmlspecialchars($methodology); ?></p>
                    </div>
                    <div class="overview-item">
                        <h3>Conclusions</h3>
                        <p><?php echo htmlspecialchars($conclusions); ?></p>
                    </div>
                    <div class="overview-item">
                        <h3>Recommendations</h3>
                        <p><?php echo htmlspecialchars($recommendations); ?></p>
                    </div>
                    <div class="overview-item">
                        <h3>Scope & Limitations</h3>
                        <p><?php echo htmlspecialchars($scope_limitations); ?></p>
                    </div>
                </div>

                <?php if (!empty($search_results)): ?>
                    <h3>Search Results from <?php echo $search_source; ?>:</h3>
                    <div class="search-results">
                        <?php foreach ($search_results as $result): ?>
                            <div class="search-item">
                                <h4><?php echo htmlspecialchars($result['course_name'] ?? $result['announcement_name']); ?></h4>
                                <p><?php echo htmlspecialchars($result['description']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <section id="courses" class="content-section">
                <h2>My Courses</h2>
                <div class="course-list">
                    <?php if (!empty($enrolled_courses)): ?>
                        <?php foreach ($enrolled_courses as $course): ?>
                            <div class="course-item">
                                <h3><?php echo htmlspecialchars($course['course_name']); ?></h3>
                                <p><?php echo htmlspecialchars($course['description']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No courses found matching your search.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section id="enroll" class="content-section">
                <h2>Enroll in New Courses</h2>
                <div class="course-list">
                    <?php if (!empty($available_courses)): ?>
                        <?php foreach ($available_courses as $course): ?>
                            <div class="course-item">
                                <h3><?php echo htmlspecialchars($course['course_name']); ?></h3>
                                <p><?php echo htmlspecialchars($course['description']); ?></p>
                                <form action="enroll.php" method="POST">
                                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                    <button id="EnrollBtn" type="submit">Enroll</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No available courses to enroll in.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section id="announcements" class="content-section">
                <h2>Announcements</h2>
                
                <!-- Add Announcement Button -->
                <button id="announcementBtn">Add Announcement</button>

                <!-- Form for Posting Announcement (Initially Hidden) -->
                <form id="announcementForm" action="upload_announcement.php" method="POST" style="display:none;">
                    <label for="course_name">Course Name:</label>
                    <input type="text" id="course_name" name="course_name" required><br>

                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea><br>

                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" required><br>

                    <button type="submit">Post Announcement</button>
                </form>

                <!-- Display Announcements -->
                <div class="announcement-list">
                    <?php if (!empty($upcoming_announcements)): ?>
                        <?php foreach ($upcoming_announcements as $announcement): ?>
                            <div class="announcement-item" id="announcement_<?php echo htmlspecialchars($announcement['id']); ?>">
                                <h3><?php echo htmlspecialchars($announcement['course_name']); ?></h3>
                                <p><?php echo htmlspecialchars($announcement['description']); ?></p>
                                <p><strong>Start Date:</strong> <?php echo htmlspecialchars($announcement['start_date']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No announcements found.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section id="profile" class="content-section">
                <h2>Profile</h2>
                <div id="profile-container">
                    <div class="profile-image">
                        <img src="images/profile.jpg" alt="Profile Picture" />
                    </div>
                    <div class="profile-details">
                        <h3><?php echo htmlspecialchars($_SESSION['username']); ?></h3>
                        <p>Email: <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                        <p>Joined: <?php echo htmlspecialchars(date('F j, Y', strtotime($_SESSION['join_date']))); ?></p>
                        <p>Role: <?php echo htmlspecialchars($_SESSION['role']); ?></p>
                        <!-- Add more user details as needed -->
                    </div>
                    <div class="profile-actions">
                        <a href="edit_profile.php" class="button">Edit Profile</a>
                        <a href="change_password.php" class="button">Change Password</a>
                        <!-- Add more actions as needed -->
                    </div>
                </div>
            </section>

            <section id="settings" class="content-section">
                <h2>Settings</h2>
                <p>Adjust your preferences and account settings.</p>

            </section>
        </div>
    </div>
    <!-- Footer HTML -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section about">
                <h2>ScholarNest</h2>
                <p>
                    ScholarNest is a collaborative online learning platform dedicated to enhancing the educational experience for students and instructors. Join us to explore and grow in your academic journey.
                </p>
                <div class="socials">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>

            <div class="footer-section links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#overview">Overview</a></li>
                    <li><a href="#courses">My Courses</a></li>
                    <li><a href="#enroll">Enroll</a></li>
                    <li><a href="#announcements">Announcements</a></li>
                    <li><a href="#profile">Profile</a></li>
                </ul>
            </div>

            <div class="footer-section contact">
                <h3>Contact Us</h3>
                <p>Email: <a href="mailto:scholarnest@gmail.com">scholarnest@gmail.com</a></p>
                <p>Phone: 0416668152</p>
                <p>Address: WentWorth institute of higher education</p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2024 ScholarNest. All rights reserved.</p>
        </div>
    </footer>

    <script src="script.js" defer></script>
</body>
</html>