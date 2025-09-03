<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AFIA IT Orbit Department</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .header {
            text-align: center;
            padding: 2rem 0;
        }
        .logo {
            font-size: 2.5rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        .subtitle {
            font-size: 1.2rem;
            color: #7f8c8d;
            margin-bottom: 2rem;
        }
        .card-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 2rem;
            margin-top: 2rem;
        }
        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 300px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-10px);
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        .card-description {
            color: #7f8c8d;
            margin-bottom: 2rem;
            min-height: 80px;
        }
        .btn {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin: 0.5rem;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .btn-register {
            background-color: #2ecc71;
        }
        .btn-register:hover {
            background-color: #27ae60;
        }
        .btn-login {
            background-color: #3498db;
        }
        .btn-login:hover {
            background-color: #2980b9;
        }
        .footer {
            text-align: center;
            margin-top: 4rem;
            padding: 2rem 0;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">AFIA IT Orbit Department</div>
            <div class="subtitle">Welcome to our platform. Please select your user type to continue.</div>
        </div>
        
        <div class="card-container">
            <div class="card">
                <div class="card-title">Client</div>
                <div class="card-description">
                    Access your client dashboard to manage your waste collection services, view schedules, and track your account.
                </div>
                <div>
                    <a href="{{ route('register.client') }}" class="btn btn-register">Register</a>
                    <a href="{{ route('login.client') }}" class="btn btn-login">Login</a>
                </div>
            </div>
            
            <div class="card">
                <div class="card-title">Contractor</div>
                <div class="card-description">
                    Access your contractor dashboard to manage your waste collection operations, client assignments, and business metrics.
                </div>
                <div>
                    <a href="{{ route('register.contractor') }}" class="btn btn-register">Register</a>
                    <a href="{{ route('login.contractor') }}" class="btn btn-login">Login</a>
                </div>
            </div>
            
            <div class="card">
                <div class="card-title">Administrator</div>
                <div class="card-description">
                    Access the admin dashboard to manage the entire system, users, contractors, and platform settings.
                </div>
                <div>
                    <a href="{{ route('register.admin') }}" class="btn btn-register">Register</a>
                    <a href="{{ route('login.admin') }}" class="btn btn-login">Login</a>
                </div>
            </div>
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} AFIA IT Orbit Department. All rights reserved.
        </div>
    </div>
</body>
</html>