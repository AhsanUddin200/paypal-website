<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user transaction history
$transactions_sql = "
    SELECT 
        t.amount, 
        t.purpose, 
        u.email AS other_party, 
        CASE 
            WHEN t.sender_id = '$user_id' THEN 'Sent'
            WHEN t.recipient_id = '$user_id' THEN 'Received'
        END AS type, 
        t.timestamp 
    FROM paypal_transactions t
    LEFT JOIN paypal_user u 
        ON (t.sender_id = u.id AND t.recipient_id != '$user_id') 
        OR (t.recipient_id = u.id AND t.sender_id != '$user_id')
    WHERE t.sender_id = '$user_id' OR t.recipient_id = '$user_id'
    ORDER BY t.timestamp DESC";

$transactions = $conn->query($transactions_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #0070ba;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #f4f4f4;
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
    <div class="container">
        <h1>Transaction History</h1>
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
        <a href="dashboard.php" class="back-button">Back to Dashboard</a>
    </div>
</body>
</html>
