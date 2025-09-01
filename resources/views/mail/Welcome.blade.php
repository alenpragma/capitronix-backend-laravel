<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Capitronix</title>
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
        .login-info {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .login-info p {
            margin: 5px 0;
            font-family: monospace;
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
            <h1>Hello, Mr. {{ $userName }}!</h1>
            <p>Welcome to Capitronix! 🎉 You’ve just taken the first step toward smarter investing.</p>
            <p>Your account has been created successfully. Here are your login details:</p>
            
            <div class="login-info">
                <p>🔑 <strong>Login Information</strong></p>
                <p>E-mail: {{ $userEmail }}</p>
                <p>Password: {{ $userPassword }}</p>
            </div>

            <p>With our tools, insights, and expert strategies, managing your investments has never been easier. Log in, explore your dashboard, and start building your financial future today.</p>
            <p>We’re glad to have you on board!</p>

            <a href="{{ $dashboardUrl }}" class="button">Go to Dashboard</a>
        </div>
        <div class="footer">
            Best,<br>
            The Capitronix Team<br>
            🌐 <a href="https://www.capitronix.com">www.capitronix.com</a> | 📧 <a href="mailto:support@capitronix.com">support@capitronix.com</a><br>
            &copy; {{ date('Y') }} Capitronix. All rights reserved.
        </div>
    </div>
</body>
</html>
