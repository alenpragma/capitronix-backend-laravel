<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $generalSettings->app_name ?? 'Capitronix Admin' }}</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #0f172a, #1e293b, #2563eb);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
            overflow: hidden;
        }

        /* Watermark */
        body::before {
            content: "Capitronix";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-20deg);
            font-size: 120px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.07);
            white-space: nowrap;
            pointer-events: none;
            user-select: none;
        }

        .login-wrapper {
            background: #ffffff;
            padding: 40px 35px;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 420px;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
            z-index: 2;
            position: relative;
        }

        .login-wrapper .logo img {
            max-height: 70px;
            margin-bottom: 20px;
        }

        .login-wrapper h2 {
            margin: 0 0 25px;
            font-weight: 600;
            font-size: 22px;
            color: #1e293b;
        }

        .form-field {
            display: flex;
            align-items: center;
            margin-bottom: 18px;
            background: #f1f5f9;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-field:focus-within {
            border: 2px solid #2563eb;
            background: #fff;
            box-shadow: 0 0 6px rgba(37,99,235,0.2);
        }

        .form-field i {
            margin-right: 10px;
            color: #64748b;
        }

        .form-field input {
            border: none;
            outline: none;
            background: transparent;
            width: 100%;
            font-size: 16px;
            color: #0f172a;
        }

        .btn {
            width: 100%;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            border: none;
            padding: 14px;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(37, 99, 235, 0.3);
        }

        .alert {
            margin-bottom: 20px;
            color: #fff;
            padding: 12px;
            background: #dc2626;
            border-radius: 8px;
            text-align: left;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="logo">
        <img src="{{ asset('storage/' . $generalSettings->logo) }}" alt="Capitronix Logo">
    </div>
    <h2>Admin Login</h2>

    {{-- Session error --}}
    @if(session('error'))
        <div class="alert">{{ session('error') }}</div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-field">
            <i class="fa fa-envelope"></i>
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
        </div>
        <div class="form-field">
            <i class="fa fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
</div>
</body>
</html>
