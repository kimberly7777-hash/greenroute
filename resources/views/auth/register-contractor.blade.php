<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contractor Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .primary-dark { color: #055c5c; }
        .primary-light { 
            background-color: rgba(5, 92, 92, 0.08);
            border-left: 4px solid #055c5c;
        }
        .accent-color { color: #640404; }
        .btn-primary-custom {
            background-color: #055c5c;
            border-color: #055c5c;
            color: white;
        }
        .btn-primary-custom:hover {
            background-color: #044a4a;
            border-color: #044a4a;
        }
        .form-control:focus, .form-select:focus {
            border-color: #055c5c;
            box-shadow: 0 0 0 0.2rem rgba(5, 92, 92, 0.25);
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            background: rgba(5, 92, 92, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: #055c5c;
        }
        .alert-success-custom {
            background-color: rgba(5, 92, 92, 0.1);
            border-color: #055c5c;
            color: #055c5c;
        }
        .alert-danger-custom {
            background-color: rgba(100, 4, 4, 0.1);
            border-color: #640404;
            color: #640404;
        }
        .form-section {
            background: white;
            border-radius: 12px;
            padding: 2.5rem;
            border: 1px solid rgba(5, 92, 92, 0.15);
            box-shadow: 0 4px 6px rgba(5, 92, 92, 0.05);
        }
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header Section -->
                <div class="text-center mb-5">
                    <div class="icon-circle">
                        <i class="bi bi-truck"></i>
                    </div>
                    <h1 class="fw-bold primary-dark mb-3">Contractor Registration</h1>
                    <p class="lead text-muted">Join our platform to grow your waste management business</p>
                </div>

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="alert alert-danger-custom mb-4 rounded">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-exclamation-triangle-fill me-3 mt-1 fs-5"></i>
                            <div class="flex-grow-1">
                                <h5 class="alert-heading mb-2">Please correct the following errors:</h5>
                                <ul class="mb-0 ps-3">
                                    @foreach($errors->all() as $error)
                                        <li class="mb-1">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Registration Form -->
                <div class="form-section">
                    <form method="POST" action="{{ route('register.contractor.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="user_type" value="contractor">
                        
                        <!-- Company Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="primary-dark mb-3 border-bottom pb-2">Company Information</h4>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label primary-dark">Company Name *</label>
                                <input id="company_name" type="text" name="company_name" value="{{ old('company_name') }}" required autofocus
                                       class="form-control form-control-lg" placeholder="Enter your company name">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="representative_name" class="form-label primary-dark">Representative Name *</label>
                                <input id="representative_name" type="text" name="name" value="{{ old('name') }}" required
                                       class="form-control form-control-lg" placeholder="Enter representative's full name">
                            </div>
                        </div>
                        
                        <!-- Contact Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="primary-dark mb-3 border-bottom pb-2">Contact Information</h4>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label primary-dark">Business Email *</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                       class="form-control form-control-lg" placeholder="Enter business email address">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label primary-dark">Business Phone *</label>
                                <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required
                                       class="form-control form-control-lg" placeholder="Enter business phone number">
                            </div>
                        </div>
                        
                        <!-- Location Information -->
                        <div class="mb-4">
                            <label for="address" class="form-label primary-dark">Business Address *</label>
                            <input id="address" type="text" name="address" value="{{ old('address') }}" required
                                   class="form-control form-control-lg" placeholder="Enter complete business address">
                        </div>
                        
                        <div class="mb-4">
                            <label for="site_locations" class="form-label primary-dark">Site Locations *</label>
                            <textarea id="site_locations" name="site_locations" rows="3" required
                                      class="form-control form-control-lg" placeholder="List your service areas or site locations (separate with commas)">{{ old('site_locations') }}</textarea>
                            <div class="form-text">Specify all areas where you provide services</div>
                        </div>
                        
                        <!-- Business Documentation -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="primary-dark mb-3 border-bottom pb-2">Business Documentation</h4>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="license_number" class="form-label primary-dark">Business License Number *</label>
                                <input id="license_number" type="text" name="license_number" value="{{ old('license_number') }}" required
                                       class="form-control form-control-lg" placeholder="Enter license number">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="certificate" class="form-label primary-dark">Certificate of Incorporation *</label>
                                <input id="certificate" type="file" name="certificate" required accept=".pdf,.jpg,.jpeg,.png"
                                       class="form-control form-control-lg">
                                <div class="form-text">Upload PDF, JPG, or PNG files (max 5MB)</div>
                            </div>
                        </div>
                        
                        <!-- Account Security -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="primary-dark mb-3 border-bottom pb-2">Account Security</h4>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label primary-dark">Password *</label>
                                <input id="password" type="password" name="password" required
                                       class="form-control form-control-lg" placeholder="Create a strong password">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label primary-dark">Confirm Password *</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" required
                                       class="form-control form-control-lg" placeholder="Confirm your password">
                            </div>
                        </div>
                        
                        <!-- Benefits Section -->
                        <div class="alert alert-success-custom mb-4 rounded">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-check-circle-fill me-3 mt-1 fs-5"></i>
                                <div class="flex-grow-1">
                                    <h5 class="alert-heading mb-3">What you'll get as a contractor:</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <i class="bi bi-people-fill me-2"></i>Manage client database
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <i class="bi bi-calendar-check me-2"></i>Schedule and track pickups
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <i class="bi bi-receipt me-2"></i>Generate invoices and reports
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <i class="bi bi-graph-up me-2"></i>Grow your business efficiently
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary-custom btn-lg py-3">
                                <i class="bi bi-building me-2"></i>Create Business Account
                            </button>
                        </div>
                    </form>

                    <!-- Login Link -->
                    <div class="text-center mt-4 pt-3 border-top">
                        <p class="text-muted mb-0">Already have an account? 
                            <a href="{{ route('login.contractor') }}" class="primary-dark text-decoration-none fw-semibold">Sign in here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>