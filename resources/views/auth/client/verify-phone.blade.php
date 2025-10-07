<x-guest-layout>
    <div class="text-center mb-4">
        <div class="icon-circle">
            <i class="bi bi-phone text-primary"></i>
        </div>
        <h2 class="fw-bold text-dark mb-2">Verify Your Phone</h2>
        <p class="text-muted">Enter the 6-digit code sent to {{ session('contact_info') ?? session('phone') }}</p>
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
                    <strong>Verification failed:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('client.verify-phone') }}">
        @csrf
        
        <div class="mb-4">
            <label for="code" class="form-label fw-medium">Verification Code</label>
            <input id="code" type="text" name="code" required autofocus maxlength="6"
                   class="form-control form-control-lg text-center" 
                   placeholder="000000" style="letter-spacing: 0.5em;">
            <div class="form-text">Enter the 6-digit code sent to your phone</div>
        </div>
        
        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
            <i class="bi bi-check-circle me-2"></i>Verify & Complete Registration
        </button>
    </form>

    <div class="text-center">
        <p class="text-muted">Didn't receive the code? 
            <a href="{{ route('client.register') }}" class="text-primary text-decoration-none fw-medium">Try again</a>
        </p>
    </div>
</x-guest-layout>