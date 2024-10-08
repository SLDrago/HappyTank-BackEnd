<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Changed</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header,
        .footer {
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .footer {
            border-top: 1px solid #e0e0e0;
            border-bottom: none;
            padding-top: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            text-align: center;
            color: #333;
        }

        .footer p {
            margin: 0;
            font-size: 13px;
            text-align: center;
            color: #333;
        }

        .content {
            padding: 20px;
        }

        .content p {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
        }

        .button {
            text-align: center;
            margin: 20px 0;
        }

        .button a {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Password Changed - HappyTank</h1>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>This is a confirmation that the password for your account on <strong>HappyTank</strong> has been
                successfully changed. If you did not make this change, please contact our support team immediately.</p>
            <p>If you have any questions or need further assistance, feel free to reach out to us.</p>
        </div>
        <div class="button">
            <a href="http://localhost:5173/" target="_blank">Contact Support</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} HappyTank. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
