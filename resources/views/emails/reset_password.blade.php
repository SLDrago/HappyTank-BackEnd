<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Reset Password') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
            color: #51545E;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .email-header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #EAEAEC;
        }

        .email-body {
            padding: 20px;
        }

        .email-footer {
            text-align: center;
            font-size: 12px;
            color: #6B6E76;
            padding-top: 20px;
            border-top: 1px solid #EAEAEC;
        }

        .button {
            background-color: #3869D4;
            color: #ffffff;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
        }

        .button:hover {
            background-color: #3457c5;
        }

        .small-text {
            font-size: 14px;
            color: #6B6E76;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h2>{{ __('Password Reset Request') }}</h2>
        </div>
        <div class="email-body">
            <p>{{ __('Hello,') }}</p>
            <p>{{ __('You are receiving this email because we received a password reset request for your account.') }}
            </p>
            <p>{{ __('If you did not request a password reset, please ignore this email. Otherwise, click the button below to reset your password:') }}
            </p>

            <a href="{{ $actionUrl }}" class="button">{{ __('Reset Password') }}</a>

            <p class="small-text">
                {{ __('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')]) }}
            </p>
            <p class="small-text">
                {{ __('If you’re having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:') }}
            </p>
            <p class="small-text"><a href="{{ $actionUrl }}">{{ $actionUrl }}</a></p>
        </div>
        <div class="email-footer">
            <p>{{ __('Thank you!') }}</p>
            <p>{{ config('app.name') }}</p>
        </div>
    </div>
</body>

</html>
