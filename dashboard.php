<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT * FROM paypal_user WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Fetch transaction history
$transactions_sql = "SELECT t.amount, t.purpose, u.email AS other_party, 
                     CASE 
                         WHEN t.sender_id = '$user_id' THEN 'Sent'
                         WHEN t.recipient_id = '$user_id' THEN 'Received'
                     END AS type, 
                     t.timestamp 
                     FROM paypal_transactions t
                     LEFT JOIN paypal_user u ON 
                     (t.sender_id = u.id AND t.recipient_id != '$user_id') OR 
                     (t.recipient_id = u.id AND t.sender_id != '$user_id')
                     WHERE t.sender_id = '$user_id' OR t.recipient_id = '$user_id'
                     ORDER BY t.timestamp DESC";

$transactions = $conn->query($transactions_sql);

if (isset($_POST['send_payment'])) {
    $recipient_email = $_POST['recipient_email'];
    $amount = $_POST['amount'];
    $purpose = $_POST['purpose'];

    // Check if recipient exists
    $recipient_sql = "SELECT * FROM paypal_user WHERE email='$recipient_email'";
    $recipient_result = $conn->query($recipient_sql);

    if ($recipient_result->num_rows > 0) {
        $recipient = $recipient_result->fetch_assoc();
        $recipient_id = $recipient['id'];

        if ($user['balance'] >= $amount) {
            // Deduct from sender and add to recipient
            $conn->query("UPDATE paypal_user SET balance = balance - $amount WHERE id='$user_id'");
            $conn->query("UPDATE paypal_user SET balance = balance + $amount WHERE id='$recipient_id'");

            // Record the transaction with purpose
            $conn->query("INSERT INTO paypal_transactions (sender_id, recipient_id, amount, purpose) VALUES ('$user_id', '$recipient_id', '$amount', '$purpose')");
            header("Location: dashboard.php");
        } else {
            echo "<script>alert('Insufficient balance.');</script>";
        }
    } else {
        echo "<script>alert('Recipient not found.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            display: grid;
            grid-template-columns: 1fr 4fr;
            gap: 20px;
            max-width: 1200px;
            margin: 20px auto;
        }

        .sidebar {
            background-color: #0070ba;
            color: white;
            border-radius: 10px;
            padding: 20px;
            height: calc(100vh - 40px);
            position: sticky;
            top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sidebar img {
            width: 100px;
            margin-bottom: 10px;
        }

        .sidebar h2 {
            margin: 10px 0 20px;
            text-align: center;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            width: 100%;
        }

        .sidebar ul li {
            margin: 10px 0;
            padding: 10px;
            background-color: #005fa3;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .sidebar ul li:hover {
            background-color: #003f7a;
        }

        .main-content {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header .user-info img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid #0070ba;
        }

        .balance-section {
            text-align: center;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .balance-section h1 {
            font-size: 24px;
            margin: 0;
            color: #0070ba;
        }

        .quick-send-section {
            margin-bottom: 20px;
        }

        .quick-send {
            text-align: center;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
        }

        .quick-send input, .quick-send select, .quick-send button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .quick-send button {
            background-color: #0070ba;
            color: white;
            border: none;
            cursor: pointer;
        }

        .quick-send button:hover {
            background-color: #005fa3;
        }

        .transaction-history {
            display: none;
            margin-top: 20px;
        }

        .transactions-section h2 {
            margin: 0 0 10px;
            font-size: 18px;
        }

        .transactions-section table {
            width: 100%;
            border-collapse: collapse;
        }

        .transactions-section table th, .transactions-section table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .transactions-section table th {
            background-color: #f4f4f4;
        }

        .toggle-btn {
            margin-top: 10px;
            background-color: #0070ba;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .toggle-btn:hover {
            background-color: #005fa3;
        }
    </style>
    <script>
        function toggleTransactionHistory() {
            const historySection = document.querySelector('.transaction-history');
            historySection.style.display = historySection.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <img src="data:image/png;base64,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" alt="PayPal Logo"> <!-- Replace with actual logo -->
            <h2>PayPal</h2>
            <ul>
    <li onclick="window.location.href='dashboard.php';">Dashboard</li>
    <li onclick="window.location.href='wallet.php';">My Wallet</li>
    <li onclick="window.location.href='transactions.php';">Transactions</li>
    <li onclick="window.location.href='subscriptions.php';">Subscriptions</li>
    <li onclick="window.location.href='settings.php';">Settings</li>
</ul>

        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header Section -->
            <div class="header">
                <div class="user-info">
                    <img src="<?php echo $user['profile_picture'] ? htmlspecialchars($user['profile_picture']) : 'default-profile.jpg'; ?>" alt="Profile Picture">
                    <span><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
            </div>

            <!-- Balance Section -->
            <div class="balance-section">
                <h1>Balance: $<?php echo number_format($user['balance'], 2); ?></h1>
            </div>

            <!-- Quick Send Section -->
            <div class="quick-send-section">
                <div class="quick-send">
                    <h3>Quick Send</h3>
                    <form method="POST">
                        <input type="email" name="recipient_email" placeholder="Recipient Email" required>
                        <input type="number" name="amount" placeholder="Amount" required>
                        <select name="purpose" required>
                            <option value="">-- Select Purpose --</option>
                            <option value="Family Support">Family Support</option>
                            <option value="Fees Challan">Fees Challan</option>
                            <option value="Grocery Shopping">Grocery Shopping</option>
                            <option value="Utility Bills">Utility Bills</option>
                            <option value="Ayanat">Other</option>
                            <option value="Sadqa">Sadqa</option>
                        </select>
                        <button type="submit" name="send_payment">Send Payment</button>
                    </form>
                    <button class="toggle-btn" onclick="toggleTransactionHistory()">Toggle Transaction History</button>
                </div>

                <div class="transaction-history transactions-section">
                    <h2>Transaction History</h2>
                    <table>
                        <tr>
                            <th>Type</th>
                            <th>Other Party</th>
                            <th>Purpose</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                        <?php if ($transactions->num_rows > 0): ?>
                            <?php while ($transaction = $transactions->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($transaction['type']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['other_party']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['purpose']); ?></td>
                                    <td>$<?php echo number_format($transaction['amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['timestamp']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No transactions found.</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
