<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contractor Registration - AFIA IT Orbit Department</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }
        .header {
            text-align: center;
            padding: 1rem 0;
        }
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        .subtitle {
            font-size: 1rem;
            color: #7f8c8d;
            margin-bottom: 2rem;
        }
        .form-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
        .form-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #2c3e50;
        }
        .form-input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        .form-input:focus {
            outline: none;
            border-color: #3498db;
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
            border: none;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #7f8c8d;
        }
        .form-footer a {
            color: #3498db;
            text-decoration: none;
        }
        .form-footer a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: #e74c3c;
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">AFIA IT Orbit Department</div>
            <div class="subtitle">Contractor Registration</div>
        </div>
        
        <div class="form-container">
            <h2 class="form-title">Create Your Contractor Account</h2>
            
            @if($errors->any())
                <div class="error-message">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('register.contractor.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_type" value="contractor">
                
                <div class="form-group">
                    <label for="company_name" class="form-label">Company Name</label>
                    <input id="company_name" type="text" class="form-input" name="company_name" value="{{ old('company_name') }}" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="representative_name" class="form-label">Representative Name</label>
                    <input id="representative_name" type="text" class="form-input" name="name" value="{{ old('name') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Business Email</label>
                    <input id="email" type="email" class="form-input" name="email" value="{{ old('email') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="phone" class="form-label">Business Phone</label>
                    <input id="phone" type="text" class="form-input" name="phone" value="{{ old('phone') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="address" class="form-label">Business Address</label>
                    <input id="address" type="text" class="form-input" name="address" value="{{ old('address') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="license_number" class="form-label">Business License Number</label>
                    <input id="license_number" type="text" class="form-input" name="license_number" value="{{ old('license_number') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="certificate" class="form-label">Certificate of Incorporation</label>
                    <input id="certificate" type="file" class="form-input" name="certificate" required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" type="password" class="form-input" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input id="password_confirmation" type="password" class="form-input" name="password_confirmation" required>
                </div>
                
                <button type="submit" class="btn">Register</button>
                
                <div class="form-footer">
                    Already have an account? <a href="{{ route('login.contractor') }}">Login here</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>