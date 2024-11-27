<?php
include('db.php');

if (isset($_POST['signup'])) {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true); // Ensure uploads/ folder exists
        }
    
        $file_name = time() . "_" . basename($_FILES["profile_picture"]["name"]);
        $target_file = $target_dir . $file_name;
    
        // Move uploaded file
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $profile_picture = $target_file;
        } else {
            echo "<script>alert('File upload failed.');</script>";
            $profile_picture = NULL; // Handle failure case
        }
    } else {
        echo "<script>alert('No file uploaded or upload error.');</script>";
    }
    

    // Insert user into the database
    $sql = "INSERT INTO paypal_user (email, password, profile_picture) VALUES ('$email', '$password', '$profile_picture')";
    if ($conn->query($sql) === TRUE) {
        header("Location: login.php?welcome=true");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .signup-container {
            background-color: white;
            width: 400px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .signup-container img {
            width: 100px;
            margin-bottom: 20px;
        }

        .signup-container h1 {
            font-size: 26px;
            color: #333;
            margin-bottom: 20px;
        }

        .signup-container input {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .signup-container button {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            width: 100%;
            padding: 10px;
            background-color: #0070ba;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .signup-container button:hover {
            background-color: #005fa3;
        }

        .signup-container .link {
            margin-top: 20px;
            font-size: 14px;
        }

        .signup-container .link a {
            color: #0070ba;
            text-decoration: none;
        }

        .signup-container .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <img src="data:image/png;base64,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" alt="PayPal Logo"> <!-- Add your logo image -->
        <h1>Sign Up</h1>
        <form method="POST" enctype="multipart/form-data">
            <input type="email" name="email" placeholder="Email or Mobile Number" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="file" name="profile_picture" accept="image/*">
            <button type="submit" name="signup">Sign Up</button>
        </form>
        <div class="link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>
