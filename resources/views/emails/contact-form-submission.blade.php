<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New {{ $contact->type }} Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f7f9;
        }

        .container {
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        h1 {
            color: #333;
            border-bottom: 2px solid #1a237e;
            padding-bottom: 10px;
            margin-top: 0;
        }

        .info-block {
            background-color: #e8eaf6;
            border-left: 4px solid #3f51b5;
            padding: 15px;
            margin-bottom: 20px;
        }

        .info-block p {
            margin: 5px 0;
        }

        .label {
            font-weight: bold;
            color: #1a237e;
        }

        .message {
            background-color: #f5f5f5;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 15px;
            margin-top: 20px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #757575;
            font-size: 0.9em;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>New {{ $contact->type }} Request</h1>
        <div class="info-block">
            <p><span class="label">Type:</span> {{ $contact->type }}</p>
            <p><span class="label">Name:</span> {{ $contact->first_name }} {{ $contact->last_name }}</p>
            <p><span class="label">Email:</span> {{ $contact->email }}</p>
        </div>
        <div class="message">
            <p><span class="label">Message:</span></p>
            <p>{{ $contact->message }}</p>
        </div>
        <div class="footer">
            <p>This email was sent from HappyTank Platform contact form.</p>
        </div>
    </div>
</body>

</html>
