<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscriptions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            max-width: 600px;
            width: 100%;
            text-align: center;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .container img {
            width: 100px;
            margin-bottom: 20px;
        }

        .subscription-buttons {
            margin-top: 20px;
        }

        .subscription-buttons button {
            width: 100%;
            margin: 10px 0;
            padding: 15px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .paypal-subscribe {
            background-color: #ffc439;
            color: #333;
            font-weight: bold;
        }

        .paypal-credit-subscribe {
            background-color: #0070ba;
            color: white;
        }

        .debit-credit {
            background-color: #333;
            color: white;
        }

        .powered-by {
            margin-top: 20px;
            font-size: 12px;
            color: #aaa;
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
        <!-- Header Logos -->
        <img src="data:image/png;base64,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" alt="PayPal Logo">
        <h1>Choose Your Subscription</h1>
        <p>Select a subscription option below:</p>

        <!-- Subscription Buttons -->
        <div class="subscription-buttons">
            <button class="paypal-subscribe" onclick="alert('PayPal Subscribe clicked!')">PayPal Subscribe</button>
            <button class="paypal-credit-subscribe" onclick="alert('PayPal Credit Subscribe clicked!')">PayPal Credit Subscribe</button>
            <button class="debit-credit" onclick="alert('Debit or Credit Card clicked!')">Debit or Credit Card</button>
        </div>

        <!-- Powered By Section -->
        <div class="powered-by">
            Powered by <b>PayPal</b>
        </div>

        <!-- Back to Dashboard Button -->
        <a href="dashboard.php" class="back-button">Back to Dashboard</a>
    </div>
</body>
</html>
