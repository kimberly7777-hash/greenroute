<x-guest-layout>
    <div class="text-center mb-4">
        <div class="icon-circle">
            <i class="bi bi-person-check text-primary"></i>
        </div>
        <h2 class="fw-bold text-dark mb-2">Client Portal Access</h2>
        <p class="text-muted">Enter your account details to access the portal</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <div class="d-flex align-items-start">
                <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
                <div>
                    <strong>Access failed:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('client.login') }}">
        @csrf
        
        <div class="mb-3">
            <label for="registration_number" class="form-label fw-medium">Registration Number</label>
            <input id="registration_number" type="text" name="registration_number" value="{{ old('registration_number') }}" required autofocus
                   class="form-control form-control-lg" placeholder="Enter your registration number">
        </div>
        
        <div class="mb-3">
            <label for="phone" class="form-label fw-medium">Phone Number</label>
            <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required
                   class="form-control form-control-lg" placeholder="Enter your phone number">
        </div>
        
        <div class="mb-4">
            <label for="account_name" class="form-label fw-medium">Account Name</label>
            <input id="account_name" type="text" name="account_name" value="{{ old('account_name') }}" required
                   class="form-control form-control-lg" placeholder="Enter your account name">
        </div>
        
        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
            <i class="bi bi-box-arrow-in-right me-2"></i>Access Portal
        </button>
    </form>

    <div class="text-center">
        <p class="text-muted">Need help accessing your account? 
            <a href="#" class="text-primary text-decoration-none fw-medium">Contact Support</a>
        </p>
    </div>
</x-guest-layout>