<x-guest-layout>
    <div class="text-center mb-4">
        <div class="icon-circle">
            <i class="bi bi-person-fill text-success"></i>
        </div>
        <h2 class="fw-bold text-dark mb-2">Client Login</h2>
        <p class="text-muted">Sign in to your account to manage your services</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <div class="d-flex align-items-start">
                <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
                <div>
                    <strong>Please correct the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login.client.authenticate') }}">
        @csrf
        <input type="hidden" name="user_type" value="client">
        
        <div class="mb-3">
            <label for="email" class="form-label fw-medium">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="form-control form-control-lg">
        </div>
        
        <div class="mb-3">
            <label for="password" class="form-label fw-medium">Password</label>
            <input id="password" type="password" name="password" required
                   class="form-control form-control-lg">
        </div>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input id="remember" name="remember" type="checkbox" class="form-check-input">
                <label for="remember" class="form-check-label">Remember me</label>
            </div>
            
            <a href="{{ route('password.request') }}" class="text-success text-decoration-none">
                Forgot password?
            </a>
        </div>
        
        <button type="submit" class="btn btn-success btn-lg w-100 mb-3">
            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
        </button>
    </form>

    <div class="text-center">
        <p class="text-muted mb-0">Don't have an account? 
            <a href="{{ route('register.client') }}" class="text-success text-decoration-none fw-medium">Register here</a>
        </p>
    </div>
</x-guest-layout>