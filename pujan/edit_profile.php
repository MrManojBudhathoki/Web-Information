<?php
// Fetch user details from the database and populate the form fields
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Edit Profile</h2>
        <form action="update_profile.php" method="post">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="role">Role:</label>
            <input type="text" id="role" name="role" value="<?php echo htmlspecialchars($user['role']); ?>" required>

            <!-- Add more fields as needed -->

            <button type="submit" class="button">Save Changes</button>
        </form>
    </div>
</body>
</html>