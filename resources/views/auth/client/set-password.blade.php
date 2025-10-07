<x-guest-layout>
    <div class="text-center mb-4">
        <div class="icon-circle">
            <i class="bi bi-key text-success"></i>
        </div>
        <h2 class="fw-bold text-dark mb-2">Set Your Password</h2>
        <p class="text-muted">Create a secure password for your account</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <div class="d-flex align-items-start">
                <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
                <div>
                    <strong>Please correct the following:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('client.set-password') }}">
        @csrf
        
        <div class="mb-3">
            <label for="password" class="form-label fw-medium">New Password</label>
            <input id="password" type="password" name="password" required
                   class="form-control form-control-lg" placeholder="Create a secure password">
        </div>
        
        <div class="mb-4">
            <label for="password_confirmation" class="form-label fw-medium">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   class="form-control form-control-lg" placeholder="Confirm your password">
        </div>
        
        <button type="submit" class="btn btn-success btn-lg w-100 mb-3">
            <i class="bi bi-check-circle me-2"></i>Set Password & Continue
        </button>
    </form>
</x-guest-layout>