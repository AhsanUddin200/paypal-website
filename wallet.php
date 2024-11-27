<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT email, balance, profile_picture FROM paypal_user WHERE id='$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wallet</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .wallet-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        .wallet-container img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 2px solid #0070ba;
            margin-bottom: 15px;
        }

        .wallet-container h1 {
            font-size: 24px;
            color: #0070ba;
            margin: 0;
        }

        .wallet-container p {
            margin: 10px 0;
            font-size: 16px;
            color: #333;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #0070ba;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .back-button:hover {
            background-color: #005fa3;
        }
    </style>
</head>
<body>
    <div class="wallet-container">
        <img src="<?php echo $user['profile_picture'] ? htmlspecialchars($user['profile_picture']) : 'default-profile.jpg'; ?>" alt="Profile Picture">
        <p><?php echo htmlspecialchars($user['email']); ?></p>
        <h1>Balance: $<?php echo number_format($user['balance'], 2); ?></h1>
        <a href="dashboard.php" class="back-button">Back to Dashboard</a>
    </div>
</body>
</html>
