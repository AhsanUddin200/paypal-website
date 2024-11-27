<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT * FROM paypal_user WHERE id='$user_id'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "Error: User not found or query failed.";
    exit();
}

// Update profile logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = $_POST['name'];
    $profile_picture = $user['profile_picture']; // Keep the old profile picture if no new one is uploaded

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/";
        $file_name = time() . "_" . basename($_FILES["profile_picture"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $profile_picture = $target_file;
        }
    }

    // Update user details in the database
    $update_sql = "UPDATE paypal_user SET name='$new_name', profile_picture='$profile_picture' WHERE id='$user_id'";
    if ($conn->query($update_sql) === TRUE) {
        header("Location: settings.php?success=true");
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 400px;
            text-align: center;
        }

        .container h1 {
            font-size: 26px;
            color: #0070ba;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 16px;
            color: #555;
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .form-group input[type="text"] {
            width: 100%;
            padding: 12px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }

        .form-group input[type="file"] {
            margin-top: 10px;
        }

        .form-group img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 20px auto;
            display: block;
            border: 3px solid #0070ba;
        }

        .form-group button {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background-color: #0070ba;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .form-group button:hover {
            background-color: #005fa3;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #0070ba;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background-color: #005fa3;
        }

        .success-message {
            color: green;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .file-upload-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
        }

        .file-upload-wrapper input[type="file"] {
            font-size: 14px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #ccc;
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Update Profile</h1>

        <?php if (isset($_GET['success'])): ?>
            <p class="success-message">Profile updated successfully!</p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="profile_picture">Profile Picture</label>
                <?php if ($user['profile_picture']): ?>
                    <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
                <?php else: ?>
                    <img src="default-profile.jpg" alt="Default Profile Picture">
                <?php endif; ?>
                <div class="file-upload-wrapper">
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                </div>
            </div>

            <div class="form-group">
                <button type="submit">Save Changes</button>
            </div>
        </form>

        <a href="dashboard.php" class="back-button">Back to Dashboard</a>
    </div>
</body>
</html>
