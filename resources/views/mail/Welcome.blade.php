<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #2C3E50;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .header img {
            max-height: 50px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px 20px;
            color: #333333;
        }
        .content h1 {
            color: #2C3E50;
        }
        .content p {
            font-size: 16px;
            line-height: 1.5;
        }
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background-color: #3498DB;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777777;
            background-color: #f4f4f7;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            {{-- <img src="https://www.capitronix.com/logo.png" alt="Capitronix Logo"> --}}
            <h2 style="color: white">Welcome to Capitronix</h2>
        </div>
        <div class="content">
            <h1>Hello, {{ $userName }}!</h1>
            <p>
                Thank you for joining <strong>Capitronix</strong>. We're thrilled to have you on board.
                Start exploring our platform and make the most out of our services.
            </p>
            <p>
                Click the button below to visit your dashboard and get started:
            </p>
            <a href="{{ $dashboardUrl }}" class="button">Go to Dashboard</a>
            <p style="margin-top: 20px;">
                If you have any questions, feel free to <a href="mailto:support@capitronix.com">contact us</a>. We're always here to help!
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Capitronix. All rights reserved.
        </div>
    </div>
</body>
</html>
